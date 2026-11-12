<?php
/**
 * Demo content, part two: the stories and extra facts, floor plans, partner logos,
 * testimonials, FAQs, menus and the theme's home page texts and photos. Called by
 * bytenot_core_install_demo().
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Photo pool for the galleries.
 *
 * @return string[]
 */
function bytenot_core_demo_pool() {
	return array( 'house-large.jpg', 'p1.jpg', 'p2.jpg', 'p3.jpg', 'p4.jpg', 'p5.jpg', 'p6.jpg', 'p7.jpg', 'p8.jpg', 'p9.jpg', 'house-small.jpg' );
}

/**
 * A project's story: three chapters (h3 + paragraph) after a specification paragraph.
 *
 * @param array $p Demo project.
 * @return string Block content.
 */
function bytenot_core_demo_project_content( $p ) {
	$h     = static fn( $t ) => '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $t ) . '</h3><!-- /wp:heading -->';
	$para  = static fn( $t ) => '<!-- wp:paragraph --><p>' . esc_html( $t ) . '</p><!-- /wp:paragraph -->';
	$villa = 'Villas' === $p['type'];
	$word  = $villa ? 'villas' : 'apartments';
	return $para( sprintf( '%s is a %s development by %s in %s, %s: %d %s%s, RERA registered, with the approvals, the title and the bank tie-ups checked by us before it was listed.', $p['title'], strtolower( $p['type'] ), $p['developer'], $p['area'], $p['city'], $p['units'], $word, $p['towers'] ? sprintf( ' across %d towers', $p['towers'] ) : '' ) )
		. $h( 'The project' ) . $para( sprintf( 'Set on a %s site in %s with a clubhouse at its centre, %s keeps %d%% of the land open: lawns, a pool deck, tree-lined walks and a play area away from the roads.', $villa ? '14-acre' : '9-acre', $p['area'], $p['title'], 68 + ( $p['key'] % 4 ) * 3 ) )
		. $h( 'The homes' ) . $para( $villa ? 'Three- and four-bedroom villas on 300 to 500 sq yd plots, each with a private lawn, a two-car porch and a terrace. Double-height living rooms, Italian marble downstairs and engineered oak above.' : 'Two-, three- and four-bedroom apartments with deep balconies, cross ventilation and a utility off every kitchen. Vitrified tile throughout, laminated wardrobes, branded fittings and three lifts to a core.' )
		. $h( 'The neighbourhood' ) . $para( sprintf( '%s sits on the growth side of %s: schools, hospitals and daily shopping within a short drive, the metro and the main tech parks a little further.', $p['area'], $p['city'] ) );
}

/**
 * An apartment's story: a specification paragraph and three chapters.
 *
 * @param array $u Demo unit.
 * @param array $p Its project.
 * @return string Block content.
 */
function bytenot_core_demo_unit_content( $u, $p ) {
	$h     = static fn( $t ) => '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $t ) . '</h3><!-- /wp:heading -->';
	$para  = static fn( $t ) => '<!-- wp:paragraph --><p>' . esc_html( $t ) . '</p><!-- /wp:paragraph -->';
	$villa = 'Villas' === $p['type'];
	return $para( sprintf( 'A %d BHK %s in %s, %s, %s sq ft of carpet area, %s facing. Documents are verified against the project\'s RERA record; visits can be arranged within a day.', $u['bhk'], $villa ? 'villa' : 'apartment', $p['title'], $p['area'], number_format( $u['sqft'] ), strtolower( $u['facing'] ) ) )
		. $h( 'The home' ) . $para( $villa ? sprintf( 'On %s: living and dining rooms downstairs with a modular kitchen, a guest bedroom and the porch; the bedrooms, a family lounge and the terrace above.', $u['unit'] ) : sprintf( 'On the %s floor of %s, with the living room and two balconies facing %s. Well-proportioned rooms, good light through the day and a utility off the kitchen.', bytenot_core_demo_ordinal( $u['floor'] ), $u['tower'], strtolower( $u['facing'] ) ) )
		. $h( 'Inside' ) . $para( sprintf( '%d bathrooms, a modular kitchen and %s. Finishes are vitrified tile, laminated wardrobes and branded fittings throughout.', $u['baths'], $villa ? 'a private lawn' : 'a generous balcony' ) )
		. $h( 'Around it' ) . $para( sprintf( 'The clubhouse, pool and gym of %s are a short walk away, and %s puts schools, hospitals and daily shopping within a short drive.', $p['title'], $p['area'] ) );
}

