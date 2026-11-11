<?php
/**
 * Admin actions: toggle the preview lock, push a lead again, send a test lead,
 * export enquiries. All through admin-post.php with a nonce and a capability check.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Back to a plugin screen with a message.
 *
 * @param string $screen Screen slug.
 * @param string $msg    Message key.
 * @param bool   $ok     Outcome.
 * @param string $text   Detail.
 */
function bytenot_core_admin_back( $screen, $msg, $ok = true, $text = '' ) {
	$args = array( 'page' => $screen, 'bn_msg' => $msg, 'bn_ok' => $ok ? 1 : 0 );
	if ( $text ) {
		$args['bn_text'] = rawurlencode( mb_substr( $text, 0, 160 ) );
	}
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}

/**
 * Preview lock on/off from the dashboard.
 */
function bytenot_core_action_toggle_preview() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'bytenot-core' ) );
	}
	check_admin_referer( 'bytenot_core_toggle_preview' );
	$s = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$s['preview_mode'] = empty( $s['preview_mode'] );
	update_option( 'bytenot_core_settings', $s );
	bytenot_core_admin_back( isset( $_GET['back'] ) ? sanitize_key( wp_unslash( $_GET['back'] ) ) : 'bytenot', 'preview', (bool) $s['preview_mode'] );
}
add_action( 'admin_post_bytenot_core_toggle_preview', 'bytenot_core_action_toggle_preview' );

/**
 * Push one lead to the CRM again.
 */
function bytenot_core_action_retry_lead() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'bytenot-core' ) );
	}
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	check_admin_referer( 'bytenot_core_retry_' . $id );
	if ( 'enquiry' !== get_post_type( $id ) ) {
		bytenot_core_admin_back( 'bytenot-leads', 'retry', false, __( 'Unknown lead.', 'bytenot-core' ) );
	}
	bytenot_core_push_lead( $id, 3 ); // one attempt, no further retries
	$status = bytenot_core_get( $id, 'crm_status' );
	bytenot_core_admin_back( 'bytenot-leads', 'retry', 'sent' === $status, (string) bytenot_core_get( $id, 'crm_error' ) );
}
add_action( 'admin_post_bytenot_core_retry_lead', 'bytenot_core_action_retry_lead' );

/**
 * Send a sample lead through the configured providers.
 */
function bytenot_core_action_test_lead() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'bytenot-core' ) );
	}
	check_admin_referer( 'bytenot_core_test_lead' );
	$user   = wp_get_current_user();
	$sample = array(
		'id'             => 0,
		'created'        => gmdate( 'c' ),
		'name'           => __( 'Test lead', 'bytenot-core' ),
		'email'          => $user->user_email,
		'phone'          => '+919876543210',
		'message'        => __( 'This is a test from Bytenot Core › Integrations. No action needed.', 'bytenot-core' ),
		'form'           => 'test',
		'preferred_date' => '',
		'consent'        => true,
		'listing_id'     => 0,
		'listing_kind'   => '',
		'listing_title'  => '',
		'listing_url'    => '',
		'listing_ref'    => '',
		'project_title'  => '',
		'crm_record_id'  => '',
		'agent_id'       => 0,
		'source_url'     => admin_url( 'admin.php?page=bytenot-integrations' ),
		'attribution'    => 'test',
		'site'           => home_url(),
	);
	$results = array();
	$email   = ( new Bytenot_Core_Email_Provider() )->push_lead( $sample );
	$results[] = true === $email ? __( 'email ok', 'bytenot-core' ) : __( 'email failed', 'bytenot-core' ) . ': ' . $email->get_error_message();
	$ok = true === $email;
	if ( 'webhook' === bytenot_core_setting( 'crm_provider' ) ) {
		$hook      = ( new Bytenot_Core_Webhook_Provider() )->push_lead( $sample );
		$results[] = true === $hook ? __( 'webhook ok', 'bytenot-core' ) : __( 'webhook failed', 'bytenot-core' ) . ': ' . $hook->get_error_message();
		$ok        = $ok && true === $hook;
	}
	bytenot_core_admin_back( 'bytenot-integrations', 'test_lead', $ok, implode( ' · ', $results ) );
}
add_action( 'admin_post_bytenot_core_test_lead', 'bytenot_core_action_test_lead' );

/**
 * Enquiries as CSV. Visitor-typed cells that start like a spreadsheet formula are
 * neutralised with a leading apostrophe.
 */
function bytenot_core_action_export_leads() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'bytenot-core' ) );
	}
	check_admin_referer( 'bytenot_core_export_leads' );
	$ids  = get_posts( array( 'post_type' => 'enquiry', 'post_status' => 'private', 'posts_per_page' => -1, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true ) );
	$safe = static fn( $v ) => preg_match( '/^[=+\-@\t\r]/', (string) $v ) ? "'" . $v : (string) $v;
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="enquiries-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fputcsv( $out, array( 'id', 'received', 'name', 'phone', 'email', 'form', 'listing', 'project', 'preferred_date', 'message', 'source_url', 'crm_status', 'crm_error' ) );
	foreach ( $ids as $id ) {
		$l  = (int) bytenot_core_get( $id, 'listing_id' );
		$pr = (int) bytenot_core_get( $id, 'project_id' );
		fputcsv( $out, array_map( $safe, array( $id, get_post_time( 'Y-m-d H:i', false, $id ), bytenot_core_get( $id, 'name' ), bytenot_core_get( $id, 'phone' ), bytenot_core_get( $id, 'email' ), bytenot_core_get( $id, 'form' ), $l ? get_the_title( $l ) : '', $pr ? get_the_title( $pr ) : '', bytenot_core_get( $id, 'preferred_date' ), bytenot_core_get( $id, 'message' ), bytenot_core_get( $id, 'source_url' ), bytenot_core_get( $id, 'crm_status' ), bytenot_core_get( $id, 'crm_error' ) ) ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_bytenot_core_export_leads', 'bytenot_core_action_export_leads' );

/**
 * After the settings form saves, come back to the screen that posted it.
 *
 * @param string $location Redirect URL.
 * @return string
 */
function bytenot_core_settings_redirect( $location ) {
	if ( false !== strpos( $location, 'options-general.php' ) && isset( $_POST['bytenot_core_settings']['_screen'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- options.php verified it.
		$screen = sanitize_key( wp_unslash( $_POST['bytenot_core_settings']['_screen'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( bytenot_core_admin_screens()[ $screen ] ) ) {
			return add_query_arg( array( 'page' => $screen, 'bn_msg' => 'saved', 'bn_ok' => 1 ), admin_url( 'admin.php' ) );
		}
	}
	return $location;
}
add_filter( 'wp_redirect', 'bytenot_core_settings_redirect' );
