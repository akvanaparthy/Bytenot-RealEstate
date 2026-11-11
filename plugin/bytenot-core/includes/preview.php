<?php
/**
 * Private preview: while it is on, only logged-in users (or visitors with the preview
 * code) see the site. Everyone else gets a holding page with a 503 status and a
 * noindex header; feeds, the REST API, sitemaps and robots.txt are closed too, so
 * nothing is indexed before launch. One switch under Bytenot › Tools turns it off.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

const BYTENOT_CORE_PREVIEW_COOKIE = 'bytenot_preview';

/**
 * Is the preview lock on?
 *
 * @return bool
 */
function bytenot_core_preview_on() {
	return (bool) bytenot_core_setting( 'preview_mode' );
}

/**
 * The cookie value that unlocks the site for one browser.
 *
 * @return string
 */
function bytenot_core_preview_token() {
	return hash_hmac( 'sha256', (string) bytenot_core_setting( 'preview_code' ), wp_salt( 'auth' ) );
}

/**
 * May this request see the site?
 *
 * @return bool
 */
function bytenot_core_preview_allowed() {
	if ( ! bytenot_core_preview_on() ) {
		return true;
	}
	if ( is_user_logged_in() ) {
		return true;
	}
	$cookie = isset( $_COOKIE[ BYTENOT_CORE_PREVIEW_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ BYTENOT_CORE_PREVIEW_COOKIE ] ) ) : '';
	return '' !== $cookie && '' !== (string) bytenot_core_setting( 'preview_code' ) && hash_equals( bytenot_core_preview_token(), $cookie );
}

/**
 * Set the unlock cookie (30 days, HttpOnly, SameSite=Lax).
 */
