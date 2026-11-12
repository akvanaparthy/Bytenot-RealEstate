<?php
/**
 * Plugin Name:       Bytenot Core
 * Plugin URI:        https://github.com/akvanaparthy/Bytenot-RealEstate
 * Description:       Real-estate data layer: listings (projects, apartments, properties), localities, advisors and lead capture. Companion plugin for the Bytenot Estate theme.
 * Version:           0.8.0
 * Requires at least: 7.1
 * Requires PHP:      8.1
 * Author:            Bytenot
 * Author URI:        https://github.com/akvanaparthy
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bytenot-core
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

define( 'BYTENOT_CORE_VERSION', '0.8.0' );
define( 'BYTENOT_CORE_FILE', __FILE__ );
define( 'BYTENOT_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BYTENOT_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once BYTENOT_CORE_DIR . 'includes/settings.php';
require_once BYTENOT_CORE_DIR . 'includes/kinds.php';
require_once BYTENOT_CORE_DIR . 'includes/post-types.php';
require_once BYTENOT_CORE_DIR . 'includes/taxonomies.php';
require_once BYTENOT_CORE_DIR . 'includes/meta.php';
require_once BYTENOT_CORE_DIR . 'includes/format.php';
require_once BYTENOT_CORE_DIR . 'includes/lifecycle.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/interface-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/class-email-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/class-webhook-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/leads.php';
require_once BYTENOT_CORE_DIR . 'includes/query.php';
require_once BYTENOT_CORE_DIR . 'includes/site-content.php';
require_once BYTENOT_CORE_DIR . 'includes/schema.php';
require_once BYTENOT_CORE_DIR . 'includes/preview.php';
require_once BYTENOT_CORE_DIR . 'includes/demo.php';
require_once BYTENOT_CORE_DIR . 'includes/demo-site.php';
if ( is_admin() ) {
	require_once BYTENOT_CORE_DIR . 'includes/admin/admin.php';
}

/**
 * Seed the vocabularies and route the kinds' addresses once the post types exist.
 */
function bytenot_core_activate() {
	bytenot_core_register_post_types();
	bytenot_core_register_taxonomies();
	bytenot_core_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bytenot_core_activate' );

/**
 * Clean up rewrite rules on deactivation. Content is never deleted.
 */
function bytenot_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bytenot_core_deactivate' );

/**
 * Closed listings leave the sitemap as well as the archives.
 *
 * @param array  $args      Query args.
 * @param string $post_type Post type.
 * @return array
 */
function bytenot_core_sitemap_args( $args, $post_type ) {
	if ( 'listing' === $post_type ) {
		$args['tax_query'][] = array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses(), 'operator' => 'NOT IN' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'bytenot_core_sitemap_args', 10, 2 );

/**
 * Translations.
 */
function bytenot_core_load_textdomain() {
	load_plugin_textdomain( 'bytenot-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'bytenot_core_load_textdomain', 0 );
