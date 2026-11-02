<?php
/**
 * Title: Testimonials
 * Slug: bytenot-estate/testimonials
 * Categories: bytenot-estate-sections
 * Keywords: testimonials, reviews, clients
 * Viewport Width: 1440
 */
$bytenot_img = static fn( $f ) => esc_url( get_theme_file_uri( 'assets/images/demo/' . $f ) );
$bytenot_cards = array(
	array( 't1.jpg', __( 'They kept us informed at every step, from the first site visit to registration. Their calm, honest approach made a stressful decision feel easy.', 'bytenot-estate' ), __( 'Priya & Arjun', 'bytenot-estate' ), __( 'Bought a 3 BHK apartment', 'bytenot-estate' ) ),
	array( 't2.jpg', __( 'As a first-time buyer I had a hundred questions. The team answered every one patiently and negotiated a better price than I expected.', 'bytenot-estate' ), __( 'Sneha', 'bytenot-estate' ), __( 'Bought a 2 BHK apartment', 'bytenot-estate' ) ),
	array( 't3.jpg', __( 'I wanted a long-term investment, not just a house. They walked me through the approvals, the numbers and the risks, and never once pushed.', 'bytenot-estate' ), __( 'Karthik', 'bytenot-estate' ), __( 'Invested in a villa plot', 'bytenot-estate' ) ),
	array( 't4.jpg', __( 'We bought our home from another city without a single wasted trip. Video walkthroughs, paperwork handled, keys ready the day we landed.', 'bytenot-estate' ), __( 'Rahul & Meera', 'bytenot-estate' ), __( 'Relocated and bought a home', 'bytenot-estate' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"testimonials","layout":{"type":"default"},"metadata":{"name":"Testimonials"}} -->
<section class="wp-block-group alignfull testimonials" id="testimonials">
	<!-- wp:group {"className":"container testimonials__head","layout":{"type":"default"}} -->
	<div class="wp-block-group container testimonials__head">
		<!-- wp:group {"className":"testimonials__intro","layout":{"type":"default"}} -->
		<div class="wp-block-group testimonials__intro">
			<!-- wp:paragraph {"className":"eyebrow"} -->
			<p class="eyebrow"><?php esc_html_e( 'Testimonials', 'bytenot-estate' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"className":"testimonials__title"} -->
			<h2 class="wp-block-heading testimonials__title"><?php esc_html_e( 'What our clients are saying', 'bytenot-estate' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"testimonials__lede"} -->
			<p class="testimonials__lede"><?php esc_html_e( 'Honest advice, clear paperwork and a home that feels right. Here is what buyers and investors say about working with us.', 'bytenot-estate' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:html -->
		<div class="testimonials__nav">
			<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'bytenot-estate' ); ?>" data-t-prev><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg></button>
			<button class="arrow is-active" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'bytenot-estate' ); ?>" data-t-next><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
		</div>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"t-track","layout":{"type":"default"}} -->
	<div class="wp-block-group t-track" data-t-track tabindex="0" aria-label="<?php esc_attr_e( 'Client testimonials', 'bytenot-estate' ); ?>">
		<?php foreach ( $bytenot_cards as $bytenot_card ) : ?>
		<!-- wp:group {"className":"t-card","layout":{"type":"default"}} -->
		<div class="wp-block-group t-card">
			<!-- wp:image {"className":"t-card__photo","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large t-card__photo"><img src="<?php echo $bytenot_img( $bytenot_card[0] ); ?>" alt="" width="600" height="700" loading="lazy"/></figure>
			<!-- /wp:image -->
			<!-- wp:group {"className":"t-card__body","layout":{"type":"default"}} -->
			<div class="wp-block-group t-card__body">
				<!-- wp:paragraph {"className":"t-card__mark"} -->
				<p class="t-card__mark" aria-hidden="true">&ldquo;</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"t-card__quote"} -->
				<p class="t-card__quote"><?php echo esc_html( $bytenot_card[1] ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"t-card__name"} -->
				<p class="t-card__name"><?php echo esc_html( $bytenot_card[2] ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"t-card__role"} -->
				<p class="t-card__role"><?php echo esc_html( $bytenot_card[3] ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
