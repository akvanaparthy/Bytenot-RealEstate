<?php
/**
 * Customizer: every text and image on the designed pages. Defaults are generic so a
 * fresh install reads well; the demo installer sets a full example.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings, grouped by section: key => [ label, type, default ].
 * Types: text, textarea, html (inline markup allowed), url, image, email.
 *
 * @return array<string, array<string, array>>
 */
function bytenot_customizer_fields() {
	$name = get_bloginfo( 'name' );
	return array(
		'header'       => array(
			'header_cta_text'  => array( __( 'Button text', 'bytenot-estate' ), 'text', __( 'Talk to an advisor', 'bytenot-estate' ) ),
			'header_cta_short' => array( __( 'Button text (phones)', 'bytenot-estate' ), 'text', __( 'Enquire', 'bytenot-estate' ) ),
			'header_cta_url'   => array( __( 'Button link', 'bytenot-estate' ), 'url', '#inquiry' ),
			'header_logo_light' => array( __( 'Logo on photos (light version, shown while the header is transparent)', 'bytenot-estate' ), 'image', 0 ),
		),
		'hero'         => array(
			'hero_image'      => array( __( 'Background photo', 'bytenot-estate' ), 'image', 0 ),
			'hero_tags'       => array( __( 'Show the quick property-type tags', 'bytenot-estate' ), 'toggle', 1 ),
			'hero_intents'    => array( __( 'Show the buy / rent / resale filter', 'bytenot-estate' ), 'toggle', 1 ),
			'hero_title'      => array( __( 'Title', 'bytenot-estate' ), 'html', __( 'Find a home you will <br>be proud to own.', 'bytenot-estate' ) ),
			'hero_lede'       => array( __( 'Lede', 'bytenot-estate' ), 'textarea', __( 'We find you the right home and investments that grow with you, with honest advice at every step.', 'bytenot-estate' ) ),
			'search_title'    => array( __( 'Search box title', 'bytenot-estate' ), 'text', __( 'Find your perfect home', 'bytenot-estate' ) ),
			'partners_before' => array( __( 'Partners strip, first word', 'bytenot-estate' ), 'text', __( 'Our', 'bytenot-estate' ) ),
			'partners_after'  => array( __( 'Partners strip, last word', 'bytenot-estate' ), 'text', __( 'Partners', 'bytenot-estate' ) ),
		),
		'about'        => array(
			/* translators: %s: site name. {pill} marks where an inline photo goes. */
			'about_statement' => array( __( 'Statement ({pill} marks an inline photo)', 'bytenot-estate' ), 'textarea', sprintf( __( '%s is a real-estate firm helping individuals, families {pill} and investors find the right home {pill} with confidence. Buying, selling, renting or investing, we walk every step with you, from the first viewing to the final signature, with deep local knowledge {pill} and a client-first approach, so every property journey is smooth, transparent and successful.', 'bytenot-estate' ), $name ) ),
			'about_pill_1'    => array( __( 'Inline photo 1', 'bytenot-estate' ), 'image', 0 ),
			'about_pill_2'    => array( __( 'Inline photo 2', 'bytenot-estate' ), 'image', 0 ),
			'about_pill_3'    => array( __( 'Inline photo 3', 'bytenot-estate' ), 'image', 0 ),
			'about_float_1'   => array( __( 'Side photo 1', 'bytenot-estate' ), 'image', 0 ),
			'about_float_2'   => array( __( 'Side photo 2', 'bytenot-estate' ), 'image', 0 ),
			'about_float_3'   => array( __( 'Side photo 3', 'bytenot-estate' ), 'image', 0 ),
			'team_eyebrow'    => array( __( 'People: eyebrow', 'bytenot-estate' ), 'text', __( 'The people', 'bytenot-estate' ) ),
			'team_title'      => array( __( 'People: title (clear it to hide the row)', 'bytenot-estate' ), 'html', __( 'Led by people who <em>know the ground.</em>', 'bytenot-estate' ) ),
			'team_1_photo'    => array( __( 'Person 1: photo', 'bytenot-estate' ), 'image', 0 ),
			'team_1_name'     => array( __( 'Person 1: name', 'bytenot-estate' ), 'text', '' ),
			'team_1_role'     => array( __( 'Person 1: role', 'bytenot-estate' ), 'text', __( 'Founder', 'bytenot-estate' ) ),
			'team_2_photo'    => array( __( 'Person 2: photo', 'bytenot-estate' ), 'image', 0 ),
			'team_2_name'     => array( __( 'Person 2: name', 'bytenot-estate' ), 'text', '' ),
			'team_2_role'     => array( __( 'Person 2: role', 'bytenot-estate' ), 'text', __( 'Co-founder', 'bytenot-estate' ) ),
			'team_3_photo'    => array( __( 'Person 3: photo', 'bytenot-estate' ), 'image', 0 ),
			'team_3_name'     => array( __( 'Person 3: name', 'bytenot-estate' ), 'text', '' ),
			'team_3_role'     => array( __( 'Person 3: role', 'bytenot-estate' ), 'text', __( 'Director', 'bytenot-estate' ) ),
		),
		'featured'     => array(
			'featured_eyebrow'       => array( __( 'Eyebrow', 'bytenot-estate' ), 'text', __( 'Featured this week', 'bytenot-estate' ) ),
			'featured_title'         => array( __( 'Title', 'bytenot-estate' ), 'html', __( 'Hand-picked homes, <br><em>ready to visit.</em>', 'bytenot-estate' ) ),
			'featured_video_url'     => array( __( 'Video URL (YouTube, Vimeo or MP4)', 'bytenot-estate' ), 'url', '' ),
			'featured_video_thumb'   => array( __( 'Video thumbnail', 'bytenot-estate' ), 'image', 0 ),
			'featured_video_caption' => array( __( 'Video caption', 'bytenot-estate' ), 'textarea', __( 'How we pick: every home here was walked through by an advisor before it was listed.', 'bytenot-estate' ) ),
		),
		'testimonials' => array(
			'testimonials_eyebrow' => array( __( 'Eyebrow', 'bytenot-estate' ), 'text', __( 'Testimonials', 'bytenot-estate' ) ),
			'testimonials_title'   => array( __( 'Title', 'bytenot-estate' ), 'html', __( 'What our clients are saying', 'bytenot-estate' ) ),
			'testimonials_lede'    => array( __( 'Lede', 'bytenot-estate' ), 'textarea', __( 'Honest advice, clear paperwork and a home that feels right. Here is what buyers and investors say about working with us.', 'bytenot-estate' ) ),
		),
		'faq'          => array(
			'faq_label'   => array( __( 'Label', 'bytenot-estate' ), 'text', __( 'Your questions', 'bytenot-estate' ) ),
			'faq_title'   => array( __( 'Title', 'bytenot-estate' ), 'html', __( 'Frequently <br>Asked <br>Questions', 'bytenot-estate' ) ),
			'faq_caption' => array( __( 'Team caption', 'bytenot-estate' ), 'html', __( 'Our team<br>helping you', 'bytenot-estate' ) ),
		),
		'inquiry'      => array(
			'inquiry_image' => array( __( 'Background photo', 'bytenot-estate' ), 'image', 0 ),
			'inquiry_title' => array( __( 'Title', 'bytenot-estate' ), 'text', __( 'Property inquiry', 'bytenot-estate' ) ),
			'inquiry_glass' => array( __( 'Glass panel text', 'bytenot-estate' ), 'html', __( 'Discover <em>your ideal</em> place', 'bytenot-estate' ) ),
			'inquiry_lede'  => array( __( 'Form lede', 'bytenot-estate' ), 'textarea', __( "Tell us what you're looking for, and we'll guide you to the best options.", 'bytenot-estate' ) ),
			'inquiry_tag'   => array( __( 'Bottom tagline', 'bytenot-estate' ), 'html', __( "Let's find <em>your perfect</em> home", 'bytenot-estate' ) ),
		),
		'listing'      => array(
			'listing_title_1' => array( __( 'Title, first line', 'bytenot-estate' ), 'html', __( 'Find the home', 'bytenot-estate' ) ),
			'listing_pill'    => array( __( 'Small photo in the title', 'bytenot-estate' ), 'image', 0 ),
			'listing_title_2' => array( __( 'Title, second line', 'bytenot-estate' ), 'html', __( '<em>that feels</em> like yours.', 'bytenot-estate' ) ),
			'listing_lede'    => array( __( 'Lede', 'bytenot-estate' ), 'textarea', __( 'Our full collection of verified listings, updated daily, priced transparently and ready to view. Use the filters to narrow by type, location, budget or configuration, and book a site visit in a few clicks.', 'bytenot-estate' ) ),
		),
		'property'     => array(
			'papers_documents' => array( __( 'Documents available on request', 'bytenot-estate' ), 'text', __( 'Sale deed, EC, approved plan, OC, tax receipts', 'bytenot-estate' ) ),
			'deal_note'        => array( __( 'Note under the numbers', 'bytenot-estate' ), 'textarea', __( "Indicative only. Stamp duty and registration use the state rate. Your bank's offer and the final agreement value will differ.", 'bytenot-estate' ) ),
		),
		'footer'       => array(
			'footer_image'     => array( __( 'Background photo', 'bytenot-estate' ), 'image', 0 ),
			'footer_cta_title' => array( __( 'Call to action, title', 'bytenot-estate' ), 'text', __( 'Ready to find your home?', 'bytenot-estate' ) ),
			'footer_cta_lede'  => array( __( 'Call to action, lede', 'bytenot-estate' ), 'textarea', __( "Tell us what you're looking for. An advisor will shortlist homes, arrange visits and handle the paperwork.", 'bytenot-estate' ) ),
			'footer_slogan'    => array( __( 'Slogan after the copyright line', 'bytenot-estate' ), 'text', '' ),
		),
		'contact'      => array(
			'contact_email'    => array( __( 'Public email', 'bytenot-estate' ), 'email', '' ),
			'contact_address'  => array( __( 'Office address', 'bytenot-estate' ), 'textarea', '' ),
			'social_linkedin'  => array( __( 'LinkedIn URL', 'bytenot-estate' ), 'url', '' ),
			'social_x'         => array( __( 'X URL', 'bytenot-estate' ), 'url', '' ),
			'social_facebook'  => array( __( 'Facebook URL', 'bytenot-estate' ), 'url', '' ),
			'social_instagram' => array( __( 'Instagram URL', 'bytenot-estate' ), 'url', '' ),
			'social_youtube'   => array( __( 'YouTube URL', 'bytenot-estate' ), 'url', '' ),
		),
	);
}

