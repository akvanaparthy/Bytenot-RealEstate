<?php
/**
 * Title: Featured properties
 * Slug: bytenot-estate/featured
 * Categories: bytenot-estate-sections, bytenot-estate-property
 * Keywords: featured, properties, spotlight
 * Viewport Width: 1440
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"featured","layout":{"type":"default"},"metadata":{"name":"Featured properties"}} -->
<section class="wp-block-group alignfull featured" id="properties">
	<!-- wp:group {"className":"container","layout":{"type":"default"}} -->
	<div class="wp-block-group container">
		<!-- wp:group {"className":"featured__head","layout":{"type":"default"}} -->
		<div class="wp-block-group featured__head">
			<!-- wp:group {"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"className":"eyebrow"} -->
				<p class="eyebrow"><?php esc_html_e( 'Featured this week', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"className":"featured__title"} -->
				<h2 class="wp-block-heading featured__title"><?php esc_html_e( 'Hand-picked homes,', 'bytenot-estate' ); ?> <br><em><?php esc_html_e( 'ready to visit.', 'bytenot-estate' ); ?></em></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
			<!-- wp:html -->
			<a class="video" href="#properties">
				<span class="video__thumb"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/demo/video-thumb.jpg' ) ); ?>" alt="" width="136" height="78" loading="lazy"><span class="video__play" aria-hidden="true"></span></span>
				<span class="video__caption"><?php esc_html_e( 'How we pick: every home here was walked through by an advisor before it was listed.', 'bytenot-estate' ); ?></span>
			</a>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->

		<!-- wp:property-core/featured {"count":4} /-->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
