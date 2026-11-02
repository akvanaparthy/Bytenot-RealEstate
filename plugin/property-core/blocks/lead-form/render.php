<?php
/**
 * Lead form: inquiry (name, email, phone, message) or visit (name, phone, date, consent).
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

$pc_variant  = 'visit' === ( $attributes['variant'] ?? '' ) ? 'visit' : 'inquiry';
$pc_post_id  = (int) ( $block->context['postId'] ?? 0 );
$pc_property = $pc_post_id && 'property' === get_post_type( $pc_post_id ) ? $pc_post_id : 0;
$pc_uid      = wp_unique_id( 'lead-' );
$pc_lede     = $attributes['lede'] ?: ( 'visit' === $pc_variant
	? __( 'Free, no obligation. An advisor confirms within the hour and meets you at the gate.', 'property-core' )
	: __( 'Tell us what you are looking for, and we will guide you to the best options.', 'property-core' ) );
$pc_class    = 'visit' === $pc_variant ? 'lead-form lead-form--visit visit__form' : 'lead-form lead-form--inquiry';
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => $pc_class ) ); ?> method="post" action="<?php echo esc_url( rest_url( 'property-core/v1/leads' ) ); ?>" data-lead-form data-endpoint="<?php echo esc_url( rest_url( 'property-core/v1/leads' ) ); ?>" data-form="<?php echo esc_attr( $pc_variant ); ?>" data-property="<?php echo esc_attr( $pc_property ); ?>" novalidate>
	<?php if ( 'inquiry' === $pc_variant ) : ?>
	<p class="inquiry__lede"><?php echo esc_html( $pc_lede ); ?></p>
	<label class="uf"><span class="uf__label"><?php esc_html_e( 'Name', 'property-core' ); ?></span><input class="uf__input" type="text" name="name" autocomplete="name" placeholder="<?php esc_attr_e( 'Your name', 'property-core' ); ?>" required></label>
	<label class="uf"><span class="uf__label"><?php esc_html_e( 'Email', 'property-core' ); ?></span><input class="uf__input" type="email" name="email" autocomplete="email" placeholder="you@example.com"></label>
	<label class="uf"><span class="uf__label"><?php esc_html_e( 'Phone', 'property-core' ); ?></span><span class="uf__phone"><span class="uf__prefix">+91</span><input class="uf__input" type="tel" name="phone" inputmode="numeric" autocomplete="tel-national" placeholder="98765 43210" required></span></label>
	<label class="uf"><span class="uf__label"><?php esc_html_e( 'Message', 'property-core' ); ?></span><textarea class="uf__input uf__input--area" name="message" rows="3" placeholder="<?php esc_attr_e( 'Tell us about the home you are looking for', 'property-core' ); ?>"></textarea></label>
	<?php else : ?>
	<div class="visit__grid">
		<label class="vf"><span class="vf__label"><?php esc_html_e( 'Your name', 'property-core' ); ?></span><input class="vf__input" type="text" name="name" autocomplete="name" required></label>
		<label class="vf"><span class="vf__label"><?php esc_html_e( 'Phone', 'property-core' ); ?></span><span class="vf__phone"><span class="vf__prefix">+91</span><input class="vf__input" type="tel" name="phone" inputmode="numeric" autocomplete="tel-national" required></span></label>
		<label class="vf"><span class="vf__label"><?php esc_html_e( 'Preferred date', 'property-core' ); ?></span><input class="vf__input" type="date" name="preferred_date"></label>
		<button class="wp-block-button__link wp-element-button is-accent visit__submit" type="submit"><?php esc_html_e( 'Request a visit', 'property-core' ); ?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
	</div>
	<?php endif; ?>
	<label class="consent<?php echo 'visit' === $pc_variant ? ' visit__consent' : ' lead-form__consent'; ?>"><input type="checkbox" name="consent" value="1" required> <span><?php esc_html_e( 'You may contact me about this. No spam, and I can opt out any time.', 'property-core' ); ?></span></label>
	<label class="lead-form__hp" aria-hidden="true"><?php esc_html_e( 'Website', 'property-core' ); ?><input type="text" name="website" tabindex="-1" autocomplete="off"></label>
	<input type="hidden" name="started" value="<?php echo esc_attr( time() ); ?>">
	<?php if ( 'inquiry' === $pc_variant ) : ?>
	<button class="wp-block-button__link wp-element-button inquiry__submit" type="submit"><?php esc_html_e( 'Submit', 'property-core' ); ?></button>
	<?php endif; ?>
	<p class="lead-form__status" id="<?php echo esc_attr( $pc_uid ); ?>-status" role="status" aria-live="polite"></p>
</form>
