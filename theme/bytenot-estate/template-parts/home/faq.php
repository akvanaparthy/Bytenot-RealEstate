<?php
/**
 * FAQ: accordion from the faq post type, with the advisors' faces beside the title.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_faqs = get_posts( array( 'post_type' => 'faq', 'posts_per_page' => 12, 'orderby' => 'menu_order', 'order' => 'ASC', 'no_found_rows' => true ) );
if ( ! $bytenot_faqs ) {
	return;
}
$bytenot_faces = get_posts( array( 'post_type' => 'agent', 'posts_per_page' => 3, 'orderby' => 'menu_order', 'order' => 'ASC', 'no_found_rows' => true, 'meta_key' => '_thumbnail_id' ) );
if ( count( $bytenot_faces ) < 3 ) {
	$bytenot_faces = array_merge( $bytenot_faces, get_posts( array( 'post_type' => 'testimonial', 'posts_per_page' => 3 - count( $bytenot_faces ), 'no_found_rows' => true, 'meta_key' => '_thumbnail_id' ) ) );
}
?>
<section class="faq" id="faq" aria-labelledby="faq-title">
	<div class="container faq__inner">
		<div class="faq__intro">
			<p class="fig-label"><?php echo esc_html( bytenot_mod( 'faq_label' ) ); ?></p>
			<h2 class="faq__title" id="faq-title"><?php echo bytenot_inline( bytenot_mod( 'faq_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<div class="faq__team">
				<?php if ( $bytenot_faces ) : ?>
				<ul class="avatars" aria-hidden="true">
					<?php foreach ( $bytenot_faces as $bytenot_face ) : ?>
					<li><?php echo get_the_post_thumbnail( $bytenot_face, 'thumbnail', array( 'alt' => '', 'width' => 56, 'height' => 56, 'loading' => 'lazy' ) ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				<a class="faq__go" href="#inquiry" aria-label="<?php esc_attr_e( 'Ask our team', 'bytenot-estate' ); ?>">
					<?php bytenot_the_icon( 'arrow', 20 ); ?>
				</a>
				<p class="faq__team-cap"><?php echo bytenot_inline( bytenot_mod( 'faq_caption' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
		</div>
		<div class="faq__list" data-faq>
			<?php foreach ( $bytenot_faqs as $bytenot_i => $bytenot_faq ) : ?>
			<div class="faq__item<?php echo 0 === $bytenot_i ? ' is-open' : ''; ?>">
				<h3 class="faq__h"><button class="faq__q" type="button" id="faq-q<?php echo (int) $bytenot_faq->ID; ?>" aria-expanded="<?php echo 0 === $bytenot_i ? 'true' : 'false'; ?>" aria-controls="faq-a<?php echo (int) $bytenot_faq->ID; ?>"><?php echo esc_html( $bytenot_faq->post_title ); ?><span class="faq__arrow"><?php bytenot_the_icon( 'external', 20 ); ?></span></button></h3>
				<div class="faq__a" id="faq-a<?php echo (int) $bytenot_faq->ID; ?>" role="region" aria-labelledby="faq-q<?php echo (int) $bytenot_faq->ID; ?>"><div class="faq__a-inner"><?php echo wp_kses_post( wpautop( do_blocks( $bytenot_faq->post_content ) ) ); ?></div></div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
