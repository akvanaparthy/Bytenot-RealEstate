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
 * Menu icon: the house mark, monochrome so WordPress can tint it.
 *
 * @return string
 */
function bytenot_core_menu_icon() {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"><path fill="#a7aaad" d="M200 18 30 158h46v74h92v-66h64v66h92v-74h46L200 18zm0 40 92 76v18h-20v-36H128v36h-20v-18l92-76z"/></svg>';
	return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- data URI.
}

/**
 * Register the menu and the screens.
 */
function bytenot_core_admin_menu() {
	$screens = bytenot_core_admin_screens();
	add_menu_page( __( 'Bytenot', 'bytenot-core' ), __( 'Bytenot', 'bytenot-core' ), 'edit_posts', BYTENOT_CORE_MENU, $screens['bytenot'][2], bytenot_core_menu_icon(), 3 );
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
	$order = array( 'bytenot', 'bytenot-leads', 'edit.php?post_type=property', 'edit.php?post_type=project', 'edit.php?post_type=agent', 'edit.php?post_type=testimonial', 'edit.php?post_type=faq', 'edit.php?post_type=partner', 'bytenot-setup', 'bytenot-integrations', 'bytenot-tools' );
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
 * Stylesheet and script on the plugin's screens (and the light touch on its post lists).
 *
 * @param string $hook Hook suffix.
 */
function bytenot_core_admin_assets( $hook ) {
	$screen = get_current_screen();
	$ours   = bytenot_core_admin_current() || ( $screen && in_array( $screen->post_type, array( 'property', 'project', 'agent', 'enquiry', 'testimonial', 'faq', 'partner' ), true ) );
	if ( ! $ours ) {
		return;
	}
	wp_enqueue_style( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.css', array(), BYTENOT_CORE_VERSION );
	wp_enqueue_script( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.js', array(), BYTENOT_CORE_VERSION, true );
	wp_localize_script( 'bytenot-core-admin', 'bytenotAdmin', array( 'copied' => __( 'Copied', 'bytenot-core' ), 'confirmDemo' => __( 'Reinstall the demo content? Existing demo records are updated in place; your own records are untouched.', 'bytenot-core' ) ) );
}
add_action( 'admin_enqueue_scripts', 'bytenot_core_admin_assets' );

/* ---------- layout helpers ---------- */

/**
 * Open a screen: the brand header, status pills and the tab row.
 *
 * @param string $slug Current screen slug.
 */
function bytenot_core_admin_open( $slug ) {
	$screens = bytenot_core_admin_screens();
	$theme   = wp_get_theme();
	$pills   = array();
	$pills[] = array( 'ok', sprintf( /* translators: %s: theme name. */ __( 'Theme: %s', 'bytenot-core' ), $theme->get( 'Name' ) ) );
	$pills[] = bytenot_core_preview_on() ? array( 'warn', __( 'Private preview on', 'bytenot-core' ) ) : array( 'ok', __( 'Site public', 'bytenot-core' ) );
	$crm     = bytenot_core_setting( 'crm_provider' );
	$pills[] = array( 'webhook' === $crm ? 'ok' : 'muted', 'webhook' === $crm ? __( 'Leads: email + webhook', 'bytenot-core' ) : __( 'Leads: email only', 'bytenot-core' ) );
	?>
	<div class="wrap bn">
		<h1 class="screen-reader-text"><?php echo esc_html( $screens[ $slug ][0] ); ?></h1>
		<header class="bn-head">
			<div class="bn-head__brand">
				<svg class="bn-head__mark" viewBox="0 0 400 250" aria-hidden="true"><defs><linearGradient id="bn-gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f9ebb2"/><stop offset="1" stop-color="#897749"/></linearGradient></defs><path fill="url(#bn-gold)" d="M200 18 30 158h46v74h92v-66h64v66h92v-74h46L200 18zm0 40 92 76v18h-20v-36H128v36h-20v-18l92-76z"/></svg>
				<div>
					<p class="bn-head__name"><?php esc_html_e( 'Bytenot Core', 'bytenot-core' ); ?> <span class="bn-head__ver"><?php echo esc_html( BYTENOT_CORE_VERSION ); ?></span></p>
					<p class="bn-head__sub"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View site', 'bytenot-core' ); ?></a></p>
				</div>
			</div>
			<ul class="bn-pills" aria-label="<?php esc_attr_e( 'Status', 'bytenot-core' ); ?>">
				<?php foreach ( $pills as $pill ) : ?>
				<li class="bn-pill bn-pill--<?php echo esc_attr( $pill[0] ); ?>"><?php echo esc_html( $pill[1] ); ?></li>
				<?php endforeach; ?>
			</ul>
		</header>
		<nav class="bn-tabs" aria-label="<?php esc_attr_e( 'Bytenot screens', 'bytenot-core' ); ?>">
			<?php foreach ( $screens as $s => $screen ) : ?>
				<?php if ( current_user_can( $screen[1] ) ) : ?>
				<a class="bn-tab<?php echo $s === $slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $s ) ); ?>"<?php echo $s === $slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $screen[0] ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>
		<?php bytenot_core_admin_flash(); ?>
		<div class="bn-body">
	<?php
}

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
	$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'enquiry' AND post_status = 'private'" );
	$week   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'enquiry' AND post_status = 'private' AND post_date_gmt >= %s", gmdate( 'Y-m-d H:i:s', time() - WEEK_IN_SECONDS ) ) );
	$by     = $wpdb->get_results( "SELECT m.meta_value AS s, COUNT(*) AS n FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE p.post_type = 'enquiry' AND m.meta_key = 'pc_crm_status' GROUP BY m.meta_value", OBJECT_K );
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
	$closed = get_posts( array( 'post_type' => 'property', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true, 'tax_query' => array( array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses() ) ) ) );
	$counts = wp_count_posts( 'property' );
	return array( 'live' => (int) $counts->publish - count( $closed ), 'closed' => count( $closed ), 'draft' => (int) $counts->draft );
}
