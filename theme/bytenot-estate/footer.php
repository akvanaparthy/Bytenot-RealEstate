<?php
/**
 * Footer: call to action over a photo, the frosted panel with menus and contact, the
 * legal line and the giant wordmark.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_email   = bytenot_contact( 'email' );
$bytenot_phone   = bytenot_contact( 'phone' );
$bytenot_archive = bytenot_archive_url();
$bytenot_social  = array_filter(
	array(
		'linkedin'  => array( __( 'LinkedIn', 'bytenot-estate' ), bytenot_mod( 'social_linkedin' ) ),
		'x'         => array( __( 'X', 'bytenot-estate' ), bytenot_mod( 'social_x' ) ),
		'facebook'  => array( __( 'Facebook', 'bytenot-estate' ), bytenot_mod( 'social_facebook' ) ),
		'instagram' => array( __( 'Instagram', 'bytenot-estate' ), bytenot_mod( 'social_instagram' ) ),
		'youtube'   => array( __( 'YouTube', 'bytenot-estate' ), bytenot_mod( 'social_youtube' ) ),
	),
	static fn( $s ) => '' !== $s[1]
);
$bytenot_legal   = bytenot_legal_links();
$bytenot_rights  = trim( get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' ) ) . ' ' . wp_date( 'Y' ) . ( bytenot_mod( 'footer_slogan' ) ? ' · ' . bytenot_mod( 'footer_slogan' ) : '' );
?>

<footer class="site-footer" id="contact">
	<div class="brand-motif" aria-hidden="true"></div>
	<div class="site-footer__bg" aria-hidden="true">
		<?php bytenot_mod_image( 'footer_image', 'full', array( 'width' => 2000, 'height' => 2000, 'loading' => 'lazy' ), get_theme_file_uri( 'assets/images/footer-placeholder.jpg' ) ); ?>
	</div>

	<div class="container site-footer__cta">
		<h2 class="site-footer__cta-title"><?php echo esc_html( bytenot_mod( 'footer_cta_title' ) ); ?></h2>
		<p class="site-footer__cta-lede"><?php echo esc_html( bytenot_mod( 'footer_cta_lede' ) ); ?></p>
		<div class="site-footer__cta-actions">
			<a class="btn btn--light" href="<?php echo esc_url( $bytenot_archive ); ?>"><?php esc_html_e( 'Get started', 'bytenot-estate' ); ?> <?php bytenot_the_icon( 'arrow' ); ?></a>
			<?php if ( $bytenot_email ) : ?>
			<a class="site-footer__cta-link" href="mailto:<?php echo esc_attr( $bytenot_email ); ?>"><?php esc_html_e( 'Contact us', 'bytenot-estate' ); ?></a>
			<?php else : ?>
			<a class="site-footer__cta-link" href="<?php echo esc_url( home_url( '/#inquiry' ) ); ?>"><?php esc_html_e( 'Contact us', 'bytenot-estate' ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<div class="site-footer__panel">
	<div class="container">
	<div class="site-footer__top">
		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'bytenot-estate' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'site-footer__links',
					'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
					'depth'          => 1,
					'fallback_cb'    => 'bytenot_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="site-footer__contact">
			<?php if ( $bytenot_phone ) : ?>
			<a class="site-footer__phone" href="<?php echo esc_url( bytenot_tel( $bytenot_phone ) ); ?>"><?php echo esc_html( $bytenot_phone ); ?></a>
			<?php endif; ?>
			<?php if ( $bytenot_email ) : ?>
			<a class="site-footer__email" href="mailto:<?php echo esc_attr( $bytenot_email ); ?>"><?php echo esc_html( $bytenot_email ); ?></a>
			<?php endif; ?>
			<?php if ( $bytenot_social ) : ?>
			<ul class="social" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: site name. */ __( '%s on social media', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?>">
				<?php foreach ( $bytenot_social as $bytenot_key => $bytenot_s ) : ?>
				<li><a href="<?php echo esc_url( $bytenot_s[1] ); ?>" aria-label="<?php echo esc_attr( $bytenot_s[0] ); ?>" rel="noopener" target="_blank"><?php echo bytenot_social_icon( $bytenot_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>

		<nav class="site-footer__col site-footer__col--right" aria-label="<?php esc_attr_e( 'Browse', 'bytenot-estate' ); ?>">
			<?php
			if ( has_nav_menu( 'footer-types' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-types',
						'container'      => false,
						'menu_class'     => 'site-footer__links',
						'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
						'depth'          => 1,
					)
				);
			} else {
				// the kinds on offer, then the primary kind's types
				$bytenot_opts = bytenot_search_options();
				echo '<ul class="site-footer__links">';
				foreach ( $bytenot_opts['kinds'] as $bytenot_slug => $bytenot_label ) {
					echo '<li><a href="' . esc_url( bytenot_archive_url( $bytenot_slug ) ) . '">' . esc_html( $bytenot_label ) . '</a></li>';
				}
				foreach ( $bytenot_opts['types'] as $bytenot_slug => $bytenot_label ) {
					echo '<li><a href="' . esc_url( add_query_arg( 'type', $bytenot_slug, $bytenot_archive ) ) . '">' . esc_html( $bytenot_label ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</nav>
	</div>

	<div class="site-footer__legal">
		<div class="site-footer__cell">
			<p class="site-footer__label"><?php esc_html_e( 'All rights reserved', 'bytenot-estate' ); ?></p>
			<p class="site-footer__meta"><?php echo esc_html( $bytenot_rights ); ?></p>
		</div>
		<div class="site-footer__cell site-footer__cell--center">
			<?php if ( bytenot_mod( 'contact_address' ) ) : ?>
			<p class="site-footer__label"><?php esc_html_e( 'Address location', 'bytenot-estate' ); ?></p>
			<p class="site-footer__meta"><?php echo esc_html( bytenot_mod( 'contact_address' ) ); ?></p>
			<?php endif; ?>
			<?php
			if ( bytenot_contact( 'rera' ) ) :
				/* translators: %s: RERA number. */
				$bytenot_rera_fmt = __( 'RERA No. %s', 'bytenot-estate' );
				if ( false !== stripos( bytenot_contact( 'rera_state' ), 'rera' ) ) {
					/* translators: %s: registration number. */
					$bytenot_rera_fmt = __( 'No. %s', 'bytenot-estate' );
				}
				?>
			<p class="site-footer__meta"><?php echo esc_html( trim( bytenot_contact( 'rera_state' ) . ' ' . sprintf( $bytenot_rera_fmt, bytenot_contact( 'rera' ) ) ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="site-footer__cell site-footer__cell--right">
			<?php foreach ( $bytenot_legal as $bytenot_i => $bytenot_link ) : ?>
				<?php if ( 0 === $bytenot_i ) : ?>
				<p class="site-footer__label"><a href="<?php echo esc_url( $bytenot_link[1] ); ?>"><?php echo esc_html( $bytenot_link[0] ); ?></a></p>
				<?php else : ?>
				<p class="site-footer__meta site-footer__meta--plain"><a href="<?php echo esc_url( $bytenot_link[1] ); ?>"><?php echo esc_html( $bytenot_link[0] ); ?></a></p>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
	</div>
	</div>

	<p class="site-footer__wordmark" aria-hidden="true"><?php bloginfo( 'name' ); ?></p>
</footer>

<div class="toast" role="status" aria-live="polite" data-toast hidden></div>

<?php wp_footer(); ?>
</body>
</html>
