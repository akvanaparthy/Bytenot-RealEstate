<?php
/**
 * Post meta for properties, projects, agents and enquiries.
 *
 * Every key is registered with a REST schema and a sanitiser so the editor, Block
 * Bindings and the CRM sync all see the same shape. Prices are whole rupees; areas are
 * numbers with a unit key; dates are YYYY-MM or YYYY-MM-DD strings.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions per post type.
 *
 * @return array<string, array<string, array>> post type => key => args.
 */
function bytenot_core_fields() {
	$text   = array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' );
	$url    = array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' );
	$int    = array( 'type' => 'integer', 'sanitize_callback' => 'absint' );
	$num    = array( 'type' => 'number', 'sanitize_callback' => 'bytenot_core_sanitize_number' );
	$bool   = array( 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean' );
	$id     = array( 'type' => 'integer', 'sanitize_callback' => 'absint' );
	$ids    = array(
		'type'              => 'array',
		'sanitize_callback' => 'bytenot_core_sanitize_ids',
		'show_in_rest'      => array( 'schema' => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ) ),
	);
	$marks  = array(
		'type'              => 'array',
		'sanitize_callback' => 'bytenot_core_sanitize_landmarks',
		'show_in_rest'      => array(
			'schema' => array(
				'type'  => 'array',
				'items' => array(
					'type'       => 'object',
					'properties' => array(
						'name'     => array( 'type' => 'string' ),
						'distance' => array( 'type' => 'number' ),
						'unit'     => array( 'type' => 'string', 'enum' => array( 'km', 'm', 'min' ) ),
						'type'     => array( 'type' => 'string' ),
						'time'     => array( 'type' => 'string' ),
					),
				),
			),
		),
	);

	$listing = array(
		// pricing
		'price'            => $int,
		'price_on_request' => $bool,
		'rent_period'      => $text,   // month | year
		'deposit'          => $int,
		'maintenance'      => $int,
		// size
		'bedrooms'         => $num,
		'bedroom_label'    => $text,   // "3 BHK", "Studio"
		'bathrooms'        => $num,
		'area_primary'     => $num,
		'area_primary_type'=> $text,   // carpet | built-up | super-built-up | plot
		'area_secondary'   => $num,
		'area_secondary_type' => $text,
		'area_unit'        => $text,   // sqft | sqyd | sqm | acre
		// building
		'floor'            => $int,
		'total_floors'     => $int,
		'facing'           => $text,
		'parking'          => $text,
		'year_built'       => $int,
		'furnishing'       => $text,
		'ownership'        => $text,
		// timeline
		'possession_date'  => $text,
		'sold_at'          => $text,
		// legal
		'rera_number'      => $text,
		'rera_state'       => $text,
		'rera_url'         => $url,
		'approvals'        => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ), // one per line
		'banks'            => $text,   // "SBI, HDFC, ICICI"
		// location
		'lat'              => $num,
		'lng'              => $num,
		'address_line'     => $text,
		'landmarks'        => $marks,
		// media
		'gallery'          => $ids,
		'floor_plans'      => $ids,
		'brochure_id'      => $id,
		'video_url'        => $url,
		'tour_url'         => $url,
		// relations and flags
		'project_id'       => $id,
		'agent_id'         => $id,
		'featured'         => $bool,
		'featured_reason'  => $text,
		'reference_id'     => $text,
		'source'           => $text,   // zoho | manual | import
		'source_id'        => $text,
	);

	return array(
		'property' => $listing,
		'project'  => array_merge(
			$listing,
			array(
				'price_min'       => $int,
				'price_max'       => $int,
				'units_total'     => $int,
				'towers'          => $int,
				'launch_date'     => $text,
				'completion_date' => $text,
				'developer'       => $text,
			)
		),
		'agent'    => array(
			'role'        => $text,
			'phone'       => $text,
			'whatsapp'    => $text,
			'email'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'rera_number' => $text,
			'languages'   => $text,
		),
		'enquiry'  => array(
			'name'        => $text,
			'email'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'phone'       => $text,
			'message'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			'property_id' => $id,
			'project_id'  => $id,
			'agent_id'    => $id,
			'source_url'  => $url,
			'form'        => $text,   // inquiry | visit | brochure
			'preferred_date' => $text,
			'consent'     => $bool,
			'attribution' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			'crm_status'  => $text,   // pending | sent | failed
			'crm_id'      => $text,
			'crm_error'   => $text,
		),
	);
}

/**
 * Register every field with `show_in_rest` so the editor and bindings can read it.
 */
function bytenot_core_register_meta() {
	foreach ( bytenot_core_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $args ) {
			register_post_meta(
				$post_type,
				'pc_' . $key,
				array(
					'type'              => $args['type'],
					'single'            => true,
					'show_in_rest'      => $args['show_in_rest'] ?? array( 'schema' => array( 'type' => $args['type'] ) ),
					'sanitize_callback' => $args['sanitize_callback'],
					'auth_callback'     => 'bytenot_core_can_edit_meta',
				)
			);
		}
	}
}
add_action( 'init', 'bytenot_core_register_meta' );

/**
 * Meta is editable by anyone who can edit the post.
 *
 * @param bool   $allowed  Unused.
 * @param string $meta_key Unused.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function bytenot_core_can_edit_meta( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Numbers: accept "1,650" and "3.5", store floats.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function bytenot_core_sanitize_number( $value ) {
	return (float) str_replace( ',', '', (string) $value );
}

/**
 * Lists of attachment IDs.
 *
 * @param mixed $value Raw value.
 * @return int[]
 */
function bytenot_core_sanitize_ids( $value ) {
	return array_values( array_filter( array_map( 'absint', (array) $value ) ) );
}

/**
 * Landmarks: name, distance, unit, type.
 *
 * @param mixed $value Raw value.
 * @return array<int, array<string, mixed>>
 */
function bytenot_core_sanitize_landmarks( $value ) {
	$out = array();
	foreach ( (array) $value as $row ) {
		if ( ! is_array( $row ) || empty( $row['name'] ) ) {
			continue;
		}
		$unit  = isset( $row['unit'] ) && in_array( $row['unit'], array( 'km', 'm', 'min' ), true ) ? $row['unit'] : 'km';
		$out[] = array(
			'name'     => sanitize_text_field( $row['name'] ),
			'distance' => isset( $row['distance'] ) ? (float) $row['distance'] : 0,
			'unit'     => $unit,
			'type'     => isset( $row['type'] ) ? sanitize_key( $row['type'] ) : '',
			'time'     => isset( $row['time'] ) ? sanitize_text_field( $row['time'] ) : '',
		);
	}
	return $out;
}

/**
 * Read a field with the plugin prefix.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field key without prefix.
 * @return mixed
 */
function bytenot_core_get( $post_id, $key ) {
	return get_post_meta( $post_id, 'pc_' . $key, true );
}
