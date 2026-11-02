<?php
/**
 * Title: FAQ
 * Slug: bytenot-estate/faq
 * Categories: bytenot-estate-sections
 * Keywords: faq, questions, accordion
 * Viewport Width: 1440
 */
$bytenot_img = static fn( $f ) => esc_url( get_theme_file_uri( 'assets/images/demo/' . $f ) );
$bytenot_faq = array(
	array( __( 'How do I find the right property?', 'bytenot-estate' ), __( 'Start with the filters on the properties page: type, location, budget and configuration. Every listing there is verified by us. If you would rather talk it through, an advisor will shortlist homes against what you actually need and set up visits in one trip.', 'bytenot-estate' ) ),
	array( __( 'How does a site visit work?', 'bytenot-estate' ), __( 'Pick a date on any listing or ask us here. Your advisor confirms within the hour, meets you at the gate and walks you through the home, the project and the neighbourhood. Visits are free and you can see several homes in one morning.', 'bytenot-estate' ) ),
	array( __( 'What is included in a listing?', 'bytenot-estate' ), __( 'Photos, price with the per sq ft rate, carpet and built-up area, floor plan, amenities, the location with nearby landmarks, the RERA registration and the documents we have checked. If something is missing, it is because we could not verify it.', 'bytenot-estate' ) ),
	array( __( 'How do I check a project\'s RERA registration?', 'bytenot-estate' ), __( 'Every listing shows its RERA number with a link to the state portal, where you can see the promoter, approvals, timelines and complaints. We check this before a home goes live and again before you sign anything.', 'bytenot-estate' ) ),
	array( __( 'What are the costs beyond the price?', 'bytenot-estate' ), __( 'Stamp duty and registration, typically 5 to 6 percent. GST applies only to under-construction homes. Add society maintenance, a one-time corpus fund and legal charges. The numbers section on each listing shows an all-in estimate.', 'bytenot-estate' ) ),
	array( __( 'Can you help with a home loan?', 'bytenot-estate' ), __( 'Yes. We work with the major banks and housing finance companies, compare offers for your profile and handle the paperwork, usually with sanction inside two weeks.', 'bytenot-estate' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"faq","layout":{"type":"default"},"metadata":{"name":"FAQ"}} -->
<section class="wp-block-group alignfull faq" id="faq">
	<!-- wp:group {"className":"container faq__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group container faq__inner">
		<!-- wp:group {"className":"faq__intro","layout":{"type":"default"}} -->
		<div class="wp-block-group faq__intro">
			<!-- wp:paragraph {"className":"fig-label"} -->
			<p class="fig-label"><?php esc_html_e( 'Your questions', 'bytenot-estate' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"className":"faq__title"} -->
			<h2 class="wp-block-heading faq__title"><?php esc_html_e( 'Frequently', 'bytenot-estate' ); ?> <br><?php esc_html_e( 'Asked', 'bytenot-estate' ); ?> <br><?php esc_html_e( 'Questions', 'bytenot-estate' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:html -->
			<div class="faq__team">
				<ul class="avatars" aria-hidden="true">
					<li><img src="<?php echo $bytenot_img( 't1.jpg' ); ?>" alt="" width="56" height="56" loading="lazy"></li>
					<li><img src="<?php echo $bytenot_img( 't2.jpg' ); ?>" alt="" width="56" height="56" loading="lazy"></li>
					<li><img src="<?php echo $bytenot_img( 't3.jpg' ); ?>" alt="" width="56" height="56" loading="lazy"></li>
				</ul>
				<a class="faq__go" href="#inquiry" aria-label="<?php esc_attr_e( 'Ask our team', 'bytenot-estate' ); ?>"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
				<p class="faq__team-cap"><?php esc_html_e( 'Our team', 'bytenot-estate' ); ?><br><?php esc_html_e( 'helping you', 'bytenot-estate' ); ?></p>
			</div>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"faq__list","layout":{"type":"default"}} -->
		<div class="wp-block-group faq__list">
			<?php foreach ( $bytenot_faq as $bytenot_i => $bytenot_qa ) : ?>
			<!-- wp:details {"name":"faq","className":"faq__item"<?php echo 0 === $bytenot_i ? ',"showContent":true' : ''; ?>} -->
			<details class="wp-block-details faq__item" name="faq"<?php echo 0 === $bytenot_i ? ' open' : ''; ?>><summary><?php echo esc_html( $bytenot_qa[0] ); ?></summary>
				<!-- wp:paragraph -->
				<p><?php echo esc_html( $bytenot_qa[1] ); ?></p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
