<?php
/**
 * Listing kinds: what a site lists.
 *
 * Three kinds ship with the plugin. A project is a developer's venture (a tower, a
 * gated community); an apartment is one unit inside a project; a property stands on its
 * own (land, a house, a commercial space). Setup decides which kinds are on, what each
 * is called, its URL base and its sub-types. "Listing" is the umbrella word, used only
 * in the admin.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The kinds with their defaults, in display order.
 *
 * @return array<string, array{on:int,singular:string,plural:string,base:string,subtypes:string}>
 */
function bytenot_core_kind_defaults() {
	return array(
		'project'   => array( 'on' => 0, 'singular' => __( 'Project', 'bytenot-core' ), 'plural' => __( 'Projects', 'bytenot-core' ), 'base' => 'projects', 'subtypes' => "Apartments\nVillas\nPlots" ),
		'apartment' => array( 'on' => 0, 'singular' => __( 'Apartment', 'bytenot-core' ), 'plural' => __( 'Apartments', 'bytenot-core' ), 'base' => 'apartments', 'subtypes' => '' ),
		'property'  => array( 'on' => 1, 'singular' => __( 'Property', 'bytenot-core' ), 'plural' => __( 'Properties', 'bytenot-core' ), 'base' => 'properties', 'subtypes' => "Apartment\nVilla\nPlot\nCommercial" ),
	);
}

/**
 * The kinds as configured, in display order. Sub-types come back as a list of labels.
 *
 * @param bool $enabled_only Only the kinds that are switched on.
 * @return array<string, array{on:bool,singular:string,plural:string,base:string,subtypes:string[]}>
 */
function bytenot_core_kinds( $enabled_only = false ) {
	static $cache = null;
	if ( null === $cache ) {
		$saved = (array) bytenot_core_setting( 'kinds' );
		$cache = array();
		foreach ( bytenot_core_kind_defaults() as $slug => $d ) {
			$k = wp_parse_args( isset( $saved[ $slug ] ) ? (array) $saved[ $slug ] : array(), $d );
			$cache[ $slug ] = array(
				'on'       => ! empty( $k['on'] ),
				'singular' => '' !== trim( (string) $k['singular'] ) ? (string) $k['singular'] : $d['singular'],
				'plural'   => '' !== trim( (string) $k['plural'] ) ? (string) $k['plural'] : $d['plural'],
				'base'     => sanitize_title( (string) $k['base'] ) ?: $d['base'],
				'subtypes' => array_values( array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) $k['subtypes'] ) ) ) ),
			);
		}
		if ( ! array_filter( array_column( $cache, 'on' ) ) ) {
			$cache['property']['on'] = true; // a site always lists something
		}
	}
	return $enabled_only ? array_filter( $cache, static fn( $k ) => $k['on'] ) : $cache;
}

/**
 * Forget the cached kinds (after a settings save).
 */
function bytenot_core_kinds_reset() {
	bytenot_core_kinds_cache_clear();
}

/**
 * Clear the static cache in bytenot_core_kinds() by re-entering it with a flag.
 */
function bytenot_core_kinds_cache_clear() {
	// PHP statics cannot be reset from outside; the option's own cache is cleared by
	// WordPress, so a fresh request reads the saved value. Nothing else to do.
}

/**
 * One kind's configuration.
 *
 * @param string $slug project | apartment | property.
 * @return array|null
 */
function bytenot_core_kind( $slug ) {
	return bytenot_core_kinds()[ $slug ] ?? null;
}

/**
 * The first enabled kind: what the nav and the home page point to.
 *
 * @return string
 */
function bytenot_core_primary_kind() {
	return (string) array_key_first( bytenot_core_kinds( true ) );
}

/**
 * The kind of a listing, by its term. A listing without a term counts as the primary kind.
 *
 * @param int $post_id Listing ID.
 * @return string
 */
function bytenot_core_kind_of( $post_id ) {
	$terms = get_the_terms( $post_id, 'listing_kind' );
	if ( $terms && ! is_wp_error( $terms ) && isset( bytenot_core_kinds()[ $terms[0]->slug ] ) ) {
		return $terms[0]->slug;
	}
	return bytenot_core_primary_kind();
}

/**
 * Label for a kind.
 *
 * @param string $slug   Kind.
 * @param bool   $plural Plural form.
 * @return string
 */
function bytenot_core_kind_label( $slug, $plural = true ) {
	$k = bytenot_core_kind( $slug );
	if ( ! $k ) {
		return $plural ? __( 'Listings', 'bytenot-core' ) : __( 'Listing', 'bytenot-core' );
	}
	return $plural ? $k['plural'] : $k['singular'];
}

/**
 * The archive page for a kind: /projects/, /properties/ ...
 *
 * @param string $kind Kind; empty for the primary kind.
 * @return string
 */
function bytenot_core_archive_url( $kind = '' ) {
	$kind = $kind ?: bytenot_core_primary_kind();
	$k    = bytenot_core_kind( $kind );
	return $k ? home_url( '/' . $k['base'] . '/' ) : home_url( '/' );
}

