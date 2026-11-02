<?php
/**
 * Blocks: search, featured spotlight, lead form. Dynamic (PHP-rendered) with plain
 * view scripts, so no build step is needed and the page works without JavaScript.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared field styles and the block types.
 */
function property_core_register_blocks() {
	wp_register_style( 'property-core-fields', PROPERTY_CORE_URL . 'assets/css/fields.css', array(), PROPERTY_CORE_VERSION );
	foreach ( array( 'search', 'featured', 'lead-form' ) as $block ) {
		register_block_type( PROPERTY_CORE_DIR . 'blocks/' . $block );
	}
}
add_action( 'init', 'property_core_register_blocks' );

/**
 * Options for the search form and the listing filters, from settings and synced terms.
 *
 * @return array<string, mixed>
 */
function property_core_search_options() {
	$vocab  = property_core_vocabularies();
	$types  = array();
	foreach ( (array) property_core_setting( 'types' ) as $slug ) {
		if ( isset( $vocab['property_type'][ $slug ] ) ) {
			$types[ $slug ] = $vocab['property_type'][ $slug ];
		}
	}
	$intents = array();
	foreach ( (array) property_core_setting( 'intents' ) as $slug ) {
		$intents[ $slug ] = 'for-rent' === $slug ? __( 'Rent', 'property-core' ) : __( 'Buy', 'property-core' );
	}
	// possession facets ride along with the transaction chips, as in the prototype
	$intents['new-launch'] = __( 'New Launch', 'property-core' );
	$intents['resale']     = __( 'Resale', 'property-core' );

	$locations = array();
	$cities    = get_terms( array( 'taxonomy' => 'location', 'parent' => 0, 'hide_empty' => false ) );
	if ( ! is_wp_error( $cities ) ) {
		foreach ( $cities as $city ) {
			$locations[ $city->slug ] = $city->name;
		}
	}
	$budgets = array();
	foreach ( property_core_budget_bands() as $band ) {
		$budgets[ $band[0] . '-' . ( null === $band[1] ? '' : $band[1] ) ] = $band[2];
	}
	return array(
		'types'          => $types,
		'intents'        => $intents,
		'locations'      => $locations,
		'budgets'        => $budgets,
		'configurations' => array( '1' => __( '1 BHK', 'property-core' ), '2' => __( '2 BHK', 'property-core' ), '3' => __( '3 BHK', 'property-core' ), '4' => __( '4 BHK+', 'property-core' ) ),
		'has_config'     => (bool) array_intersect( array( 'apartment', 'villa' ), array_keys( $types ) ),
	);
}