/**
 * 1st, 2nd, 3rd, 12th.
 *
 * @param int $n Number.
 * @return string
 */
function bytenot_core_demo_ordinal( $n ) {
	$n = (int) $n;
	if ( $n % 100 >= 11 && $n % 100 <= 13 ) {
		return $n . 'th';
	}
	return $n . ( array( 'th', 'st', 'nd', 'rd' )[ $n % 10 ] ?? 'th' );
}

/**
 * Place facts shared by a project and inherited by its apartments: landmarks,
 * coordinates, approvals, banks.
 *
 * @param string $city City.
 * @param int    $seed Varies the numbers.
 * @return array<string, mixed> Meta key (without prefix) => value.
 */
function bytenot_core_demo_place_meta( $city, $seed ) {
	$hyd       = 'Hyderabad' === $city;
	$landmarks = $hyd
		? array(
			array( 'name' => 'Raidurg Metro', 'type' => 'metro', 'distance' => 2.1 + $seed * 0.4, 'unit' => 'km', 'time' => ( 6 + $seed ) . ' min' ),
			array( 'name' => 'Financial District', 'type' => 'work', 'distance' => 4.5, 'unit' => 'km', 'time' => '10 min' ),
			array( 'name' => 'Oakridge International School', 'type' => 'school', 'distance' => 3.2, 'unit' => 'km', 'time' => '8 min' ),
			array( 'name' => 'Continental Hospitals', 'type' => 'hospital', 'distance' => 3.8, 'unit' => 'km', 'time' => '9 min' ),
			array( 'name' => 'Inorbit Mall', 'type' => 'shopping', 'distance' => 5.6, 'unit' => 'km', 'time' => '14 min' ),
			array( 'name' => 'RGIA Airport', 'type' => 'airport', 'distance' => 31, 'unit' => 'km', 'time' => '40 min' ),
		)
		: array(
			array( 'name' => 'Whitefield Metro', 'type' => 'metro', 'distance' => 1.8, 'unit' => 'km', 'time' => '5 min' ),
			array( 'name' => 'ITPL', 'type' => 'work', 'distance' => 3.9, 'unit' => 'km', 'time' => '12 min' ),
			array( 'name' => 'International school', 'type' => 'school', 'distance' => 2.7, 'unit' => 'km', 'time' => '8 min' ),
			array( 'name' => 'Multi-speciality hospital', 'type' => 'hospital', 'distance' => 3.1, 'unit' => 'km', 'time' => '9 min' ),
			array( 'name' => 'Phoenix Marketcity', 'type' => 'shopping', 'distance' => 4.4, 'unit' => 'km', 'time' => '13 min' ),
			array( 'name' => 'Kempegowda Airport', 'type' => 'airport', 'distance' => 38, 'unit' => 'km', 'time' => '55 min' ),
		);
	return array(
		'landmarks' => $landmarks,
		'lat'       => ( $hyd ? 17.40 : 12.97 ) + ( $seed % 7 ) * 0.012,
		'lng'       => ( $hyd ? 78.33 : 77.72 ) + ( $seed % 5 ) * 0.009,
		'banks'     => implode( ', ', array_slice( array( 'SBI', 'HDFC', 'ICICI', 'Axis', 'Kotak' ), 0, 3 + ( $seed % 3 ) ) ),
		'approvals' => "Clear title, EC verified\nApproved plan\n" . ( $seed % 2 ? 'Occupancy certificate received' : 'Construction on schedule' ),
	);
}

/**
 * Amenity terms per project.
 *
 * @param array $p Demo project.
 * @return string[]
 */
function bytenot_core_demo_amenities( $p ) {
	$all = array( 'Clubhouse', 'Swimming pool', 'Gym', "Children's play area", '24x7 security', 'Landscaped gardens', 'Jogging track', 'Indoor games', 'Rainwater harvesting', 'Visitor parking', 'Lift', 'Power backup' );
	return array_slice( $all, 0, 8 + ( $p['key'] % 5 ) );
}

/**
 * Floor plans on one project: the drawings with the room schedule in the attachment description.
 *
 * @param int $project_id Project listing ID.
 */
