<?php
/**
 * Demo content, part two: the listing stories and extra facts, testimonials, FAQs,
 * partner logos, menus and the theme's home page texts and photos. Called by
 * bytenot_core_install_demo().
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Photo pool for the listing galleries.
 *
 * @return string[]
 */
function bytenot_core_demo_pool() {
	return array( 'house-large.jpg', 'p1.jpg', 'p2.jpg', 'p3.jpg', 'p4.jpg', 'p5.jpg', 'p6.jpg', 'p7.jpg', 'p8.jpg', 'p9.jpg', 'house-small.jpg' );
}

/**
 * Gallery files for a demo listing (the featured image is added by the caller).
 *
 * @param array $p Demo listing.
 * @return string[]
 */
function bytenot_core_demo_gallery( $p ) {
	if ( 1 === $p['key'] ) {
		return array( 'p6.jpg', 'p2.jpg', 'p8.jpg', 'house-small.jpg', 'p4.jpg', 'p9.jpg', 'p5.jpg' );
	}
	$pool = array_values( array_diff( bytenot_core_demo_pool(), array( $p['img'] ) ) );
	return array_slice( $pool, $p['key'] % 5, 5 );
}

/**
 * The story: three chapters (h3 + paragraph) and a closing paragraph.
 *
 * @param array $p Demo listing.
 * @return string Block content.
 */
function bytenot_core_demo_content( $p ) {
	$h    = static fn( $t ) => '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $t ) . '</h3><!-- /wp:heading -->';
	$para = static fn( $t ) => '<!-- wp:paragraph --><p>' . esc_html( $t ) . '</p><!-- /wp:paragraph -->';
	$n    = static fn( $v ) => number_format( $v );
	$plot = 'plot' === $p['type'];
	$word = $plot ? 'plot' : ( 'villa' === $p['type'] ? 'villa' : 'apartment' );
	$rent = 'for-rent' === $p['status'];
	// the paragraphs before the first heading are the specification prose; each heading opens a chapter
	if ( 1 === $p['key'] ) {
		return $para( 'Lakeview Villa sits at the quiet end of a 42-home gated community in Gachibowli. Downstairs holds the living and dining rooms, a modular kitchen with a separate utility, a guest bedroom and a two-car porch. Upstairs are three bedrooms, a family lounge and the terrace.' )
			. $para( 'The home is brand new and never occupied, with the occupancy certificate in hand and immediate possession.' )
			. $h( 'The lawn' ) . $para( 'Eleven hundred square feet of lawn runs from the porch to the community green, with the lake behind it. The house faces east, so mornings arrive in the living room and evenings settle on the terrace above.' )
			. $h( 'The light' ) . $para( 'A double-height living room, floor-to-ceiling glazing on the east wall, and a courtyard that pulls morning sun through to the dining table. Italian marble below, engineered oak above, teak at every door and window.' )
			. $h( 'Upstairs' ) . $para( 'Three bedrooms and a family lounge. The master suite takes the lake view with a walk-in wardrobe and a balcony, and the 420 sq ft terrace is laid out for evenings with people over.' );
	}
	if ( $plot ) {
		return $para( 'Documents are verified: title, approvals and tax receipts are available on request, and the plot is ready to register.' )
			. $h( 'The land' ) . $para( sprintf( 'A %s sq yd plot in an approved, gated layout in %s. Roads, drainage, water and power lines are already in, and the layout holds clear title with encumbrance verified for 30 years.', $n( $p['sqft'] ), $p['area'] ) )
			. $h( 'The layout' ) . $para( 'Forty-foot internal roads, a boundary wall, street lighting and a small park at the centre. The layout allows ground plus two floors when you are ready to build.' )
			. $h( 'The neighbourhood' ) . $para( sprintf( '%s is seeing steady appreciation on the back of new road and metro links, with schools and hospitals already within a short drive.', $p['area'] ) );
	}
	return $para( $rent ? 'Maintained by the owner, close to work and transit, with society amenities included in the rent. Available on an 11-month lease with a two-month deposit.' : 'Documents are verified: title, approvals and tax receipts are available on request. Visits are free and can be arranged within a day.' )
		. $h( 'The home' ) . $para( sprintf( '%s is a %d BHK %s in %s, %s, with %s sq ft of carpet area across well-proportioned rooms and good cross ventilation.', $p['title'], $p['bhk'], $word, $p['area'], $p['city'], $n( $p['sqft'] ) ) )
		. $h( 'Inside' ) . $para( sprintf( '%d bathrooms, a modular kitchen with a separate utility and a %s. Finishes are vitrified tile, laminated wardrobes and branded fittings throughout.', $p['baths'], 'villa' === $p['type'] ? 'private garden' : 'generous balcony' ) )
		. $h( 'Around it' ) . $para( sprintf( 'The community has a clubhouse, pool and gym, and %s puts schools, hospitals and daily shopping within a short drive.', $p['area'] ) );
}

