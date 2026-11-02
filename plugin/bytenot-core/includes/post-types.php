<?php
/**
 * Post types: property, project, agent, enquiry.
 *
 * URL structure (assumed, see DECISIONS.md): /properties/{slug}/, /projects/{slug}/,
 * /advisors/{slug}/. Localities are the `location` taxonomy at /localities/{slug}/.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the post types.
 */
function bytenot_core_register_post_types() {
	register_post_type(
		'property',
		array(
			'labels'       => array(
				'name'               => _x( 'Properties', 'post type general name', 'bytenot-core' ),
				'singular_name'      => _x( 'Property', 'post type singular name', 'bytenot-core' ),
				'add_new_item'       => __( 'Add new property', 'bytenot-core' ),
				'edit_item'          => __( 'Edit property', 'bytenot-core' ),
				'new_item'           => __( 'New property', 'bytenot-core' ),
				'view_item'          => __( 'View property', 'bytenot-core' ),
				'search_items'       => __( 'Search properties', 'bytenot-core' ),
				'not_found'          => __( 'No properties found.', 'bytenot-core' ),
				'not_found_in_trash' => __( 'No properties in the bin.', 'bytenot-core' ),
				'all_items'          => __( 'All properties', 'bytenot-core' ),
				'archives'           => __( 'Properties', 'bytenot-core' ),
			),
			'description'  => __( 'A home, plot or commercial space that can be bought or rented.', 'bytenot-core' ),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-home',
			'menu_position'=> 20,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ),
			'has_archive'  => 'properties',
			'rewrite'      => array( 'slug' => 'properties', 'with_front' => false ),
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Describe the home: who it suits, what makes it worth a visit.', 'bytenot-core' ) ) ),
			),
		)
	);

	register_post_type(
		'project',
		array(
			'labels'       => array(
				'name'          => _x( 'Projects', 'post type general name', 'bytenot-core' ),
				'singular_name' => _x( 'Project', 'post type singular name', 'bytenot-core' ),
				'add_new_item'  => __( 'Add new project', 'bytenot-core' ),
				'edit_item'     => __( 'Edit project', 'bytenot-core' ),
				'all_items'     => __( 'All projects', 'bytenot-core' ),
				'not_found'     => __( 'No projects found.', 'bytenot-core' ),
			),
			'description'  => __( 'A development or community that groups several properties.', 'bytenot-core' ),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-building',
			'menu_position'=> 21,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
			'has_archive'  => 'projects',
			'rewrite'      => array( 'slug' => 'projects', 'with_front' => false ),
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
			'menu_icon'    => 'dashicons-businessperson',
			'menu_position'=> 22,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
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
			'show_in_menu' => true,
			'show_in_rest' => false,
			'menu_icon'    => 'dashicons-email-alt',
			'menu_position'=> 23,
			'supports'     => array( 'title', 'custom-fields' ),
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'bytenot_core_register_post_types' );
