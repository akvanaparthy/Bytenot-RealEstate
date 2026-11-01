<?php
/**
 * Post types: property, project, agent, enquiry.
 *
 * URL structure (assumed, see DECISIONS.md): /properties/{slug}/, /projects/{slug}/,
 * /advisors/{slug}/. Localities are the `location` taxonomy at /localities/{slug}/.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the post types.
 */
function property_core_register_post_types() {
	register_post_type(
		'property',
		array(
			'labels'       => array(
				'name'               => _x( 'Properties', 'post type general name', 'property-core' ),
				'singular_name'      => _x( 'Property', 'post type singular name', 'property-core' ),
				'add_new_item'       => __( 'Add new property', 'property-core' ),
				'edit_item'          => __( 'Edit property', 'property-core' ),
				'new_item'           => __( 'New property', 'property-core' ),
				'view_item'          => __( 'View property', 'property-core' ),
				'search_items'       => __( 'Search properties', 'property-core' ),
				'not_found'          => __( 'No properties found.', 'property-core' ),
				'not_found_in_trash' => __( 'No properties in the bin.', 'property-core' ),
				'all_items'          => __( 'All properties', 'property-core' ),
				'archives'           => __( 'Properties', 'property-core' ),
			),
			'description'  => __( 'A home, plot or commercial space that can be bought or rented.', 'property-core' ),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-home',
			'menu_position'=> 20,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ),
			'has_archive'  => 'properties',
			'rewrite'      => array( 'slug' => 'properties', 'with_front' => false ),
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Describe the home: who it suits, what makes it worth a visit.', 'property-core' ) ) ),
			),
		)
	);

	register_post_type(
		'project',
		array(
			'labels'       => array(
				'name'          => _x( 'Projects', 'post type general name', 'property-core' ),
				'singular_name' => _x( 'Project', 'post type singular name', 'property-core' ),
				'add_new_item'  => __( 'Add new project', 'property-core' ),
				'edit_item'     => __( 'Edit project', 'property-core' ),
				'all_items'     => __( 'All projects', 'property-core' ),
				'not_found'     => __( 'No projects found.', 'property-core' ),
			),
			'description'  => __( 'A development or community that groups several properties.', 'property-core' ),
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
				'name'          => _x( 'Advisors', 'post type general name', 'property-core' ),
				'singular_name' => _x( 'Advisor', 'post type singular name', 'property-core' ),
				'add_new_item'  => __( 'Add new advisor', 'property-core' ),
				'edit_item'     => __( 'Edit advisor', 'property-core' ),
				'all_items'     => __( 'All advisors', 'property-core' ),
				'not_found'     => __( 'No advisors found.', 'property-core' ),
			),
			'description'  => __( 'A member of the team who handles enquiries and visits.', 'property-core' ),
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
				'name'          => _x( 'Enquiries', 'post type general name', 'property-core' ),
				'singular_name' => _x( 'Enquiry', 'post type singular name', 'property-core' ),
				'all_items'     => __( 'Enquiries', 'property-core' ),
				'edit_item'     => __( 'Enquiry', 'property-core' ),
				'not_found'     => __( 'No enquiries yet.', 'property-core' ),
			),
			'description'  => __( 'A lead captured by a form on the site. Private; the local copy of every lead.', 'property-core' ),
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
add_action( 'init', 'property_core_register_post_types' );
