<?php
/**
 * Search block: the quick-type tags (part=tags) or the form (part=form).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

$pc_opts = bytenot_core_search_options();
$pc_part = $attributes['part'] ?? 'form';

if ( 'tags' === $pc_part ) {
	if ( ! $pc_opts['types'] ) {
		return;
	}
	?>
	<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'tags', 'aria-label' => esc_attr__( 'Quick property type', 'bytenot-core' ) ) ); ?> data-search-tags>
		<?php foreach ( array_slice( $pc_opts['types'], 0, 4, true ) as $pc_slug => $pc_name ) : ?>
			<li><button class="tag" type="button" data-type="<?php echo esc_attr( $pc_slug ); ?>"><?php echo esc_html( $pc_name ); ?></button></li>
		<?php endforeach; ?>
	</ul>
	<?php
	return;
}

$pc_action  = get_post_type_archive_link( 'property' );
$pc_title   = $attributes['title'] ? $attributes['title'] : __( 'Find your perfect home', 'bytenot-core' );
$pc_classes = 'search' . ( $pc_opts['has_config'] ? '' : ' search--three' );
$pc_intents = $pc_opts['intents'];
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => $pc_classes ) ); ?> action="<?php echo esc_url( $pc_action ); ?>" method="get" data-search novalidate>
	<h2 class="search__title"><?php echo esc_html( $pc_title ); ?></h2>
	<div class="search__fields">
		<div class="field">
			<label class="field__label" for="f-type"><?php esc_html_e( 'Looking for', 'bytenot-core' ); ?></label>
			<span class="field__select">
				<select class="field__input" id="f-type" name="type">
					<option value=""><?php esc_html_e( 'Enter type', 'bytenot-core' ); ?></option>
					<?php foreach ( $pc_opts['types'] as $pc_slug => $pc_name ) : ?>
						<option value="<?php echo esc_attr( $pc_slug ); ?>"><?php echo esc_html( $pc_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
		<div class="field">
			<label class="field__label" for="f-budget"><?php esc_html_e( 'Budget', 'bytenot-core' ); ?></label>
			<span class="field__select">
				<select class="field__input" id="f-budget" name="budget">
					<option value=""><?php esc_html_e( 'Price range', 'bytenot-core' ); ?></option>
					<?php foreach ( $pc_opts['budgets'] as $pc_slug => $pc_name ) : ?>
						<option value="<?php echo esc_attr( $pc_slug ); ?>"><?php echo esc_html( $pc_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
		<div class="field">
			<label class="field__label" for="f-location"><?php esc_html_e( 'Locations', 'bytenot-core' ); ?></label>
			<span class="field__select">
				<select class="field__input" id="f-location" name="location">
					<option value=""><?php esc_html_e( 'Location', 'bytenot-core' ); ?></option>
					<?php foreach ( $pc_opts['locations'] as $pc_slug => $pc_name ) : ?>
						<option value="<?php echo esc_attr( $pc_slug ); ?>"><?php echo esc_html( $pc_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
		<?php if ( $pc_opts['has_config'] ) : ?>
		<div class="field" data-field="bhk">
			<label class="field__label" for="f-bhk"><?php esc_html_e( 'Configuration', 'bytenot-core' ); ?></label>
			<span class="field__select">
				<select class="field__input" id="f-bhk" name="bhk">
					<option value=""><?php esc_html_e( 'Any', 'bytenot-core' ); ?></option>
					<?php foreach ( $pc_opts['configurations'] as $pc_slug => $pc_name ) : ?>
						<option value="<?php echo esc_attr( $pc_slug ); ?>"><?php echo esc_html( $pc_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
		<?php endif; ?>
	</div>
	<div class="search__footer">
		<?php if ( count( $pc_intents ) > 1 ) : ?>
		<fieldset class="filters">
			<legend class="filters__label"><?php esc_html_e( 'Filter:', 'bytenot-core' ); ?></legend>
			<?php $pc_i = 0; foreach ( $pc_intents as $pc_slug => $pc_name ) : ?>
				<label class="chip"><input type="radio" name="intent" value="<?php echo esc_attr( $pc_slug ); ?>" <?php checked( 0 === $pc_i++ ); ?>><span><?php echo esc_html( $pc_name ); ?></span></label>
			<?php endforeach; ?>
		</fieldset>
		<?php endif; ?>
		<button class="wp-block-button__link wp-element-button search__submit" type="submit"><?php esc_html_e( 'Search Properties', 'bytenot-core' ); ?></button>
	</div>
</form>
