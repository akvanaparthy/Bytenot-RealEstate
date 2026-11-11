<?php
/**
 * Template tags: brand lockup, menus, icons, listing data and cards.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * A design class name as served. The production build replaces class names with short
 * hashes and writes inc/classmap.php; the few names composed at runtime go through here.
 *
 * @param string $names Space-separated class names.
 * @return string
 */
function bytenot_class( $names ) {
	static $map = null;
	if ( null === $map ) {
		$file = get_template_directory() . '/inc/classmap.php';
		$map  = file_exists( $file ) ? (array) include $file : array();
	}
	if ( ! $map ) {
		return $names;
	}
	return implode( ' ', array_map( static fn( $n ) => $map[ $n ] ?? $n, preg_split( '/\s+/', trim( (string) $names ) ) ) );
}

/**
 * Inline SVG icons used across the templates.
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 * @return string
 */
function bytenot_icon( $name, $size = 16 ) {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
	$paths  = array(
		'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'close'      => '<path d="M6 6l12 12M18 6 6 18"/>',
		'external'   => '<path d="M7 17L17 7M9 7h8v8"/>',
		'pin'        => '<path d="M12 21s7-6.4 7-11.5A7 7 0 0 0 5 9.5C5 14.6 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'area'       => '<path d="M4 4h6M4 4v6M20 20h-6M20 20v-6"/>',
		'bed'        => '<path d="M3 18v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6M3 18h18M5 10V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3"/>',
		'bath'       => '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3zM6 12V6a2 2 0 0 1 2-2h1"/>',
		'units'      => '<path d="M4 20V8l8-5 8 5v12M4 20h16M9 20v-5h6v5M8 11h2M14 11h2M8 15h2M14 15h2"/>',
		'download'   => '<path d="M12 4v12M7 11l5 5 5-5M5 20h14"/>',
		'play'       => '<path d="M8 5v14l11-7z"/>',
		'check'      => '<path d="M4 12.5l5 5L20 7"/>',
		'grid'       => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
		'phone'      => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
		'globe'      => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3.5 3 14.5 0 18M12 3c-3 3.5-3 14.5 0 18"/>',
	);
	$width = array( 'arrow-left' => '1.6', 'close' => '1.8', 'check' => '2.8', 'globe' => '1.6' );
	if ( isset( $width[ $name ] ) ) {
		$stroke = str_replace( 'stroke-width="1.8"', 'stroke-width="' . $width[ $name ] . '"', $stroke );
	}
	if ( 'grid' === $name ) {
		$stroke = 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"';
	}
	return sprintf( '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" %2$s aria-hidden="true">%3$s</svg>', (int) $size, $stroke, $paths[ $name ] ?? '' );
}

/**
 * Print an icon.
 *
 * @param string $name Icon name.
 * @param int    $size Pixel size.
 */
