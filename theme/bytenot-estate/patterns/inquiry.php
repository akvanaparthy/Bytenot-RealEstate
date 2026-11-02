<?php
/**
 * Title: Inquiry band
 * Slug: bytenot-estate/inquiry
 * Categories: bytenot-estate-sections
 * Keywords: inquiry, contact, lead, form
 * Viewport Width: 1440
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"inquiry","layout":{"type":"default"},"metadata":{"name":"Inquiry band"}} -->
<section class="wp-block-group alignfull inquiry" id="inquiry">
	<!-- wp:group {"className":"inquiry__frame","layout":{"type":"default"}} -->
	<div class="wp-block-group inquiry__frame" data-settle>
		<!-- wp:html -->
		<img class="inquiry__bg" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/demo/p7.jpg' ) ); ?>" alt="" width="1600" height="1000" loading="lazy"><div class="inquiry__shade"></div>
		<!-- /wp:html -->
		<!-- wp:heading {"className":"inquiry__title"} -->
		<h2 class="wp-block-heading inquiry__title"><?php esc_html_e( 'Property inquiry', 'bytenot-estate' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:group {"className":"inquiry__card","layout":{"type":"default"}} -->
		<div class="wp-block-group inquiry__card">
			<!-- wp:group {"className":"inquiry__glass","layout":{"type":"default"}} -->
			<div class="wp-block-group inquiry__glass">
				<!-- wp:paragraph {"className":"inquiry__glass-text"} -->
				<p class="inquiry__glass-text"><?php esc_html_e( 'Discover', 'bytenot-estate' ); ?> <em><?php esc_html_e( 'your ideal', 'bytenot-estate' ); ?></em> <?php esc_html_e( 'place', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:property-core/lead-form {"variant":"inquiry","className":"inquiry__form"} /-->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"inquiry__tag"} -->
		<p class="inquiry__tag" aria-hidden="true"><?php esc_html_e( "Let's find", 'bytenot-estate' ); ?> <em><?php esc_html_e( 'your perfect', 'bytenot-estate' ); ?></em> <?php esc_html_e( 'home', 'bytenot-estate' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
