<?php
/**
 * Listing lifecycle: sold, let and withdrawn stock leaves the archives and search
 * but keeps its page (marked, de-indexed). Nothing is ever deleted or redirected to
 * the home page.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Statuses that are no longer available.
 *
 * @return string[]
 */
function bytenot_core_closed_statuses() {
	return array( 'sold', 'let', 'withdrawn' );
}

/**
 * Drop closed listings from archives, taxonomy pages and search.
 *
 * @param WP_Query $query The query.
 */
function bytenot_core_hide_closed( $query ) {
	if ( is_admin() || ! $query->is_main_query() || $query->is_singular() ) {
		return;
	}
	$type       = $query->get( 'post_type' );
	$is_listing = 'listing' === $type || ( is_array( $type ) && in_array( 'listing', $type, true ) ) || $query->is_tax( array( 'location', 'listing_type', 'possession', 'feature' ) );
	if ( ! $is_listing && ! $query->is_search() ) {
		return;
	}
	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => 'listing_status',
		'field'    => 'slug',
		'terms'    => bytenot_core_closed_statuses(),
		'operator' => 'NOT IN',
	);
	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'bytenot_core_hide_closed' );

/**
 * Closed listings are not indexed (unless an SEO plugin takes over the robots tag).
 *
 * @param array $robots Robots directives.
 * @return array
 */
function bytenot_core_robots( $robots ) {
	if ( is_singular( 'listing' ) && has_term( bytenot_core_closed_statuses(), 'listing_status' ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'bytenot_core_robots' );

/**
 * Record when a listing closed, and refresh its project's summary.
 *
 * @param int    $object_id Post ID.
 * @param array  $terms     Term IDs or slugs.
 * @param array  $tt_ids    Term taxonomy IDs.
 * @param string $taxonomy  Taxonomy.
 */
function bytenot_core_stamp_closed( $object_id, $terms, $tt_ids, $taxonomy ) {
	if ( 'listing_status' !== $taxonomy ) {
		return;
	}
	$closed = has_term( bytenot_core_closed_statuses(), 'listing_status', $object_id );
	if ( $closed && ! bytenot_core_get( $object_id, 'sold_at' ) ) {
		bytenot_core_set( $object_id, 'sold_at', gmdate( 'Y-m-d' ) );
	} elseif ( ! $closed ) {
		bytenot_core_set( $object_id, 'sold_at', '' );
	}
	$project = bytenot_core_project_of( $object_id );
	if ( $project ) {
		bytenot_core_rollup( $project );
	}
}
add_action( 'set_object_terms', 'bytenot_core_stamp_closed', 10, 4 );