/**
 * Sub-types of a kind as slug => label (the slug is the listing_type term slug).
 *
 * @param string $kind Kind.
 * @return array<string, string>
 */
function bytenot_core_subtypes( $kind ) {
	$out = array();
	$k   = bytenot_core_kind( $kind );
	foreach ( $k ? $k['subtypes'] : array() as $label ) {
		$out[ sanitize_title( $label ) ] = $label;
	}
	return $out;
}

/**
 * Make sure every configured sub-type exists as a listing_type term. Runs after Setup saves.
 */
function bytenot_core_sync_subtype_terms() {
	foreach ( bytenot_core_kinds() as $kind => $k ) {
		foreach ( bytenot_core_subtypes( $kind ) as $slug => $label ) {
			if ( ! term_exists( $slug, 'listing_type' ) ) {
				wp_insert_term( $label, 'listing_type', array( 'slug' => $slug ) );
			}
		}
	}
}

/**
 * The project an apartment belongs to.
 *
 * @param int $post_id Listing ID.
 * @return int Project listing ID or 0.
 */
function bytenot_core_project_of( $post_id ) {
	$parent = (int) get_post_field( 'post_parent', $post_id );
	return $parent && 'listing' === get_post_type( $parent ) && 'project' === bytenot_core_kind_of( $parent ) ? $parent : 0;
}

/**
 * The apartments listed under a project, curated order then title.
 *
 * @param int    $project_id Project listing ID.
 * @param string $status     Post status.
 * @return int[]
 */
function bytenot_core_units_of( $project_id, $status = 'publish' ) {
	return get_posts(
		array(
			'post_type'      => 'listing',
			'post_parent'    => (int) $project_id,
			'post_status'    => $status,
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'tax_query'      => array( array( 'taxonomy' => 'listing_kind', 'field' => 'slug', 'terms' => 'apartment' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		)
	);
}

/**
 * Fill a project's summary from its apartments: price range, configurations, area range
 * and unit count. Only runs when the project has units; otherwise the values typed in
 * the editor stand.
 *
 * @param int $project_id Project listing ID.
 */
function bytenot_core_rollup( $project_id ) {
	$units = bytenot_core_units_of( $project_id );
	if ( ! $units ) {
		delete_post_meta( $project_id, 'bn_units_listed' );
		return;
	}
	$prices = array();
	$areas  = array();
	$bhks   = array();
	foreach ( $units as $uid ) {
		if ( has_term( bytenot_core_closed_statuses(), 'listing_status', $uid ) ) {
			continue;
		}
		$price = (int) bytenot_core_get( $uid, 'price' );
		$area  = (float) bytenot_core_get( $uid, 'area_primary' );
		$bhk   = (float) bytenot_core_get( $uid, 'bedrooms' );
		if ( $price > 0 ) {
			$prices[] = $price;
		}
		if ( $area > 0 ) {
			$areas[] = $area;
		}
		if ( $bhk > 0 ) {
			$bhks[] = $bhk;
		}
	}
	$bhks = array_values( array_unique( $bhks ) );
	sort( $bhks );
	$set = array(
		'units_listed' => count( $units ),
		'price_min'    => $prices ? min( $prices ) : '',
		'price_max'    => $prices ? max( $prices ) : '',
		'area_min'     => $areas ? min( $areas ) : '',
		'area_max'     => $areas ? max( $areas ) : '',
		'bhk_set'      => implode( ',', array_map( static fn( $b ) => rtrim( rtrim( number_format( $b, 1, '.', '' ), '0' ), '.' ), $bhks ) ),
	);
	if ( $bhks ) {
		/* translators: %s: comma-separated bedroom counts, e.g. "2, 3". */
		$set['config_label'] = sprintf( __( '%s BHK', 'bytenot-core' ), implode( ', ', array_map( static fn( $b ) => rtrim( rtrim( number_format( $b, 1, '.', '' ), '0' ), '.' ), $bhks ) ) );
	}
	foreach ( $set as $k => $v ) {
		if ( '' === $v ) {
			delete_post_meta( $project_id, 'bn_' . $k );
		} else {
			update_post_meta( $project_id, 'bn_' . $k, $v );
		}
	}
}

/**
 * Keep the parent project's summary current whenever an apartment is saved, moved or binned.
 *
 * @param int $post_id Post ID.
 */
function bytenot_core_rollup_on_change( $post_id ) {
	if ( 'listing' !== get_post_type( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	$project = bytenot_core_project_of( $post_id );
	if ( $project ) {
		bytenot_core_rollup( $project );
	}
}
add_action( 'save_post_listing', 'bytenot_core_rollup_on_change', 20 );
add_action( 'trashed_post', 'bytenot_core_rollup_on_change' );
add_action( 'untrashed_post', 'bytenot_core_rollup_on_change' );
add_action( 'deleted_post', 'bytenot_core_rollup_on_change' );
