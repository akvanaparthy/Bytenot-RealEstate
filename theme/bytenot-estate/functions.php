<?php
/**
 * Bytenot Estate theme functions.
 *
 * A classic theme built from the design prototype: the templates carry the
 * prototype's markup, assets/css/theme.css its stylesheet and assets/js/theme.js
 * its behaviour. Texts and images are Customizer settings; listings, advisors,
 * testimonials, FAQs and partners come from the Property Core plugin.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

define( 'BYTENOT_VERSION', wp_get_theme()->get( 'Version' ) );

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/property.php';
require get_template_directory() . '/inc/credits.php';

/**
 * Theme setup.
 */
function bytenot_setup() {
	load_theme_textdomain( 'bytenot-estate', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 26, 'width' => 42, 'flex-height' => true, 'flex-width' => true, 'unlink-homepage-logo' => false ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	remove_theme_support( 'core-block-patterns' );

	register_nav_menus(
		array(
			'primary'      => __( 'Primary (header and mobile drawer)', 'bytenot-estate' ),
			'footer'       => __( 'Footer', 'bytenot-estate' ),
			'footer-types' => __( 'Footer: property types', 'bytenot-estate' ),
			'legal'        => __( 'Footer: legal', 'bytenot-estate' ),
		)
	);

	add_image_size( 'bytenot-card', 900, 660, true );
	add_image_size( 'bytenot-stage', 1200, 800, true );
	add_image_size( 'bytenot-square', 560, 560, true );
	add_image_size( 'bytenot-portrait', 600, 700, true );
}
add_action( 'after_setup_theme', 'bytenot_setup' );

/**
 * Styles and the script. The prototype stylesheet is loaded as is; wp.css adds the
 * WordPress-only pieces (admin bar, editor content, forms status).
 */
function bytenot_assets() {
	wp_enqueue_style( 'bytenot-estate', get_theme_file_uri( 'assets/css/theme.css' ), array(), BYTENOT_VERSION );
	wp_enqueue_style( 'bytenot-estate-wp', get_theme_file_uri( 'assets/css/wp.css' ), array( 'bytenot-estate' ), BYTENOT_VERSION );
	// The brand motif (faint logomark on dark panels) is the site icon or the logo: content, not code.
	$motif = get_site_icon_url( 512 );
	if ( ! $motif && has_custom_logo() ) {
		$motif = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}
	$vars = $motif ? '--bytenot-motif:url("' . esc_url( $motif ) . '");' : '';
	$vars .= '--bytenot-title-chars:' . max( 3, mb_strlen( wp_strip_all_tags( get_bloginfo( 'name' ) ) ) ) . ';';
	wp_add_inline_style( 'bytenot-estate-wp', ':root{' . $vars . '}' );

	wp_enqueue_script( 'bytenot-estate', get_theme_file_uri( 'assets/js/theme.js' ), array(), BYTENOT_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_add_inline_script( 'bytenot-estate', 'window.bytenotConfig = ' . wp_json_encode( bytenot_config() ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'bytenot_assets' );

/**
 * What the script needs from the server: the agency's vocabulary, the leads endpoint
 * and translatable strings.
 *
 * @return array<string, mixed>
 */
function bytenot_config() {
	$opts    = function_exists( 'property_core_search_options' ) ? property_core_search_options() : array( 'types' => array(), 'intents' => array(), 'locations' => array(), 'budgets' => array(), 'configurations' => array(), 'has_config' => false );
	$budgets = array();
	foreach ( array_keys( $opts['budgets'] ) as $key ) {
		list( $min, $max ) = array_pad( explode( '-', $key, 2 ), 2, '' );
		$budgets[ $key ]   = array( (int) $min, '' === $max ? null : (int) $max );
	}
	return array(
		'types'      => (object) $opts['types'],
		'typePlural' => (object) bytenot_type_plurals( array_keys( $opts['types'] ) ),
		'intents'    => (object) $opts['intents'],
		'locations'  => (object) $opts['locations'],
		'budgets'    => (object) $budgets,
		'hasConfig'  => (bool) $opts['has_config'],
		'leads'      => esc_url_raw( rest_url( 'property-core/v1/leads' ) ),
		'pageSize'   => 6,
		'i18n'       => array(
			'allProperties' => __( 'All properties', 'bytenot-estate' ),
			/* translators: %s: city name. */
			'in'            => __( ' in %s', 'bytenot-estate' ),
			'forRent'       => __( ' for rent', 'bytenot-estate' ),
			'result'        => __( '(%d result)', 'bytenot-estate' ),
			'results'       => __( '(%d results)', 'bytenot-estate' ),
			'error'         => __( 'Something went wrong. Please call us instead.', 'bytenot-estate' ),
			/* translators: %s: first name. */
			'thanks'        => __( 'Thanks %s, an advisor will reach you within the hour.', 'bytenot-estate' ),
			/* translators: 1: first name, 2: advisor first name, 3: property title. */
			'thanksVisit'   => __( 'Thanks %1$s, %2$s will call you within the hour about %3$s.', 'bytenot-estate' ),
			'saved'         => __( '%s saved to your shortlist.', 'bytenot-estate' ),
			'removed'       => __( '%s removed from your shortlist.', 'bytenot-estate' ),
			'save'          => __( 'Save', 'bytenot-estate' ),
			'savedLabel'    => __( 'Saved', 'bytenot-estate' ),
			'copied'        => __( 'Link copied to your clipboard.', 'bytenot-estate' ),
			'years'         => __( '%s years', 'bytenot-estate' ),
			'tenure'        => __( 'Tenure', 'bytenot-estate' ),
			'rate'          => __( 'Interest rate', 'bytenot-estate' ),
			'financed'      => __( 'Financed', 'bytenot-estate' ),
			'photo'         => __( '%1$s, photo %2$d of %3$d', 'bytenot-estate' ),
			'km'            => __( '%s km', 'bytenot-estate' ),
		),
	);
}

/**
 * Body classes: the header is light over the home hero and dark everywhere else.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function bytenot_body_class( $classes ) {
	if ( is_singular( 'property' ) ) {
		$classes[] = bytenot_class( 'page--property' );
	} elseif ( ! is_front_page() ) {
		$classes[] = bytenot_class( 'page--solid' );
	}
	return $classes;
}
add_filter( 'body_class', 'bytenot_body_class' );

/**
 * Menu links carry the design's classes.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu args.
 * @return array
 */
function bytenot_menu_link_attributes( $atts, $item, $args ) {
	$current = $item->current && false === strpos( (string) $item->url, '#' );
	if ( ! $current ) {
		unset( $atts['aria-current'] );
	}
	if ( 'primary' === ( $args->theme_location ?? '' ) && 'nav__list' === ( $args->menu_class ?? '' ) ) {
		$atts['class'] = bytenot_class( 'nav__link' . ( $current ? ' is-active' : '' ) );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'bytenot_menu_link_attributes', 10, 3 );

/**
 * Nothing on the <li> except what the script and styles need.
 *
 * @param string[] $classes Item classes.
 * @return string[]
 */
function bytenot_menu_item_classes( $classes, $item ) {
	$keep = array( 'menu-item-has-children' );
	if ( false === strpos( (string) $item->url, '#' ) ) {
		$keep[] = 'current-menu-item';
	}
	return array_values( array_intersect( $classes, $keep ) );
}
add_filter( 'nav_menu_css_class', 'bytenot_menu_item_classes', 10, 2 );

/**
 * Lighter head: no emoji script, no shortlink, no generator tag.
 */
function bytenot_head_cleanup() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'bytenot_head_cleanup' );

/**
 * Listing cards and stage images only need the sizes the design uses.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function bytenot_image_size_names( $sizes ) {
	return array_merge( $sizes, array( 'bytenot-card' => __( 'Listing card', 'bytenot-estate' ) ) );
}
add_filter( 'image_size_names_choose', 'bytenot_image_size_names' );
