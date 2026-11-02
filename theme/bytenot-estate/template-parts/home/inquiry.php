<?php
/**
 * Inquiry: the lead form over a photo. Posts to Property Core's leads endpoint.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="inquiry" id="inquiry" aria-labelledby="inquiry-title">
	<div class="inquiry__frame">
		<?php bytenot_mod_image( 'inquiry_image', 'full', array( 'class' => 'inquiry__bg', 'width' => 2000, 'height' => 1333, 'loading' => 'lazy' ) ); ?>
		<div class="inquiry__shade" aria-hidden="true"></div>
		<h2 class="inquiry__title" id="inquiry-title"><?php echo esc_html( bytenot_mod( 'inquiry_title' ) ); ?></h2>
		<div class="inquiry__card">
			<div class="inquiry__glass">
				<p class="inquiry__glass-text"><?php echo bytenot_inline( bytenot_mod( 'inquiry_glass' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<form class="inquiry__form" method="post" action="<?php echo esc_url( rest_url( 'property-core/v1/leads' ) ); ?>" data-inquiry data-lead="inquiry">
				<p class="inquiry__lede"><?php echo esc_html( bytenot_mod( 'inquiry_lede' ) ); ?></p>
				<label class="uf"><span class="uf__label"><?php esc_html_e( 'Name', 'bytenot-estate' ); ?></span><input class="uf__input" type="text" name="name" placeholder="<?php esc_attr_e( 'Jane Smith', 'bytenot-estate' ); ?>" autocomplete="name" required></label>
				<label class="uf"><span class="uf__label"><?php esc_html_e( 'Email', 'bytenot-estate' ); ?></span><input class="uf__input" type="email" name="email" placeholder="jane@example.com" autocomplete="email" required></label>
				<label class="uf"><span class="uf__label"><?php esc_html_e( 'Phone', 'bytenot-estate' ); ?></span><span class="uf__phone"><span class="uf__prefix">+91</span><input class="uf__input" type="tel" name="phone" placeholder="98765 43210" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" autocomplete="tel-national" required></span></label>
				<label class="uf"><span class="uf__label"><?php esc_html_e( 'Message', 'bytenot-estate' ); ?></span><textarea class="uf__input uf__input--area" name="message" rows="3" placeholder="<?php esc_attr_e( "Tell us about the home you're looking for", 'bytenot-estate' ); ?>"></textarea></label>
				<?php bytenot_lead_fields(); ?>
				<button class="btn btn--dark inquiry__submit" type="submit"><?php esc_html_e( 'Submit', 'bytenot-estate' ); ?></button>
			</form>
		</div>
		<p class="inquiry__tag" aria-hidden="true"><?php echo bytenot_inline( bytenot_mod( 'inquiry_tag' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	</div>
</section>
