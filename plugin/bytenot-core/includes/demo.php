<?php
/**
 * Demo content: a channel partner's catalogue (projects with their apartments),
 * localities, advisors, menus and site texts, so a fresh site looks like the design
 * within a minute. Idempotent: records carry a demo key and are updated, not
 * duplicated; settings and texts the owner already filled in are left alone. Runs from
 * Bytenot › Tools or `wp eval-file demo/seed.php`.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The demo projects.
 *
 * @return array<int, array<string, mixed>>
 */
function bytenot_core_demo_projects() {
	return array(
		array( 'key' => 1, 'title' => 'Rajapushpa Provincia', 'developer' => 'Rajapushpa Properties', 'type' => 'Apartments', 'city' => 'Hyderabad', 'area' => 'Kokapet', 'units' => 620, 'towers' => 6, 'possession' => 'under-construction', 'date' => '2026-12', 'launch' => '2023-10', 'img' => 'house-large.jpg', 'featured' => "Advisor's pick", 'gallery' => array( 'p6.jpg', 'p2.jpg', 'p8.jpg', 'house-small.jpg', 'p4.jpg' ) ),
		array( 'key' => 2, 'title' => 'Aparna Zenon', 'developer' => 'Aparna Constructions', 'type' => 'Apartments', 'city' => 'Hyderabad', 'area' => 'Gachibowli', 'units' => 420, 'towers' => 4, 'possession' => 'new-launch', 'date' => '2028-03', 'launch' => '2025-09', 'img' => 'p1.jpg', 'featured' => 'New launch', 'gallery' => array( 'p9.jpg', 'p5.jpg', 'p3.jpg' ) ),
		array( 'key' => 3, 'title' => 'SMR Vinay Iconia', 'developer' => 'SMR Holdings', 'type' => 'Apartments', 'city' => 'Hyderabad', 'area' => 'Kondapur', 'units' => 280, 'towers' => 3, 'possession' => 'ready', 'date' => '', 'launch' => '2021-02', 'img' => 'p2.jpg', 'featured' => 'Ready to move', 'gallery' => array( 'p4.jpg', 'p7.jpg', 'p1.jpg' ) ),
		array( 'key' => 4, 'title' => 'Jayabheri Orange County', 'developer' => 'Jayabheri', 'type' => 'Villas', 'city' => 'Hyderabad', 'area' => 'Tellapur', 'units' => 120, 'towers' => 0, 'possession' => 'ready', 'date' => '', 'launch' => '2019-06', 'img' => 'p4.jpg', 'featured' => '', 'gallery' => array( 'house-large.jpg', 'p6.jpg', 'p8.jpg' ) ),
		array( 'key' => 5, 'title' => 'Sobha Dream Acres', 'developer' => 'Sobha', 'type' => 'Apartments', 'city' => 'Bangalore', 'area' => 'Whitefield', 'units' => 360, 'towers' => 5, 'possession' => 'ready', 'date' => '', 'launch' => '2020-01', 'img' => 'p5.jpg', 'featured' => 'Metro in 5 min', 'gallery' => array( 'p3.jpg', 'p9.jpg', 'p2.jpg' ) ),
	);
}

/**
 * The demo apartments: units inside the projects above.
 *
 * @return array<int, array<string, mixed>>
 */
