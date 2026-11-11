<?php
/**
 * Post meta for listings, agents and enquiries. Keys carry the `bn_` prefix.
 *
 * Every key is registered with a REST schema and a sanitiser so the editor, the REST
 * API and the CRM sync all see the same shape. Prices are whole rupees; areas are
 * numbers with a unit key; dates are YYYY-MM or YYYY-MM-DD strings.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

const BYTENOT_CORE_META_PREFIX = 'bn_';

/**
 * Field definitions per post type. Listing fields carry the kinds they belong to.
 *
 * @return array<string, array<string, array>> post type => key => args.
 */
function bytenot_core_fields() {
	$text  = array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' );
	$lines = array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' );
	$url   = array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' );
	$int   = array( 'type' => 'integer', 'sanitize_callback' => 'absint' );
	$num   = array( 'type' => 'number', 'sanitize_callback' => 'bytenot_core_sanitize_number' );
	$bool  = array( 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean' );
	$id    = array( 'type' => 'integer', 'sanitize_callback' => 'absint' );
	$ids   = array(
		'type'              => 'array',
		'sanitize_callback' => 'bytenot_core_sanitize_ids',
		'show_in_rest'      => array( 'schema' => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ) ),
	);
	$marks = array(
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

	return array(
		'listing' => array(
			// pricing
			'price'               => $int,    // a unit's or a property's price; for rentals the monthly rent
			'price_min'           => $int,    // a project's range; filled from its units when it has any
			'price_max'           => $int,
			'price_on_request'    => $bool,
			'rent_period'         => $text,   // month | year
			'deposit'             => $int,
			'maintenance'         => $int,
			// size
			'bedrooms'            => $num,
			'bedroom_label'       => $text,   // "3 BHK", "Studio"
			'bathrooms'           => $num,
			'area_primary'        => $num,
			'area_primary_type'   => $text,   // carpet | built-up | super-built-up | plot
			'area_secondary'      => $num,
			'area_secondary_type' => $text,
			'area_unit'           => $text,   // sqft | sqyd | sqm | acre
			'area_min'            => $num,    // a project's range
			'area_max'            => $num,
			'config_label'        => $text,   // a project's configurations: "2, 3 BHK"
			'bhk_set'             => $text,   // a project's bedroom counts for filtering: "2,3"
			// a unit inside a project
			'tower'               => $text,
			'unit_no'             => $text,
			// building
			'floor'               => $int,
			'total_floors'        => $int,
			'facing'              => $text,
			'parking'             => $text,
			'year_built'          => $int,
			'furnishing'          => $text,
			'ownership'           => $text,
			// a project
			'developer'           => $text,
			'developer_id'        => $id,     // a partner
			'units_total'         => $int,
			'units_listed'        => $int,    // how many apartments are listed under it (rollup)
			'towers'              => $int,
			'launch_date'         => $text,
			'completion_date'     => $text,
			// timeline
			'possession_date'     => $text,
			'sold_at'             => $text,
			// legal
			'rera_number'         => $text,
			'rera_state'          => $text,
			'rera_url'            => $url,
			'approvals'           => $lines,  // one per line
			'banks'               => $text,   // "SBI, HDFC, ICICI"
			// location
			'lat'                 => $num,
			'lng'                 => $num,
			'address_line'        => $text,
			'landmarks'           => $marks,
			// media
			'gallery'             => $ids,
			'floor_plans'         => $ids,
			'brochure_id'         => $id,
			'video_url'           => $url,
			'tour_url'            => $url,
			// people and flags
			'agent_id'            => $id,
			'featured'            => $bool,
			'featured_reason'     => $text,
			'reference_id'        => $text,
			'source'              => $text,   // manual | import | demo | a CRM name
			'source_id'           => $text,
		),
		'agent'   => array(
			'role'        => $text,
			'phone'       => $text,
			'whatsapp'    => $text,
			'email'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'rera_number' => $text,
			'languages'   => $text,
		),
		'enquiry' => array(
			'name'           => $text,
			'email'          => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'phone'          => $text,
			'message'        => $lines,
			'listing_id'     => $id,
			'project_id'     => $id,     // the listing's project when it is an apartment
			'agent_id'       => $id,
			'source_url'     => $url,
			'form'           => $text,   // inquiry | visit | brochure
			'preferred_date' => $text,
			'consent'        => $bool,
			'attribution'    => $lines,
			'crm_status'     => $text,   // pending | sent | failed
			'crm_id'         => $text,
			'crm_error'      => $text,
		),
	);
}

/**
 * Register every field with `show_in_rest` so the editor and the REST API can read it.
 */
function bytenot_core_register_meta() {
	foreach ( bytenot_core_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $args ) {
			register_post_meta(
				$post_type,
				BYTENOT_CORE_META_PREFIX . $key,
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
 * Landmarks: name, distance, unit, type, drive time.
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
	return get_post_meta( $post_id, BYTENOT_CORE_META_PREFIX . $key, true );
}

/**
 * Write a field with the plugin prefix; an empty value removes it.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field key without prefix.
 * @param mixed  $value   Value.
 */
function bytenot_core_set( $post_id, $key, $value ) {
	if ( '' === $value || null === $value || ( is_array( $value ) && ! $value ) ) {
		delete_post_meta( $post_id, BYTENOT_CORE_META_PREFIX . $key );
	} else {
		update_post_meta( $post_id, BYTENOT_CORE_META_PREFIX . $key, $value );
	}
}
