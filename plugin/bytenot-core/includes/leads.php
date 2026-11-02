<?php
/**
 * Leads: REST endpoint, local storage, email, queued CRM push with retries.
 *
 * POST /wp-json/bytenot/v1/leads
 * { name, email, phone, message, property_id, form, preferred_date, consent, source_url, website (honeypot), started (timestamp) }
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Route.
 */
function bytenot_core_register_routes() {
	register_rest_route(
		'bytenot/v1',
		'/leads',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'bytenot_core_rest_create_lead',
			'permission_callback' => '__return_true', // public form; abuse is limited below
			'args'                => array(
				'name'           => array( 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
				'email'          => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
				'phone'          => array( 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
				'message'        => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
				'property_id'    => array( 'required' => false, 'type' => 'integer', 'sanitize_callback' => 'absint' ),
				'form'           => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_key', 'default' => 'inquiry' ),
				'preferred_date' => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
				'consent'        => array( 'required' => false, 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean' ),
				'source_url'     => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
				'website'        => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
				'started'        => array( 'required' => false, 'type' => 'integer', 'sanitize_callback' => 'absint' ),
				'attribution'    => array( 'required' => false, 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'bytenot_core_register_routes' );

/**
 * Create the enquiry.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function bytenot_core_rest_create_lead( WP_REST_Request $request ) {
	// Honeypot and time-to-submit: bots fill hidden fields and submit instantly.
	if ( $request['website'] ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}
	if ( $request['started'] && ( time() - (int) $request['started'] ) < 3 ) {
		return new WP_Error( 'too_fast', __( 'Please take a moment and try again.', 'bytenot-core' ), array( 'status' => 429 ) );
	}
	// Per-IP throttle: 5 leads per 10 minutes.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'pc_leads_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		return new WP_Error( 'rate_limited', __( 'Too many requests. Please try again later.', 'bytenot-core' ), array( 'status' => 429 ) );
	}
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );

	$phone = preg_replace( '/[^\d+]/', '', (string) $request['phone'] );
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 10 ) {
		return new WP_Error( 'bad_phone', __( 'Please enter a valid phone number.', 'bytenot-core' ), array( 'status' => 400 ) );
	}
	if ( $request['email'] && ! is_email( $request['email'] ) ) {
		return new WP_Error( 'bad_email', __( 'Please enter a valid email address.', 'bytenot-core' ), array( 'status' => 400 ) );
	}

	$property_id = (int) $request['property_id'];
	$property    = $property_id ? get_post( $property_id ) : null;
	$title       = $property ? $property->post_title : get_bloginfo( 'name' );

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'enquiry',
			'post_status' => 'private',
			/* translators: 1: lead name, 2: property or site name. */
			'post_title'  => sprintf( __( '%1$s · %2$s', 'bytenot-core' ), $request['name'], $title ),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		return new WP_Error( 'store_failed', __( 'Your enquiry could not be saved. Please call us instead.', 'bytenot-core' ), array( 'status' => 500 ) );
	}

	$meta = array(
		'name'           => $request['name'],
		'email'          => $request['email'],
		'phone'          => $phone,
		'message'        => $request['message'],
		'property_id'    => $property_id,
		'project_id'     => $property ? (int) bytenot_core_get( $property_id, 'project_id' ) : 0,
		'agent_id'       => $property ? (int) bytenot_core_get( $property_id, 'agent_id' ) : 0,
		'source_url'     => $request['source_url'],
		'form'           => $request['form'],
		'preferred_date' => $request['preferred_date'],
		'consent'        => (bool) $request['consent'],
		'attribution'    => $request['attribution'],
		'crm_status'     => 'pending',
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $post_id, 'pc_' . $k, $v );
	}

	// Email now, CRM in the background with retries.
	( new Bytenot_Core_Email_Provider() )->push_lead( bytenot_core_lead_payload( $post_id ) );
	if ( 'email' !== bytenot_core_setting( 'crm_provider' ) ) {
		wp_schedule_single_event( time() + 5, 'bytenot_core_push_lead', array( $post_id, 1 ) );
	} else {
		update_post_meta( $post_id, 'pc_crm_status', 'sent' );
	}

	return new WP_REST_Response(
		array(
			'ok'      => true,
			'id'      => $post_id,
			'message' => __( 'Thanks. An advisor will call you shortly.', 'bytenot-core' ),
		),
		201
	);
}

/**
 * The lead as the CRM sees it.
 *
 * @param int $post_id Enquiry ID.
 * @return array<string, mixed>
 */
function bytenot_core_lead_payload( $post_id ) {
	$get         = static fn( $k ) => bytenot_core_get( $post_id, $k );
	$property_id = (int) $get( 'property_id' );
	return array(
		'id'             => $post_id,
		'created'        => get_post_time( 'c', true, $post_id ),
		'name'           => (string) $get( 'name' ),
		'email'          => (string) $get( 'email' ),
		'phone'          => (string) $get( 'phone' ),
		'message'        => (string) $get( 'message' ),
		'form'           => (string) $get( 'form' ),
		'preferred_date' => (string) $get( 'preferred_date' ),
		'consent'        => (bool) $get( 'consent' ),
		'property_id'    => $property_id,
		'property_title' => $property_id ? get_the_title( $property_id ) : '',
		'property_url'   => $property_id ? get_permalink( $property_id ) : '',
		'property_ref'   => $property_id ? (string) bytenot_core_get( $property_id, 'reference_id' ) : '',
		'crm_record_id'  => $property_id ? (string) bytenot_core_get( $property_id, 'source_id' ) : '',
		'agent_id'       => (int) $get( 'agent_id' ),
		'source_url'     => (string) $get( 'source_url' ),
		'attribution'    => (string) $get( 'attribution' ),
		'site'           => home_url(),
	);
}

/**
 * Background push with up to three retries.
 *
 * @param int $post_id Enquiry ID.
 * @param int $attempt Attempt number.
 */
function bytenot_core_push_lead( $post_id, $attempt = 1 ) {
	$provider = 'webhook' === bytenot_core_setting( 'crm_provider' ) ? new Bytenot_Core_Webhook_Provider() : null;
	$provider = apply_filters( 'bytenot_core_crm_provider', $provider );
	if ( ! $provider instanceof Bytenot_Core_CRM_Provider ) {
		update_post_meta( $post_id, 'pc_crm_status', 'sent' );
		return;
	}
	$result = $provider->push_lead( bytenot_core_lead_payload( $post_id ) );
	if ( true === $result ) {
		update_post_meta( $post_id, 'pc_crm_status', 'sent' );
		delete_post_meta( $post_id, 'pc_crm_error' );
		return;
	}
	update_post_meta( $post_id, 'pc_crm_error', $result->get_error_message() );
	if ( $attempt < 3 ) {
		wp_schedule_single_event( time() + ( 5 * MINUTE_IN_SECONDS * $attempt ), 'bytenot_core_push_lead', array( $post_id, $attempt + 1 ) );
	} else {
		update_post_meta( $post_id, 'pc_crm_status', 'failed' );
	}
}
add_action( 'bytenot_core_push_lead', 'bytenot_core_push_lead', 10, 2 );

/**
 * Admin columns for enquiries.
 *
 * @param array $columns Columns.
 * @return array
 */
function bytenot_core_enquiry_columns( $columns ) {
	return array(
		'cb'       => $columns['cb'],
		'title'    => __( 'Lead', 'bytenot-core' ),
		'phone'    => __( 'Phone', 'bytenot-core' ),
		'form'     => __( 'Form', 'bytenot-core' ),
		'crm'      => __( 'CRM', 'bytenot-core' ),
		'date'     => __( 'Received', 'bytenot-core' ),
	);
}
add_filter( 'manage_enquiry_posts_columns', 'bytenot_core_enquiry_columns' );

/**
 * Column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function bytenot_core_enquiry_column( $column, $post_id ) {
	if ( 'phone' === $column ) {
		echo esc_html( bytenot_core_get( $post_id, 'phone' ) );
	} elseif ( 'form' === $column ) {
		echo esc_html( bytenot_core_get( $post_id, 'form' ) );
	} elseif ( 'crm' === $column ) {
		$status = bytenot_core_get( $post_id, 'crm_status' );
		$error  = bytenot_core_get( $post_id, 'crm_error' );
		echo esc_html( $status ) . ( $error ? ' <span title="' . esc_attr( $error ) . '">⚠</span>' : '' );
	}
}
add_action( 'manage_enquiry_posts_custom_column', 'bytenot_core_enquiry_column', 10, 2 );
