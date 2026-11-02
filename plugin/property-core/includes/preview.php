<?php
/**
 * Private preview: while it is on, only logged-in users (or visitors with the preview
 * code) see the site. Everyone else gets a holding page with a 503 status and a
 * noindex header; feeds, the REST API, sitemaps and robots.txt are closed too, so
 * nothing is indexed before launch. One switch under Properties › Setup turns it off.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

const PROPERTY_CORE_PREVIEW_COOKIE = 'pc_preview';

/**
 * Is the preview lock on?
 *
 * @return bool
 */
function property_core_preview_on() {
	return (bool) property_core_setting( 'preview_mode' );
}

/**
 * The cookie value that unlocks the site for one browser.
 *
 * @return string
 */
function property_core_preview_token() {
	return hash_hmac( 'sha256', (string) property_core_setting( 'preview_code' ), wp_salt( 'auth' ) );
}

/**
 * May this request see the site?
 *
 * @return bool
 */
function property_core_preview_allowed() {
	if ( ! property_core_preview_on() ) {
		return true;
	}
	if ( is_user_logged_in() ) {
		return true;
	}
	$cookie = isset( $_COOKIE[ PROPERTY_CORE_PREVIEW_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ PROPERTY_CORE_PREVIEW_COOKIE ] ) ) : '';
	return '' !== $cookie && '' !== (string) property_core_setting( 'preview_code' ) && hash_equals( property_core_preview_token(), $cookie );
}

/**
 * Set the unlock cookie (30 days, HttpOnly, SameSite=Lax).
 */
function property_core_preview_set_cookie() {
	setcookie(
		PROPERTY_CORE_PREVIEW_COOKIE,
		property_core_preview_token(),
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
function property_core_preview_gate() {
	if ( ! property_core_preview_on() || is_robots() || is_favicon() ) {
		return; // robots.txt is served closed by its own filter
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow, noarchive' );

	// ?preview_code=… unlocks this browser and drops the parameter from the address
	$given = isset( $_GET['preview_code'] ) ? sanitize_text_field( wp_unslash( $_GET['preview_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a shared access code, not a form.
	$code  = (string) property_core_setting( 'preview_code' );
	if ( '' !== $given && '' !== $code && hash_equals( $code, $given ) ) {
		property_core_preview_set_cookie();
		wp_safe_redirect( remove_query_arg( 'preview_code' ) );
		exit;
	}
	if ( is_user_logged_in() ) {
		// the browser gets the cookie too, so the site's own forms (REST) keep working
		if ( ! isset( $_COOKIE[ PROPERTY_CORE_PREVIEW_COOKIE ] ) && '' !== $code ) {
			property_core_preview_set_cookie();
		}
		return;
	}
	if ( property_core_preview_allowed() ) {
		return;
	}
	status_header( 503 );
	header( 'Retry-After: 86400' );
	header( 'Content-Type: text/html; charset=utf-8' );
	property_core_preview_page();
	exit;
}
add_action( 'template_redirect', 'property_core_preview_gate', 0 );

/**
 * The holding page: the site name, the tagline and one line. No theme, no scripts.
 */
function property_core_preview_page() {
	$name = get_bloginfo( 'name' );
	$tag  = get_bloginfo( 'description' );
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
	body { min-height: 100%; margin: 0; display: grid; place-items: center; padding: 24px; box-sizing: border-box; background: #142f3f; color: #fff; font: 16px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; -webkit-font-smoothing: antialiased; }
	.card { text-align: center; max-width: 560px; }
	.mark { width: 56px; height: 56px; margin: 0 auto 28px; border-radius: 50%; background: linear-gradient(135deg, #f9ebb2, #897749); }
	h1 { margin: 0; font-size: clamp(28px, 5vw, 44px); font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
	.tag { margin: 6px 0 0; font-size: 12px; letter-spacing: .34em; text-transform: uppercase; color: #f9ebb2; }
	p.lede { margin: 32px 0 0; font-size: 17px; color: rgba(255, 255, 255, .78); }
	a { color: #f9ebb2; }
</style>
</head>
<body>
	<div class="card">
		<div class="mark" aria-hidden="true"></div>
		<h1><?php echo esc_html( $name ); ?></h1>
		<?php if ( $tag ) : ?><p class="tag"><?php echo esc_html( $tag ); ?></p><?php endif; ?>
		<p class="lede"><?php esc_html_e( 'We are putting the finishing touches on our new home. Back very soon.', 'property-core' ); ?></p>
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
function property_core_preview_rest( $result ) {
	if ( ! empty( $result ) || ! property_core_preview_on() || property_core_preview_allowed() ) {
		return $result;
	}
	return new WP_Error( 'preview_locked', __( 'This site is not open yet.', 'property-core' ), array( 'status' => 503 ) );
}
add_filter( 'rest_authentication_errors', 'property_core_preview_rest', 99 );

/**
 * No sitemaps and a closed robots.txt while locked.
 *
 * @param bool $enabled Sitemaps enabled.
 * @return bool
 */
function property_core_preview_sitemaps( $enabled ) {
	return property_core_preview_on() ? false : $enabled;
}
add_filter( 'wp_sitemaps_enabled', 'property_core_preview_sitemaps' );

/**
 * Robots: disallow everything while locked.
 *
 * @param string $output Robots text.
 * @return string
 */
function property_core_preview_robots( $output ) {
	return property_core_preview_on() ? "User-agent: *\nDisallow: /\n" : $output;
}
add_filter( 'robots_txt', 'property_core_preview_robots', 5 );

/**
 * Robots meta on every page while locked (for logged-in views too).
 *
 * @param array $robots Directives.
 * @return array
 */
function property_core_preview_meta_robots( $robots ) {
	if ( property_core_preview_on() ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'property_core_preview_meta_robots', 99 );

/**
 * A reminder in the admin bar and on every admin screen while the lock is on.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function property_core_preview_admin_bar( $bar ) {
	if ( ! property_core_preview_on() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'pc-preview',
			'title' => '<span style="color:#f9ebb2">● ' . esc_html__( 'Private preview on', 'property-core' ) . '</span>',
			'href'  => admin_url( 'edit.php?post_type=property&page=property-core' ),
			'meta'  => array( 'title' => __( 'Only logged-in users can see the site. Switch off under Properties › Setup.', 'property-core' ) ),
		)
	);
}
add_action( 'admin_bar_menu', 'property_core_preview_admin_bar', 100 );

/**
 * Admin notice.
 */
function property_core_preview_notice() {
	if ( ! property_core_preview_on() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Private preview is on: visitors see a holding page and search engines index nothing. Switch it off under Properties › Setup when you launch.', 'property-core' ) . '</p></div>';
}
add_action( 'admin_notices', 'property_core_preview_notice' );
