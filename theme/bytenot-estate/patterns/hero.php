<?php
/**
 * Title: Hero with search and partners
 * Slug: bytenot-estate/hero
 * Categories: bytenot-estate-sections, featured
 * Keywords: hero, search, partners
 * Viewport Width: 1440
 */
$bytenot_img = static fn( $f ) => esc_url( get_theme_file_uri( 'assets/images/' . $f ) );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"hero","layout":{"type":"default"},"metadata":{"name":"Hero"}} -->
<section class="wp-block-group alignfull hero">
	<!-- wp:html -->
	<div class="hero__media" aria-hidden="true"><img class="hero__bg" src="<?php echo $bytenot_img( 'demo/hero.jpg' ); ?>" alt="" width="1800" height="1200" fetchpriority="high"><div class="hero__shade"></div></div>
	<!-- /wp:html -->

	<!-- wp:group {"className":"hero__inner container","layout":{"type":"default"}} -->
	<div class="wp-block-group hero__inner container">
		<!-- wp:group {"className":"hero__content","layout":{"type":"default"}} -->
		<div class="wp-block-group hero__content">
			<!-- wp:property-core/search {"part":"tags"} /-->
			<!-- wp:group {"className":"hero__row","layout":{"type":"default"}} -->
			<div class="wp-block-group hero__row">
				<!-- wp:heading {"level":1,"className":"hero__title"} -->
				<h1 class="wp-block-heading hero__title"><?php esc_html_e( 'Home with honor,', 'bytenot-estate' ); ?> <br><?php esc_html_e( 'wealth with vision.', 'bytenot-estate' ); ?></h1>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"hero__lede"} -->
				<p class="hero__lede"><?php esc_html_e( 'We find you a home you will feel proud to own, and investments that grow with you. Verified listings, honest advice, visits arranged in one trip.', 'bytenot-estate' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"search-wrap","layout":{"type":"default"}} -->
		<div class="wp-block-group search-wrap">
			<!-- wp:property-core/search {"part":"form"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"partners","layout":{"type":"default"},"metadata":{"name":"Channel partners"}} -->
	<div class="wp-block-group partners">
		<!-- wp:html -->
		<div class="partners__inner">
			<p class="partners__word" aria-hidden="true"><?php esc_html_e( 'Our', 'bytenot-estate' ); ?></p>
			<div class="marquee" aria-label="<?php esc_attr_e( 'Channel partners', 'bytenot-estate' ); ?>">
				<div class="marquee__inner">
					<ul class="marquee__track">
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/aparna.png' ); ?>" alt="Aparna Constructions" width="423" height="271" loading="lazy"></li>
						<li class="partner partner--wide"><img src="<?php echo $bytenot_img( 'partners/jayabheri.png' ); ?>" alt="Jayabheri" width="324" height="35" loading="lazy"></li>
						<li class="partner"><img src="<?php echo $bytenot_img( 'partners/smr.png' ); ?>" alt="SMR Holdings" width="85" height="40" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/myhome.svg' ); ?>" alt="My Home Group" width="137" height="72" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/prestige.svg' ); ?>" alt="Prestige Group" width="48" height="51" loading="lazy"></li>
						<li class="partner partner--mark"><img src="<?php echo $bytenot_img( 'partners/brigade.png' ); ?>" alt="" width="53" height="54" loading="lazy"><span>Brigade Group</span></li>
						<li class="partner partner--wide"><img src="<?php echo $bytenot_img( 'partners/sobha.png' ); ?>" alt="Sobha" width="1032" height="237" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/rajapushpa.png' ); ?>" alt="Rajapushpa Properties" width="689" height="495" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/ramky.svg' ); ?>" alt="Ramky Estates" width="154" height="90" loading="lazy"></li>
					</ul>
					<ul class="marquee__track" aria-hidden="true">
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/aparna.png' ); ?>" alt="" width="423" height="271" loading="lazy"></li>
						<li class="partner partner--wide"><img src="<?php echo $bytenot_img( 'partners/jayabheri.png' ); ?>" alt="" width="324" height="35" loading="lazy"></li>
						<li class="partner"><img src="<?php echo $bytenot_img( 'partners/smr.png' ); ?>" alt="" width="85" height="40" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/myhome.svg' ); ?>" alt="" width="137" height="72" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/prestige.svg' ); ?>" alt="" width="48" height="51" loading="lazy"></li>
						<li class="partner partner--mark"><img src="<?php echo $bytenot_img( 'partners/brigade.png' ); ?>" alt="" width="53" height="54" loading="lazy"><span>Brigade Group</span></li>
						<li class="partner partner--wide"><img src="<?php echo $bytenot_img( 'partners/sobha.png' ); ?>" alt="" width="1032" height="237" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/rajapushpa.png' ); ?>" alt="" width="689" height="495" loading="lazy"></li>
						<li class="partner partner--tall"><img src="<?php echo $bytenot_img( 'partners/ramky.svg' ); ?>" alt="" width="154" height="90" loading="lazy"></li>
					</ul>
				</div>
			</div>
			<p class="partners__word" aria-hidden="true"><?php esc_html_e( 'Partners', 'bytenot-estate' ); ?></p>
		</div>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