function bytenot_core_demo_units() {
	$l  = 100000;
	$cr = 10000000;
	return array(
		array( 'key' => 1, 'project' => 1, 'title' => '3 BHK, Tower B 1204', 'tower' => 'Tower B', 'unit' => '1204', 'floor' => 12, 'bhk' => 3, 'sqft' => 1850, 'baths' => 3, 'price' => 1.85 * $cr, 'facing' => 'East', 'img' => 'p6.jpg' ),
		array( 'key' => 2, 'project' => 1, 'title' => '2 BHK, Tower A 707', 'tower' => 'Tower A', 'unit' => '707', 'floor' => 7, 'bhk' => 2, 'sqft' => 1250, 'baths' => 2, 'price' => 1.2 * $cr, 'facing' => 'North', 'img' => 'p8.jpg' ),
		array( 'key' => 3, 'project' => 1, 'title' => '4 BHK, Tower F 2201', 'tower' => 'Tower F', 'unit' => '2201', 'floor' => 22, 'bhk' => 4, 'sqft' => 2640, 'baths' => 4, 'price' => 2.9 * $cr, 'facing' => 'West', 'img' => 'house-small.jpg' ),
		array( 'key' => 4, 'project' => 2, 'title' => '3 BHK, Tower 2 1502', 'tower' => 'Tower 2', 'unit' => '1502', 'floor' => 15, 'bhk' => 3, 'sqft' => 1980, 'baths' => 3, 'price' => 2.1 * $cr, 'facing' => 'East', 'img' => 'p9.jpg' ),
		array( 'key' => 5, 'project' => 2, 'title' => '2 BHK, Tower 1 903', 'tower' => 'Tower 1', 'unit' => '903', 'floor' => 9, 'bhk' => 2, 'sqft' => 1310, 'baths' => 2, 'price' => 1.35 * $cr, 'facing' => 'South-east', 'img' => 'p3.jpg' ),
		array( 'key' => 6, 'project' => 3, 'title' => '3 BHK, Block C 404', 'tower' => 'Block C', 'unit' => '404', 'floor' => 4, 'bhk' => 3, 'sqft' => 1650, 'baths' => 3, 'price' => 1.42 * $cr, 'facing' => 'East', 'img' => 'p7.jpg' ),
		array( 'key' => 7, 'project' => 3, 'title' => '2 BHK, Block A 1108', 'tower' => 'Block A', 'unit' => '1108', 'floor' => 11, 'bhk' => 2, 'sqft' => 1210, 'baths' => 2, 'price' => 98 * $l, 'facing' => 'North', 'img' => 'p4.jpg' ),
		array( 'key' => 8, 'project' => 4, 'title' => '4 BHK villa, Plot 42', 'tower' => '', 'unit' => 'Plot 42', 'floor' => 0, 'bhk' => 4, 'sqft' => 3650, 'baths' => 4, 'price' => 3.4 * $cr, 'facing' => 'East', 'img' => 'house-large.jpg' ),
		array( 'key' => 9, 'project' => 4, 'title' => '3 BHK villa, Plot 17', 'tower' => '', 'unit' => 'Plot 17', 'floor' => 0, 'bhk' => 3, 'sqft' => 2900, 'baths' => 3, 'price' => 2.7 * $cr, 'facing' => 'West', 'img' => 'p6.jpg' ),
		array( 'key' => 10, 'project' => 5, 'title' => '3 BHK, Wing D 801', 'tower' => 'Wing D', 'unit' => '801', 'floor' => 8, 'bhk' => 3, 'sqft' => 1780, 'baths' => 3, 'price' => 1.65 * $cr, 'facing' => 'East', 'img' => 'p2.jpg' ),
		array( 'key' => 11, 'project' => 5, 'title' => '1 BHK, Wing A 306', 'tower' => 'Wing A', 'unit' => '306', 'floor' => 3, 'bhk' => 1, 'sqft' => 720, 'baths' => 1, 'price' => 62 * $l, 'facing' => 'North', 'img' => 'p9.jpg' ),
		array( 'key' => 12, 'project' => 5, 'title' => '2 BHK, Wing B 1105', 'tower' => 'Wing B', 'unit' => '1105', 'floor' => 11, 'bhk' => 2, 'sqft' => 1180, 'baths' => 2, 'price' => 1.05 * $cr, 'facing' => 'South', 'img' => 'p5.jpg' ),
	);
}

/**
 * Find or create a term, returning its ID.
 *
 * @param string $name     Name.
 * @param string $taxonomy Taxonomy.
 * @param int    $parent   Parent term ID.
 * @return int
 */
