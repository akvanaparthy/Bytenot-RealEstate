<?php
/**
 * Formatting helpers: Indian price notation, areas, one-line summaries.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whole rupees to "₹2.4 Cr" / "₹95 L" / "₹42,000".
 *
 * @param int|float $amount Rupees.
 * @param bool      $rent   Append "/mo".
 * @return string
 */
function bytenot_core_format_price( $amount, $rent = false ) {
	$amount = (float) $amount;
	if ( $amount <= 0 ) {
		return '';
	}
	if ( $amount >= 10000000 ) {
		$n   = $amount / 10000000;
		$out = sprintf( /* translators: %s: amount in crore. */ __( '₹%s Cr', 'bytenot-core' ), rtrim( rtrim( number_format( $n, 2, '.', '' ), '0' ), '.' ) );
	} elseif ( $amount >= 100000 ) {
		$out = sprintf( /* translators: %s: amount in lakh. */ __( '₹%s L', 'bytenot-core' ), number_format( round( $amount / 100000 ), 0, '.', ',' ) );
	} else {
		$out = '₹' . bytenot_core_format_indian( $amount );
	}
	return $rent ? sprintf( /* translators: %s: formatted monthly rent. */ __( '%s/mo', 'bytenot-core' ), $out ) : $out;
}

/**
 * A project's price range: "₹1.2 Cr – ₹1.9 Cr", or "From ₹1.2 Cr" when only one end is known.
 *
 * @param int $min From.
 * @param int $max To.
 * @return string
 */
function bytenot_core_format_range( $min, $max ) {
	$min = (int) $min;
	$max = (int) $max;
	if ( $min > 0 && $max > $min ) {
		/* translators: 1: lowest price, 2: highest price. */
		return sprintf( __( '%1$s – %2$s', 'bytenot-core' ), bytenot_core_format_price( $min ), bytenot_core_format_price( $max ) );
	}
	$one = $min > 0 ? $min : $max;
	/* translators: %s: price. */
	return $one > 0 ? sprintf( __( 'From %s', 'bytenot-core' ), bytenot_core_format_price( $one ) ) : '';
}

/**
 * Indian digit grouping: 1,23,456.
 *
 * @param int|float $n Number.
 * @return string
 */
function bytenot_core_format_indian( $n ) {
	$n = (string) round( $n );
	if ( strlen( $n ) <= 3 ) {
		return $n;
	}
	$last = substr( $n, -3 );
	$rest = substr( $n, 0, -3 );
	return preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest ) . ',' . $last;
}

/**
 * Area with its unit: "3,650 sq ft", "2,400 sq yd".
 *
 * @param float  $value Area.
 * @param string $unit  sqft | sqyd | sqm | acre.
 * @return string
 */
function bytenot_core_format_area( $value, $unit = 'sqft' ) {
	if ( (float) $value <= 0 ) {
		return '';
	}
	return bytenot_core_format_indian( $value ) . ' ' . bytenot_core_area_unit_label( $unit );
}

/**
 * An area range: "1,250–1,850 sq ft".
 *
 * @param float  $min  From.
 * @param float  $max  To.
 * @param string $unit Unit key.
 * @return string
 */
function bytenot_core_format_area_range( $min, $max, $unit = 'sqft' ) {
	$min = (float) $min;
	$max = (float) $max;
	if ( $min > 0 && $max > $min ) {
		return bytenot_core_format_indian( $min ) . '–' . bytenot_core_format_indian( $max ) . ' ' . bytenot_core_area_unit_label( $unit );
	}
	return bytenot_core_format_area( $min > 0 ? $min : $max, $unit );
}

/**
 * Unit label: "sq ft".
 *
 * @param string $unit Unit key.
 * @return string
 */
function bytenot_core_area_unit_label( $unit ) {
	$units = array(
		'sqft' => __( 'sq ft', 'bytenot-core' ),
		'sqyd' => __( 'sq yd', 'bytenot-core' ),
		'sqm'  => __( 'sq m', 'bytenot-core' ),
		'acre' => __( 'acres', 'bytenot-core' ),
	);
	return $units[ $unit ] ?? $unit;
}

/**
 * Price per unit of area: "₹6,575 per sq ft".
 *
 * @param int $post_id Listing ID.
 * @return string
 */
