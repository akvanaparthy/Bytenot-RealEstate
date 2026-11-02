<?php
/**
 * Structured data for listings (RealEstateListing) and the agency (RealEstateAgent).
 * Skipped when an SEO plugin that writes schema is active.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Does an SEO plugin already emit schema?
 *
 * @return bool
 */
function bytenot_core_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'SLIM_SEO_VER' );
}

/**
 * JSON-LD in the head.
 */
function bytenot_core_jsonld() {
	if ( bytenot_core_seo_plugin_active() ) {
		return;
	}
	$graph = array();
	if ( is_front_page() ) {
		$agent = array(
			'@type' => 'RealEstateAgent',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);
		if ( bytenot_core_setting( 'phone' ) ) {
			$agent['telephone'] = bytenot_core_setting( 'phone' );
		}
		if ( has_custom_logo() ) {
			$agent['logo'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		}
		$graph[] = $agent;
	}
	if ( is_singular( 'property' ) ) {
		$id     = get_the_ID();
		$get    = static fn( $k ) => bytenot_core_get( $id, $k );
		$rent   = bytenot_core_is_rent( $id );
		$type   = get_the_terms( $id, 'property_type' );
		$type   = $type && ! is_wp_error( $type ) ? $type[0]->slug : '';
		$photos = array_values( array_unique( array_filter( array_merge( array( get_post_thumbnail_id( $id ) ), (array) $get( 'gallery' ) ) ) ) );
		$place  = get_the_terms( $id, 'location' );
		$city   = '';
		$area   = '';
		if ( $place && ! is_wp_error( $place ) ) {
			foreach ( $place as $t ) {
				if ( $t->parent ) {
					$area = $t->name;
				} else {
					$city = $t->name;
				}
			}
		}
		$unit  = $get( 'area_unit' ) ?: ( 'plot' === $type ? 'sqyd' : 'sqft' );
		$about = array_filter(
			array(
				'@type'                  => 'plot' === $type ? 'Place' : ( 'villa' === $type ? 'House' : ( 'commercial' === $type ? 'Place' : 'Apartment' ) ),
				'numberOfBedrooms'       => (float) $get( 'bedrooms' ) ?: null,
				'numberOfBathroomsTotal' => (float) $get( 'bathrooms' ) ?: null,
				'floorSize'              => (float) $get( 'area_primary' ) ? array( '@type' => 'QuantitativeValue', 'value' => (float) $get( 'area_primary' ), 'unitCode' => array( 'sqft' => 'FTK', 'sqyd' => 'YDK', 'sqm' => 'MTK', 'acre' => 'ACR' )[ $unit ] ?? 'FTK' ) : null,
				'address'                => array_filter( array( '@type' => 'PostalAddress', 'streetAddress' => $get( 'address_line' ) ?: null, 'addressLocality' => $area ?: null, 'addressRegion' => $city ?: null, 'addressCountry' => 'IN' ) ),
				'geo'                    => (float) $get( 'lat' ) ? array( '@type' => 'GeoCoordinates', 'latitude' => (float) $get( 'lat' ), 'longitude' => (float) $get( 'lng' ) ) : null,
			)
		);
		$features = get_the_terms( $id, 'feature' );
		if ( $features && ! is_wp_error( $features ) ) {
			$about['amenityFeature'] = array_map( static fn( $f ) => array( '@type' => 'LocationFeatureSpecification', 'name' => $f->name, 'value' => true ), $features );
		}
		$listing = array(
			'@type'       => 'RealEstateListing',
			'name'        => get_the_title( $id ),
			'url'         => get_permalink( $id ),
			'datePosted'  => get_post_time( 'c', true, $id ),
			'description' => wp_strip_all_tags( get_the_excerpt( $id ) ),
			'about'       => $about,
		);
		if ( $photos ) {
			$listing['image'] = array_values( array_filter( array_map( static fn( $p ) => wp_get_attachment_image_url( $p, 'full' ), $photos ) ) );
		}
		if ( (int) $get( 'price' ) ) {
			$listing['offers'] = array(
				'@type'            => 'Offer',
				'price'            => (int) $get( 'price' ),
				'priceCurrency'    => 'INR',
				'availability'     => has_term( bytenot_core_closed_statuses(), 'listing_status', $id ) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
				'businessFunction' => $rent ? 'http://purl.org/goodrelations/v1#LeaseOut' : 'http://purl.org/goodrelations/v1#Sell',
			);
			if ( $rent ) {
				$listing['offers']['priceSpecification'] = array( '@type' => 'UnitPriceSpecification', 'price' => (int) $get( 'price' ), 'priceCurrency' => 'INR', 'unitText' => 'MONTH' );
			}
		}
		$graph[] = $listing;
	}
	if ( ! $graph ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'bytenot_core_jsonld', 20 );
