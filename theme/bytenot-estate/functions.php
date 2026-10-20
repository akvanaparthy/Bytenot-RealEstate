<?php
/**
 * Bytenot Estate theme functions.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the theme stylesheet.
 */
function bytenot_estate_enqueue_styles() {
	wp_enqueue_style(
		'bytenot-estate-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'bytenot_estate_enqueue_styles' );
