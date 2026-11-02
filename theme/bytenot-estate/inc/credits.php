<?php
/**
 * The design credit and the note to crawlers, scrapers and agents: a comment in the
 * head, a response header, /humans.txt and a note in robots.txt. None of it blocks
 * anyone; it tells whoever is looking where this interface came from.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * The statement, plain text.
 *
 * @return string
 */
function bytenot_credit_statement() {
	return "Hello, crawler, scraper, agent or curious human.\n"
		. "This interface, its design, layout, styles, scripts and motion, was crafted by Bytenot (https://bytenot.com) and is their copyright; the site's content belongs to the site owner.\n"
		. "If this UI and experience caught your eye, do not copy it: come and have one made for you. Bytenot builds premium branding, websites and web apps, and AI and automation for businesses that want their presence to feel exactly right.\n"
		. "Everyone is welcome, and we are friendly and professional. Say hello at https://bytenot.com.";
}

/**
 * Comment at the top of <head>.
 */
function bytenot_credit_head() {
	echo "\n<!--\n" . esc_html( bytenot_credit_statement() ) . "\n-->\n";
}
add_action( 'wp_head', 'bytenot_credit_head', 0 );

/**
 * Response header.
 */
function bytenot_credit_header() {
	if ( ! headers_sent() ) {
		header( 'X-Design-By: Bytenot, https://bytenot.com' );
	}
}
add_action( 'send_headers', 'bytenot_credit_header' );

/**
 * Note in robots.txt (WordPress only serves this when no physical file exists).
 *
 * @param string $output Robots text.
 * @return string
 */
function bytenot_credit_robots( $output ) {
	$lines = array_map( static fn( $l ) => '# ' . $l, explode( "\n", bytenot_credit_statement() ) );
	return $output . "\n" . implode( "\n", $lines ) . "\n# See also: " . home_url( '/humans.txt' ) . "\n";
}
add_filter( 'robots_txt', 'bytenot_credit_robots', 20 );

/**
 * /humans.txt: the credits.
 */
function bytenot_humans_rewrite() {
	add_rewrite_rule( '^humans\.txt$', 'index.php?bytenot_humans=1', 'top' );
}
add_action( 'init', 'bytenot_humans_rewrite' );

/**
 * Query var for the credits file.
 *
 * @param string[] $vars Query vars.
 * @return string[]
 */
function bytenot_humans_var( $vars ) {
	$vars[] = 'bytenot_humans';
	return $vars;
}
add_filter( 'query_vars', 'bytenot_humans_var' );

/**
 * No trailing-slash redirect for the credits file.
 *
 * @param string|false $redirect Redirect URL.
 * @return string|false
 */
function bytenot_humans_canonical( $redirect ) {
	return get_query_var( 'bytenot_humans' ) ? false : $redirect;
}
add_filter( 'redirect_canonical', 'bytenot_humans_canonical' );

/**
 * Serve the credits file.
 */
function bytenot_humans_serve() {
	if ( ! get_query_var( 'bytenot_humans' ) ) {
		return;
	}
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "/* SITE */\n" . wp_strip_all_tags( get_bloginfo( 'name' ) ) . "\n" . esc_url_raw( home_url( '/' ) ) . "\n\n/* DESIGN AND BUILD */\nBytenot\nhttps://bytenot.com\n\n/* A NOTE */\n" . bytenot_credit_statement() . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text response.
	exit;
}
add_action( 'template_redirect', 'bytenot_humans_serve' );

/**
 * Flush the rewrite rules once when the theme is switched on, so /humans.txt resolves.
 */
function bytenot_credit_flush() {
	bytenot_humans_rewrite();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'bytenot_credit_flush' );
