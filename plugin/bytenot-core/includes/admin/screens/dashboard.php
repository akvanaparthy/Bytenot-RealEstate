<?php
/**
 * Dashboard: the numbers, the setup checklist, quick actions and the latest enquiries.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_dashboard() {
	$leads    = bytenot_core_lead_stats();
	$listings = bytenot_core_listing_stats();
	$s        = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$preview  = bytenot_core_preview_on();
	$primary  = bytenot_core_primary_kind();
	bytenot_core_admin_open( 'bytenot' );

	// the numbers
	$stats = array(
		array( $listings['live'], __( 'Live listings', 'bytenot-core' ), admin_url( 'edit.php?post_type=listing' ) ),
		array( $listings['closed'], __( 'Sold, let or withdrawn', 'bytenot-core' ), admin_url( 'edit.php?post_type=listing&listing_status=sold' ) ),
		array( $leads['week'], __( 'Enquiries this week', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-leads' ) ),
		array( $leads['total'], __( 'Enquiries in total', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-leads' ) ),
		array( $leads['pending'] + $leads['failed'], __( 'Waiting for the CRM', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-leads&crm=failed' ) ),
	);
	echo '<div class="bn-stats">';
	foreach ( $stats as $st ) {
		echo '<a class="bn-stat" href="' . esc_url( $st[2] ) . '"><span class="bn-stat__n">' . esc_html( number_format_i18n( $st[0] ) ) . '</span><span class="bn-stat__l">' . esc_html( $st[1] ) . '</span></a>';
	}
	echo '</div>';

	echo '<div class="bn-grid bn-grid--2">';

	// checklist
	$checks = array(
		array( (bool) $s['phone'], __( 'Phone number', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-setup' ) ),
		array( is_email( $s['email_to'] ), __( 'Lead email address', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-setup' ) ),
		array( (bool) $s['rera_number'], __( 'Agency RERA number', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-setup' ) ),
		array( has_custom_logo(), __( 'Logo', 'bytenot-core' ), admin_url( 'customize.php?autofocus[section]=title_tagline' ) ),
		array( (bool) get_theme_mod( 'bytenot_hero_image' ), __( 'Home page photos and texts', 'bytenot-core' ), admin_url( 'customize.php?autofocus[panel]=bytenot' ) ),
		array( has_nav_menu( 'primary' ), __( 'Menus assigned', 'bytenot-core' ), admin_url( 'nav-menus.php' ) ),
		array( $listings['live'] > 0, __( 'At least one live listing', 'bytenot-core' ), admin_url( 'post-new.php?post_type=listing&kind=' . $primary ) ),
		array( 'webhook' === $s['crm_provider'] && $s['crm_webhook'], __( 'CRM connected (optional)', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-integrations' ) ),
	);
	$done = count( array_filter( $checks, static fn( $c ) => $c[0] ) );
	$html = '<ol class="bn-check">';
	foreach ( $checks as $c ) {
		$html .= '<li class="bn-check__item' . ( $c[0] ? ' is-done' : '' ) . '"><span class="bn-check__dot" aria-hidden="true"></span><a href="' . esc_url( $c[2] ) . '">' . esc_html( $c[1] ) . '</a></li>';
	}
	$html .= '</ol>';
	/* translators: 1: done, 2: total. */
	bytenot_core_card( __( 'Setup', 'bytenot-core' ), $html, sprintf( __( '%1$d of %2$d done.', 'bytenot-core' ), $done, count( $checks ) ) );

	// quick actions
	$toggle = wp_nonce_url( admin_url( 'admin-post.php?action=bytenot_core_toggle_preview&back=bytenot' ), 'bytenot_core_toggle_preview' );
	$adds   = '';
	foreach ( bytenot_core_kinds( true ) as $slug => $k ) {
		/* translators: %s: kind, e.g. "project". */
		$adds .= '<a class="bn-btn" href="' . esc_url( admin_url( 'post-new.php?post_type=listing&kind=' . $slug ) ) . '">' . esc_html( sprintf( __( 'Add a %s', 'bytenot-core' ), mb_strtolower( $k['singular'] ) ) ) . '</a>';
	}
	$html = '<div class="bn-actions">' . $adds
		. '<a class="bn-btn bn-btn--ghost" href="' . esc_url( admin_url( 'customize.php?autofocus[panel]=bytenot' ) ) . '">' . esc_html__( 'Edit home page texts', 'bytenot-core' ) . '</a>'
		. '<a class="bn-btn bn-btn--ghost" href="' . esc_url( admin_url( 'admin.php?page=bytenot-leads' ) ) . '">' . esc_html__( 'See enquiries', 'bytenot-core' ) . '</a>'
		. ( current_user_can( 'manage_options' ) ? '<a class="bn-btn ' . ( $preview ? 'bn-btn--gold' : 'bn-btn--ghost' ) . '" href="' . esc_url( $toggle ) . '">' . ( $preview ? esc_html__( 'Open the site to the public', 'bytenot-core' ) : esc_html__( 'Lock the site (private preview)', 'bytenot-core' ) ) . '</a>' : '' )
		. '</div>'
		. '<p class="bn-card__note">' . ( $preview ? esc_html__( 'Visitors currently see the holding page. Nothing is indexed.', 'bytenot-core' ) : esc_html__( 'The site is open and can be indexed.', 'bytenot-core' ) ) . '</p>';
	bytenot_core_card( __( 'Quick actions', 'bytenot-core' ), $html );

	echo '</div>';

	// latest enquiries
	$ids  = get_posts( array( 'post_type' => 'enquiry', 'post_status' => 'private', 'posts_per_page' => 6, 'fields' => 'ids', 'no_found_rows' => true ) );
	$html = '';
	if ( $ids ) {
		$html .= '<table class="bn-table"><thead><tr><th>' . esc_html__( 'Lead', 'bytenot-core' ) . '</th><th>' . esc_html__( 'Form', 'bytenot-core' ) . '</th><th>' . esc_html__( 'Listing', 'bytenot-core' ) . '</th><th>' . esc_html__( 'Received', 'bytenot-core' ) . '</th><th>' . esc_html__( 'CRM', 'bytenot-core' ) . '</th></tr></thead><tbody>';
		foreach ( $ids as $id ) {
			$l     = (int) bytenot_core_get( $id, 'listing_id' );
			$html .= '<tr><td><a href="' . esc_url( admin_url( 'admin.php?page=bytenot-leads&lead=' . $id ) ) . '"><b>' . esc_html( bytenot_core_get( $id, 'name' ) ) . '</b></a><br><span class="bn-muted">' . esc_html( bytenot_core_get( $id, 'phone' ) ) . '</span></td>'
				. '<td>' . esc_html( bytenot_core_get( $id, 'form' ) ) . '</td>'
				. '<td>' . ( $l ? '<a href="' . esc_url( get_edit_post_link( $l ) ) . '">' . esc_html( get_the_title( $l ) ) . '</a>' : '<span class="bn-muted">—</span>' ) . '</td>'
				. '<td>' . esc_html( human_time_diff( get_post_time( 'U', true, $id ) ) ) . ' ' . esc_html__( 'ago', 'bytenot-core' ) . '</td>'
				. '<td>' . bytenot_core_badge( (string) bytenot_core_get( $id, 'crm_status' ) ) . '</td></tr>';
		}
		$html .= '</tbody></table>';
	} else {
		$html .= '<p class="bn-empty">' . esc_html__( 'No enquiries yet. They appear here the moment a visitor sends one.', 'bytenot-core' ) . '</p>';
	}
	bytenot_core_card( __( 'Latest enquiries', 'bytenot-core' ), $html, '', 'bn-card--wide' );

	bytenot_core_admin_close();
}