/**
 * Read a setting with its default.
 *
 * @param string $key Key without the prefix.
 * @return mixed
 */
function bytenot_mod( $key ) {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = array();
		foreach ( bytenot_customizer_fields() as $fields ) {
			foreach ( $fields as $k => $f ) {
				$defaults[ $k ] = $f[2];
			}
		}
	}
	return get_theme_mod( 'bytenot_' . $key, $defaults[ $key ] ?? '' );
}

/**
 * Register panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function bytenot_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'bytenot', array( 'title' => __( 'Bytenot Estate', 'bytenot-estate' ), 'priority' => 30 ) );
	$sections = array(
		'header'       => __( 'Header button', 'bytenot-estate' ),
		'hero'         => __( 'Home: hero and search', 'bytenot-estate' ),
		'about'        => __( 'Home: about', 'bytenot-estate' ),
		'featured'     => __( 'Home: featured', 'bytenot-estate' ),
		'testimonials' => __( 'Home: testimonials', 'bytenot-estate' ),
		'faq'          => __( 'Home: FAQ', 'bytenot-estate' ),
		'inquiry'      => __( 'Home: inquiry', 'bytenot-estate' ),
		'listing'      => __( 'Listings page', 'bytenot-estate' ),
		'property'     => __( 'Listing page', 'bytenot-estate' ),
		'footer'       => __( 'Footer', 'bytenot-estate' ),
		'contact'      => __( 'Contact and social', 'bytenot-estate' ),
	);
	$sanitize = array(
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'html'     => 'bytenot_inline',
		'url'      => 'esc_url_raw',
		'email'    => 'sanitize_email',
		'image'    => 'absint',
		'toggle'   => 'rest_sanitize_boolean',
	);
	foreach ( bytenot_customizer_fields() as $section => $fields ) {
		$wp_customize->add_section( 'bytenot_' . $section, array( 'title' => $sections[ $section ], 'panel' => 'bytenot' ) );
		foreach ( $fields as $key => $f ) {
			$id = 'bytenot_' . $key;
			$wp_customize->add_setting( $id, array( 'default' => $f[2], 'sanitize_callback' => $sanitize[ $f[1] ], 'transport' => 'refresh' ) );
			if ( 'image' === $f[1] ) {
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array( 'label' => $f[0], 'section' => 'bytenot_' . $section, 'mime_type' => 'image' ) ) );
			} else {
				$types = array( 'text' => 'text', 'textarea' => 'textarea', 'html' => 'textarea', 'url' => 'url', 'email' => 'email', 'toggle' => 'checkbox' );
				$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'bytenot_' . $section, 'type' => $types[ $f[1] ] ) );
			}
		}
	}
}
add_action( 'customize_register', 'bytenot_customize_register' );
