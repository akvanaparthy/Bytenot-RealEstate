<?php
/**
 * Formatting helpers: Indian price notation, areas, labels.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whole rupees to "₹2.4 Cr" / "₹95 L" / "₹42,000".
 *
 * @param int|float $amount Rupees.
 * @param bool      $rent   Append "/mo".
 * @return string
 */
function property_core_format_price( $amount, $rent = false ) {
	$amount = (float) $amount;
	if ( $amount <= 0 ) {
		return '';
	}
	if ( $amount >= 10000000 ) {
		$n   = $amount / 10000000;
		$out = sprintf( /* translators: %s: amount in crore. */ __( '₹%s Cr', 'property-core' ), rtrim( rtrim( number_format( $n, 2, '.', '' ), '0' ), '.' ) );
	} elseif ( $amount >= 100000 ) {
		$out = sprintf( /* translators: %s: amount in lakh. */ __( '₹%s L', 'property-core' ), number_format( round( $amount / 100000 ), 0, '.', ',' ) );
	} else {
		$out = '₹' . property_core_format_indian( $amount );
	}
	return $rent ? sprintf( /* translators: %s: formatted monthly rent. */ __( '%s/mo', 'property-core' ), $out ) : $out;
}

/**
 * Indian digit grouping: 1,23,456.
 *
 * @param int|float $n Number.
 * @return string
 */
function property_core_format_indian( $n ) {
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
function property_core_format_area( $value, $unit = 'sqft' ) {
	if ( (float) $value <= 0 ) {
		return '';
	}
	$units = array(
		'sqft' => __( 'sq ft', 'property-core' ),
		'sqyd' => __( 'sq yd', 'property-core' ),
		'sqm'  => __( 'sq m', 'property-core' ),
		'acre' => __( 'acres', 'property-core' ),
	);
	return property_core_format_indian( $value ) . ' ' . ( $units[ $unit ] ?? $unit );
}

/**
 * Price per unit of area: "₹6,575 per sq ft".
 *
 * @param int $post_id Property ID.
 * @return string
 */
function property_core_format_psf( $post_id ) {
	$price = (float) property_core_get( $post_id, 'price' );
	$area  = (float) property_core_get( $post_id, 'area_primary' );
	if ( $price <= 0 || $area <= 0 || property_core_is_rent( $post_id ) ) {
		return '';
	}
	$unit = property_core_get( $post_id, 'area_unit' ) ?: 'sqft';
	$per  = array( 'sqft' => __( 'per sq ft', 'property-core' ), 'sqyd' => __( 'per sq yd', 'property-core' ), 'sqm' => __( 'per sq m', 'property-core' ) );
	return '₹' . property_core_format_indian( $price / $area ) . ' ' . ( $per[ $unit ] ?? '' );
}

/**
 * Is the listing for rent?
 *
 * @param int $post_id Property ID.
 * @return bool
 */
function property_core_is_rent( $post_id ) {
	return has_term( array( 'for-rent', 'let' ), 'listing_status', $post_id );
}

/**
 * "4 BHK villa · 3,650 sq ft · Ready to move".
 *
 * @param int $post_id Property ID.
 * @return string
 */
function property_core_format_line( $post_id ) {
	$parts = array();
	$label = property_core_get( $post_id, 'bedroom_label' );
	$type  = get_the_terms( $post_id, 'property_type' );
	$type  = $type && ! is_wp_error( $type ) ? $type[0]->name : '';
	if ( $label ) {
		$parts[] = trim( $label . ' ' . strtolower( $type ) );
	} elseif ( $type ) {
		$parts[] = $type;
	}
	$area = property_core_format_area( property_core_get( $post_id, 'area_primary' ), property_core_get( $post_id, 'area_unit' ) ?: 'sqft' );
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
 * "Gachibowli, Hyderabad" from the deepest location term and its parent.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function property_core_format_place( $post_id ) {
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