function bytenot_core_preview_set_cookie() {
	setcookie(
		BYTENOT_CORE_PREVIEW_COOKIE,
		bytenot_core_preview_token(),
		array(
			'expires'  => time() + 30 * DAY_IN_SECONDS,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
}

/**
 * Front-end gate. Runs before any template, feed or sitemap renders.
 */
function bytenot_core_preview_gate() {
	if ( ! bytenot_core_preview_on() || is_robots() || is_favicon() ) {
		return; // robots.txt is served closed by its own filter
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow, noarchive' );

	// ?preview_code=… unlocks this browser and drops the parameter from the address
	$given = isset( $_GET['preview_code'] ) ? sanitize_text_field( wp_unslash( $_GET['preview_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a shared access code, not a form.
	$code  = (string) bytenot_core_setting( 'preview_code' );
	if ( '' !== $given && '' !== $code && hash_equals( $code, $given ) ) {
		bytenot_core_preview_set_cookie();
		wp_safe_redirect( remove_query_arg( 'preview_code' ) );
		exit;
	}
	if ( is_user_logged_in() ) {
		// the browser gets the cookie too, so the site's own forms (REST) keep working
		if ( ! isset( $_COOKIE[ BYTENOT_CORE_PREVIEW_COOKIE ] ) && '' !== $code ) {
			bytenot_core_preview_set_cookie();
		}
		return;
	}
	if ( bytenot_core_preview_allowed() ) {
		return;
	}
	status_header( 503 );
	header( 'Retry-After: 86400' );
	header( 'Content-Type: text/html; charset=utf-8' );
	bytenot_core_preview_page();
	exit;
}
add_action( 'template_redirect', 'bytenot_core_preview_gate', 0 );

/**
 * The holding page: the logo or the site name, the tagline and one line, in the theme's
 * own colours. No theme templates, no scripts.
 */
function bytenot_core_preview_page() {
	$name    = get_bloginfo( 'name' );
	$tag     = get_bloginfo( 'description' );
	$palette = (array) wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
	$colors  = array();
	foreach ( $palette as $c ) {
		if ( isset( $c['slug'], $c['color'] ) ) {
			$colors[ $c['slug'] ] = $c['color'];
		}
	}
	$bg    = $colors['contrast'] ?? '#1b2630';
	$gold  = $colors['primary'] ?? '#d9c58f';
	$logo  = has_custom_logo() ? wp_get_attachment_image_url( (int) get_theme_mod( 'bytenot_header_logo_light' ) ?: (int) get_theme_mod( 'custom_logo' ), 'full' ) : '';
	?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow, noarchive">
<title><?php echo esc_html( $name ); ?></title>
<style>
	html { height: 100%; }
	body { min-height: 100%; margin: 0; display: grid; place-items: center; padding: 24px; box-sizing: border-box; background: <?php echo esc_html( $bg ); ?>; color: #fff; font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; -webkit-font-smoothing: antialiased; }
	.card { text-align: center; max-width: 560px; }
	.logo { display: block; max-width: min(320px, 70vw); max-height: 96px; margin: 0 auto; }
	h1 { margin: 0; font-size: clamp(28px, 5vw, 44px); font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
	.tag { margin: 6px 0 0; font-size: 12px; letter-spacing: .34em; text-transform: uppercase; color: <?php echo esc_html( $gold ); ?>; }
	p.lede { margin: 32px 0 0; font-size: 17px; color: rgba(255, 255, 255, .78); }
	a { color: <?php echo esc_html( $gold ); ?>; }
</style>
</head>
<body>
	<div class="card">
		<?php if ( $logo ) : ?>
		<img class="logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $name ); ?>">
		<?php else : ?>
		<h1><?php echo esc_html( $name ); ?></h1>
		<?php if ( $tag ) : ?><p class="tag"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
		<?php endif; ?>
		<p class="lede"><?php esc_html_e( 'We are putting the finishing touches on our new home. Back very soon.', 'bytenot-core' ); ?></p>
	</div>
</body>
</html>
	<?php
}

/**
 * The REST API is closed to anonymous requests while the preview is on; the site's own
 * forms still work because the browser carries the unlock cookie.
 *
 * @param WP_Error|null|true $result Current result.
 * @return WP_Error|null|true
 */
function bytenot_core_preview_rest( $result ) {
	if ( ! empty( $result ) || ! bytenot_core_preview_on() || bytenot_core_preview_allowed() ) {
		return $result;
	}
	return new WP_Error( 'preview_locked', __( 'This site is not open yet.', 'bytenot-core' ), array( 'status' => 503 ) );
}
add_filter( 'rest_authentication_errors', 'bytenot_core_preview_rest', 99 );

/**
 * No sitemaps and a closed robots.txt while locked.
 *
 * @param bool $enabled Sitemaps enabled.
 * @return bool
 */
function bytenot_core_preview_sitemaps( $enabled ) {
	return bytenot_core_preview_on() ? false : $enabled;
}
add_filter( 'wp_sitemaps_enabled', 'bytenot_core_preview_sitemaps' );

/**
 * Robots: disallow everything while locked.
 *
 * @param string $output Robots text.
 * @return string
 */
function bytenot_core_preview_robots( $output ) {
	return bytenot_core_preview_on() ? "User-agent: *\nDisallow: /\n" : $output;
}
add_filter( 'robots_txt', 'bytenot_core_preview_robots', 5 );

/**
 * Robots meta on every page while locked (for logged-in views too).
 *
 * @param array $robots Directives.
 * @return array
 */
function bytenot_core_preview_meta_robots( $robots ) {
	if ( bytenot_core_preview_on() ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'bytenot_core_preview_meta_robots', 99 );

/**
 * A reminder in the admin bar and on every admin screen while the lock is on.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function bytenot_core_preview_admin_bar( $bar ) {
	if ( ! bytenot_core_preview_on() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'bytenot-preview',
			'title' => '● ' . esc_html__( 'Private preview on', 'bytenot-core' ),
			'href'  => admin_url( 'admin.php?page=bytenot-tools' ),
			'meta'  => array( 'title' => __( 'Only logged-in users can see the site. Switch off under Bytenot › Tools.', 'bytenot-core' ) ),
		)
	);
}
add_action( 'admin_bar_menu', 'bytenot_core_preview_admin_bar', 100 );

/**
 * Admin notice.
 */
function bytenot_core_preview_notice() {
	if ( ! bytenot_core_preview_on() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Private preview is on: visitors see a holding page and search engines index nothing. Switch it off under Bytenot › Tools when you launch.', 'bytenot-core' ) . '</p></div>';
}
add_action( 'admin_notices', 'bytenot_core_preview_notice' );
