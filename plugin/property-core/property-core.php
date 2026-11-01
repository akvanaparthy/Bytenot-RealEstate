<?php
/**
 * Plugin Name:       Property Core
 * Plugin URI:        https://github.com/akvanaparthy/Bytenot-RealEstate
 * Description:       Real-estate data layer: property listings, projects, localities, agents and lead capture. Companion plugin for the Bytenot Estate theme.
 * Version:           0.2.0
 * Requires at least: 7.1
 * Requires PHP:      8.1
 * Author:            Bytenot
 * Author URI:        https://github.com/akvanaparthy
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       property-core
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

define( 'PROPERTY_CORE_VERSION', '0.2.0' );
define( 'PROPERTY_CORE_FILE', __FILE__ );
define( 'PROPERTY_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'PROPERTY_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once PROPERTY_CORE_DIR . 'includes/settings.php';
require_once PROPERTY_CORE_DIR . 'includes/post-types.php';
require_once PROPERTY_CORE_DIR . 'includes/taxonomies.php';
require_once PROPERTY_CORE_DIR . 'includes/meta.php';
require_once PROPERTY_CORE_DIR . 'includes/format.php';
require_once PROPERTY_CORE_DIR . 'includes/bindings.php';
require_once PROPERTY_CORE_DIR . 'includes/lifecycle.php';
require_once PROPERTY_CORE_DIR . 'includes/crm/interface-provider.php';
require_once PROPERTY_CORE_DIR . 'includes/crm/class-email-provider.php';
require_once PROPERTY_CORE_DIR . 'includes/crm/class-webhook-provider.php';
require_once PROPERTY_CORE_DIR . 'includes/leads.php';

/**
 * Flush rewrite rules once the post types exist.
 */
function property_core_activate() {
	property_core_register_post_types();
	property_core_register_taxonomies();
	property_core_seed_terms();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'property_core_activate' );

/**
 * Clean up rewrite rules on deactivation. Content is never deleted.
 */
function property_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'property_core_deactivate' );

/**
 * Translations.
 */
function property_core_load_textdomain() {
	load_plugin_textdomain( 'property-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'property_core_load_textdomain', 0 );
