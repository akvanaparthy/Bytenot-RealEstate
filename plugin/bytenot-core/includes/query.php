<?php
/**
 * Search query vars → the listing archive query. Server-side, so every filter URL is
 * crawlable and works without JavaScript.
 *
 * /projects/?type=villas&location=hyderabad&budget=10000000-20000000&bhk=3&intent=for-sale&sort=price-asc
 *
 * A project matches a budget when its price range overlaps the band and a configuration
 * when its units include it; an apartment or a property matches on its own price and
 * bedrooms.
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
 * Apply the filters to the main listing archive query.
 *
 * @param WP_Query $query The query.
 */
function bytenot_core_filter_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'listing' ) ) {
		return;
	}
	$tax  = (array) $query->get( 'tax_query' );
	$meta = (array) $query->get( 'meta_query' );

	$kind = sanitize_key( (string) $query->get( 'kind' ) );
	if ( ! isset( bytenot_core_kinds( true )[ $kind ] ) ) {
		$kind = bytenot_core_primary_kind();
		$query->set( 'kind', $kind );
	}
	$tax[] = array( 'taxonomy' => 'listing_kind', 'field' => 'slug', 'terms' => $kind );

	$type = sanitize_key( (string) $query->get( 'type' ) );
	if ( $type ) {
		$tax[] = array( 'taxonomy' => 'listing_type', 'field' => 'slug', 'terms' => $type );
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

	$project = 'project' === $kind;
	$budget  = (string) $query->get( 'budget' );
	if ( 'for-rent' !== $intent && preg_match( '/^(\d+)-(\d*)$/', $budget, $m ) ) { // rents are monthly; the bands are purchase prices
		$min = (int) $m[1];
		$max = '' === $m[2] ? null : (int) $m[2];
		if ( $project ) {
			// the range overlaps the band: max >= band min, and min < band max
			$meta[] = array( 'key' => 'bn_price_max', 'value' => $min, 'compare' => '>=', 'type' => 'NUMERIC' );
			if ( null !== $max ) {
				$meta[] = array( 'key' => 'bn_price_min', 'value' => $max, 'compare' => '<', 'type' => 'NUMERIC' );
			}
		} else {
			$meta[] = null === $max
				? array( 'key' => 'bn_price', 'value' => $min, 'compare' => '>=', 'type' => 'NUMERIC' )
				: array( 'key' => 'bn_price', 'value' => array( $min, $max ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' );
		}
	}
	$bhk = (string) $query->get( 'bhk' );
	if ( '' !== $bhk ) {
		if ( $project ) {
			$meta[] = '4' === $bhk
				? array( 'key' => 'bn_bhk_set', 'value' => '([4-9]|[1-9][0-9])', 'compare' => 'REGEXP' )
				: array( 'key' => 'bn_bhk_set', 'value' => '(^|,)' . (int) $bhk . '(,|$)', 'compare' => 'REGEXP' );
		} else {
			$meta[] = '4' === $bhk
				? array( 'key' => 'bn_bedrooms', 'value' => 4, 'compare' => '>=', 'type' => 'NUMERIC' )
				: array( 'key' => 'bn_bedrooms', 'value' => (float) $bhk, 'compare' => '=', 'type' => 'NUMERIC' );
		}
	}
	$sort      = sanitize_key( (string) $query->get( 'sort' ) );
	$price_key = $project ? 'bn_price_min' : 'bn_price';
	if ( 'price-asc' === $sort || 'price-desc' === $sort ) {
		$query->set( 'meta_key', $price_key );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'price-asc' === $sort ? 'ASC' : 'DESC' );
	} elseif ( 'newest' === $sort ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	} else {
		// the curated order (menu order), then newest
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	$query->set( 'tax_query', $tax );
	if ( $meta ) {
		$query->set( 'meta_query', $meta );
	}
}
add_action( 'pre_get_posts', 'bytenot_core_filter_archive', 20 );
