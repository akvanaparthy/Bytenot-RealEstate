<?php
/**
 * Taxonomies: listing_kind, listing_type, listing_status, location, feature, possession.
 *
 * `listing_kind` is fixed (project, apartment, property); `listing_type` holds the
 * sub-types typed in Setup (Apartment, Villa, Plot, Land, Commercial ...). `location`
 * is the spine: City > Locality, with its own pages at /localities/{slug}/.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the taxonomies.
 */
function bytenot_core_register_taxonomies() {
	register_taxonomy(
		'listing_kind',
		array( 'listing' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Kinds', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Kind', 'taxonomy singular name', 'bytenot-core' ),
			),
			'description'       => __( 'Project, apartment or property. Set in the listing editor, never by hand.', 'bytenot-core' ),
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => false,
			'show_in_rest'      => true,
			'show_admin_column' => false,
			'rewrite'           => false,
		)
	);

	register_taxonomy(
		'listing_type',
		array( 'listing' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Types', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Type', 'taxonomy singular name', 'bytenot-core' ),
			),
			'description'       => __( 'The sub-type within a kind: villa, plot, commercial ... Defined in Setup.', 'bytenot-core' ),
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'listing_status',
		array( 'listing' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Listing status', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Status', 'taxonomy singular name', 'bytenot-core' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'status', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'location',
		array( 'listing', 'agent' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Localities', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Locality', 'taxonomy singular name', 'bytenot-core' ),
				'parent_item'   => __( 'Parent (city)', 'bytenot-core' ),
				'add_new_item'  => __( 'Add new locality', 'bytenot-core' ),
			),
			'description'       => __( 'City and locality. Each term is also a page with its own content.', 'bytenot-core' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'localities', 'with_front' => false, 'hierarchical' => false ),
		)
	);

	register_taxonomy(
		'feature',
		array( 'listing' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Amenities', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Amenity', 'taxonomy singular name', 'bytenot-core' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => false,
			'rewrite'           => array( 'slug' => 'amenity', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'possession',
		array( 'listing' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Possession', 'taxonomy general name', 'bytenot-core' ),
				'singular_name' => _x( 'Possession', 'taxonomy singular name', 'bytenot-core' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'possession', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'bytenot_core_register_taxonomies' );

/**
 * The fixed vocabularies, seeded on activation and referenced by slug.
 *
 * @return array<string, array<string, string>> taxonomy => slug => name.
 */
function bytenot_core_vocabularies() {
	return array(
		'listing_kind'   => array(
			'project'   => __( 'Project', 'bytenot-core' ),
			'apartment' => __( 'Apartment', 'bytenot-core' ),
			'property'  => __( 'Property', 'bytenot-core' ),
		),
		'listing_status' => array(
			'for-sale'    => __( 'For sale', 'bytenot-core' ),
			'for-rent'    => __( 'For rent', 'bytenot-core' ),
			'under-offer' => __( 'Under offer', 'bytenot-core' ),
			'sold'        => __( 'Sold', 'bytenot-core' ),
			'let'         => __( 'Let', 'bytenot-core' ),
			'withdrawn'   => __( 'Withdrawn', 'bytenot-core' ),
		),
		'possession'     => array(
			'ready'              => __( 'Ready to move', 'bytenot-core' ),
			'under-construction' => __( 'Under construction', 'bytenot-core' ),
			'new-launch'         => __( 'New launch', 'bytenot-core' ),
			'resale'             => __( 'Resale', 'bytenot-core' ),
		),
	);
}

/**
 * Create the vocabulary terms that do not exist yet, plus the sub-types from Setup.
 */
function bytenot_core_seed_terms() {
	foreach ( bytenot_core_vocabularies() as $taxonomy => $terms ) {
		foreach ( $terms as $slug => $name ) {
			if ( ! term_exists( $slug, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			}
		}
	}
	bytenot_core_sync_subtype_terms();
}