function bytenot_core_install_demo_plans( $project_id ) {
	if ( ! $project_id ) {
		return;
	}
	$plans = array(
		array( 'plan-ground.png', '3 BHK, lower level', "1.1 Living | 18' × 14'\n1.2 Dining | 14' × 11'\n1.3 Kitchen | 12' × 9'\n1.4 Bedroom 1 | 14' × 12'\n1.5 Bath | 8' × 6'\n1.6 Utility | 9' × 6'\n1.7 Balcony | 6' deep" ),
		array( 'plan-first.png', '3 BHK, upper level', "2.1 Master bedroom | 18' × 14'\n2.2 Bedroom 2 | 14' × 12'\n2.3 Bedroom 3 | 13' × 12'\n2.4 Family lounge | 20' × 14'\n2.5 Bath, master | 9' × 7'\n2.6 Bath, shared | 8' × 6'\n2.7 Terrace | 420 sq ft" ),
	);
	$ids   = array();
	foreach ( $plans as $pl ) {
		$pid = bytenot_core_demo_image( $pl[0], $project_id );
		if ( $pid ) {
			wp_update_post( array( 'ID' => $pid, 'post_title' => $pl[1], 'post_content' => $pl[2] ) );
			$ids[] = $pid;
		}
	}
	bytenot_core_set( $project_id, 'floor_plans', $ids );
}

/**
 * Partner logos (SVG allowed for the duration of the import).
 *
 * @return array<string, int> Partner name => post ID.
 */
