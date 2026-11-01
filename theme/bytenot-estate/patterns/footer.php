<?php
/**
 * Title: Footer
 * Slug: bytenot-estate/footer
 * Categories: bytenot-estate-sections
 * Block Types: core/template-part/footer
 * Inserter: false
 */
?>
<!-- wp:group {"tagName":"footer","className":"site-footer","layout":{"type":"default"},"metadata":{"name":"Site footer"}} -->
<footer class="wp-block-group site-footer">
	<!-- wp:group {"className":"brand-motif","layout":{"type":"default"}} -->
	<div class="wp-block-group brand-motif"></div>
	<!-- /wp:group -->

	<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/images/footer-placeholder.jpg' ) ); ?>","dimRatio":0,"isUserOverlayColor":true,"focalPoint":{"x":0.5,"y":0.3},"minHeight":100,"minHeightUnit":"%","className":"site-footer__bg","layout":{"type":"default"}} -->
	<div class="wp-block-cover site-footer__bg"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/footer-placeholder.jpg' ) ); ?>" style="object-position:50% 30%" data-object-fit="cover" data-object-position="50% 30%"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"></div></div>
	<!-- /wp:cover -->

	<!-- wp:group {"className":"site-footer__cta","layout":{"type":"constrained","contentSize":"880px"}} -->
	<div class="wp-block-group site-footer__cta">
		<!-- wp:heading {"textAlign":"center","className":"site-footer__cta-title"} -->
		<h2 class="wp-block-heading has-text-align-center site-footer__cta-title"><?php esc_html_e( 'Ready to find your home?', 'bytenot-estate' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","className":"site-footer__cta-lede"} -->
		<p class="has-text-align-center site-footer__cta-lede"><?php esc_html_e( 'Tell us what you are looking for. An advisor will shortlist homes, arrange visits and handle the paperwork.', 'bytenot-estate' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"className":"site-footer__cta-actions","layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons site-footer__cta-actions">
			<!-- wp:button {"className":"is-style-light"} -->
			<div class="wp-block-button is-style-light"><a class="wp-block-button__link wp-element-button" href="#"><?php esc_html_e( 'Get started', 'bytenot-estate' ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"site-footer__panel","layout":{"type":"constrained"}} -->
	<div class="wp-block-group site-footer__panel">
		<!-- wp:group {"className":"site-footer__top","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
		<div class="wp-block-group site-footer__top">
			<!-- wp:navigation {"className":"site-footer__links","overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"metadata":{"name":"Footer menu"}} /-->

			<!-- wp:group {"className":"site-footer__contact","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
			<div class="wp-block-group site-footer__contact">
				<!-- wp:paragraph {"align":"center","className":"site-footer__phone"} -->
				<p class="has-text-align-center site-footer__phone"><a href="tel:+910000000000">+91 00000 00000</a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"align":"center","className":"site-footer__email"} -->
				<p class="has-text-align-center site-footer__email"><a href="mailto:hello@example.com">hello@example.com</a></p>
				<!-- /wp:paragraph -->
				<!-- wp:social-links {"size":"has-small-icon-size","className":"social","layout":{"type":"flex","justifyContent":"center"}} -->
				<ul class="wp-block-social-links has-small-icon-size social">
					<!-- wp:social-link {"url":"#","service":"linkedin"} /-->
					<!-- wp:social-link {"url":"#","service":"x"} /-->
					<!-- wp:social-link {"url":"#","service":"facebook"} /-->
					<!-- wp:social-link {"url":"#","service":"instagram"} /-->
				</ul>
				<!-- /wp:social-links -->
			</div>
			<!-- /wp:group -->

			<!-- wp:navigation {"className":"site-footer__links site-footer__links--right","overlayMenu":"never","layout":{"type":"flex","orientation":"vertical","justifyContent":"right"},"metadata":{"name":"Footer menu, right"}} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"site-footer__legal","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
		<div class="wp-block-group site-footer__legal">
			<!-- wp:group {"className":"site-footer__cell","layout":{"type":"default"}} -->
			<div class="wp-block-group site-footer__cell">
				<!-- wp:paragraph {"className":"site-footer__label"} -->
				<p class="site-footer__label"><?php esc_html_e( 'All rights reserved', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"site-footer__meta"} -->
				<p class="site-footer__meta"><?php
					/* translators: %s: the current year. */
					echo esc_html( sprintf( __( 'Company name %s', 'bytenot-estate' ), gmdate( 'Y' ) ) );
				?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"site-footer__cell site-footer__cell--center","layout":{"type":"default"}} -->
			<div class="wp-block-group site-footer__cell site-footer__cell--center">
				<!-- wp:paragraph {"align":"center","className":"site-footer__label"} -->
				<p class="has-text-align-center site-footer__label"><?php esc_html_e( 'Address', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"align":"center","className":"site-footer__meta"} -->
				<p class="has-text-align-center site-footer__meta"><?php esc_html_e( 'Office address, City 000000', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"align":"center","className":"site-footer__meta"} -->
				<p class="has-text-align-center site-footer__meta"><?php esc_html_e( 'Registration number', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"site-footer__cell site-footer__cell--right","layout":{"type":"default"}} -->
			<div class="wp-block-group site-footer__cell site-footer__cell--right">
				<!-- wp:paragraph {"align":"right","className":"site-footer__label"} -->
				<p class="has-text-align-right site-footer__label"><a href="#"><?php esc_html_e( 'Terms & Conditions', 'bytenot-estate' ); ?></a></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"align":"right","className":"site-footer__meta site-footer__meta--plain"} -->
				<p class="has-text-align-right site-footer__meta site-footer__meta--plain"><a href="#"><?php esc_html_e( 'Privacy policy', 'bytenot-estate' ); ?></a></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:site-title {"level":0,"isLink":false,"className":"site-footer__wordmark"} /-->
</footer>
<!-- /wp:group -->