function bytenot_core_format_psf( $post_id ) {
	$price = (float) bytenot_core_get( $post_id, 'price' );
	$area  = (float) bytenot_core_get( $post_id, 'area_primary' );
	if ( $price <= 0 || $area <= 0 || bytenot_core_is_rent( $post_id ) ) {
		return '';
	}
	$unit = bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft';
	$per  = array( 'sqft' => __( 'per sq ft', 'bytenot-core' ), 'sqyd' => __( 'per sq yd', 'bytenot-core' ), 'sqm' => __( 'per sq m', 'bytenot-core' ) );
	return '₹' . bytenot_core_format_indian( $price / $area ) . ' ' . ( $per[ $unit ] ?? '' );
}

/**
 * Is the listing for rent?
 *
 * @param int $post_id Listing ID.
 * @return bool
 */
function bytenot_core_is_rent( $post_id ) {
	return has_term( array( 'for-rent', 'let' ), 'listing_status', $post_id );
}

/**
 * The sub-type's name: "Villa", "Plots".
 *
 * @param int $post_id Listing ID.
 * @return string
 */
function bytenot_core_type_name( $post_id ) {
	$type = get_the_terms( $post_id, 'listing_type' );
	return $type && ! is_wp_error( $type ) ? $type[0]->name : '';
}

/**
 * One line: "4 BHK villa · 3,650 sq ft · Ready to move" for a home,
 * "2, 3 BHK apartments · 42 units · by Aparna" for a project.
 *
 * @param int $post_id Listing ID.
 * @return string
 */
function bytenot_core_format_line( $post_id ) {
	$parts = array();
	$type  = bytenot_core_type_name( $post_id );
	if ( 'project' === bytenot_core_kind_of( $post_id ) ) {
		$config = bytenot_core_get( $post_id, 'config_label' );
		if ( $config || $type ) {
			$parts[] = trim( $config . ' ' . strtolower( $type ) );
		}
		$units = (int) bytenot_core_get( $post_id, 'units_total' ) ?: (int) bytenot_core_get( $post_id, 'units_listed' );
		if ( $units ) {
			/* translators: %d: units. */
			$parts[] = sprintf( _n( '%d unit', '%d units', $units, 'bytenot-core' ), $units );
		}
		$dev = bytenot_core_developer_name( $post_id );
		if ( $dev ) {
			/* translators: %s: developer. */
			$parts[] = sprintf( __( 'by %s', 'bytenot-core' ), $dev );
		}
		return implode( ' · ', $parts );
	}
	$label = bytenot_core_get( $post_id, 'bedroom_label' );
	if ( $label ) {
		$parts[] = trim( $label . ' ' . strtolower( $type ) );
	} elseif ( $type ) {
		$parts[] = $type;
	}
	$area = bytenot_core_format_area( bytenot_core_get( $post_id, 'area_primary' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' );
	if ( $area ) {
		$parts[] = $area;
	}
	$poss = get_the_terms( $post_id, 'possession' );
	if ( $poss && ! is_wp_error( $poss ) ) {
		$parts[] = $poss[0]->name;
	}
	return implode( ' · ', $parts );
}

/**
 * A project's developer: the partner's name, or the typed name.
 *
 * @param int $post_id Listing ID.
 * @return string
 */
function bytenot_core_developer_name( $post_id ) {
	$partner = (int) bytenot_core_get( $post_id, 'developer_id' );
	if ( $partner && 'partner' === get_post_type( $partner ) ) {
		return get_the_title( $partner );
	}
	return (string) bytenot_core_get( $post_id, 'developer' );
}

/**
 * "Gachibowli, Hyderabad" from the deepest location term and its parent.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function bytenot_core_format_place( $post_id ) {
	$terms = get_the_terms( $post_id, 'location' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	usort( $terms, static fn( $a, $b ) => (int) ( $b->parent > 0 ) - (int) ( $a->parent > 0 ) );
	$leaf  = $terms[0];
	$names = array( $leaf->name );
	if ( $leaf->parent ) {
		$parent = get_term( $leaf->parent, 'location' );
		if ( $parent && ! is_wp_error( $parent ) ) {
			$names[] = $parent->name;
		}
	}
	return implode( ', ', $names );
}
