<?php
/**
 * Properties: intro, filters, results. The server renders the filtered query (so every
 * filter URL is a real page); the script then filters the full set client-side from the
 * JSON island, exactly as the prototype does.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();

$bytenot_opts  = bytenot_core_search_options();
$bytenot_q     = static fn( $k ) => sanitize_text_field( (string) get_query_var( $k ) );
$bytenot_label = $bytenot_q( 'type' ) && isset( $bytenot_opts['types'][ $bytenot_q( 'type' ) ] ) ? bytenot_type_plurals( array( $bytenot_q( 'type' ) ) )[ $bytenot_q( 'type' ) ] : __( 'All properties', 'bytenot-estate' );
if ( $bytenot_q( 'location' ) && isset( $bytenot_opts['locations'][ $bytenot_q( 'location' ) ] ) ) {
	$bytenot_label .= sprintf( /* translators: %s: city. */ __( ' in %s', 'bytenot-estate' ), $bytenot_opts['locations'][ $bytenot_q( 'location' ) ] );
}
if ( 'for-rent' === $bytenot_q( 'intent' ) ) {
	$bytenot_label .= __( ' for rent', 'bytenot-estate' );
}
$bytenot_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="main">

<section class="listing-intro" aria-labelledby="listing-title">
	<div class="container listing-intro__inner">
		<h1 class="listing-intro__title" id="listing-title">
			<span class="listing-intro__line"><?php echo bytenot_inline( bytenot_mod( 'listing_title_1' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php if ( (int) bytenot_mod( 'listing_pill' ) ) : ?><span class="listing-intro__pill" aria-hidden="true"><?php bytenot_mod_image( 'listing_pill', 'bytenot-card', array( 'width' => 120, 'height' => 60 ) ); ?></span><?php endif; ?></span>
			<span class="listing-intro__line"><?php echo bytenot_inline( bytenot_mod( 'listing_title_2' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</h1>
		<p class="listing-intro__lede"><?php echo esc_html( bytenot_mod( 'listing_lede' ) ); ?></p>
	</div>
</section>

<section class="listing" aria-labelledby="results-title">
	<div class="container">
		<script type="application/json" data-listings><?php echo wp_json_encode( bytenot_listings() ); ?></script>
		<form class="filters-bar<?php echo $bytenot_opts['has_config'] ? '' : ' filters-bar--four'; ?>" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" data-listing-filters aria-label="<?php esc_attr_e( 'Filter properties', 'bytenot-estate' ); ?>">
			<label class="filters-bar__field">
				<span class="filters-bar__label"><?php esc_html_e( 'Looking for', 'bytenot-estate' ); ?></span>
				<span class="field__select">
					<select class="filters-bar__select" name="type">
						<option value=""><?php esc_html_e( 'Any type', 'bytenot-estate' ); ?></option>
						<?php foreach ( $bytenot_opts['types'] as $bytenot_slug => $bytenot_name ) : ?>
						<option value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php selected( $bytenot_q( 'type' ), $bytenot_slug ); ?>><?php echo esc_html( $bytenot_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</label>
			<label class="filters-bar__field">
				<span class="filters-bar__label"><?php esc_html_e( 'Location', 'bytenot-estate' ); ?></span>
				<span class="field__select">
					<select class="filters-bar__select" name="location">
						<option value=""><?php esc_html_e( 'Anywhere', 'bytenot-estate' ); ?></option>
						<?php foreach ( $bytenot_opts['locations'] as $bytenot_slug => $bytenot_name ) : ?>
						<option value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php selected( $bytenot_q( 'location' ), $bytenot_slug ); ?>><?php echo esc_html( $bytenot_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</label>
			<label class="filters-bar__field">
				<span class="filters-bar__label"><?php esc_html_e( 'Budget', 'bytenot-estate' ); ?></span>
				<span class="field__select">
					<select class="filters-bar__select" name="budget">
						<option value=""><?php esc_html_e( 'Any budget', 'bytenot-estate' ); ?></option>
						<?php foreach ( $bytenot_opts['budgets'] as $bytenot_slug => $bytenot_name ) : ?>
						<option value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php selected( $bytenot_q( 'budget' ), $bytenot_slug ); ?>><?php echo esc_html( $bytenot_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</label>
			<?php if ( $bytenot_opts['has_config'] ) : ?>
			<label class="filters-bar__field" data-field="bhk">
				<span class="filters-bar__label"><?php esc_html_e( 'Configuration', 'bytenot-estate' ); ?></span>
				<span class="field__select">
					<select class="filters-bar__select" name="bhk">
						<option value=""><?php esc_html_e( 'Any', 'bytenot-estate' ); ?></option>
						<?php foreach ( $bytenot_opts['configurations'] as $bytenot_slug => $bytenot_name ) : ?>
						<option value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php selected( $bytenot_q( 'bhk' ), $bytenot_slug ); ?>><?php echo esc_html( $bytenot_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</label>
			<?php endif; ?>
			<label class="filters-bar__field">
				<span class="filters-bar__label"><?php esc_html_e( 'Status', 'bytenot-estate' ); ?></span>
				<span class="field__select">
					<select class="filters-bar__select" name="intent">
						<option value=""><?php esc_html_e( 'Buy or rent', 'bytenot-estate' ); ?></option>
						<?php foreach ( $bytenot_opts['intents'] as $bytenot_slug => $bytenot_name ) : ?>
						<option value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php selected( $bytenot_q( 'intent' ), $bytenot_slug ); ?>><?php echo esc_html( $bytenot_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</label>
			<noscript><button class="btn btn--dark btn--sm" type="submit"><?php esc_html_e( 'Apply', 'bytenot-estate' ); ?></button></noscript>
		</form>

		<div class="results-head">
			<h2 class="results-title" id="results-title"><span data-results-label><?php echo esc_html( $bytenot_label ); ?></span> <small data-results-count><?php echo esc_html( sprintf( _n( '(%d result)', '(%d results)', $bytenot_found, 'bytenot-estate' ), $bytenot_found ) ); ?></small></h2>
			<div class="results-sort">
				<label class="results-sort__label" for="sort"><?php esc_html_e( 'Sort by', 'bytenot-estate' ); ?></label>
				<span class="field__select">
					<select class="results-sort__select" id="sort" name="sort" data-sort>
						<option value="featured"<?php selected( $bytenot_q( 'sort' ), 'featured' ); ?>><?php esc_html_e( 'Featured', 'bytenot-estate' ); ?></option>
						<option value="price-asc"<?php selected( $bytenot_q( 'sort' ), 'price-asc' ); ?>><?php esc_html_e( 'Price: low to high', 'bytenot-estate' ); ?></option>
						<option value="price-desc"<?php selected( $bytenot_q( 'sort' ), 'price-desc' ); ?>><?php esc_html_e( 'Price: high to low', 'bytenot-estate' ); ?></option>
						<option value="newest"<?php selected( $bytenot_q( 'sort' ), 'newest' ); ?>><?php esc_html_e( 'Newest', 'bytenot-estate' ); ?></option>
					</select>
				</span>
			</div>
		</div>

		<ul class="cards" data-cards aria-live="polite">
			<?php
			while ( have_posts() ) :
				the_post();
				bytenot_card( bytenot_listing( get_the_ID() ) );
			endwhile;
			?>
		</ul>

		<p class="results-empty" data-results-empty<?php echo have_posts() ? ' hidden' : ''; ?>><?php esc_html_e( 'No properties match these filters yet.', 'bytenot-estate' ); ?> <button type="button" class="link-btn" data-results-reset><?php esc_html_e( 'Clear filters', 'bytenot-estate' ); ?></button> <?php esc_html_e( "or tell us what you're looking for.", 'bytenot-estate' ); ?></p>

		<div class="results-more">
			<button class="btn btn--outline" type="button" data-load-more hidden><?php esc_html_e( 'Load more', 'bytenot-estate' ); ?></button>
			<noscript><?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'bytenot-estate' ), 'next_text' => __( 'Next', 'bytenot-estate' ) ) ); ?></noscript>
		</div>
	</div>
</section>

</main>

<template id="card-template">
	<?php bytenot_card( null ); ?>
</template>

<?php
get_footer();