/**
 * Extra facts per demo listing: floors, parking, furnishing, landmarks, coordinates,
 * approvals, banks, amenities.
 *
 * @param array $p Demo listing.
 * @return array<string, mixed> Meta key (without prefix) => value.
 */
function bytenot_core_demo_extra_meta( $p ) {
	$seed = $p['key'];
	$plot = 'plot' === $p['type'];
	$rent = 'for-rent' === $p['status'];
	$hyd  = 'Hyderabad' === $p['city'];
	$landmarks = $hyd
		? array(
			array( 'name' => 'Raidurg Metro', 'type' => 'metro', 'distance' => 2.1, 'unit' => 'km', 'time' => '6 min' ),
			array( 'name' => 'Financial District', 'type' => 'work', 'distance' => 4.5, 'unit' => 'km', 'time' => '10 min' ),
			array( 'name' => 'Oakridge International School', 'type' => 'school', 'distance' => 3.2, 'unit' => 'km', 'time' => '8 min' ),
			array( 'name' => 'Continental Hospitals', 'type' => 'hospital', 'distance' => 3.8, 'unit' => 'km', 'time' => '9 min' ),
			array( 'name' => 'Inorbit Mall', 'type' => 'shopping', 'distance' => 5.6, 'unit' => 'km', 'time' => '14 min' ),
			array( 'name' => 'RGIA Airport', 'type' => 'airport', 'distance' => 31, 'unit' => 'km', 'time' => '40 min' ),
		)
		: array(
			array( 'name' => 'Nearest Metro station', 'type' => 'metro', 'distance' => 1.8, 'unit' => 'km', 'time' => '5 min' ),
			array( 'name' => 'Tech park cluster', 'type' => 'work', 'distance' => 3.9, 'unit' => 'km', 'time' => '12 min' ),
			array( 'name' => 'International school', 'type' => 'school', 'distance' => 2.7, 'unit' => 'km', 'time' => '8 min' ),
			array( 'name' => 'Multi-speciality hospital', 'type' => 'hospital', 'distance' => 3.1, 'unit' => 'km', 'time' => '9 min' ),
			array( 'name' => 'Neighbourhood mall', 'type' => 'shopping', 'distance' => 4.4, 'unit' => 'km', 'time' => '13 min' ),
			array( 'name' => 'Kempegowda Airport', 'type' => 'airport', 'distance' => 38, 'unit' => 'km', 'time' => '55 min' ),
		);
	$meta = array(
		'facing'     => $plot ? array( 'East', 'North', 'West' )[ $seed % 3 ] : array( 'East', 'North', 'West', 'South-east' )[ $seed % 4 ],
		'landmarks'  => $landmarks,
		'lat'        => ( $hyd ? 17.44 : 12.97 ) + ( $seed % 7 ) * 0.004,
		'lng'        => ( $hyd ? 78.35 : 77.64 ) + ( $seed % 5 ) * 0.005,
		'banks'      => implode( ', ', array_slice( array( 'SBI', 'HDFC', 'ICICI', 'Axis', 'Kotak' ), 0, 3 + ( $seed % 3 ) ) ),
		'approvals'  => $plot ? "Approved layout\nClear title, EC verified\nReady to register" : "Clear title, EC verified\nApproved plan\n" . ( 'ready' === $p['possession'] ? 'Occupancy certificate received' : 'Construction on schedule' ),
		'ownership'  => 'Freehold',
	);
	if ( ! $plot ) {
		$meta['total_floors']        = 'villa' === $p['type'] ? 2 : 12 + ( $seed % 10 );
		$meta['floor']               = 'villa' === $p['type'] ? 0 : 3 + ( $seed % 12 );
		$meta['year_built']          = 2019 + ( $seed % 7 );
		$meta['parking']             = $p['bhk'] >= 3 ? '2 covered' : '1 covered';
		$meta['furnishing']          = array( 'Unfurnished', 'Semi-furnished', 'Fully furnished' )[ $seed % 3 ];
		$meta['area_secondary']      = round( $p['sqft'] * 1.1 / 5 ) * 5;
		$meta['area_secondary_type'] = 'built-up';
	}
	$dates = array( 2 => '2026-12', 8 => '2027-06', 11 => '2026-12-01' );
	if ( isset( $dates[ $seed ] ) ) {
		$meta['possession_date'] = $dates[ $seed ];
	}
	if ( $rent ) {
		$meta['deposit']     = (int) $p['price'] * 2;
		$meta['maintenance'] = (int) round( $p['sqft'] * 3.5 );
	}
	if ( 1 === $p['key'] ) {
		$meta = array_merge( $meta, array( 'facing' => 'East', 'year_built' => 2025, 'parking' => '2 covered + 1 open', 'furnishing' => 'Semi-furnished, modular kitchen', 'area_secondary' => 4015, 'approvals' => "Occupancy certificate received\nHMDA approved layout\nClear title, EC verified for 30 years\nVastu compliant", 'banks' => 'SBI, HDFC, ICICI, Axis', 'lat' => 17.4401, 'lng' => 78.3489, 'rera_number' => 'P02400001234' ) );
	}
	return $meta;
}

