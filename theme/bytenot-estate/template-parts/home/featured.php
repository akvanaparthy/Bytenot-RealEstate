<?php
/**
 * Featured: one spotlight and a rail of the rest. The first item is server-rendered in
 * the stage; the script switches between items from the JSON island.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'property_core_get' ) ) {
	return;
}
$bytenot_items = bytenot_featured_items( 4 );
if ( ! $bytenot_items ) {
	return;
}
$bytenot_first   = $bytenot_items[0];
$bytenot_total   = count( $bytenot_items );
$bytenot_all     = (int) wp_count_posts( 'property' )->publish;
$bytenot_archive = get_post_type_archive_link( 'property' ) ?: home_url( '/' );
$bytenot_video   = bytenot_mod( 'featured_video_url' );
$bytenot_embed   = $bytenot_video ? wp_oembed_get( $bytenot_video, array( 'width' => 1280 ) ) : '';
if ( $bytenot_video && ! $bytenot_embed && preg_match( '/\.(mp4|webm)(\?|$)/i', $bytenot_video ) ) {
	$bytenot_embed = '<video controls playsinline preload="none" src="' . esc_url( $bytenot_video ) . '"></video>';
}
$bytenot_json = array_map(
	static fn( $p ) => array_intersect_key( $p, array_flip( array( 'id', 'title', 'place', 'area', 'line', 'meta', 'priceText', 'psf', 'why', 'stage', 'thumb', 'alt', 'url', 'visit' ) ) ),
	$bytenot_items
);
?>
<section class="featured" id="properties" aria-labelledby="featured-title">
	<div class="container">
		<div class="featured__head">
			<div>
				<p class="eyebrow"><?php echo esc_html( bytenot_mod( 'featured_eyebrow' ) ); ?></p>
				<h2 class="featured__title" id="featured-title"><?php echo bytenot_inline( bytenot_mod( 'featured_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			</div>
			<?php if ( bytenot_mod( 'featured_video_caption' ) || $bytenot_embed ) : ?>
			<button class="video" type="button" data-video-open aria-haspopup="dialog">
				<span class="video__thumb">
					<?php bytenot_mod_image( 'featured_video_thumb', 'bytenot-card', array( 'width' => 136, 'height' => 78, 'loading' => 'lazy' ), $bytenot_first['img'] ); ?>
					<span class="video__play" aria-hidden="true"></span>
				</span>
				<span class="video__caption"><?php echo esc_html( bytenot_mod( 'featured_video_caption' ) ); ?></span>
			</button>
			<?php endif; ?>
		</div>

		<div class="spot" data-featured aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured properties', 'bytenot-estate' ); ?>">
			<script type="application/json" data-featured-items><?php echo wp_json_encode( $bytenot_json ); ?></script>
			<div class="spot__stage">
				<figure class="spot__media" aria-hidden="false">
					<img class="spot__img is-on" data-spot-img src="<?php echo esc_url( $bytenot_first['stage'] ); ?>" alt="<?php echo esc_attr( $bytenot_first['alt'] ); ?>" width="1200" height="800">
					<img class="spot__img" data-spot-img-next src="" alt="" width="1200" height="800" aria-hidden="true">
					<div class="spot__shade"></div>
				</figure>
				<p class="spot__why" data-spot-tab><span class="spot__why-text" data-spot-why><?php echo esc_html( $bytenot_first['why'] ); ?></span></p>
				<div class="spot__info" data-spot-info aria-live="polite">
					<p class="spot__place" data-spot-place><?php echo esc_html( $bytenot_first['place'] ); ?></p>
					<h3 class="spot__name"><a data-spot-link data-spot-title href="<?php echo esc_url( $bytenot_first['url'] ); ?>"><?php echo esc_html( $bytenot_first['title'] ); ?></a></h3>
					<p class="spot__line" data-spot-line><?php echo esc_html( $bytenot_first['line'] ); ?></p>
					<div class="spot__actions">
						<a class="btn btn--accent btn--sm" data-spot-link href="<?php echo esc_url( $bytenot_first['url'] ); ?>"><?php esc_html_e( 'View', 'bytenot-estate' ); ?>
							<?php bytenot_the_icon( 'arrow' ); ?>
						</a>
						<a class="btn btn--glass btn--sm" data-spot-visit href="<?php echo esc_url( $bytenot_first['visit'] ); ?>"><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></a>
					</div>
				</div>
				<p class="spot__price" data-spot-pricebox><strong data-spot-price><?php echo esc_html( $bytenot_first['priceText'] ); ?></strong><span data-spot-psf><?php echo esc_html( $bytenot_first['psf'] ); ?></span></p>
			</div>

			<div class="rail-wrap">
				<ol class="rail" data-rail><div class="rail__hl" data-rail-hl aria-hidden="true"></div>
					<?php foreach ( $bytenot_items as $bytenot_i => $bytenot_p ) : ?>
					<li><button class="rail__row<?php echo 0 === $bytenot_i ? ' is-active' : ''; ?>" type="button" aria-pressed="<?php echo 0 === $bytenot_i ? 'true' : 'false'; ?>">
						<img class="rail__thumb" src="<?php echo esc_url( $bytenot_p['thumb'] ); ?>" alt="" width="72" height="72" loading="lazy">
						<span class="rail__text"><span class="rail__name"><?php echo esc_html( $bytenot_p['title'] ); ?></span><span class="rail__meta"><?php echo esc_html( $bytenot_p['meta'] ); ?></span></span>
						<span class="rail__price"><?php echo esc_html( $bytenot_p['priceText'] ); ?><span class="rail__why"><?php echo esc_html( $bytenot_p['why'] ); ?></span></span></button></li>
					<?php endforeach; ?>
				</ol>
				<div class="rail__foot">
					<div class="spot__nav"<?php echo $bytenot_total < 2 ? ' hidden' : ''; ?>>
						<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Previous property', 'bytenot-estate' ); ?>" data-prev>
							<?php bytenot_the_icon( 'arrow-left', 20 ); ?>
						</button>
						<span class="spot__count" data-spot-count aria-live="polite">01 / <?php echo esc_html( str_pad( (string) $bytenot_total, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Next property', 'bytenot-estate' ); ?>" data-next>
							<?php bytenot_the_icon( 'arrow', 20 ); ?>
						</button>
					</div>
					<a class="link-arrow rail__all" href="<?php echo esc_url( $bytenot_archive ); ?>"><span><?php echo esc_html( sprintf( /* translators: %s: "12 properties". */ __( 'See all %s', 'bytenot-estate' ), sprintf( _n( '%d property', '%d properties', $bytenot_all, 'bytenot-estate' ), $bytenot_all ) ) ); ?></span>
						<?php bytenot_the_icon( 'arrow' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ( bytenot_mod( 'featured_video_caption' ) || $bytenot_embed ) : ?>
<dialog class="modal" id="video-modal" data-video-modal aria-label="<?php esc_attr_e( 'Property tour video', 'bytenot-estate' ); ?>">
	<button class="modal__close" type="button" aria-label="<?php esc_attr_e( 'Close video', 'bytenot-estate' ); ?>" data-video-close>
		<?php bytenot_the_icon( 'close', 22 ); ?>
	</button>
	<div class="modal__frame">
		<?php if ( $bytenot_embed ) : ?>
		<template data-video-embed><?php echo $bytenot_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oEmbed output. ?></template>
		<div class="modal__embed" data-video-slot></div>
		<?php else : ?>
		<div class="modal__placeholder">
			<span class="video__play video__play--lg" aria-hidden="true"></span>
			<p><?php esc_html_e( 'Add a video URL under Customize › Bytenot Estate › Home: featured to play it here.', 'bytenot-estate' ); ?></p>
		</div>
		<?php endif; ?>
	</div>
</dialog>
<?php endif; ?>
