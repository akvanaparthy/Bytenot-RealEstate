<?php
/**
 * Plugin Name:       Bytenot Core
 * Plugin URI:        https://github.com/akvanaparthy/Bytenot-RealEstate
 * Description:       Real-estate data layer: property listings, projects, localities, agents and lead capture. Companion plugin for the Bytenot Estate theme.
 * Version:           0.3.0
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

define( 'BYTENOT_CORE_VERSION', '0.3.0' );
define( 'BYTENOT_CORE_FILE', __FILE__ );
define( 'BYTENOT_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BYTENOT_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once BYTENOT_CORE_DIR . 'includes/settings.php';
require_once BYTENOT_CORE_DIR . 'includes/post-types.php';
require_once BYTENOT_CORE_DIR . 'includes/taxonomies.php';
require_once BYTENOT_CORE_DIR . 'includes/meta.php';
require_once BYTENOT_CORE_DIR . 'includes/format.php';
require_once BYTENOT_CORE_DIR . 'includes/bindings.php';
require_once BYTENOT_CORE_DIR . 'includes/lifecycle.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/interface-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/class-email-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/crm/class-webhook-provider.php';
require_once BYTENOT_CORE_DIR . 'includes/leads.php';
require_once BYTENOT_CORE_DIR . 'includes/query.php';
require_once BYTENOT_CORE_DIR . 'includes/blocks.php';
require_once BYTENOT_CORE_DIR . 'includes/site-content.php';
require_once BYTENOT_CORE_DIR . 'includes/schema.php';
require_once BYTENOT_CORE_DIR . 'includes/preview.php';
require_once BYTENOT_CORE_DIR . 'includes/demo.php';
require_once BYTENOT_CORE_DIR . 'includes/demo-site.php';
if ( is_admin() ) {
	require_once BYTENOT_CORE_DIR . 'includes/admin/admin.php';
}

/**
 * Flush rewrite rules once the post types exist.
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
 * Translations.
 */
function bytenot_core_load_textdomain() {
	load_plugin_textdomain( 'bytenot-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'bytenot_core_load_textdomain', 0 );
