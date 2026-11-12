<?php
/**
 * The Bytenot admin: one top-level menu with the plugin's own screens (dashboard,
 * setup, enquiries, integrations, tools) and the content types nested under it.
 * The screens draw themselves with the plugin's stylesheet; WordPress supplies only
 * the outer frame, the settings plumbing and the post editors.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/actions.php';
require_once __DIR__ . '/screens/dashboard.php';
require_once __DIR__ . '/screens/setup.php';
require_once __DIR__ . '/screens/leads.php';
require_once __DIR__ . '/screens/integrations.php';
require_once __DIR__ . '/screens/tools.php';
require_once __DIR__ . '/editor.php';

const BYTENOT_CORE_MENU = 'bytenot';

/**
 * The post types the admin dresses: the listing, then the content types.
 *
 * @return string[]
 */
function bytenot_core_post_types() {
	return array( 'listing', 'agent', 'testimonial', 'faq', 'partner' );
}

/**
 * The screens: slug => [ title, capability, renderer ].
 *
 * @return array<string, array{0:string,1:string,2:callable}>
 */
function bytenot_core_admin_screens() {
	return array(
		'bytenot'              => array( __( 'Dashboard', 'bytenot-core' ), 'edit_posts', 'bytenot_core_screen_dashboard' ),
		'bytenot-setup'        => array( __( 'Setup', 'bytenot-core' ), 'manage_options', 'bytenot_core_screen_setup' ),
		'bytenot-leads'        => array( __( 'Enquiries', 'bytenot-core' ), 'edit_posts', 'bytenot_core_screen_leads' ),
		'bytenot-integrations' => array( __( 'Integrations', 'bytenot-core' ), 'manage_options', 'bytenot_core_screen_integrations' ),
		'bytenot-tools'        => array( __( 'Tools', 'bytenot-core' ), 'manage_options', 'bytenot_core_screen_tools' ),
	);
}

/**
 * Register the menu and the screens.
 */
function bytenot_core_admin_menu() {
	$screens = bytenot_core_admin_screens();
	add_menu_page( __( 'Bytenot', 'bytenot-core' ), __( 'Bytenot', 'bytenot-core' ), 'edit_posts', BYTENOT_CORE_MENU, $screens['bytenot'][2], 'dashicons-admin-home', 3 );
	foreach ( $screens as $slug => $screen ) {
		add_submenu_page( BYTENOT_CORE_MENU, $screen[0], $screen[0], $screen[1], $slug, $screen[2] );
	}
}
add_action( 'admin_menu', 'bytenot_core_admin_menu', 9 );

/**
 * Order the submenu: the plugin's screens around the content types.
 */
function bytenot_core_admin_menu_order() {
	global $submenu;
	if ( empty( $submenu[ BYTENOT_CORE_MENU ] ) ) {
		return;
	}
	$order = array_merge( array( 'bytenot', 'bytenot-leads' ), array_map( static fn( $t ) => 'edit.php?post_type=' . $t, bytenot_core_post_types() ), array( 'bytenot-setup', 'bytenot-integrations', 'bytenot-tools' ) );
	$items = $submenu[ BYTENOT_CORE_MENU ];
	usort(
		$items,
		static function ( $a, $b ) use ( $order ) {
			$ia = array_search( $a[2], $order, true );
			$ib = array_search( $b[2], $order, true );
			return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
		}
	);
	$submenu[ BYTENOT_CORE_MENU ] = $items; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- reordering our own menu.
}
add_action( 'admin_menu', 'bytenot_core_admin_menu_order', 100 );

/**
 * Is this one of the plugin's screens?
 *
 * @return string Screen slug or ''.
 */
function bytenot_core_admin_current() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return isset( bytenot_core_admin_screens()[ $page ] ) ? $page : '';
}

/**
 * Is the current screen one of the plugin's post types' list tables?
 *
 * @return string Post type or ''.
 */
