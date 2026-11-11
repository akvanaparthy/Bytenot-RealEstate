<?php
/**
 * Uninstall: the plugin's options and transients go; listings, enquiries and every
 * other record stay, as the plugin promises. Delete them from wp-admin if you mean it.
 *
 * @package bytenot-core
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'bytenot_core_settings' );
delete_option( 'bytenot_core_demo_installed' );
delete_transient( 'bytenot_core_flush' );
