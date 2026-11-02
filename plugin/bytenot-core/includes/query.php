<?php
/**
 * Search query vars → the property archive query. Server-side, so every filter URL is
 * crawlable and works without JavaScript.
 *
 * ?type=villa&location=hyderabad&budget=10000000-20000000&bhk=3&intent=for-sale&sort=price-asc
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the public query vars.
 *
 * @param string[] $vars Query vars.
 * @return string[]
 */
function bytenot_core_query_vars( $vars ) {
	return array_merge( $vars, array( 'type', 'location', 'budget', 'bhk', 'intent', 'sort' ) );
}
add_filter( 'query_vars', 'bytenot_core_query_vars' );

/**
 * Apply the filters to the main property archive query.
 *
 * @param WP_Query $query The query.
 */
function bytenot_core_filter_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'property' ) ) {
		return;
	}
	$tax  = (array) $query->get( 'tax_query' );
	$meta = (array) $query->get( 'meta_query' );

	$type = sanitize_key( (string) $query->get( 'type' ) );
	if ( $type ) {
		$tax[] = array( 'taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $type );
	}
	$location = sanitize_key( (string) $query->get( 'location' ) );
	if ( $location ) {
		$tax[] = array( 'taxonomy' => 'location', 'field' => 'slug', 'terms' => $location, 'include_children' => true );
	}
	$intent = sanitize_key( (string) $query->get( 'intent' ) );
	if ( in_array( $intent, array( 'for-sale', 'for-rent' ), true ) ) {
		$tax[] = array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => $intent );
	} elseif ( in_array( $intent, array( 'new-launch', 'resale' ), true ) ) {
		$tax[] = array( 'taxonomy' => 'possession', 'field' => 'slug', 'terms' => $intent );
	}
	$budget = (string) $query->get( 'budget' );
	if ( preg_match( '/^(\d+)-(\d*)$/', $budget, $m ) ) {
		$min = (int) $m[1];
		$max = '' === $m[2] ? null : (int) $m[2];
		$meta[] = null === $max
			? array( 'key' => 'pc_price', 'value' => $min, 'compare' => '>=', 'type' => 'NUMERIC' )
			: array( 'key' => 'pc_price', 'value' => array( $min, $max ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' );
	}
	$bhk = (string) $query->get( 'bhk' );
	if ( '' !== $bhk ) {
		$meta[] = '4' === $bhk
			? array( 'key' => 'pc_bedrooms', 'value' => 4, 'compare' => '>=', 'type' => 'NUMERIC' )
			: array( 'key' => 'pc_bedrooms', 'value' => (float) $bhk, 'compare' => '=', 'type' => 'NUMERIC' );
	}
	$sort = sanitize_key( (string) $query->get( 'sort' ) );
	if ( 'price-asc' === $sort || 'price-desc' === $sort ) {
		$query->set( 'meta_key', 'pc_price' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'price-asc' === $sort ? 'ASC' : 'DESC' );
	} elseif ( 'newest' === $sort ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	} else {
		// the curated order (menu order), then newest
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $tax ) {
		$query->set( 'tax_query', $tax );
	}
	if ( $meta ) {
		$query->set( 'meta_query', $meta );
	}
}
add_action( 'pre_get_posts', 'bytenot_core_filter_archive', 20 );