function bytenot_core_demo_term( $name, $taxonomy, $parent = 0 ) {
	$slug  = sanitize_title( $name );
	$found = get_term_by( 'slug', $slug, $taxonomy );
	if ( $found ) {
		return (int) $found->term_id;
	}
	$made = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug, 'parent' => $parent ) );
	return is_wp_error( $made ) ? 0 : (int) $made['term_id'];
}

/**
 * Import a demo image once; later calls return the existing attachment.
 *
 * @param string $file    File name under demo/images.
 * @param int    $post_id Parent post.
 * @return int Attachment ID.
 */
function bytenot_core_demo_image( $file, $post_id = 0 ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1, 'meta_key' => '_bn_demo_file', 'meta_value' => $file, 'fields' => 'ids' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $existing ) {
		return (int) $existing[0];
	}
	$path = BYTENOT_CORE_DIR . 'demo/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( basename( $file ) );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => basename( $file ), 'tmp_name' => $tmp ), $post_id );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}
	update_post_meta( $id, '_bn_demo_file', $file );
	return (int) $id;
}

/**
 * Install everything. Safe to run again.
 *
 * @return array<string, int> Counts.
 */
function bytenot_core_install_demo() {
	$counts = array( 'projects' => 0, 'apartments' => 0, 'advisors' => 0, 'images' => 0 );

	// what this site lists: projects and the apartments inside them
	$settings          = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$settings['kinds'] = array(
		'project'   => array( 'on' => 1, 'singular' => '', 'plural' => '', 'base' => 'projects', 'subtypes' => "Apartments\nVillas\nPlots" ),
		'apartment' => array( 'on' => 1, 'singular' => '', 'plural' => '', 'base' => 'apartments', 'subtypes' => '' ),
		'property'  => array( 'on' => 0, 'singular' => '', 'plural' => '', 'base' => 'properties', 'subtypes' => "Land\nHouse\nVilla\nCommercial" ),
	);
	$settings['intents'] = array( 'for-sale' );
	foreach ( array( 'phone' => '+91 98765 43210', 'whatsapp' => '919876543210', 'rera_number' => 'A02400001234', 'rera_state' => 'Telangana RERA' ) as $k => $v ) {
		if ( empty( $settings[ $k ] ) ) {
			$settings[ $k ] = $v; // only where nothing is filled in yet
		}
	}
	update_option( 'bytenot_core_settings', $settings );
	bytenot_core_seed_terms();
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	if ( preg_match( '/wordpress/i', (string) get_option( 'blogname' ) ) || '' === trim( (string) get_option( 'blogname' ) ) ) {
		update_option( 'blogname', 'Lavenor' );
	}
	if ( preg_match( '/another wordpress/i', (string) get_option( 'blogdescription' ) ) ) {
		update_option( 'blogdescription', 'Realty' );
	}

	// advisors
	$advisors = array();
	foreach ( array( array( 'Priya Menon', 'Senior advisor, Hyderabad West', 'Hyderabad', 't2.jpg' ), array( 'Arjun Rao', 'Senior advisor, Bangalore East', 'Bangalore', 't1.jpg' ) ) as $a ) {
		$id = bytenot_core_demo_upsert( 'agent', $a[0], 'advisor-' . sanitize_title( $a[0] ), '' );
		bytenot_core_set( $id, 'role', $a[1] );
		bytenot_core_set( $id, 'rera_number', 'A02400001234' );
		bytenot_core_set( $id, 'phone', '+91 98765 43210' );
		$img = bytenot_core_demo_image( $a[3], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
		$advisors[ $a[2] ] = $id;
		$counts['advisors']++;
	}

	// partners first, so projects can name their developer
	$partners = bytenot_core_install_demo_partners();

	// projects
	$projects = array();
	foreach ( bytenot_core_demo_projects() as $p ) {
		$city_id = bytenot_core_demo_term( $p['city'], 'location' );
		$loc_id  = bytenot_core_demo_term( $p['area'], 'location', $city_id );
		$id      = bytenot_core_demo_upsert( 'listing', $p['title'], 'project-' . $p['key'], bytenot_core_demo_project_content( $p ) );
		wp_set_object_terms( $id, 'project', 'listing_kind' );
		wp_set_object_terms( $id, sanitize_title( $p['type'] ), 'listing_type' );
		wp_set_object_terms( $id, 'for-sale', 'listing_status' );
		wp_set_object_terms( $id, $p['possession'], 'possession' );
		wp_set_object_terms( $id, array( $city_id, $loc_id ), 'location' );
		wp_set_object_terms( $id, bytenot_core_demo_amenities( $p ), 'feature' );
		$meta = array_merge(
			array(
				'developer_id'    => $partners[ $p['developer'] ] ?? 0,
				'developer'       => isset( $partners[ $p['developer'] ] ) ? '' : $p['developer'],
				'units_total'     => $p['units'],
				'towers'          => $p['towers'],
				'launch_date'     => $p['launch'],
				'completion_date' => $p['date'],
				'possession_date' => $p['date'],
				'area_unit'       => 'sqft',
				'rera_number'     => 'Hyderabad' === $p['city'] ? 'P0240000' . ( 1000 + $p['key'] * 37 ) : 'PRM/KA/RERA/1251/446/PR/' . ( 100000 + $p['key'] * 911 ),
				'rera_state'      => 'Hyderabad' === $p['city'] ? 'Telangana RERA' : 'Karnataka RERA',
				'rera_url'        => 'Hyderabad' === $p['city'] ? 'https://rera.telangana.gov.in' : 'https://rera.karnataka.gov.in',
				'agent_id'        => $advisors[ $p['city'] ] ?? 0,
				'featured'        => $p['featured'] ? 1 : 0,
				'featured_reason' => $p['featured'],
				'reference_id'    => ( 'Hyderabad' === $p['city'] ? 'HYD' : 'BLR' ) . '-P' . str_pad( (string) $p['key'], 3, '0', STR_PAD_LEFT ),
				'source'          => 'demo',
			),
			bytenot_core_demo_place_meta( $p['city'], $p['key'] )
		);
		foreach ( $meta as $k => $v ) {
			bytenot_core_set( $id, $k, $v );
		}
		$img = bytenot_core_demo_image( $p['img'], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
			$counts['images']++;
		}
		$gallery = array();
		foreach ( $p['gallery'] as $g ) {
			$gid = bytenot_core_demo_image( $g, $id );
			if ( $gid ) {
				$gallery[] = $gid;
			}
		}
		bytenot_core_set( $id, 'gallery', $gallery );
		$projects[ $p['key'] ] = $id;
		$counts['projects']++;
	}

	// apartments, each inside its project
	foreach ( bytenot_core_demo_units() as $u ) {
		$project = $projects[ $u['project'] ] ?? 0;
		$p       = bytenot_core_demo_projects()[ $u['project'] - 1 ];
		$id      = bytenot_core_demo_upsert( 'listing', $u['title'], 'unit-' . $u['key'], bytenot_core_demo_unit_content( $u, $p ), $project );
		wp_set_object_terms( $id, 'apartment', 'listing_kind' );
		wp_set_object_terms( $id, array(), 'listing_type' );
		wp_set_object_terms( $id, 'for-sale', 'listing_status' );
		wp_set_object_terms( $id, $p['possession'], 'possession' );
		wp_set_object_terms( $id, wp_get_object_terms( $project, 'location', array( 'fields' => 'ids' ) ), 'location' );
		$villa = 'Villas' === $p['type'];
		$meta  = array(
			'price'               => (int) $u['price'],
			'bedrooms'            => $u['bhk'],
			'bedroom_label'       => $u['bhk'] . ' BHK',
			'bathrooms'           => $u['baths'],
			'area_primary'        => $u['sqft'],
			'area_primary_type'   => 'carpet',
			'area_unit'           => 'sqft',
			'area_secondary'      => round( $u['sqft'] * 1.18 / 5 ) * 5,
			'area_secondary_type' => 'super-built-up',
			'tower'               => $u['tower'],
			'unit_no'             => $u['unit'],
			'floor'               => $u['floor'],
			'total_floors'        => $villa ? 2 : 18 + ( $u['key'] % 9 ),
			'facing'              => $u['facing'],
			'parking'             => $u['bhk'] >= 3 ? '2 covered' : '1 covered',
			'furnishing'          => 'Unfurnished',
			'ownership'           => 'Freehold',
			'possession_date'     => $p['date'],
			'agent_id'            => $advisors[ $p['city'] ] ?? 0,
			'featured'            => in_array( $u['key'], array( 1, 4, 10 ), true ) ? 1 : 0,
			'featured_reason'     => array( 1 => 'Corner unit', 4 => 'Lake view', 10 => 'Price revised' )[ $u['key'] ] ?? '',
			'reference_id'        => ( 'Hyderabad' === $p['city'] ? 'HYD' : 'BLR' ) . '-' . str_pad( (string) $u['key'], 4, '0', STR_PAD_LEFT ),
			'source'              => 'demo',
		);
		foreach ( $meta as $k => $v ) {
			bytenot_core_set( $id, $k, $v );
		}
		$img = bytenot_core_demo_image( $u['img'], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
			$counts['images']++;
		}
		$pool = array_values( array_diff( bytenot_core_demo_pool(), array( $u['img'], $p['img'] ) ) );
		$gal  = array();
		foreach ( array_slice( $pool, $u['key'] % 5, 3 ) as $g ) {
			$gid = bytenot_core_demo_image( $g, $id );
			if ( $gid ) {
				$gal[] = $gid;
			}
		}
		bytenot_core_set( $id, 'gallery', $gal );
		$counts['apartments']++;
	}
	foreach ( $projects as $pid ) {
		bytenot_core_rollup( $pid );
	}
	bytenot_core_install_demo_plans( $projects[1] ?? 0 );

	bytenot_core_install_demo_site();

	update_option( 'bytenot_core_demo_installed', time() );
	flush_rewrite_rules();
	return $counts;
}

/**
 * Create or update a demo post by its demo key.
 *
 * @param string $type    Post type.
 * @param string $title   Title.
 * @param string $key     Demo key.
 * @param string $content Block content.
 * @param int    $parent  Parent post.
 * @return int Post ID.
 */
function bytenot_core_demo_upsert( $type, $title, $key, $content, $parent = 0 ) {
	$existing = get_posts( array( 'post_type' => $type, 'posts_per_page' => 1, 'meta_key' => '_bn_demo_key', 'meta_value' => $key, 'fields' => 'ids', 'post_status' => 'any' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$id       = wp_insert_post(
		array(
			'ID'           => $existing ? (int) $existing[0] : 0,
			'post_type'    => $type,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_content' => $content,
			'post_parent'  => (int) $parent,
			'menu_order'   => (int) preg_replace( '/\D/', '', $key ), // featured order follows the demo key
		)
	);
	update_post_meta( $id, '_bn_demo_key', $key );
	return (int) $id;
}

/**
 * Admin action: Bytenot › Tools › Install demo content.
 */
function bytenot_core_demo_admin_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'bytenot-core' ) );
	}
	check_admin_referer( 'bytenot_core_install_demo' );
	$counts = bytenot_core_install_demo();
	/* translators: 1: projects, 2: apartments. */
	$text = sprintf( __( '%1$d projects with %2$d apartments, advisors, localities, testimonials, FAQs, partners, menus and the home page texts.', 'bytenot-core' ), $counts['projects'], $counts['apartments'] );
	wp_safe_redirect( add_query_arg( array( 'page' => 'bytenot-tools', 'bn_msg' => 'demo', 'bn_ok' => 1, 'bn_text' => rawurlencode( $text ) ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_bytenot_core_install_demo', 'bytenot_core_demo_admin_action' );
