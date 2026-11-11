<?php
/**
 * Head, the fixed header and the mobile drawer. Templates open their own <main>.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'bytenot-estate' ); ?></a>

<header class="site-header<?php echo is_front_page() || is_singular( 'listing' ) ? '' : ' ' . esc_attr( bytenot_class( 'site-header--dark' ) ); ?>" id="site-header" data-header>
	<div class="container site-header__inner">
		<?php bytenot_brand(); ?>

		<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'bytenot-estate' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav__list',
					'items_wrap'     => '<ul class="%2$s"><li class="nav__hl" aria-hidden="true"></li>%3$s</ul>',
					'depth'          => 1,
					'fallback_cb'    => 'bytenot_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="site-header__right">
			<?php bytenot_language_switcher(); ?>
			<a class="btn btn--accent btn--cta" href="<?php echo esc_url( bytenot_mod( 'header_cta_url' ) ); ?>"><span class="btn__long"><?php echo esc_html( bytenot_mod( 'header_cta_text' ) ); ?></span><span class="btn__short"><?php echo esc_html( bytenot_mod( 'header_cta_short' ) ); ?></span></a>
			<button class="menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'bytenot-estate' ); ?>" aria-expanded="false" aria-controls="drawer" data-drawer-open>
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="drawer" id="drawer" hidden data-drawer>
	<div class="drawer__backdrop" data-drawer-close></div>
	<div class="drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'bytenot-estate' ); ?>">
		<div class="drawer__head">
			<?php bytenot_brand( 'brand--dark', false, true ); ?>
			<button class="drawer__close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'bytenot-estate' ); ?>" data-drawer-close>
				<?php bytenot_the_icon( 'close', 22 ); ?>
			</button>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Mobile', 'bytenot-estate' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'drawer__nav',
					'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
					'depth'          => 1,
					'fallback_cb'    => 'bytenot_menu_fallback',
				)
			);
			?>
		</nav>
		<a class="btn btn--accent drawer__cta" href="<?php echo esc_url( bytenot_mod( 'header_cta_url' ) ); ?>"><?php echo esc_html( bytenot_mod( 'header_cta_text' ) ); ?></a>
	</div>
</div>