function bytenot_the_icon( $name, $size = 16 ) {
	echo bytenot_icon( $name, $size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
}

/**
 * Social brand glyphs (filled).
 *
 * @param string $name linkedin | x | facebook | instagram | youtube.
 * @return string
 */
function bytenot_social_icon( $name ) {
	$icons = array(
		'linkedin'  => '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zm1.78 13.02H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>',
		'x'         => '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.97 6.82H1.68l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23zm-1.16 17.52h1.83L7.08 4.13H5.12l11.96 15.64z"/></svg>',
		'facebook'  => '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.07C24 5.45 18.63.07 12 .07S0 5.45 0 12.07c0 5.99 4.39 10.95 10.13 11.85v-8.38H7.08v-3.47h3.05V9.43c0-3.01 1.79-4.67 4.53-4.67 1.31 0 2.69.23 2.69.23v2.96h-1.51c-1.49 0-1.96.92-1.96 1.87v2.25h3.33l-.53 3.47h-2.8v8.38C19.61 23.02 24 18.06 24 12.07z"/></svg>',
		'instagram' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.6" fill="currentColor"/></svg>',
		'youtube'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.6 15.6V8.4L15.8 12z"/></svg>',
	);
	return $icons[ $name ] ?? '';
}

/**
 * Text that may carry simple inline markup (br, em, strong).
 *
 * @param string $html Text.
 * @return string
 */
function bytenot_inline( $html ) {
	return wp_kses( (string) $html, array( 'br' => array(), 'em' => array(), 'strong' => array(), 'span' => array( 'class' => array() ) ) );
}

/**
 * "2.5" from 2.5, "3" from 3.0.
 *
 * @param float $n Number.
 * @return string
 */
function bytenot_trim_number( $n ) {
	return rtrim( rtrim( number_format( (float) $n, 1, '.', '' ), '0' ), '.' );
}

/**
 * The brand lockup: logo mark plus name and tagline, or a wordmark or lockup alone.
 *
 * @param string $classes Extra classes.
 * @param bool   $link    Wrap in a link to the home page.
 * @param bool   $dark    Always the dark version (the mobile drawer); the header also gets the light one for photos.
 */
function bytenot_brand( $classes = '', $link = true, $dark = false ) {
	$name = get_bloginfo( 'name' );
	$sub  = get_bloginfo( 'description' );
	$logo = (int) get_theme_mod( 'custom_logo' );
	if ( $logo && 'mark' !== bytenot_logo_shape( $logo ) ) {
		// a wordmark or lockup already carries the name: it stands alone, 40px tall. A light
		// version, when set, shows over photos while the header is transparent.
		$img = static function ( $id, $class, $alt ) {
			$size = wp_get_attachment_metadata( $id );
			if ( empty( $size['height'] ) ) {
				$size = bytenot_svg_size( $id );
			}
			$w = ! empty( $size['height'] ) ? (int) round( 40 * $size['width'] / $size['height'] ) : 160;
			return '<img class="' . esc_attr( bytenot_class( $class ) ) . '" src="' . esc_url( wp_get_attachment_url( $id ) ) . '" alt="' . esc_attr( $alt ) . '" width="' . $w . '" height="40"' . ( '' === $alt ? ' aria-hidden="true"' : '' ) . '>';
		};
		$light = $dark ? 0 : (int) bytenot_mod( 'header_logo_light' );
		$inner = $light
			? '<span class="brand__logos">' . $img( $logo, 'brand__logo brand__logo--dark', $name ) . $img( $light, 'brand__logo brand__logo--light', '' ) . '</span>'
			: $img( $logo, 'brand__logo', $name );
	} else {
		$mark  = $logo
			? wp_get_attachment_image( $logo, 'full', false, array( 'class' => 'brand__mark', 'alt' => '', 'width' => 42, 'height' => 26 ) )
			: '<img class="brand__mark" src="' . esc_url( get_theme_file_uri( 'assets/images/mark.svg' ) ) . '" alt="" width="42" height="26">';
		$inner = $mark . '<span class="brand__text"><span class="brand__name">' . esc_html( $name ) . '</span>' . ( $sub ? '<span class="brand__sub">' . esc_html( $sub ) . '</span>' : '' ) . '</span>';
	}
	if ( $link ) {
		/* translators: %s: site name. */
		printf( '<a class="brand %1$s" href="%2$s" aria-label="%3$s">%4$s</a>', esc_attr( $classes ), esc_url( home_url( '/' ) ), esc_attr( sprintf( __( '%s, home', 'bytenot-estate' ), $name ) ), $inner ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	} else {
		printf( '<span class="brand %1$s">%2$s</span>', esc_attr( $classes ), $inner ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/* ---------- what the site lists ---------- */

/**
 * The search vocabulary from Bytenot Core, with an empty fallback when the plugin is off.
 *
 * @return array<string, mixed>
 */
function bytenot_search_options() {
	static $opts = null;
	if ( null === $opts ) {
		$opts = function_exists( 'bytenot_core_search_options' )
			? bytenot_core_search_options()
			: array( 'kinds' => array(), 'primary' => '', 'types' => array(), 'typesByKind' => array(), 'intents' => array(), 'locations' => array(), 'budgets' => array(), 'configurations' => array(), 'has_config' => false );
	}
	return $opts;
}

/**
 * The listings page for a kind (the primary kind by default).
 *
 * @param string $kind Kind.
 * @return string
 */
function bytenot_archive_url( $kind = '' ) {
	return function_exists( 'bytenot_core_archive_url' ) ? bytenot_core_archive_url( $kind ) : home_url( '/' );
}

/**
 * The kind a page is about: the archive's kind, a listing's kind, else the primary kind.
 *
 * @return string
 */
function bytenot_current_kind() {
	if ( ! function_exists( 'bytenot_core_kind_of' ) ) {
		return '';
	}
	if ( is_singular( 'listing' ) ) {
		return bytenot_core_kind_of( get_the_ID() );
	}
	$kind = is_post_type_archive( 'listing' ) ? sanitize_key( (string) get_query_var( 'kind' ) ) : '';
	return $kind && isset( bytenot_core_kinds( true )[ $kind ] ) ? $kind : bytenot_core_primary_kind();
}

/**
 * What listings are called on this page, plural: "projects", "properties".
 *
 * @param bool   $capitalised First letter upper-cased, for buttons and menu items.
 * @param string $kind        A specific kind, else the current one.
 * @return string
 */
function bytenot_noun( $capitalised = false, $kind = '' ) {
	$noun = function_exists( 'bytenot_core_kind_label' ) ? bytenot_core_kind_label( $kind ?: bytenot_current_kind(), true ) : __( 'Listings', 'bytenot-estate' );
	return $capitalised ? $noun : mb_strtolower( $noun );
}

/**
 * One listing is called ... "project", "apartment".
 *
 * @param string $kind        Kind.
 * @param bool   $capitalised First letter upper-cased.
 * @return string
 */
function bytenot_kind_one( $kind, $capitalised = false ) {
	$word = function_exists( 'bytenot_core_kind_label' ) ? bytenot_core_kind_label( $kind, false ) : __( 'Listing', 'bytenot-estate' );
	return $capitalised ? $word : mb_strtolower( $word );
}

/**
 * Default primary menu when none is assigned: Home, About, the primary kind, Contact.
 *
 * @param array $args Menu args.
 */
function bytenot_menu_fallback( $args ) {
	$home  = home_url( '/' );
	$items = array(
		array( __( 'Home', 'bytenot-estate' ), $home, is_front_page() ),
		array( __( 'About Us', 'bytenot-estate' ), $home . '#about', false ),
		array( bytenot_noun( true, function_exists( 'bytenot_core_primary_kind' ) ? bytenot_core_primary_kind() : '' ), bytenot_archive_url(), is_post_type_archive( 'listing' ) || is_singular( 'listing' ) ),
		array( __( 'Contact Us', 'bytenot-estate' ), $home . '#inquiry', false ),
	);
	$nav = 'nav__list' === $args['menu_class'];
	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '">' . ( $nav ? '<li class="nav__hl" aria-hidden="true"></li>' : '' );
	foreach ( $items as $item ) {
		printf(
			'<li><a%1$s href="%2$s"%3$s>%4$s</a></li>',
			$nav ? ' class="' . esc_attr( bytenot_class( 'nav__link' . ( $item[2] ? ' is-active' : '' ) ) ) . '"' : '',
			esc_url( $item[1] ),
			$item[2] ? ' aria-current="page"' : '',
			esc_html( $item[0] )
		);
	}
	echo '</ul>';
}

/**
 * Language switcher: shown only when a multilingual plugin provides languages through
 * the `bytenot_languages` filter: [ [ 'code' => 'Eng', 'label' => 'English', 'url' => ..., 'current' => true ], ... ].
 */
function bytenot_language_switcher() {
	$languages = apply_filters( 'bytenot_languages', array() );
	if ( count( $languages ) < 2 ) {
		return;
	}
	$current = $languages[0];
	foreach ( $languages as $l ) {
		if ( ! empty( $l['current'] ) ) {
			$current = $l;
		}
	}
	?>
	<div class="lang" data-lang>
		<button class="lang__button" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="lang-menu">
			<?php bytenot_the_icon( 'globe', 20 ); ?>
			<span class="lang__label" data-lang-label><?php echo esc_html( $current['code'] ); ?></span>
		</button>
		<ul class="lang__menu" id="lang-menu" role="menu" hidden>
			<?php foreach ( $languages as $l ) : ?>
				<li><a role="menuitemradio" href="<?php echo esc_url( $l['url'] ); ?>" lang="<?php echo esc_attr( $l['lang'] ?? '' ); ?>" aria-checked="<?php echo ! empty( $l['current'] ) ? 'true' : 'false'; ?>"><?php echo esc_html( $l['label'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * The agency's phone, WhatsApp number, phone prefix and public email.
 *
 * @param string $key phone | whatsapp | email | rera | rera_state | prefix.
 * @return string
 */
function bytenot_contact( $key ) {
	if ( 'email' === $key ) {
		return (string) bytenot_mod( 'contact_email' );
	}
	if ( ! function_exists( 'bytenot_core_setting' ) ) {
		return 'prefix' === $key ? '+91' : '';
	}
	$map = array( 'phone' => 'phone', 'whatsapp' => 'whatsapp', 'rera' => 'rera_number', 'rera_state' => 'rera_state', 'prefix' => 'phone_prefix' );
	return (string) bytenot_core_setting( $map[ $key ] ?? $key );
}

/**
 * A tel: link for a phone number.
 *
 * @param string $phone Phone as entered.
 * @return string
 */
function bytenot_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^\d+]/', '', (string) $phone );
}

/**
 * WhatsApp link with an optional prefilled message.
 *
 * @param string $number Digits with country code.
 * @param string $text   Message.
 * @return string
 */
function bytenot_wa( $number, $text = '' ) {
	$number = preg_replace( '/\D/', '', (string) $number );
	if ( ! $number ) {
		return '';
	}
	return 'https://wa.me/' . $number . ( $text ? '?text=' . rawurlencode( $text ) : '' );
}

/* ---------- listings ---------- */

/**
 * One listing as the templates and the script see it.
 *
 * @param int $id Listing ID.
 * @return array<string, mixed>
 */
function bytenot_listing( $id ) {
	$get   = static fn( $k ) => bytenot_core_get( $id, $k );
	$kind  = bytenot_core_kind_of( $id );
	$type  = get_the_terms( $id, 'listing_type' );
	$type  = $type && ! is_wp_error( $type ) ? $type[0] : null;
	$poss  = get_the_terms( $id, 'possession' );
	$poss  = $poss && ! is_wp_error( $poss ) ? $poss[0] : null;
	$locs  = get_the_terms( $id, 'location' );
	$city  = null;
	$area  = null;
	if ( $locs && ! is_wp_error( $locs ) ) {
		foreach ( $locs as $t ) {
			if ( $t->parent ) {
				$area = $t;
			} else {
				$city = $t;
			}
		}
		if ( $area && ! $city ) {
			$city = get_term( $area->parent, 'location' );
		}
		if ( ! $area ) {
			$area = $city;
		}
	}
	$project = 'project' === $kind;
	$rent    = ! $project && bytenot_core_is_rent( $id );
	$land    = $type && preg_match( '/plot|land/i', $type->slug );
	$unit    = $get( 'area_unit' ) ?: ( $land ? 'sqyd' : 'sqft' );
	$sqft    = (float) $get( 'area_primary' );
	$price   = (int) $get( 'price' );
	$min     = (int) $get( 'price_min' );
	$max     = (int) $get( 'price_max' );
	$bhk     = (float) $get( 'bedrooms' );
	$baths   = (float) $get( 'bathrooms' );
	$img     = get_the_post_thumbnail_url( $id, 'bytenot-card' );
	$parent  = bytenot_core_project_of( $id );
	$units   = $project ? ( (int) $get( 'units_total' ) ?: (int) $get( 'units_listed' ) ) : 0;
	$bhk_txt = $get( 'bedroom_label' ) ?: ( $bhk ? sprintf( /* translators: %s: bedrooms. */ __( '%s BHK', 'bytenot-estate' ), bytenot_trim_number( $bhk ) ) : '' );
	$config  = (string) $get( 'config_label' );

	if ( $project ) {
		$facts = array(
			bytenot_core_format_area_range( $get( 'area_min' ), $get( 'area_max' ), $unit ),
			$config,
			/* translators: %d: units. */
			$units ? sprintf( _n( '%d unit', '%d units', $units, 'bytenot-estate' ), $units ) : bytenot_trim_number( (int) $get( 'towers' ) ) . ' ' . __( 'towers', 'bytenot-estate' ),
		);
		if ( ! (int) $get( 'towers' ) && ! $units ) {
			$facts[2] = '';
		}
		$price_text = $get( 'price_on_request' ) ? __( 'Price on request', 'bytenot-estate' ) : bytenot_core_format_range( $min, $max );
		$psf        = $config && $type ? trim( $config . ' ' . mb_strtolower( $type->name ) ) : '';
	} else {
		$facts = array(
			bytenot_core_format_area( $sqft, $unit ),
			$bhk > 0 ? $bhk_txt : ( $type ? $type->name : '' ),
			$baths > 0 ? sprintf( /* translators: %s: bathrooms. */ _n( '%s Bath', '%s Baths', (int) ceil( $baths ), 'bytenot-estate' ), bytenot_trim_number( $baths ) ) : '',
		);
		$price_text = $get( 'price_on_request' ) ? __( 'Price on request', 'bytenot-estate' ) : bytenot_core_format_price( $price, $rent );
		$psf        = $rent ? __( 'per month', 'bytenot-estate' ) : bytenot_core_format_psf( $id );
	}
	return array(
		'id'         => $id,
		'title'      => get_the_title( $id ),
		'url'        => get_permalink( $id ),
		'kind'       => $kind,
		'kindLabel'  => bytenot_kind_one( $kind, true ),
		'type'       => $type ? $type->slug : '',
		'typeLabel'  => $type ? $type->name : '',
		'city'       => $city ? $city->slug : '',
		'cityLabel'  => $city ? $city->name : '',
		'area'       => $area ? $area->name : '',
		'place'      => bytenot_core_format_place( $id ),
		'bhk'        => $bhk,
		'bhkLabel'   => $bhk_txt,
		'bhkSet'     => array_values( array_filter( array_map( 'floatval', explode( ',', (string) $get( 'bhk_set' ) ) ) ) ),
		'baths'      => $baths,
		'sqft'       => $sqft,
		'unit'       => $unit,
		'areaText'   => $facts[0],
		'facts'      => $facts,
		'price'      => $project ? $min : $price,
		'priceMin'   => $min,
		'priceMax'   => $max,
		'priceText'  => $price_text,
		'psf'        => $psf,
		'intent'     => $rent ? 'rent' : 'buy',
		'possession' => $poss ? $poss->slug : '',
		'status'     => bytenot_listing_status( $id, $rent, $poss ),
		'line'       => bytenot_core_format_line( $id ),
		'img'        => $img ?: '',
		'thumb'      => get_the_post_thumbnail_url( $id, 'thumbnail' ) ?: '',
		'stage'      => get_the_post_thumbnail_url( $id, 'bytenot-stage' ) ?: ( $img ?: '' ),
		'added'      => (int) get_post_time( 'U', true, $id ),
		'featured'   => (bool) $get( 'featured' ),
		'why'        => (string) $get( 'featured_reason' ),
		'ref'        => (string) $get( 'reference_id' ),
		'parent'     => $parent ? array( 'id' => $parent, 'title' => get_the_title( $parent ), 'url' => get_permalink( $parent ) ) : null,
		'units'      => $units,
		'developer'  => $project ? bytenot_core_developer_name( $id ) : '',
		'tower'      => (string) $get( 'tower' ),
		'unitNo'     => (string) $get( 'unit_no' ),
		'cta'        => $project ? sprintf( /* translators: %s: kind, e.g. "project". */ __( 'View %s', 'bytenot-estate' ), bytenot_kind_one( $kind ) ) : __( 'Book a visit', 'bytenot-estate' ),
		'ctaHref'    => $project ? get_permalink( $id ) : get_permalink( $id ) . '#visit',
	);
}

/**
 * The short status a card shows: "Ready to move", "Available now", "Possession Dec 2026".
 *
 * @param int          $id   Listing ID.
 * @param bool         $rent For rent.
 * @param WP_Term|null $poss Possession term.
 * @return string
 */
function bytenot_listing_status( $id, $rent, $poss ) {
	$date = bytenot_core_get( $id, 'possession_date' );
	if ( $rent ) {
		if ( $date && strtotime( $date ) > time() ) {
			/* translators: %s: date. */
			return sprintf( __( 'Available %s', 'bytenot-estate' ), wp_date( 'j M', strtotime( $date ) ) );
		}
		return __( 'Available now', 'bytenot-estate' );
	}
	if ( $poss && in_array( $poss->slug, array( 'under-construction', 'new-launch' ), true ) && $date && strtotime( $date ) > time() ) {
		/* translators: %s: year or date. */
		return sprintf( __( 'Possession %s', 'bytenot-estate' ), wp_date( strlen( $date ) > 7 ? 'M Y' : 'M Y', strtotime( $date ) ) );
	}
	return $poss ? $poss->name : __( 'For sale', 'bytenot-estate' );
}

/**
 * All open listings of a kind, curated order then newest, as the listing page's data set.
 *
 * @param string $kind Kind; empty for the current kind.
 * @return array<int, array<string, mixed>>
 */
function bytenot_listings( $kind = '' ) {
	$ids = get_posts(
		array(
			'post_type'      => 'listing',
			'post_status'    => 'publish',
			'posts_per_page' => 300,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array( 'taxonomy' => 'listing_kind', 'field' => 'slug', 'terms' => $kind ?: bytenot_current_kind() ),
				array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses(), 'operator' => 'NOT IN' ),
			),
		)
	);
	return array_map( 'bytenot_listing', $ids );
}

/**
 * The listings the featured spotlight shows: flagged ones first, newest fill in.
 *
 * @param int $count How many.
 * @return array<int, array<string, mixed>>
 */
function bytenot_featured_items( $count = 4 ) {
	$base  = array(
		'post_type'     => 'listing',
		'post_status'   => 'publish',
		'fields'        => 'ids',
		'no_found_rows' => true,
		'tax_query'     => array( array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses(), 'operator' => 'NOT IN' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	);
	$ids   = get_posts( array_merge( $base, array( 'posts_per_page' => $count, 'meta_key' => 'bn_featured', 'meta_value' => '1', 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( count( $ids ) < $count ) {
		$ids = array_merge( $ids, get_posts( array_merge( $base, array( 'posts_per_page' => $count - count( $ids ), 'post__not_in' => $ids ) ) ) ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
	}
	$items = array();
	foreach ( $ids as $id ) {
		$l          = bytenot_listing( $id );
		$l['why']   = $l['why'] ?: __( 'New this week', 'bytenot-estate' );
		$l['meta']  = $l['place'] . ' · ' . ( 'project' === $l['kind'] ? ( $l['facts'][1] ?: $l['facts'][0] ) : ( $l['bhkLabel'] ?: $l['areaText'] ) );
		$l['visit'] = $l['url'] . '#visit';
		$l['alt']   = $l['title'] . ', ' . $l['area'];
		$items[]    = $l;
	}
	return $items;
}

/**
 * A listing card (listing page, nearby, and the script's template).
 *
 * @param array|null $p Listing array from bytenot_listing(); null prints the empty template.
 */
function bytenot_card( $p ) {
	$empty = null === $p;
	$p     = $p ?: array( 'url' => '#', 'img' => '', 'title' => '', 'area' => '', 'status' => '', 'place' => '', 'priceText' => '', 'facts' => array( '', '', '' ), 'kind' => '', 'cta' => '', 'ctaHref' => '#' );
	$icons = array( 'area', 'bed', 'project' === $p['kind'] ? 'units' : 'bath' );
	?>
	<li class="card" data-kind="<?php echo esc_attr( $p['kind'] ); ?>">
		<a class="card__media" href="<?php echo esc_url( $p['url'] ); ?>">
			<img class="card__img" src="<?php echo esc_url( $p['img'] ); ?>" alt="<?php echo esc_attr( $empty ? '' : $p['title'] . ', ' . $p['area'] ); ?>" width="900" height="660" loading="lazy">
		</a>
		<span class="card__tab"><span class="card__status"><?php echo esc_html( $p['status'] ); ?></span></span>
		<div class="card__body">
		<div class="card__head">
			<h3 class="card__title"><a href="<?php echo esc_url( $p['url'] ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
			<p class="card__place">
				<?php bytenot_the_icon( 'pin', 14 ); ?>
				<span><?php echo esc_html( $p['place'] ); ?></span>
			</p>
		</div>
		<ul class="card__facts">
			<?php foreach ( $p['facts'] as $bytenot_i => $bytenot_fact ) : ?>
			<li class="fact"<?php echo '' === $bytenot_fact && ! $empty ? ' hidden' : ''; ?>><?php if ( $empty && 2 === $bytenot_i ) : ?><span class="fact__icon" data-icon="bath"><?php bytenot_the_icon( 'bath', 14 ); ?></span><span class="fact__icon" data-icon="units" hidden><?php bytenot_the_icon( 'units', 14 ); ?></span><?php else : ?><span class="fact__icon"><?php bytenot_the_icon( $icons[ $bytenot_i ], 14 ); ?></span><?php endif; ?><span data-fact="<?php echo (int) $bytenot_i; ?>"><?php echo esc_html( $bytenot_fact ); ?></span></li>
			<?php endforeach; ?>
		</ul>
		<div class="card__foot">
			<p class="card__price"><?php echo esc_html( $p['priceText'] ); ?></p>
			<a class="btn btn--accent btn--sm" href="<?php echo esc_url( $p['ctaHref'] ); ?>" data-cta><span data-cta-text><?php echo esc_html( $p['cta'] ); ?></span> <?php bytenot_the_icon( 'arrow' ); ?></a>
		</div>
		</div>
	</li>
	<?php
}

/**
 * The hidden fields every lead form carries (honeypot and time-to-submit).
 */
function bytenot_lead_fields() {
	?>
	<label class="hp" aria-hidden="true"><?php esc_html_e( 'Website', 'bytenot-estate' ); ?><input type="text" name="website" tabindex="-1" autocomplete="off"></label>
	<input type="hidden" name="started" value="<?php echo esc_attr( time() ); ?>">
	<?php
}

/**
 * The phone field's country prefix and the input, shared by both forms.
 *
 * @param string $class Input class.
 */
function bytenot_phone_input( $class ) {
	$prefix = bytenot_contact( 'prefix' );
	?>
	<span class="<?php echo esc_attr( bytenot_class( $class . '__phone' ) ); ?>"><?php if ( $prefix ) : ?><span class="<?php echo esc_attr( bytenot_class( $class . '__prefix' ) ); ?>"><?php echo esc_html( $prefix ); ?></span><?php endif; ?><input class="<?php echo esc_attr( bytenot_class( $class . '__input' ) ); ?>" type="tel" name="phone" inputmode="tel" minlength="8" maxlength="15" autocomplete="<?php echo $prefix ? 'tel-national' : 'tel'; ?>" required></span>
	<?php
}

/**
 * Customizer image as an <img>, or nothing.
 *
 * @param string $mod      Theme mod holding an attachment ID.
 * @param string $size     Image size.
 * @param array  $attrs    Attributes.
 * @param string $fallback Fallback URL (theme file) when no image is set.
 */
function bytenot_mod_image( $mod, $size, $attrs = array(), $fallback = '' ) {
	$id = (int) bytenot_mod( $mod );
	if ( $id ) {
		echo wp_get_attachment_image( $id, $size, false, array_merge( array( 'alt' => '' ), $attrs, bytenot_svg_size( $id ) ) );
	} elseif ( $fallback ) {
		$a = '';
		foreach ( array_merge( array( 'alt' => '' ), $attrs ) as $k => $v ) {
			$a .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		echo '<img src="' . esc_url( $fallback ) . '"' . $a . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
	}
}

/**
 * Width and height for an SVG attachment (WordPress stores none), read once from the
 * file's width/height or viewBox and cached in post meta.
 *
 * @param int $id Attachment ID.
 * @return array{width?:int,height?:int}
 */
function bytenot_svg_size( $id ) {
	if ( 'image/svg+xml' !== get_post_mime_type( $id ) ) {
		return array();
	}
	$cached = get_post_meta( $id, '_bytenot_svg_size', true );
	if ( is_array( $cached ) && ! empty( $cached['width'] ) ) {
		return $cached;
	}
	$file = get_attached_file( $id );
	$size = array();
	if ( $file && is_readable( $file ) ) {
		$head = file_get_contents( $file, false, null, 0, 4096 );
		if ( preg_match( '/<svg[^>]*\bwidth="([\d.]+)(?:px)?"[^>]*\bheight="([\d.]+)(?:px)?"/i', $head, $m ) ) {
			$size = array( 'width' => (int) round( $m[1] ), 'height' => (int) round( $m[2] ) );
		} elseif ( preg_match( '/viewBox="\s*[\d.-]+[\s,]+[\d.-]+[\s,]+([\d.]+)[\s,]+([\d.]+)/i', $head, $m ) ) {
			$size = array( 'width' => (int) round( $m[1] ), 'height' => (int) round( $m[2] ) );
		}
	}
	if ( $size ) {
		update_post_meta( $id, '_bytenot_svg_size', $size );
	}
	return $size;
}

/**
 * A logo's shape from its proportions: wide strips, tall blocks, square marks
 * (shown with the name beside them), or normal.
 *
 * @param int $id Attachment ID.
 * @return string '' | tall | wide | mark
 */
function bytenot_logo_shape( $id ) {
	$meta = $id ? wp_get_attachment_metadata( $id ) : null;
	$w    = (int) ( $meta['width'] ?? 0 );
	$h    = (int) ( $meta['height'] ?? 0 );
	if ( ! $w || ! $h ) {
		$svg = bytenot_svg_size( $id );
		$w   = (int) ( $svg['width'] ?? 0 );
		$h   = (int) ( $svg['height'] ?? 0 );
	}
	if ( ! $w || ! $h ) {
		return '';
	}
	$ratio = $h / $w;
	if ( $ratio >= 0.9 ) {
		return 'mark';
	}
	if ( $ratio >= 0.45 ) {
		return 'tall';
	}
	return $ratio <= 0.3 ? 'wide' : '';
}

/**
 * The legal links (footer, right cell): the "legal" menu, else the privacy policy page.
 *
 * @return array<int, array{0:string,1:string}>
 */
function bytenot_legal_links() {
	$locations = get_nav_menu_locations();
	$out       = array();
	if ( ! empty( $locations['legal'] ) ) {
		foreach ( (array) wp_get_nav_menu_items( $locations['legal'] ) as $item ) {
			$out[] = array( $item->title, $item->url );
		}
	}
	if ( ! $out && get_privacy_policy_url() ) {
		$out[] = array( __( 'Privacy policy', 'bytenot-estate' ), get_privacy_policy_url() );
	}
	return $out;
}
