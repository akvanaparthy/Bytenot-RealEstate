<?php
/**
 * Hero: full-viewport photo, quick-type tags, title, the search card and the partners
 * marquee. Options come from Property Core's setup; partners are a post type.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_opts    = function_exists( 'property_core_search_options' ) ? property_core_search_options() : array( 'types' => array(), 'intents' => array(), 'locations' => array(), 'budgets' => array(), 'configurations' => array(), 'has_config' => false );
$bytenot_archive = get_post_type_archive_link( 'property' ) ?: home_url( '/' );
$bytenot_partners = get_posts( array( 'post_type' => 'partner', 'posts_per_page' => 30, 'orderby' => 'menu_order', 'order' => 'ASC', 'no_found_rows' => true ) );
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__media" aria-hidden="true">
		<?php bytenot_mod_image( 'hero_image', 'full', array( 'class' => 'hero__bg', 'width' => 1800, 'height' => 1200, 'fetchpriority' => 'high' ) ); ?>
		<div class="hero__shade"></div>
	</div>

	<div class="container hero__inner">
		<div class="hero__content">
			<?php if ( $bytenot_opts['types'] ) : ?>
			<ul class="tags" aria-label="<?php esc_attr_e( 'Quick property type', 'bytenot-estate' ); ?>">
				<?php foreach ( array_slice( $bytenot_opts['types'], 0, 4, true ) as $bytenot_slug => $bytenot_name ) : ?>
				<li><button class="tag" type="button" data-type="<?php echo esc_attr( $bytenot_slug ); ?>"><?php echo esc_html( $bytenot_name ); ?></button></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<div class="hero__row">
				<h1 class="hero__title" id="hero-title"><?php echo bytenot_inline( bytenot_mod( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
				<p class="hero__lede"><?php echo esc_html( bytenot_mod( 'hero_lede' ) ); ?></p>
			</div>
		</div>

		<div class="search-wrap">
		<form class="search<?php echo $bytenot_opts['has_config'] ? '' : ' search--three'; ?>" action="<?php echo esc_url( $bytenot_archive ); ?>" method="get" aria-labelledby="search-title" data-search novalidate>
			<h2 class="search__title" id="search-title"><?php echo esc_html( bytenot_mod( 'search_title' ) ); ?></h2>

			<div class="search__fields">
				<div class="field">
					<label class="field__label" for="f-type"><?php esc_html_e( 'Looking for', 'bytenot-estate' ); ?></label>
					<span class="field__select">
						<select class="field__input" id="f-type" name="type">
							<option value=""><?php esc_html_e( 'Enter type', 'bytenot-estate' ); ?></option>
							<?php foreach ( $bytenot_opts['types'] as $bytenot_slug => $bytenot_name ) : ?>
							<option value="<?php echo esc_attr( $bytenot_slug ); ?>"><?php echo esc_html( $bytenot_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</div>
				<div class="field">
					<label class="field__label" for="f-budget"><?php esc_html_e( 'Budget', 'bytenot-estate' ); ?></label>
					<span class="field__select">
						<select class="field__input" id="f-budget" name="budget">
							<option value=""><?php esc_html_e( 'Price range', 'bytenot-estate' ); ?></option>
							<?php foreach ( $bytenot_opts['budgets'] as $bytenot_slug => $bytenot_name ) : ?>
							<option value="<?php echo esc_attr( $bytenot_slug ); ?>"><?php echo esc_html( $bytenot_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</div>
				<div class="field">
					<label class="field__label" for="f-location"><?php esc_html_e( 'Locations', 'bytenot-estate' ); ?></label>
					<span class="field__select">
						<select class="field__input" id="f-location" name="location">
							<option value=""><?php esc_html_e( 'Location', 'bytenot-estate' ); ?></option>
							<?php foreach ( $bytenot_opts['locations'] as $bytenot_slug => $bytenot_name ) : ?>
							<option value="<?php echo esc_attr( $bytenot_slug ); ?>"><?php echo esc_html( $bytenot_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</div>
				<?php if ( $bytenot_opts['has_config'] ) : ?>
				<div class="field" data-field="bhk">
					<label class="field__label" for="f-bhk"><?php esc_html_e( 'Configuration', 'bytenot-estate' ); ?></label>
					<span class="field__select">
						<select class="field__input" id="f-bhk" name="bhk">
							<option value=""><?php esc_html_e( 'Any', 'bytenot-estate' ); ?></option>
							<?php foreach ( $bytenot_opts['configurations'] as $bytenot_slug => $bytenot_name ) : ?>
							<option value="<?php echo esc_attr( $bytenot_slug ); ?>"><?php echo esc_html( $bytenot_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
				</div>
				<?php endif; ?>
			</div>

			<div class="search__footer">
				<?php if ( count( $bytenot_opts['intents'] ) > 1 ) : ?>
				<fieldset class="filters">
					<legend class="filters__label"><?php esc_html_e( 'Filter:', 'bytenot-estate' ); ?></legend>
					<?php $bytenot_i = 0; foreach ( $bytenot_opts['intents'] as $bytenot_slug => $bytenot_name ) : ?>
					<label class="chip"><input type="radio" name="intent" value="<?php echo esc_attr( $bytenot_slug ); ?>"<?php checked( 0 === $bytenot_i++ ); ?>><span><?php echo esc_html( $bytenot_name ); ?></span></label>
					<?php endforeach; ?>
				</fieldset>
				<?php endif; ?>
				<button class="btn btn--dark" type="submit"><?php esc_html_e( 'Search Properties', 'bytenot-estate' ); ?></button>
			</div>
		</form>
		</div>
	</div>

	<?php if ( $bytenot_partners ) : ?>
	<div class="partners" aria-label="<?php esc_attr_e( 'Our channel partners', 'bytenot-estate' ); ?>">
		<div class="partners__inner">
			<p class="partners__word" aria-hidden="true"><?php echo esc_html( bytenot_mod( 'partners_before' ) ); ?></p>
			<div class="marquee" data-marquee>
				<div class="marquee__inner" data-marquee-inner>
					<?php for ( $bytenot_copy = 0; $bytenot_copy < 2; $bytenot_copy++ ) : ?>
					<ul class="marquee__track"<?php echo $bytenot_copy ? ' aria-hidden="true"' : ''; ?>>
						<?php
						foreach ( $bytenot_partners as $bytenot_partner ) :
							$bytenot_shape = get_post_meta( $bytenot_partner->ID, 'pc_shape', true );
							$bytenot_thumb = get_post_thumbnail_id( $bytenot_partner->ID );
							$bytenot_alt   = $bytenot_copy || 'mark' === $bytenot_shape ? '' : $bytenot_partner->post_title;
							?>
						<li class="partner<?php echo $bytenot_shape ? ' partner--' . esc_attr( $bytenot_shape ) : ''; ?>">
							<?php
							if ( $bytenot_thumb ) {
								echo wp_get_attachment_image( $bytenot_thumb, 'full', false, array_merge( array( 'alt' => $bytenot_alt, 'loading' => 'lazy' ), bytenot_svg_size( $bytenot_thumb ) ) );
							}
							if ( 'mark' === $bytenot_shape || ! $bytenot_thumb ) {
								echo '<span>' . esc_html( $bytenot_partner->post_title ) . '</span>';
							}
							?>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endfor; ?>
				</div>
			</div>
			<p class="partners__word" aria-hidden="true"><?php echo esc_html( bytenot_mod( 'partners_after' ) ); ?></p>
		</div>
	</div>
	<?php endif; ?>
</section>