function bytenot_core_install_demo_partners() {
	$svg_mimes = static fn( $m ) => $m + array( 'svg' => 'image/svg+xml' );
	$svg_check = static fn( $data, $file, $filename ) => preg_match( '/\.svg$/i', (string) $filename ) ? array( 'ext' => 'svg', 'type' => 'image/svg+xml', 'proper_filename' => false ) : $data;
	add_filter( 'upload_mimes', $svg_mimes );
	add_filter( 'wp_check_filetype_and_ext', $svg_check, 10, 3 );
	$partners = array(
		array( 'Aparna Constructions', 'partners/aparna.png', 'tall' ),
		array( 'Jayabheri', 'partners/jayabheri.png', 'wide' ),
		array( 'SMR Holdings', 'partners/smr.png', '' ),
		array( 'My Home Group', 'partners/myhome.svg', 'tall' ),
		array( 'Prestige Group', 'partners/prestige.svg', 'tall' ),
		array( 'Brigade Group', 'partners/brigade.png', 'mark' ),
		array( 'Sobha', 'partners/sobha.png', 'wide' ),
		array( 'Rajapushpa Properties', 'partners/rajapushpa.png', 'tall' ),
		array( 'Ramky Estates', 'partners/ramky.svg', 'tall' ),
	);
	$ids      = array();
	foreach ( $partners as $i => $pt ) {
		$id = bytenot_core_demo_upsert( 'partner', $pt[0], 'partner-' . ( $i + 1 ), '' );
		if ( $pt[2] ) {
			update_post_meta( $id, 'bn_shape', $pt[2] );
		} else {
			delete_post_meta( $id, 'bn_shape' );
		}
		$img = bytenot_core_demo_image( $pt[1], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
		$ids[ $pt[0] ] = $id;
	}
	// the site logo: the horizontal lockup, and its light version for the transparent header
	$logo = bytenot_core_demo_image( 'logo.svg' );
	if ( $logo ) {
		set_theme_mod( 'custom_logo', $logo );
	}
	$light = bytenot_core_demo_image( 'logo-light.svg' );
	if ( $light ) {
		set_theme_mod( 'bytenot_header_logo_light', $light );
	}
	// the site icon (favicon, home-screen icon and the faint motif on dark panels), with the sizes WordPress expects
	if ( ! get_option( 'site_icon' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-site-icon.php';
		$sizes = array( new WP_Site_Icon(), 'additional_sizes' );
		add_filter( 'intermediate_image_sizes_advanced', $sizes );
		$icon = bytenot_core_demo_image( 'favicon.png' );
		remove_filter( 'intermediate_image_sizes_advanced', $sizes );
		if ( $icon ) {
			update_post_meta( $icon, '_wp_attachment_context', 'site-icon' );
			update_option( 'site_icon', $icon );
		}
	}
	// the sharing image, if none is set
	$settings = (array) get_option( 'bytenot_core_settings', array() );
	if ( empty( $settings['og_image'] ) ) {
		$og = bytenot_core_demo_image( 'og.png' );
		if ( $og ) {
			$settings['og_image'] = $og;
			update_option( 'bytenot_core_settings', $settings );
		}
	}
	remove_filter( 'upload_mimes', $svg_mimes );
	remove_filter( 'wp_check_filetype_and_ext', $svg_check, 10 );
	return $ids;
}

/**
 * Testimonials, FAQs, menus, home page texts and photos.
 */
function bytenot_core_install_demo_site() {
	// testimonials
	$quotes = array(
		array( 'Priya & Arjun Reddy', 'Bought a 3 BHK in Gachibowli, Hyderabad', 'Lavenor kept us informed at every step, from the first site visit to registration. Their calm, honest approach made a stressful decision feel easy.', 't1.jpg' ),
		array( 'Sneha Iyer', 'Bought a 2 BHK in Whitefield, Bangalore', 'As a first-time buyer I had a hundred questions. The team answered every one patiently and negotiated a better price than I expected.', 't2.jpg' ),
		array( 'Karthik Menon', 'Booked a villa at Tellapur', 'I wanted a long-term investment, not just a house. Lavenor walked me through the approvals, the numbers and the risks, and never once pushed.', 't3.jpg' ),
		array( 'Rahul & Meera Sharma', 'Relocated to Hyderabad, bought in Kondapur', 'We bought our home from Pune without a single wasted trip. Video walkthroughs, paperwork handled, keys ready the day we landed.', 't4.jpg' ),
	);
	foreach ( $quotes as $i => $q ) {
		$id = bytenot_core_demo_upsert( 'testimonial', $q[0], 'testimonial-' . ( $i + 1 ), '<!-- wp:paragraph --><p>' . esc_html( $q[2] ) . '</p><!-- /wp:paragraph -->' );
		update_post_meta( $id, 'bn_role', $q[1] );
		$img = bytenot_core_demo_image( $q[3], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
	}

	// FAQs
	$faqs = array(
		array( 'How do I find the right project?', 'Start with the filters on the projects page: location, budget and configuration. Every project there is verified by us, and every apartment inside it carries its own floor, facing and price. If you would rather talk it through, an advisor will shortlist against what you actually need and set up visits in one trip.' ),
		array( 'How does a site visit work?', 'Pick a date on any listing or ask us here. Your advisor confirms within the hour, meets you at the gate and walks you through the home, the project and the neighbourhood. Visits are free and you can see several homes in one morning.' ),
		array( "What's included in a listing?", 'Photos, price with the per sq ft rate, carpet and built-up area, floor plan, amenities, the location with nearby landmarks, the RERA registration and the documents we have checked. If something is missing, it is because we could not verify it.' ),
		array( 'How do I check a project’s RERA registration?', 'Every project shows its RERA number with a link to the state portal, where you can see the promoter, approvals, timelines and complaints. We check this before a project goes live and again before you sign anything.' ),
		array( 'What are the costs beyond the price?', 'Stamp duty and registration, about 6 percent in Telangana and 5 to 6 percent in Karnataka. GST applies only to under-construction homes. Add society maintenance, a one-time corpus fund and legal charges. The numbers section on each listing shows an all-in estimate.' ),
		array( 'Can you help with a home loan?', 'Yes. Each project shows the banks that have pre-approved it. We coordinate the application, the valuation and the disbursement schedule with the bank so the paperwork moves while you focus on the move.' ),
	);
	foreach ( $faqs as $i => $f ) {
		bytenot_core_demo_upsert( 'faq', $f[0], 'faq-' . ( $i + 1 ), '<!-- wp:paragraph --><p>' . esc_html( $f[1] ) . '</p><!-- /wp:paragraph -->' );
	}

	// classic menus for the theme's locations: only menus the demo created are ever rewritten
	$home     = home_url( '/' );
	$projects = bytenot_core_archive_url( 'project' );
	$menus    = array(
		'primary'      => array( 'Primary', array( array( 'Home', $home ), array( 'About Us', $home . '#about' ), array( 'Projects', $projects ), array( 'Contact Us', $home . '#inquiry' ) ) ),
		'footer'       => array( 'Footer', array( array( 'Home', $home ), array( 'About Us', $home . '#about' ), array( 'Projects', $projects ), array( 'Contact Us', $home . '#inquiry' ) ) ),
		'footer-types' => array( 'Browse', array( array( 'Apartments', add_query_arg( 'type', 'apartments', $projects ) ), array( 'Villas', add_query_arg( 'type', 'villas', $projects ) ), array( 'Hyderabad', add_query_arg( 'location', 'hyderabad', $projects ) ), array( 'Bangalore', add_query_arg( 'location', 'bangalore', $projects ) ) ) ),
		'legal'        => array( 'Legal', array( array( 'Terms & Conditions', $home ), array( 'Privacy policy', get_privacy_policy_url() ?: $home ) ) ),
	);
	$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $m ) {
		$menu    = wp_get_nav_menu_object( $m[0] );
		$menu_id = $menu ? (int) $menu->term_id : 0;
		if ( $menu_id && ! get_term_meta( $menu_id, '_bn_demo', true ) ) {
			continue; // somebody's own menu: never touched
		}
		if ( ! $menu_id ) {
			$menu_id = (int) wp_create_nav_menu( $m[0] );
			if ( ! $menu_id || is_wp_error( $menu_id ) ) {
				continue;
			}
			update_term_meta( $menu_id, '_bn_demo', 1 );
		}
		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $old ) {
			wp_delete_post( $old->ID, true );
		}
		foreach ( $m[1] as $i => $item ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $item[0], 'menu-item-url' => $item[1], 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-position' => $i + 1 ) );
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	// the theme's texts and photos (Customize › Bytenot Estate); texts already changed by the owner are kept
	$img  = static fn( $file ) => bytenot_core_demo_image( $file );
	$mods = array(
		'hero_image'             => $img( 'hero.jpg' ),
		'hero_title'             => 'Home with Honor, <br>Wealth with Vision.',
		'hero_lede'              => "Lavenor Realty finds you a home you'll feel lavish in and honored to own, and investments that grow with you. Hyderabad, Bangalore and beyond.",
		'hero_tags'              => 0,
		'hero_intents'           => 0,
		'about_statement'        => "Redefining the real estate experience through trust, transparency and integrity.\n\nWe are committed to helping customers {pill} make confident property decisions through honest guidance, carefully selected opportunities and clear market insights. {pill} Our approach is built on understanding individual needs, providing fair market value and offering complete information without bias or pressure.\n\nWe believe in putting customers first, building lasting relationships {pill} and ensuring every property decision is backed by transparency, trust and genuine value.",
		'about_pill_1'           => $img( 't2.jpg' ),
		'about_pill_2'           => $img( 'p3.jpg' ),
		'about_pill_3'           => $img( 'p7.jpg' ),
		'about_float_1'          => $img( 'p1.jpg' ),
		'about_float_2'          => $img( 'p5.jpg' ),
		'about_float_3'          => $img( 'p8.jpg' ),
		'featured_eyebrow'       => 'Featured this week',
		'featured_title'         => 'Hand-picked projects, <br><em>ready to visit.</em>',
		'featured_video_thumb'   => $img( 'thumb-1.jpg' ),
		'featured_video_caption' => 'How we pick: every project here was walked through by a Lavenor advisor before it was listed.',
		'testimonials_lede'      => 'Honest advice, clear paperwork and a home that feels right. Here is what buyers and investors across Hyderabad and Bangalore say about working with Lavenor.',
		'inquiry_image'          => $img( 'p7.jpg' ),
		'listing_pill'           => $img( 'house-small.jpg' ),
		'listing_title_1'        => 'Find the project',
		'listing_title_2'        => '<em>that feels</em> like home.',
		'listing_lede'           => "Welcome to Lavenor's full collection of verified projects across Hyderabad and Bangalore, with every apartment inside them priced transparently and ready to view. Use the filters to narrow by location, budget or configuration, and book a site visit in a few clicks.",
		'footer_cta_lede'        => "Tell us what you're looking for. A Lavenor advisor will shortlist homes, arrange visits and handle the paperwork.",
		'footer_slogan'          => 'Home with Honor',
		'footer_image'           => $img( 'footer.jpg' ),
	);
	foreach ( $mods as $k => $v ) {
		set_theme_mod( 'bytenot_' . $k, $v );
	}
}
