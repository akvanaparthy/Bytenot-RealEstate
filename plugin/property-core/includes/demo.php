<?php
/**
 * Demo content: the prototype's twelve listings, localities, advisors, menu and site
 * settings, so a fresh site looks like the design within a minute. Idempotent: records
 * carry a demo key and are updated, not duplicated. Runs from Properties › Setup or
 * `wp eval-file demo/seed.php`.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The demo listings.
 *
 * @return array<int, array<string, mixed>>
 */
function property_core_demo_listings() {
	$l  = 100000;
	$cr = 10000000;
	return array(
		array( 'key' => 1, 'title' => 'Lakeview Villa', 'type' => 'villa', 'city' => 'Hyderabad', 'area' => 'Gachibowli', 'bhk' => 4, 'sqft' => 3650, 'baths' => 4, 'price' => 2.4 * $cr, 'status' => 'for-sale', 'possession' => 'ready', 'img' => 'house-large.jpg', 'featured' => "Advisor's pick", 'gallery' => array( 'p2.jpg', 'p8.jpg' ) ),
		array( 'key' => 2, 'title' => 'Skyline Residences', 'type' => 'apartment', 'city' => 'Bangalore', 'area' => 'Whitefield', 'bhk' => 3, 'sqft' => 1780, 'baths' => 3, 'price' => 95 * $l, 'status' => 'for-sale', 'possession' => 'under-construction', 'img' => 'p1.jpg', 'featured' => 'Metro in 5 min', 'gallery' => array( 'p6.jpg', 'p9.jpg' ) ),
		array( 'key' => 3, 'title' => 'Courtyard Homes Plot', 'type' => 'plot', 'city' => 'Hyderabad', 'area' => 'Kokapet', 'bhk' => 0, 'sqft' => 2400, 'baths' => 0, 'price' => 1.1 * $cr, 'status' => 'for-sale', 'possession' => 'ready', 'img' => 'p3.jpg', 'featured' => '', 'gallery' => array( 'p5.jpg', 'p7.jpg' ), 'unit' => 'sqyd' ),
		array( 'key' => 4, 'title' => 'Palm Grove Apartment', 'type' => 'apartment', 'city' => 'Hyderabad', 'area' => 'Kondapur', 'bhk' => 2, 'sqft' => 1210, 'baths' => 2, 'price' => 78 * $l, 'status' => 'for-sale', 'possession' => 'ready', 'img' => 'p2.jpg', 'featured' => '', 'gallery' => array( 'p4.jpg' ) ),
		array( 'key' => 5, 'title' => 'Aralia Garden Villa', 'type' => 'villa', 'city' => 'Bangalore', 'area' => 'Sarjapur Road', 'bhk' => 4, 'sqft' => 3100, 'baths' => 4, 'price' => 3.2 * $cr, 'status' => 'for-sale', 'possession' => 'new-launch', 'img' => 'p4.jpg', 'featured' => 'New launch', 'gallery' => array( 'p1.jpg' ) ),
		array( 'key' => 6, 'title' => 'Hebbal Lake Heights', 'type' => 'apartment', 'city' => 'Bangalore', 'area' => 'Hebbal', 'bhk' => 3, 'sqft' => 1650, 'baths' => 3, 'price' => 1.35 * $cr, 'status' => 'for-sale', 'possession' => 'ready', 'img' => 'p5.jpg', 'featured' => '', 'gallery' => array() ),
		array( 'key' => 7, 'title' => 'Jubilee Hills Duplex', 'type' => 'villa', 'city' => 'Hyderabad', 'area' => 'Jubilee Hills', 'bhk' => 4, 'sqft' => 4200, 'baths' => 5, 'price' => 5.5 * $cr, 'status' => 'for-sale', 'possession' => 'resale', 'img' => 'p6.jpg', 'featured' => 'Price revised', 'gallery' => array( 'p9.jpg' ) ),
		array( 'key' => 8, 'title' => 'Tellapur Meadows', 'type' => 'apartment', 'city' => 'Hyderabad', 'area' => 'Tellapur', 'bhk' => 2, 'sqft' => 1150, 'baths' => 2, 'price' => 62 * $l, 'status' => 'for-sale', 'possession' => 'under-construction', 'img' => 'p7.jpg', 'featured' => '', 'gallery' => array() ),
		array( 'key' => 9, 'title' => 'Indiranagar Studio', 'type' => 'apartment', 'city' => 'Bangalore', 'area' => 'Indiranagar', 'bhk' => 1, 'sqft' => 720, 'baths' => 1, 'price' => 48 * $l, 'status' => 'for-sale', 'possession' => 'ready', 'img' => 'p8.jpg', 'featured' => '', 'gallery' => array() ),
		array( 'key' => 10, 'title' => 'Manikonda Residency', 'type' => 'apartment', 'city' => 'Hyderabad', 'area' => 'Manikonda', 'bhk' => 3, 'sqft' => 1580, 'baths' => 3, 'price' => 42000, 'status' => 'for-rent', 'possession' => 'ready', 'img' => 'p9.jpg', 'featured' => '', 'gallery' => array() ),
		array( 'key' => 11, 'title' => 'Electronic City Suites', 'type' => 'apartment', 'city' => 'Bangalore', 'area' => 'Electronic City', 'bhk' => 2, 'sqft' => 1080, 'baths' => 2, 'price' => 28000, 'status' => 'for-rent', 'possession' => 'ready', 'img' => 'p2.jpg', 'featured' => '', 'gallery' => array() ),
		array( 'key' => 12, 'title' => 'Banjara Hills Penthouse', 'type' => 'apartment', 'city' => 'Hyderabad', 'area' => 'Banjara Hills', 'bhk' => 4, 'sqft' => 3900, 'baths' => 4, 'price' => 160000, 'status' => 'for-rent', 'possession' => 'ready', 'img' => 'house-small.jpg', 'featured' => '', 'gallery' => array() ),
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
function property_core_demo_term( $name, $taxonomy, $parent = 0 ) {
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
 * @param string $file File name under demo/images.
 * @param int    $post_id Parent post.
 * @return int Attachment ID.
 */
function property_core_demo_image( $file, $post_id = 0 ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'posts_per_page' => 1, 'meta_key' => '_pc_demo_file', 'meta_value' => $file, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$path = PROPERTY_CORE_DIR . 'demo/images/' . $file;
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
	update_post_meta( $id, '_pc_demo_file', $file );
	return (int) $id;
}

/**
 * Install everything. Safe to run again.
 *
 * @return array<string, int> Counts.
 */
function property_core_install_demo() {
	$counts = array( 'properties' => 0, 'advisors' => 0, 'localities' => 0, 'images' => 0 );
	property_core_seed_terms();

	// site settings
	update_option( 'blogname', 'Lavenor' );
	update_option( 'blogdescription', 'Realty' );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	$settings = wp_parse_args( (array) get_option( 'property_core_settings', array() ), property_core_default_settings() );
	$settings = array_merge( $settings, array( 'phone' => '+91 98765 43210', 'whatsapp' => '919876543210', 'rera_number' => 'A02400001234', 'rera_state' => 'Telangana RERA' ) );
	update_option( 'property_core_settings', $settings );

	// advisors
	$advisors = array();
	foreach ( array( array( 'Priya Menon', 'Senior advisor, Hyderabad West', 't2.jpg' ), array( 'Arjun Rao', 'Senior advisor, Bangalore East', 't1.jpg' ) ) as $a ) {
		$id = property_core_demo_upsert( 'agent', $a[0], 'advisor-' . sanitize_title( $a[0] ), '' );
		update_post_meta( $id, 'pc_role', $a[1] );
		update_post_meta( $id, 'pc_rera_number', 'A02400001234' );
		update_post_meta( $id, 'pc_phone', '+91 98765 43210' );
		$img = property_core_demo_image( $a[2], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
		$advisors[ 'Hyderabad' === substr( $a[1], -14, 9 ) ? 'Hyderabad' : 'Bangalore' ] = $id;
		$counts['advisors']++;
	}

	// listings
	foreach ( property_core_demo_listings() as $p ) {
		$city_id = property_core_demo_term( $p['city'], 'location' );
		$loc_id  = property_core_demo_term( $p['area'], 'location', $city_id );
		$counts['localities']++;
		$rent = 'for-rent' === $p['status'];
		$id = property_core_demo_upsert( 'property', $p['title'], 'property-' . $p['key'], property_core_demo_content( $p ) );
		wp_set_object_terms( $id, $p['type'], 'property_type' );
		wp_set_object_terms( $id, $p['status'], 'listing_status' );
		wp_set_object_terms( $id, $p['possession'], 'possession' );
		wp_set_object_terms( $id, array( $city_id, $loc_id ), 'location' );
		$meta = array(
			'price'             => (int) $p['price'],
			'rent_period'       => $rent ? 'month' : '',
			'bedrooms'          => $p['bhk'],
			'bedroom_label'     => $p['bhk'] ? $p['bhk'] . ' BHK' : '',
			'bathrooms'         => $p['baths'],
			'area_primary'      => $p['sqft'],
			'area_primary_type' => 'plot' === $p['type'] ? 'plot' : 'carpet',
			'area_unit'         => $p['unit'] ?? 'sqft',
			'facing'            => 'East',
			'rera_number'       => 'Hyderabad' === $p['city'] ? 'P0240000' . ( 1000 + $p['key'] * 37 ) : 'PRM/KA/RERA/1251/446/PR/' . ( 100000 + $p['key'] * 911 ),
			'rera_state'        => 'Hyderabad' === $p['city'] ? 'Telangana RERA' : 'Karnataka RERA',
			'rera_url'          => 'Hyderabad' === $p['city'] ? 'https://rera.telangana.gov.in' : 'https://rera.karnataka.gov.in',
			'agent_id'          => $advisors[ $p['city'] ] ?? 0,
			'featured'          => $p['featured'] ? 1 : 0,
			'featured_reason'   => $p['featured'],
			'reference_id'      => ( 'Hyderabad' === $p['city'] ? 'HYD' : 'BLR' ) . '-' . str_pad( (string) $p['key'], 4, '0', STR_PAD_LEFT ),
			'source'            => 'demo',
		);
		foreach ( array_merge( $meta, property_core_demo_extra_meta( $p ) ) as $k => $v ) {
			update_post_meta( $id, 'pc_' . $k, $v );
		}
		$img = property_core_demo_image( $p['img'], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
			$counts['images']++;
		}
		$gallery = array();
		foreach ( property_core_demo_gallery( $p ) as $g ) {
			$gid = property_core_demo_image( $g, $id );
			if ( $gid ) {
				$gallery[] = $gid;
			}
		}
		update_post_meta( $id, 'pc_gallery', $gallery );
		$counts['properties']++;
	}

	property_core_install_demo_site( $advisors );

	update_option( 'property_core_demo_installed', time() );
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
 * @return int Post ID.
 */
function property_core_demo_upsert( $type, $title, $key, $content ) {
	$existing = get_posts( array( 'post_type' => $type, 'posts_per_page' => 1, 'meta_key' => '_pc_demo_key', 'meta_value' => $key, 'fields' => 'ids', 'post_status' => 'any' ) );
	$id = wp_insert_post( array(
		'ID'           => $existing ? (int) $existing[0] : 0,
		'post_type'    => $type,
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_content' => $content,
		'menu_order'   => (int) preg_replace( '/\D/', '', $key ), // featured order follows the demo key
	) );
	update_post_meta( $id, '_pc_demo_key', $key );
	return (int) $id;
}

/**
 * Admin action: Properties › Setup › Install demo content.
 */
function property_core_demo_admin_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'property-core' ) );
	}
	check_admin_referer( 'property_core_install_demo' );
	$counts = property_core_install_demo();
	wp_safe_redirect( add_query_arg( array( 'page' => 'property-core', 'demo' => $counts['properties'] ), admin_url( 'edit.php?post_type=property' ) ) );
	exit;
}
add_action( 'admin_post_property_core_install_demo', 'property_core_demo_admin_action' );

/**
 * The button and the result notice on the Setup page.
 */
function property_core_demo_box() {
	if ( isset( $_GET['demo'] ) && isset( $_GET['page'] ) && 'property-core' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %d: number of properties. */
		echo '<div class="notice notice-success"><p>' . esc_html( sprintf( __( 'Demo content installed: %d properties, advisors, localities, testimonials, FAQs, partners, menus and the home page texts.', 'property-core' ), absint( $_GET['demo'] ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	?>
	<hr>
	<h2><?php esc_html_e( 'Demo content', 'property-core' ); ?></h2>
	<p><?php esc_html_e( 'Installs twelve sample listings with photos, two advisors, the localities, testimonials, FAQs, partner logos, the menus and the home page texts and photos, so the site looks like the design. Running it again updates the same records.', 'property-core' ); ?></p>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="property_core_install_demo">
		<?php wp_nonce_field( 'property_core_install_demo' ); ?>
		<?php submit_button( get_option( 'property_core_demo_installed' ) ? __( 'Reinstall demo content', 'property-core' ) : __( 'Install demo content', 'property-core' ), 'secondary', 'submit', false ); ?>
	</form>
	<?php
}
add_action( 'property_core_settings_after', 'property_core_demo_box' );
