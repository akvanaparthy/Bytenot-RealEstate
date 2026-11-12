<?php
/**
 * Structured data: RealEstateAgent on the home page, RealEstateListing on listing pages
 * (an ApartmentComplex for a project, an Apartment for a unit, a House, Place or
 * Apartment for a property by its sub-type). Skipped when an SEO plugin that writes
 * schema is active.
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
	if ( is_singular( 'listing' ) ) {
		$graph[] = bytenot_core_listing_schema( get_the_ID() );
	}
	if ( ! $graph ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'bytenot_core_jsonld', 20 );

/**
 * Description, Open Graph and Twitter card tags, so a shared link shows a title, a line and a
 * picture. A listing shares its own photo; everything else shares the sharing image from
 * Setup, or the site icon. Skipped when an SEO plugin is active.
 */
function bytenot_core_social_meta() {
	if ( bytenot_core_seo_plugin_active() || is_404() || is_search() ) {
		return;
	}
	$single = is_singular();
	$title  = wp_get_document_title();
	$desc   = $single ? wp_strip_all_tags( get_the_excerpt() ) : '';
	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}
	/**
	 * The line a shared link shows. The theme supplies its own on the home page.
	 *
	 * @param string $desc Description so far.
	 */
	$desc = apply_filters( 'bytenot_core_social_description', $desc );
	$desc = wp_trim_words( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) ), 28, '…' );
	$url   = $single ? get_permalink() : ( is_front_page() ? home_url( '/' ) : home_url( '/' . ltrim( (string) ( $GLOBALS['wp']->request ?? '' ), '/' ) ) );
	$image = $single && has_post_thumbnail() ? get_post_thumbnail_id() : 0;
	$size  = $image ? 'large' : 'full';
	if ( ! $image ) {
		$image = absint( bytenot_core_setting( 'og_image' ) );
	}
	if ( ! $image ) {
		$image = (int) get_option( 'site_icon' );
	}
	$src = $image ? wp_get_attachment_image_src( $image, $size ) : null;
	$tag = static function ( $attr, $key, $value ) {
		if ( '' !== (string) $value ) {
			echo '<meta ' . $attr . '="' . esc_attr( $key ) . '" content="' . esc_attr( $value ) . '">' . "\n";
		}
	};
	$tag( 'name', 'description', $desc );
	$tag( 'property', 'og:type', is_singular( 'post' ) ? 'article' : 'website' );
	$tag( 'property', 'og:site_name', get_bloginfo( 'name' ) );
	$tag( 'property', 'og:locale', get_locale() );
	$tag( 'property', 'og:title', $title );
	$tag( 'property', 'og:description', $desc );
	$tag( 'property', 'og:url', $url );
	if ( $src ) {
		$tag( 'property', 'og:image', $src[0] );
		$tag( 'property', 'og:image:width', (int) $src[1] );
		$tag( 'property', 'og:image:height', (int) $src[2] );
	}
	$tag( 'name', 'twitter:card', $src ? 'summary_large_image' : 'summary' );
	$tag( 'name', 'twitter:title', $title );
	$tag( 'name', 'twitter:description', $desc );
	if ( $src ) {
		$tag( 'name', 'twitter:image', $src[0] );
	}
}
add_action( 'wp_head', 'bytenot_core_social_meta', 5 );

/**
 * The RealEstateListing node for one listing.
 *
 * @param int $id Listing ID.
 * @return array<string, mixed>
 */
function bytenot_core_listing_schema( $id ) {
	$get    = static fn( $k ) => bytenot_core_get( $id, $k );
	$kind   = bytenot_core_kind_of( $id );
	$rent   = bytenot_core_is_rent( $id );
	$type   = bytenot_core_type_name( $id );
	$slug   = sanitize_title( $type );
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
	$land = (bool) preg_match( '/plot|land/', $slug );
	$unit = $get( 'area_unit' ) ?: ( $land ? 'sqyd' : 'sqft' );
	if ( 'project' === $kind ) {
		$about_type = 'ApartmentComplex';
	} elseif ( 'apartment' === $kind ) {
		$about_type = 'Apartment';
	} else {
		$about_type = $land || preg_match( '/commercial|office|shop/', $slug ) ? 'Place' : ( preg_match( '/villa|house|bungalow/', $slug ) ? 'House' : 'Apartment' );
	}
	$about = array_filter(
		array(
			'@type'                  => $about_type,
			'numberOfBedrooms'       => (float) $get( 'bedrooms' ) ?: null,
			'numberOfBathroomsTotal' => (float) $get( 'bathrooms' ) ?: null,
			'floorSize'              => (float) $get( 'area_primary' ) ? array( '@type' => 'QuantitativeValue', 'value' => (float) $get( 'area_primary' ), 'unitCode' => array( 'sqft' => 'FTK', 'sqyd' => 'YDK', 'sqm' => 'MTK', 'acre' => 'ACR' )[ $unit ] ?? 'FTK' ) : null,
			'numberOfAccommodationUnits' => 'project' === $kind && (int) $get( 'units_total' ) ? (int) $get( 'units_total' ) : null,
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
	$project = bytenot_core_project_of( $id );
	if ( $project ) {
		$listing['isPartOf'] = array( '@type' => 'RealEstateListing', 'name' => get_the_title( $project ), 'url' => get_permalink( $project ) );
	}
	if ( $photos ) {
		$listing['image'] = array_values( array_filter( array_map( static fn( $p ) => wp_get_attachment_image_url( $p, 'full' ), $photos ) ) );
	}
	$closed = has_term( bytenot_core_closed_statuses(), 'listing_status', $id );
	$price  = 'project' === $kind ? (int) $get( 'price_min' ) : (int) $get( 'price' );
	if ( $price ) {
		$listing['offers'] = array(
			'@type'            => 'Offer',
			'price'            => $price,
			'priceCurrency'    => 'INR',
			'availability'     => $closed ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
			'businessFunction' => $rent ? 'http://purl.org/goodrelations/v1#LeaseOut' : 'http://purl.org/goodrelations/v1#Sell',
		);
		if ( 'project' === $kind && (int) $get( 'price_max' ) > $price ) {
			$listing['offers'] = array( '@type' => 'AggregateOffer', 'lowPrice' => $price, 'highPrice' => (int) $get( 'price_max' ), 'priceCurrency' => 'INR', 'availability' => $listing['offers']['availability'] );
		} elseif ( $rent ) {
			$listing['offers']['priceSpecification'] = array( '@type' => 'UnitPriceSpecification', 'price' => $price, 'priceCurrency' => 'INR', 'unitText' => 'MONTH' );
		}
	}
	return $listing;
}