/**
 * Amenity terms per listing.
 *
 * @param array $p Demo listing.
 * @return string[]
 */
function bytenot_core_demo_amenities( $p ) {
	if ( 'plot' === $p['type'] ) {
		return array( '24x7 security', 'Landscaped gardens', 'Jogging track', 'Rainwater harvesting', 'Street lighting', 'Underground drainage', "Children's play area", 'Clubhouse' );
	}
	$all = array( 'Clubhouse', 'Swimming pool', 'Gym', "Children's play area", '24x7 security', 'Landscaped gardens', 'Jogging track', 'Indoor games', 'Rainwater harvesting', 'Visitor parking', 'Lift', 'Power backup' );
	return array_slice( $all, 0, 8 + ( $p['key'] % 5 ) );
}

/**
 * Testimonials, FAQs, partners, menus, home page texts and photos.
 *
 * @param array<string, int> $advisors City => agent ID.
 */
function bytenot_core_install_demo_site( $advisors ) {
	// testimonials
	$quotes = array(
		array( 'Priya & Arjun Reddy', 'Bought a 3 BHK in Gachibowli, Hyderabad', 'Lavenor kept us informed at every step, from the first site visit to registration. Their calm, honest approach made a stressful decision feel easy.', 't1.jpg' ),
		array( 'Sneha Iyer', 'Bought a 2 BHK in Whitefield, Bangalore', 'As a first-time buyer I had a hundred questions. The team answered every one patiently and negotiated a better price than I expected.', 't2.jpg' ),
		array( 'Karthik Menon', 'Invested in a villa plot at Kokapet', 'I wanted a long-term investment, not just a house. Lavenor walked me through the approvals, the numbers and the risks, and never once pushed.', 't3.jpg' ),
		array( 'Rahul & Meera Sharma', 'Relocated to Hyderabad, bought in Kondapur', 'We bought our home from Pune without a single wasted trip. Video walkthroughs, paperwork handled, keys ready the day we landed.', 't4.jpg' ),
	);
	foreach ( $quotes as $i => $q ) {
		$id = bytenot_core_demo_upsert( 'testimonial', $q[0], 'testimonial-' . ( $i + 1 ), '<!-- wp:paragraph --><p>' . esc_html( $q[2] ) . '</p><!-- /wp:paragraph -->' );
		update_post_meta( $id, 'pc_role', $q[1] );
		$img = bytenot_core_demo_image( $q[3], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
	}

	// FAQs
	$faqs = array(
		array( 'How do I find the right property?', 'Start with the filters on the projects page: type, location, budget and configuration. Every listing there is verified by us. If you would rather talk it through, an advisor will shortlist homes against what you actually need and set up visits in one trip.' ),
		array( 'How does a site visit work?', 'Pick a date on any listing or ask us here. Your advisor confirms within the hour, meets you at the gate and walks you through the home, the project and the neighbourhood. Visits are free and you can see several homes in one morning.' ),
		array( "What's included in a listing?", 'Photos, price with the per sq ft rate, carpet and built-up area, floor plan, amenities, the location with nearby landmarks, the RERA registration and the documents we have checked. If something is missing, it is because we could not verify it.' ),
		array( 'How do I check a project’s RERA registration?', 'Every listing shows its RERA number with a link to the state portal, where you can see the promoter, approvals, timelines and complaints. We check this before a home goes live and again before you sign anything.' ),
		array( 'What are the costs beyond the price?', 'Stamp duty and registration, about 6 percent in Telangana and 5 to 6 percent in Karnataka. GST applies only to under-construction homes. Add society maintenance, a one-time corpus fund and legal charges. The numbers section on each listing shows an all-in estimate.' ),
		array( 'Can Lavenor help with a home loan?', 'Yes. Each listing shows the banks that have pre-approved the project. We coordinate the application, the valuation and the disbursement schedule with the bank so the paperwork moves while you focus on the move.' ),
	);
	foreach ( $faqs as $i => $f ) {
		bytenot_core_demo_upsert( 'faq', $f[0], 'faq-' . ( $i + 1 ), '<!-- wp:paragraph --><p>' . esc_html( $f[1] ) . '</p><!-- /wp:paragraph -->' );
	}

	// partners (SVG logos are allowed for the duration of the import)
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
	foreach ( $partners as $i => $pt ) {
		$id = bytenot_core_demo_upsert( 'partner', $pt[0], 'partner-' . ( $i + 1 ), '' );
		update_post_meta( $id, 'pc_shape', $pt[2] );
		$img = bytenot_core_demo_image( $pt[1], $id );
		if ( $img ) {
			set_post_thumbnail( $id, $img );
		}
	}
	remove_filter( 'upload_mimes', $svg_mimes );
	remove_filter( 'wp_check_filetype_and_ext', $svg_check, 10 );

	// amenities and the extra facts on each listing
	foreach ( bytenot_core_demo_listings() as $p ) {
		$ids = get_posts( array( 'post_type' => 'property', 'posts_per_page' => 1, 'meta_key' => '_pc_demo_key', 'meta_value' => 'property-' . $p['key'], 'fields' => 'ids', 'post_status' => 'any' ) );
		if ( ! $ids ) {
			continue;
		}
		wp_set_object_terms( (int) $ids[0], bytenot_core_demo_amenities( $p ), 'feature' );
		if ( 1 === $p['key'] ) {
			// floor plans: the drawings with the room schedule in the attachment description
			$plans = array(
				array( 'plan-ground.png', 'Ground floor', "1.1 Living | 18' × 14'\n1.2 Dining | 14' × 11'\n1.3 Kitchen | 12' × 9'\n1.4 Bedroom 1 | 14' × 12'\n1.5 Bath | 8' × 6'\n1.6 Utility | 9' × 6'\n1.7 Car porch | 2 cars\n1.8 Private lawn | 1,100 sq ft" ),
				array( 'plan-first.png', 'First floor', "2.1 Master bedroom | 18' × 14'\n2.2 Bedroom 2 | 14' × 12'\n2.3 Bedroom 3 | 13' × 12'\n2.4 Family lounge | 20' × 14'\n2.5 Bath, master | 9' × 7'\n2.6 Bath, shared | 8' × 6'\n2.7 Terrace | 420 sq ft\n2.8 Balcony | 6' deep" ),
			);
			$plan_ids = array();
			foreach ( $plans as $pl ) {
				$pid = bytenot_core_demo_image( $pl[0], (int) $ids[0] );
				if ( $pid ) {
					wp_update_post( array( 'ID' => $pid, 'post_title' => $pl[1], 'post_content' => $pl[2] ) );
					$plan_ids[] = $pid;
				}
			}
			update_post_meta( (int) $ids[0], 'pc_floor_plans', $plan_ids );
		}
	}

	// classic menus for the theme's locations
	$home    = home_url( '/' );
	$archive = get_post_type_archive_link( 'property' );
	$menus   = array(
		'primary'      => array( 'Primary', array( array( 'Home', $home ), array( 'About Us', $home . '#about' ), array( 'Projects', $archive ), array( 'Contact Us', $home . '#inquiry' ) ) ),
		'footer'       => array( 'Footer', array( array( 'Home', $home ), array( 'About Us', $home . '#about' ), array( 'Projects', $archive ), array( 'Contact Us', $home . '#inquiry' ) ) ),
		'footer-types' => array( 'Property types', array( array( 'Apartments', add_query_arg( 'type', 'apartment', $archive ) ), array( 'Villas', add_query_arg( 'type', 'villa', $archive ) ), array( 'Plots', add_query_arg( 'type', 'plot', $archive ) ), array( 'Commercial', add_query_arg( 'type', 'commercial', $archive ) ) ) ),
		'legal'        => array( 'Legal', array( array( 'Terms & Conditions', $home ), array( 'Privacy policy', get_privacy_policy_url() ?: $home ) ) ),
	);
	$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $m ) {
		$menu = wp_get_nav_menu_object( $m[0] );
		$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $m[0] );
		if ( ! $menu_id || is_wp_error( $menu_id ) ) {
			continue;
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

	// the theme's texts and photos (Customize › Bytenot Estate)
	$img  = static fn( $file ) => bytenot_core_demo_image( $file );
	$mods = array(
		'hero_image'           => $img( 'hero.jpg' ),
		'hero_title'           => 'Home with Honor, <br>Wealth with Vision.',
		'hero_lede'            => "Lavenor Realty finds you a home you'll feel lavish in and honored to own, and investments that grow with you. Hyderabad, Bangalore and beyond.",
		'about_statement'      => "Redefining the real estate experience through trust, transparency and integrity.\n\nWe are committed to helping customers {pill} make confident property decisions through honest guidance, carefully selected opportunities and clear market insights. {pill} Our approach is built on understanding individual needs, providing fair market value and offering complete information without bias or pressure.\n\nWe believe in putting customers first, building lasting relationships {pill} and ensuring every property decision is backed by transparency, trust and genuine value.",
		'about_pill_1'         => $img( 't2.jpg' ),
		'about_pill_2'         => $img( 'p3.jpg' ),
		'about_pill_3'         => $img( 'p7.jpg' ),
		'about_float_1'        => $img( 'p1.jpg' ),
		'about_float_2'        => $img( 'p5.jpg' ),
		'about_float_3'        => $img( 'p8.jpg' ),
		'featured_video_thumb' => $img( 'thumb-1.jpg' ),
		'featured_video_caption' => 'How we pick: every home here was walked through by a Lavenor advisor before it was listed.',
		'testimonials_lede'    => 'Honest advice, clear paperwork and a home that feels right. Here is what buyers and investors across Hyderabad and Bangalore say about working with Lavenor.',
		'inquiry_image'        => $img( 'p7.jpg' ),
		'listing_pill'         => $img( 'house-small.jpg' ),
		'listing_noun'         => 'projects',
		'listing_lede'         => "Welcome to Lavenor's full collection of verified projects across Hyderabad and Bangalore, updated daily, priced transparently and ready to view. Use the filters to narrow by type, location, budget or configuration, and book a site visit in a few clicks.",
		'footer_cta_lede'      => "Tell us what you're looking for. A Lavenor advisor will shortlist homes, arrange visits and handle the paperwork.",
		'footer_slogan'        => 'Home with Honor',
		'contact_email'        => 'hello@lavenorrealty.com',
		'contact_address'      => 'Office 402, Plot 12, Road No. 36, Jubilee Hills, Hyderabad 500033',
		'social_linkedin'      => '#',
		'social_x'             => '#',
		'social_facebook'      => '#',
		'social_instagram'     => '#',
	);
	foreach ( $mods as $k => $v ) {
		set_theme_mod( 'bytenot_' . $k, $v );
	}
	$footer = bytenot_core_demo_image( 'footer.jpg' );
	if ( $footer ) {
		set_theme_mod( 'bytenot_footer_image', $footer );
	}
}
