<?php
/**
 * Featured spotlight: server-renders the first listing in the stage and every row in the
 * rail, plus a JSON list the view script uses to switch the stage.
 *
 * @var array $attributes Block attributes.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

$pc_count = max( 1, (int) ( $attributes['count'] ?? 4 ) );
$pc_base  = array(
	'post_type'      => 'property',
	'post_status'    => 'publish',
	'no_found_rows'  => true,
	'tax_query'      => array( array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => bytenot_core_closed_statuses(), 'operator' => 'NOT IN' ) ),
);
$pc_posts = get_posts( array_merge( $pc_base, array( 'posts_per_page' => $pc_count, 'meta_key' => 'pc_featured', 'meta_value' => '1', 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ) );
if ( count( $pc_posts ) < $pc_count ) {
	$pc_more  = get_posts( array_merge( $pc_base, array( 'posts_per_page' => $pc_count - count( $pc_posts ), 'post__not_in' => wp_list_pluck( $pc_posts, 'ID' ) ) ) );
	$pc_posts = array_merge( $pc_posts, $pc_more );
}
if ( ! $pc_posts ) {
	return;
}

$pc_items = array();
foreach ( $pc_posts as $pc_post ) {
	$pc_id    = $pc_post->ID;
	$pc_why   = bytenot_core_get( $pc_id, 'featured_reason' ) ?: __( 'New this week', 'bytenot-core' );
	$pc_label = bytenot_core_get( $pc_id, 'bedroom_label' );
	$pc_items[] = array(
		'id'    => $pc_id,
		'title' => get_the_title( $pc_id ),
		'place' => bytenot_core_format_place( $pc_id ),
		'line'  => bytenot_core_format_line( $pc_id ),
		'meta'  => trim( bytenot_core_format_place( $pc_id ) . ( $pc_label ? ' · ' . $pc_label : ' · ' . bytenot_core_format_area( bytenot_core_get( $pc_id, 'area_primary' ), bytenot_core_get( $pc_id, 'area_unit' ) ?: 'sqft' ) ) ),
		'price' => bytenot_core_binding_value( array( 'key' => 'price' ), (object) array( 'context' => array( 'postId' => $pc_id ) ) ),
		'psf'   => bytenot_core_is_rent( $pc_id ) ? __( 'per month', 'bytenot-core' ) : bytenot_core_format_psf( $pc_id ),
		'why'   => $pc_why,
		'img'   => get_the_post_thumbnail_url( $pc_id, 'large' ) ?: '',
		'thumb' => get_the_post_thumbnail_url( $pc_id, 'thumbnail' ) ?: '',
		'alt'   => get_the_title( $pc_id ) . ', ' . bytenot_core_format_place( $pc_id ),
		'url'   => get_permalink( $pc_id ),
		'visit' => get_permalink( $pc_id ) . '#visit',
	);
}
$pc_first = $pc_items[0];
$pc_total = count( $pc_items );
$pc_arch  = get_post_type_archive_link( 'property' );
$pc_all   = wp_count_posts( 'property' )->publish;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'spot' ) ); ?> data-featured aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured properties', 'bytenot-core' ); ?>">
	<script type="application/json" data-featured-items><?php echo wp_json_encode( $pc_items ); ?></script>
	<div class="spot__stage">
		<figure class="spot__media">
			<img class="spot__img is-on" data-spot-img src="<?php echo esc_url( $pc_first['img'] ); ?>" alt="<?php echo esc_attr( $pc_first['alt'] ); ?>" width="1200" height="800">
			<img class="spot__img" data-spot-img-next src="" alt="" width="1200" height="800" aria-hidden="true">
			<div class="spot__shade"></div>
		</figure>
		<p class="spot__why" data-spot-tab><span class="spot__why-text" data-spot-why><?php echo esc_html( $pc_first['why'] ); ?></span></p>
		<div class="spot__info" data-spot-info aria-live="polite">
			<p class="spot__place" data-spot-place><?php echo esc_html( $pc_first['place'] ); ?></p>
			<h3 class="spot__name"><a data-spot-link data-spot-title href="<?php echo esc_url( $pc_first['url'] ); ?>"><?php echo esc_html( $pc_first['title'] ); ?></a></h3>
			<p class="spot__line" data-spot-line><?php echo esc_html( $pc_first['line'] ); ?></p>
			<div class="spot__actions">
				<a class="wp-block-button__link wp-element-button is-accent btn--sm" data-spot-link href="<?php echo esc_url( $pc_first['url'] ); ?>"><?php esc_html_e( 'View', 'bytenot-core' ); ?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
				<a class="wp-block-button__link wp-element-button is-glass btn--sm" data-spot-visit href="<?php echo esc_url( $pc_first['visit'] ); ?>"><?php esc_html_e( 'Book a visit', 'bytenot-core' ); ?></a>
			</div>
		</div>
		<p class="spot__price" data-spot-pricebox><strong data-spot-price><?php echo esc_html( $pc_first['price'] ); ?></strong><span data-spot-psf><?php echo esc_html( $pc_first['psf'] ); ?></span></p>
	</div>
	<div class="rail-wrap">
		<ol class="rail" data-rail><div class="rail__hl" data-rail-hl aria-hidden="true"></div>
			<?php foreach ( $pc_items as $pc_i => $pc_item ) : ?>
			<li><button class="rail__row<?php echo 0 === $pc_i ? ' is-active' : ''; ?>" type="button" aria-pressed="<?php echo 0 === $pc_i ? 'true' : 'false'; ?>">
				<img class="rail__thumb" src="<?php echo esc_url( $pc_item['thumb'] ); ?>" alt="" width="72" height="72" loading="lazy">
				<span class="rail__text"><span class="rail__name"><?php echo esc_html( $pc_item['title'] ); ?></span><span class="rail__meta"><?php echo esc_html( $pc_item['meta'] ); ?></span></span>
				<span class="rail__price"><?php echo esc_html( $pc_item['price'] ); ?><span class="rail__why"><?php echo esc_html( $pc_item['why'] ); ?></span></span></button></li>
			<?php endforeach; ?>
		</ol>
		<div class="rail__foot">
			<div class="spot__nav"<?php echo $pc_total < 2 ? ' hidden' : ''; ?>>
				<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Previous property', 'bytenot-core' ); ?>" data-prev><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg></button>
				<span class="spot__count" data-spot-count aria-live="polite">01 / <?php echo esc_html( str_pad( (string) $pc_total, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<button class="arrow" type="button" aria-label="<?php esc_attr_e( 'Next property', 'bytenot-core' ); ?>" data-next><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
			</div>
			<a class="link-arrow rail__all" href="<?php echo esc_url( $pc_arch ); ?>"><span><?php
				/* translators: %d: number of published properties. */
				echo esc_html( sprintf( _n( 'See all %d property', 'See all %d properties', (int) $pc_all, 'bytenot-core' ), (int) $pc_all ) );
			?></span> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
		</div>
	</div>
</div>
