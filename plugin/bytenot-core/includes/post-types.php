<?php
/**
 * Post types: listing, agent, enquiry.
 *
 * One post type holds every kind of listing (project, apartment, property); the kind is
 * a term and decides the fields, the card and the address. Addresses follow the kinds'
 * URL bases from Setup: /projects/{slug}/, /projects/{project}/{unit}/ for an apartment
 * inside a project, /properties/{slug}/, and /{base}/ for each kind's archive.
 * Advisors live at /advisors/{slug}/; localities are the `location` taxonomy.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the post types.
 */
function bytenot_core_register_post_types() {
	register_post_type(
		'listing',
		array(
			'labels'       => array(
				'name'               => _x( 'Listings', 'post type general name', 'bytenot-core' ),
				'singular_name'      => _x( 'Listing', 'post type singular name', 'bytenot-core' ),
				'add_new'            => __( 'Add listing', 'bytenot-core' ),
				'add_new_item'       => __( 'Add listing', 'bytenot-core' ),
				'edit_item'          => __( 'Edit listing', 'bytenot-core' ),
				'new_item'           => __( 'New listing', 'bytenot-core' ),
				'view_item'          => __( 'View listing', 'bytenot-core' ),
				'search_items'       => __( 'Search listings', 'bytenot-core' ),
				'not_found'          => __( 'No listings found.', 'bytenot-core' ),
				'not_found_in_trash' => __( 'No listings in the bin.', 'bytenot-core' ),
				'all_items'          => __( 'All listings', 'bytenot-core' ),
				'archives'           => __( 'Listings', 'bytenot-core' ),
			),
			'description'  => __( 'A project, an apartment inside a project, or a standalone property.', 'bytenot-core' ),
			'public'       => true,
			'show_in_rest' => true,
			'show_in_menu' => 'bytenot',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'has_archive'  => true,
			'rewrite'      => false, // the kinds supply the rules, see below
			'query_var'    => 'listing',
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Describe it: who it suits, what makes it worth a visit.', 'bytenot-core' ) ) ),
			),
		)
	);

	register_post_type(
		'agent',
		array(
			'labels'       => array(
				'name'          => _x( 'Advisors', 'post type general name', 'bytenot-core' ),
				'singular_name' => _x( 'Advisor', 'post type singular name', 'bytenot-core' ),
				'add_new_item'  => __( 'Add new advisor', 'bytenot-core' ),
				'edit_item'     => __( 'Edit advisor', 'bytenot-core' ),
				'all_items'     => __( 'All advisors', 'bytenot-core' ),
				'not_found'     => __( 'No advisors found.', 'bytenot-core' ),
			),
			'description'  => __( 'A member of the team who handles enquiries and visits.', 'bytenot-core' ),
			'public'       => true,
			'show_in_rest' => true,
			'show_in_menu' => 'bytenot',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'advisors', 'with_front' => false ),
		)
	);

	register_post_type(
		'enquiry',
		array(
			'labels'       => array(
				'name'          => _x( 'Enquiries', 'post type general name', 'bytenot-core' ),
				'singular_name' => _x( 'Enquiry', 'post type singular name', 'bytenot-core' ),
				'all_items'     => __( 'Enquiries', 'bytenot-core' ),
				'edit_item'     => __( 'Enquiry', 'bytenot-core' ),
				'not_found'     => __( 'No enquiries yet.', 'bytenot-core' ),
			),
			'description'  => __( 'A lead captured by a form on the site. Private; the local copy of every lead.', 'bytenot-core' ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => false,
			'show_in_rest' => false,
			'supports'     => array( 'title' ),
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'bytenot_core_register_post_types' );

/**
 * Rewrite rules for every enabled kind: its archive, its pages, and apartments nested
 * under their project.
 *
 * @param array<string, string> $rules Existing rules.
 * @return array<string, string>
 */
function bytenot_core_listing_rules( $rules ) {
	$new = array();
	foreach ( bytenot_core_kinds( true ) as $kind => $k ) {
		$b = preg_quote( $k['base'], '/' );
		$new[ '^' . $b . '/?$' ]                       = 'index.php?post_type=listing&kind=' . $kind;
		$new[ '^' . $b . '/page/(\d+)/?$' ]            = 'index.php?post_type=listing&kind=' . $kind . '&paged=$matches[1]';
		$new[ '^' . $b . '/([^/]+)/([^/]+)/?$' ]       = 'index.php?listing=$matches[2]';
		$new[ '^' . $b . '/([^/]+)/?$' ]               = 'index.php?listing=$matches[1]';
	}
	return $new + $rules;
}
add_filter( 'rewrite_rules_array', 'bytenot_core_listing_rules' );

/**
 * A listing's address by its kind.
 *
 * @param string  $link Default link.
 * @param WP_Post $post Post.
 * @return string
 */
function bytenot_core_listing_link( $link, $post ) {
	if ( 'listing' !== $post->post_type ) {
		return $link;
	}
	if ( in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft', 'future' ), true ) ) {
		return home_url( '/?post_type=listing&p=' . $post->ID );
	}
	$kind    = bytenot_core_kind_of( $post->ID );
	$project = 'apartment' === $kind ? bytenot_core_project_of( $post->ID ) : 0;
	if ( $project ) {
		$pk = bytenot_core_kind( 'project' );
		return home_url( '/' . $pk['base'] . '/' . get_post_field( 'post_name', $project ) . '/' . $post->post_name . '/' );
	}
	$k = bytenot_core_kind( $kind );
	return home_url( '/' . ( $k ? $k['base'] : 'listings' ) . '/' . $post->post_name . '/' );
}
add_filter( 'post_type_link', 'bytenot_core_listing_link', 10, 2 );

/**
 * The listings archive link is the primary kind's page.
 *
 * @param string $link      Default link.
 * @param string $post_type Post type.
 * @return string
 */
function bytenot_core_listing_archive_link( $link, $post_type ) {
	return 'listing' === $post_type ? bytenot_core_archive_url() : $link;
}
add_filter( 'post_type_archive_link', 'bytenot_core_listing_archive_link', 10, 2 );

/**
 * Register the public query vars the kinds and filters use.
 *
 * @param string[] $vars Query vars.
 * @return string[]
 */
function bytenot_core_kind_query_vars( $vars ) {
	$vars[] = 'kind';
	return $vars;
}
add_filter( 'query_vars', 'bytenot_core_kind_query_vars' );
