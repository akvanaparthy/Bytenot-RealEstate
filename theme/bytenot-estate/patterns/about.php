<?php
/**
 * Title: About statement
 * Slug: bytenot-estate/about
 * Categories: bytenot-estate-sections
 * Keywords: about, statement, reveal
 * Viewport Width: 1440
 */
$bytenot_img = static fn( $f ) => esc_url( get_theme_file_uri( 'assets/images/demo/' . $f ) );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"about","layout":{"type":"default"},"metadata":{"name":"About statement"}} -->
<section class="wp-block-group alignfull about" id="about" data-about>
	<!-- wp:html -->
	<div class="about__stage">
		<div class="about__float about__float--1" data-float data-in="0.04" data-out="0.4" aria-hidden="true"><div class="about__float-in"><img src="<?php echo $bytenot_img( 'p1.jpg' ); ?>" alt="" width="560" height="560" loading="lazy"></div></div>
		<div class="about__float about__float--2" data-float data-in="0.36" data-out="0.72" aria-hidden="true"><div class="about__float-in"><img src="<?php echo $bytenot_img( 'p5.jpg' ); ?>" alt="" width="560" height="560" loading="lazy"></div></div>
		<div class="about__float about__float--3" data-float data-in="0.64" data-out="0.92" aria-hidden="true"><div class="about__float-in"><img src="<?php echo $bytenot_img( 'p8.jpg' ); ?>" alt="" width="560" height="560" loading="lazy"></div></div>
		<div class="container about__inner">
			<h2 class="visually-hidden"><?php esc_html_e( 'About us', 'bytenot-estate' ); ?></h2>
			<p class="about__statement" data-reveal-words>
				<?php esc_html_e( 'We are a real-estate firm helping individuals, families', 'bytenot-estate' ); ?>
				<span class="about__pill"><span class="about__pill-img"><img src="<?php echo $bytenot_img( 't2.jpg' ); ?>" alt="" width="260" height="176" loading="lazy"></span></span>
				<?php esc_html_e( 'and investors find the right home', 'bytenot-estate' ); ?>
				<span class="about__pill"><span class="about__pill-img"><img src="<?php echo $bytenot_img( 'p3.jpg' ); ?>" alt="" width="260" height="176" loading="lazy"></span></span>
				<?php esc_html_e( 'with confidence. Buying, selling, renting or investing, we walk every step with you, from the first viewing to the final signature, with deep local knowledge', 'bytenot-estate' ); ?>
				<span class="about__pill"><span class="about__pill-img"><img src="<?php echo $bytenot_img( 'p7.jpg' ); ?>" alt="" width="260" height="176" loading="lazy"></span></span>
				<?php esc_html_e( 'and a client-first approach, so every property journey is smooth, transparent and successful.', 'bytenot-estate' ); ?>
			</p>
		</div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->
