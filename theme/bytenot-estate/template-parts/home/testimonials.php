<?php
/**
 * Testimonials: a scroll-snap track of cards from the testimonial post type.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_cards = get_posts( array( 'post_type' => 'testimonial', 'posts_per_page' => 12, 'orderby' => 'menu_order', 'order' => 'ASC', 'no_found_rows' => true ) );
if ( ! $bytenot_cards ) {
	return;
}
?>
<section class="testimonials" id="testimonials" aria-labelledby="testimonials-title">
	<div class="container testimonials__head">
		<div class="testimonials__intro">
			<p class="eyebrow"><?php echo esc_html( bytenot_mod( 'testimonials_eyebrow' ) ); ?></p>
			<h2 class="testimonials__title" id="testimonials-title"><?php echo bytenot_inline( bytenot_mod( 'testimonials_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<p class="testimonials__lede"><?php echo esc_html( bytenot_mod( 'testimonials_lede' ) ); ?></p>
		</div>
		<div class="testimonials__nav" data-t-nav>
			<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Previous testimonial', 'bytenot-estate' ); ?>" data-t-prev>
				<?php bytenot_the_icon( 'arrow-left', 20 ); ?>
			</button>
			<button class="arrow is-active" type="button" aria-label="<?php esc_attr_e( 'Next testimonial', 'bytenot-estate' ); ?>" data-t-next>
				<?php bytenot_the_icon( 'arrow', 20 ); ?>
			</button>
		</div>
	</div>

	<ul class="t-track" data-t-track aria-label="<?php esc_attr_e( 'Client testimonials', 'bytenot-estate' ); ?>" tabindex="0">
		<?php foreach ( $bytenot_cards as $bytenot_card ) : ?>
		<li class="t-card">
			<?php echo get_the_post_thumbnail( $bytenot_card, 'bytenot-portrait', array( 'class' => 't-card__photo', 'alt' => '', 'loading' => 'lazy' ) ); ?>
			<div class="t-card__body">
				<span class="t-card__mark" aria-hidden="true">&ldquo;</span>
				<blockquote class="t-card__quote"><?php echo wp_kses_post( wpautop( do_blocks( $bytenot_card->post_content ) ) ); ?></blockquote>
				<p class="t-card__name"><?php echo esc_html( $bytenot_card->post_title ); ?></p>
				<p class="t-card__role"><?php echo esc_html( get_post_meta( $bytenot_card->ID, 'pc_role', true ) ); ?></p>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
</section>
