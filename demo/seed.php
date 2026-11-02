<?php
/**
 * Seed demo content from Local's site shell:
 *   wp eval-file C:/Disk/Projs/Bytenot-RealEstate/demo/seed.php
 * Same routine as Properties › Setup › Install demo content.
 */

if ( ! function_exists( 'property_core_install_demo' ) ) {
	WP_CLI::error( 'Activate the Property Core plugin first.' );
}
$counts = property_core_install_demo();
WP_CLI::success( sprintf( 'Demo content installed: %d properties, %d advisors, %d images.', $counts['properties'], $counts['advisors'], $counts['images'] ) );