function bytenot_core_admin_list_type() {
	$screen = get_current_screen();
	return $screen && 'edit' === $screen->base && in_array( $screen->post_type, bytenot_core_post_types(), true ) ? $screen->post_type : '';
}

/**
 * Stylesheet and script on the plugin's screens (and the light touch on its post lists).
 *
 * @param string $hook Hook suffix.
 */
function bytenot_core_admin_assets( $hook ) {
	$screen = get_current_screen();
	$ours   = bytenot_core_admin_current() || ( $screen && in_array( $screen->post_type, array_merge( bytenot_core_post_types(), array( 'enquiry' ) ), true ) );
	if ( ! $ours ) {
		return;
	}
	wp_enqueue_media(); // the sharing image picker on the setup screen
	wp_enqueue_style( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.css', array(), BYTENOT_CORE_VERSION );
	wp_enqueue_script( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.js', array(), BYTENOT_CORE_VERSION, true );
	wp_localize_script( 'bytenot-core-admin', 'bytenotAdmin', array( 'copied' => __( 'Copied', 'bytenot-core' ), 'choose' => __( 'Choose image', 'bytenot-core' ), 'none' => __( 'None', 'bytenot-core' ), 'confirmDemo' => __( 'Reinstall the demo content? Demo records are updated in place; your own records and settings are untouched.', 'bytenot-core' ) ) );
}
add_action( 'admin_enqueue_scripts', 'bytenot_core_admin_assets' );

/* ---------- layout helpers ---------- */

/**
 * The tab row: the plugin's screens and the content types, in one place.
 *
 * @return array<int, array{slug:string,label:string,url:string,match:string[]}>
 */
function bytenot_core_admin_tabs() {
	$t = static fn( $slug, $label, $url, $match ) => array( 'slug' => $slug, 'label' => $label, 'url' => $url, 'match' => $match );
	return array(
		$t( 'bytenot', __( 'Dashboard', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot' ), array( 'page:bytenot' ) ),
		$t( 'listings', __( 'Listings', 'bytenot-core' ), admin_url( 'edit.php?post_type=listing' ), array( 'type:listing' ) ),
		$t( 'content', __( 'Content', 'bytenot-core' ), admin_url( 'edit.php?post_type=partner' ), array( 'type:agent', 'type:testimonial', 'type:faq', 'type:partner' ) ),
		$t( 'bytenot-leads', __( 'Enquiries', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-leads' ), array( 'page:bytenot-leads' ) ),
		$t( 'bytenot-setup', __( 'Setup', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-setup' ), array( 'page:bytenot-setup' ) ),
		$t( 'bytenot-integrations', __( 'Integrations', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-integrations' ), array( 'page:bytenot-integrations' ) ),
		$t( 'bytenot-tools', __( 'Tools', 'bytenot-core' ), admin_url( 'admin.php?page=bytenot-tools' ), array( 'page:bytenot-tools' ) ),
	);
}

/**
 * The content types shown as a second row under the Content tab.
 *
 * @return array<string, string> post type => label.
 */
function bytenot_core_admin_content_types() {
	return array(
		'partner'     => __( 'Partners', 'bytenot-core' ),
		'testimonial' => __( 'Testimonials', 'bytenot-core' ),
		'faq'         => __( 'FAQs', 'bytenot-core' ),
		'agent'       => __( 'Advisors', 'bytenot-core' ),
	);
}

/**
 * The brand header and the tab row. Used by the plugin's screens and, above the
 * list tables, by the content types.
 *
 * @param string $current "page:slug" or "type:post_type".
 */
function bytenot_core_admin_header( $current ) {
	$theme  = wp_get_theme();
	$status = array(
		array( 'ok', sprintf( /* translators: %s: theme name. */ __( '%s theme', 'bytenot-core' ), $theme->get( 'Name' ) ) ),
		bytenot_core_preview_on() ? array( 'warn', __( 'Private preview on', 'bytenot-core' ) ) : array( 'ok', __( 'Site public', 'bytenot-core' ) ),
		'webhook' === bytenot_core_setting( 'crm_provider' ) ? array( 'ok', __( 'Leads by email and webhook', 'bytenot-core' ) ) : array( 'muted', __( 'Leads by email', 'bytenot-core' ) ),
	);
	$type = 0 === strpos( $current, 'type:' ) ? substr( $current, 5 ) : '';
	?>
	<header class="bn-head">
		<div class="bn-head__brand">
			<svg class="bn-head__mark" viewBox="0 0 24 24" aria-hidden="true"><defs><linearGradient id="bn-gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f9ebb2"/><stop offset="1" stop-color="#897749"/></linearGradient></defs><path fill="url(#bn-gold)" d="M12 3 2 12h3v8h5v-6h4v6h5v-8h3z"/></svg>
			<div>
				<p class="bn-head__name"><?php esc_html_e( 'Bytenot Core', 'bytenot-core' ); ?> <span class="bn-head__ver"><?php echo esc_html( BYTENOT_CORE_VERSION ); ?></span></p>
				<p class="bn-head__sub"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View site', 'bytenot-core' ); ?></a></p>
			</div>
		</div>
		<ul class="bn-status" aria-label="<?php esc_attr_e( 'Status', 'bytenot-core' ); ?>">
			<?php foreach ( $status as $st ) : ?>
			<li class="bn-status__item bn-status__item--<?php echo esc_attr( $st[0] ); ?>"><?php echo esc_html( $st[1] ); ?></li>
			<?php endforeach; ?>
		</ul>
	</header>
	<nav class="bn-tabs" aria-label="<?php esc_attr_e( 'Bytenot screens', 'bytenot-core' ); ?>">
		<?php
		$screens = bytenot_core_admin_screens();
		foreach ( bytenot_core_admin_tabs() as $tab ) :
			if ( isset( $screens[ $tab['slug'] ] ) && ! current_user_can( $screens[ $tab['slug'] ][1] ) ) {
				continue;
			}
			$on = in_array( $current, $tab['match'], true );
			?>
		<a class="bn-tab<?php echo $on ? ' is-active' : ''; ?>" href="<?php echo esc_url( $tab['url'] ); ?>"<?php echo $on ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $tab['label'] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php if ( $type && isset( bytenot_core_admin_content_types()[ $type ] ) ) : ?>
	<nav class="bn-subtabs bn-subtabs--content" aria-label="<?php esc_attr_e( 'Content types', 'bytenot-core' ); ?>">
		<?php foreach ( bytenot_core_admin_content_types() as $pt => $label ) : ?>
		<a class="bn-subtab<?php echo $pt === $type ? ' is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $pt ) ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php endif; ?>
	<?php
}

/**
 * Open a screen: the header, the flash and the body.
 *
 * @param string $slug Current screen slug.
 */
function bytenot_core_admin_open( $slug ) {
	$screens = bytenot_core_admin_screens();
	?>
	<div class="wrap bn">
		<h1 class="screen-reader-text"><?php echo esc_html( $screens[ $slug ][0] ); ?></h1>
		<?php bytenot_core_admin_header( 'page:' . $slug ); ?>
		<?php bytenot_core_admin_flash(); ?>
		<div class="bn-body">
	<?php
}

/**
 * The header above the content types' list tables, so they are part of the same admin.
 */
function bytenot_core_admin_list_header() {
	$type = bytenot_core_admin_list_type();
	if ( ! $type ) {
		return;
	}
	echo '<div class="bn bn--bar">';
	bytenot_core_admin_header( 'type:' . $type );
	echo '</div>';
}
add_action( 'all_admin_notices', 'bytenot_core_admin_list_header', 1 );

/**
 * A body class on those list screens, for the list table styling.
 *
 * @param string $classes Body classes.
 * @return string
 */
function bytenot_core_admin_body_class( $classes ) {
	return bytenot_core_admin_list_type() ? $classes . ' bn-list-screen' : $classes;
}
add_filter( 'admin_body_class', 'bytenot_core_admin_body_class' );

/**
 * Close a screen.
 */
function bytenot_core_admin_close() {
	?>
		</div>
		<p class="bn-foot"><?php echo esc_html__( 'Bytenot Core, the engine behind the Bytenot Estate theme.', 'bytenot-core' ); ?> <a href="https://bytenot.com" target="_blank" rel="noopener">bytenot.com</a></p>
	</div>
	<?php
}

/**
 * One-off messages after an action (?bn_msg=key&bn_ok=1).
 */
function bytenot_core_admin_flash() {
	if ( empty( $_GET['bn_msg'] ) && empty( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$key  = empty( $_GET['bn_msg'] ) ? 'saved' : sanitize_key( wp_unslash( $_GET['bn_msg'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$ok   = ! empty( $_GET['bn_ok'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$text = isset( $_GET['bn_text'] ) ? sanitize_text_field( wp_unslash( $_GET['bn_text'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$msgs = array(
		'demo'      => __( 'Demo content installed.', 'bytenot-core' ),
		'test_lead' => $ok ? __( 'Test lead delivered.', 'bytenot-core' ) : __( 'The test lead could not be delivered.', 'bytenot-core' ),
		'retry'     => $ok ? __( 'Lead pushed again.', 'bytenot-core' ) : __( 'The push failed again.', 'bytenot-core' ),
		'preview'   => $ok ? __( 'Private preview switched on.', 'bytenot-core' ) : __( 'Private preview switched off: the site is public.', 'bytenot-core' ),
		'saved'     => __( 'Settings saved.', 'bytenot-core' ),
	);
	if ( ! isset( $msgs[ $key ] ) ) {
		return;
	}
	$type = in_array( $key, array( 'test_lead', 'retry' ), true ) && ! $ok ? 'error' : 'ok';
	echo '<div class="bn-flash bn-flash--' . esc_attr( $type ) . '" role="status">' . esc_html( $msgs[ $key ] ) . ( $text ? ' <span class="bn-flash__detail">' . esc_html( $text ) . '</span>' : '' ) . '</div>';
}

/**
 * A card.
 *
 * @param string $title   Title.
 * @param string $content HTML content (already escaped).
 * @param string $lede    Optional one-line description.
 * @param string $class   Extra classes.
 */
function bytenot_core_card( $title, $content, $lede = '', $class = '' ) {
	echo '<section class="bn-card ' . esc_attr( $class ) . '">';
	if ( $title ) {
		echo '<h2 class="bn-card__title">' . esc_html( $title ) . '</h2>';
	}
	if ( $lede ) {
		echo '<p class="bn-card__lede">' . esc_html( $lede ) . '</p>';
	}
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the caller.
	echo '</section>';
}

/**
 * A labelled field row.
 *
 * @param string $label   Label.
 * @param string $control Control HTML (escaped by the caller).
 * @param string $help    Help text.
 * @param string $for     Control ID.
 * @return string
 */
function bytenot_core_field( $label, $control, $help = '', $for = '' ) {
	return '<div class="bn-field">'
		. ( $for ? '<label class="bn-field__label" for="' . esc_attr( $for ) . '">' : '<span class="bn-field__label">' ) . esc_html( $label ) . ( $for ? '</label>' : '</span>' )
		. '<div class="bn-field__control">' . $control . ( $help ? '<p class="bn-field__help">' . esc_html( $help ) . '</p>' : '' ) . '</div></div>';
}

/**
 * A single-image picker bound to the settings array: a thumbnail, the file name, choose and remove.
 *
 * @param string $key Setting key holding an attachment ID.
 * @param array  $s   Settings.
 * @return string
 */
function bytenot_core_image_picker( $key, $s ) {
	$id  = absint( $s[ $key ] ?? 0 );
	$src = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	return '<div class="bn-media bn-media--image" data-media="image">'
		. '<input type="hidden" name="bytenot_core_settings[' . esc_attr( $key ) . ']" value="' . ( $id ? $id : '' ) . '">'
		. '<img class="bn-media__thumb" src="' . esc_url( $src ) . '" alt=""' . ( $src ? '' : ' hidden' ) . '>'
		. '<span class="bn-media__name">' . ( $id ? esc_html( basename( (string) get_attached_file( $id ) ) ) : '<span class="bn-muted">' . esc_html__( 'None', 'bytenot-core' ) . '</span>' ) . '</span> '
		. '<button type="button" class="bn-btn bn-btn--ghost bn-media__add">' . esc_html__( 'Choose', 'bytenot-core' ) . '</button> '
		. '<button type="button" class="bn-btn bn-btn--ghost bn-media__clear"' . ( $id ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'bytenot-core' ) . '</button>'
		. '</div>';
}

/**
 * A text-like input bound to the settings array.
 *
 * @param string $key   Setting key.
 * @param array  $s     Settings.
 * @param string $type  Input type.
 * @param array  $attrs Extra attributes.
 * @return string
 */
function bytenot_core_input( $key, $s, $type = 'text', $attrs = array() ) {
	$a = '';
	foreach ( $attrs as $k => $v ) {
		$a .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}
	return '<input class="bn-input" id="bn-' . esc_attr( $key ) . '" type="' . esc_attr( $type ) . '" name="bytenot_core_settings[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) ( $s[ $key ] ?? '' ) ) . '"' . $a . '>';
}

/**
 * Status badge for a CRM result.
 *
 * @param string $status pending | sent | failed.
 * @return string
 */
function bytenot_core_badge( $status ) {
	$map = array( 'sent' => array( 'ok', __( 'Sent', 'bytenot-core' ) ), 'failed' => array( 'bad', __( 'Failed', 'bytenot-core' ) ), 'pending' => array( 'warn', __( 'Pending', 'bytenot-core' ) ) );
	$b   = $map[ $status ] ?? array( 'muted', $status ?: __( 'New', 'bytenot-core' ) );
	return '<span class="bn-badge bn-badge--' . esc_attr( $b[0] ) . '">' . esc_html( $b[1] ) . '</span>';
}

/**
 * Lead counts for the dashboard and the integrations screen.
 *
 * @return array{total:int,week:int,pending:int,failed:int,sent:int}
 */
function bytenot_core_lead_stats() {
	global $wpdb;
	$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'enquiry' AND post_status = 'private'" );
	$week  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'enquiry' AND post_status = 'private' AND post_date_gmt >= %s", gmdate( 'Y-m-d H:i:s', time() - WEEK_IN_SECONDS ) ) );
	$by    = $wpdb->get_results( "SELECT m.meta_value AS s, COUNT(*) AS n FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE p.post_type = 'enquiry' AND m.meta_key = 'bn_crm_status' GROUP BY m.meta_value", OBJECT_K );
	return array(
		'total'   => $total,
		'week'    => $week,
		'pending' => isset( $by['pending'] ) ? (int) $by['pending']->n : 0,
		'failed'  => isset( $by['failed'] ) ? (int) $by['failed']->n : 0,
		'sent'    => isset( $by['sent'] ) ? (int) $by['sent']->n : 0,
	);
}

/**
 * Listing counts.
 *
 * @return array{live:int,closed:int,draft:int}
 */
function bytenot_core_listing_stats() {
	$closed = get_posts( array( 'post_type' => 'listing', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true, 'tax_query' => array( array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses() ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$counts = wp_count_posts( 'listing' );
	return array( 'live' => (int) $counts->publish - count( $closed ), 'closed' => count( $closed ), 'draft' => (int) $counts->draft );
}
