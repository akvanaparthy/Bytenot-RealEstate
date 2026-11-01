<?php
/**
 * Bytenot Estate theme functions.
 *
 * Presentation only: enqueue the stylesheet and the small header script,
 * register pattern categories and block styles. All data lives in the
 * Property Core plugin.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 */
function bytenot_estate_setup() {
	load_theme_textdomain( 'bytenot-estate', get_template_directory() . '/languages' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	// The header art-directs its own menu toggle; the core overlay still works without it.
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'bytenot_estate_setup' );

/**
 * Front-end and editor stylesheet.
 */
function bytenot_estate_enqueue_styles() {
	wp_enqueue_style(
		'bytenot-estate-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
	// The brand motif (faint logomark on dark panels) is the site icon, so it is content, not code.
	$icon = get_site_icon_url( 512 );
	$vars = $icon ? '--bytenot-motif:url("' . esc_url( $icon ) . '");' : '';
	// The footer wordmark fills the width whatever the site title's length.
	$vars .= '--bytenot-title-chars:' . max( 3, mb_strlen( wp_strip_all_tags( get_bloginfo( 'name' ) ) ) ) . ';';
	wp_add_inline_style( 'bytenot-estate-style', ':root{' . $vars . '}' );
}
add_action( 'wp_enqueue_scripts', 'bytenot_estate_enqueue_styles' );

/**
 * Header behaviour (bookmark pieces after scroll) and the inquiry band settle.
 * A module with no dependencies; the page works fully without it.
 */
function bytenot_estate_enqueue_scripts() {
	wp_enqueue_script_module(
		'bytenot-estate-theme',
		get_theme_file_uri( 'assets/js/theme.js' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'bytenot_estate_enqueue_scripts' );

/**
 * Pattern categories.
 */
function bytenot_estate_pattern_categories() {
	register_block_pattern_category(
		'bytenot-estate-sections',
		array( 'label' => __( 'Bytenot Estate: sections', 'bytenot-estate' ) )
	);
	register_block_pattern_category(
		'bytenot-estate-property',
		array( 'label' => __( 'Bytenot Estate: property', 'bytenot-estate' ) )
	);
}
add_action( 'init', 'bytenot_estate_pattern_categories' );

/**
 * Block styles.
 */
function bytenot_estate_block_styles() {
	register_block_style( 'core/button', array( 'name' => 'accent', 'label' => __( 'Accent', 'bytenot-estate' ) ) );
	register_block_style( 'core/button', array( 'name' => 'outline', 'label' => __( 'Outline', 'bytenot-estate' ) ) );
	register_block_style( 'core/button', array( 'name' => 'glass', 'label' => __( 'Glass', 'bytenot-estate' ) ) );
	register_block_style( 'core/button', array( 'name' => 'light', 'label' => __( 'Light', 'bytenot-estate' ) ) );
	register_block_style( 'core/paragraph', array( 'name' => 'eyebrow', 'label' => __( 'Eyebrow', 'bytenot-estate' ) ) );
	register_block_style( 'core/paragraph', array( 'name' => 'label', 'label' => __( 'Label', 'bytenot-estate' ) ) );
	register_block_style( 'core/heading', array( 'name' => 'section-title', 'label' => __( 'Section title', 'bytenot-estate' ) ) );
	register_block_style( 'core/group', array( 'name' => 'motif', 'label' => __( 'Brand motif', 'bytenot-estate' ) ) );
}
add_action( 'init', 'bytenot_estate_block_styles' );
