<?php
/**
 * Setup: what the agency sells, how prices read, who to call.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_setup() {
	$s     = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$vocab = bytenot_core_vocabularies();
	bytenot_core_admin_open( 'bytenot-setup' );
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" class="bn-form">
		<?php settings_fields( 'bytenot_core' ); ?>
		<input type="hidden" name="bytenot_core_settings[_screen]" value="bytenot-setup">
		<div class="bn-grid bn-grid--2">
			<?php
			$chips = '<div class="bn-chips">';
			foreach ( $vocab['property_type'] as $slug => $name ) {
				$chips .= '<label class="bn-chip"><input type="checkbox" name="bytenot_core_settings[types][]" value="' . esc_attr( $slug ) . '" ' . checked( in_array( $slug, (array) $s['types'], true ), true, false ) . '><span>' . esc_html( $name ) . '</span></label>';
			}
			$chips .= '</div>';
			$trans  = '<div class="bn-chips">'
				. '<label class="bn-chip"><input type="checkbox" name="bytenot_core_settings[intents][]" value="for-sale" ' . checked( in_array( 'for-sale', (array) $s['intents'], true ), true, false ) . '><span>' . esc_html__( 'Sale', 'bytenot-core' ) . '</span></label>'
				. '<label class="bn-chip"><input type="checkbox" name="bytenot_core_settings[intents][]" value="for-rent" ' . checked( in_array( 'for-rent', (array) $s['intents'], true ), true, false ) . '><span>' . esc_html__( 'Rent', 'bytenot-core' ) . '</span></label>'
				. '</div>';
			bytenot_core_card(
				__( 'What you sell', 'bytenot-core' ),
				bytenot_core_field( __( 'Property types', 'bytenot-core' ), $chips, __( 'Only these appear in the search and the filters. The configuration (BHK) filter shows when apartments or villas are offered.', 'bytenot-core' ) )
				. bytenot_core_field( __( 'Transactions', 'bytenot-core' ), $trans )
				. bytenot_core_field( __( 'Budget bands', 'bytenot-core' ), '<textarea class="bn-input bn-input--code" id="bn-budget_bands" name="bytenot_core_settings[budget_bands]" rows="5">' . esc_textarea( $s['budget_bands'] ) . '</textarea>', __( 'One per line: min-max|Label, in whole rupees. Leave max empty for "and above".', 'bytenot-core' ), 'bn-budget_bands' )
			);
			bytenot_core_card(
				__( 'Contact and legal', 'bytenot-core' ),
				bytenot_core_field( __( 'Phone', 'bytenot-core' ), bytenot_core_input( 'phone', $s, 'text', array( 'placeholder' => '+91 98765 43210' ) ), __( 'Shown in the footer and on listing pages.', 'bytenot-core' ), 'bn-phone' )
				. bytenot_core_field( __( 'WhatsApp', 'bytenot-core' ), bytenot_core_input( 'whatsapp', $s, 'text', array( 'placeholder' => '919876543210', 'inputmode' => 'numeric' ) ), __( 'Digits only, with the country code.', 'bytenot-core' ), 'bn-whatsapp' )
				. bytenot_core_field( __( 'Send leads to', 'bytenot-core' ), bytenot_core_input( 'email_to', $s, 'email' ), __( 'Every enquiry is emailed here immediately.', 'bytenot-core' ), 'bn-email_to' )
				. bytenot_core_field( __( 'Agency RERA', 'bytenot-core' ), '<div class="bn-row">' . bytenot_core_input( 'rera_number', $s, 'text', array( 'placeholder' => __( 'Number', 'bytenot-core' ) ) ) . bytenot_core_input( 'rera_state', $s, 'text', array( 'placeholder' => __( 'Authority, e.g. Telangana RERA', 'bytenot-core' ) ) ) . '</div>', '', 'bn-rera_number' )
			);
			bytenot_core_card(
				__( 'Numbers on listing pages', 'bytenot-core' ),
				bytenot_core_field( __( 'EMI defaults', 'bytenot-core' ), '<div class="bn-row bn-row--3">'
					. '<label class="bn-unit">' . bytenot_core_input( 'emi_rate', $s, 'number', array( 'step' => '0.1', 'min' => '1', 'max' => '20' ) ) . '<span>' . esc_html__( '% rate', 'bytenot-core' ) . '</span></label>'
					. '<label class="bn-unit">' . bytenot_core_input( 'emi_years', $s, 'number', array( 'min' => '1', 'max' => '40' ) ) . '<span>' . esc_html__( 'years', 'bytenot-core' ) . '</span></label>'
					. '<label class="bn-unit">' . bytenot_core_input( 'emi_ltv', $s, 'number', array( 'min' => '10', 'max' => '100' ) ) . '<span>' . esc_html__( '% financed', 'bytenot-core' ) . '</span></label>'
					. '</div>', __( 'The starting values of the EMI sentence; visitors can change them.', 'bytenot-core' ) )
				. bytenot_core_field( __( 'Stamp duty and registration', 'bytenot-core' ), '<label class="bn-unit bn-unit--short">' . bytenot_core_input( 'stamp_duty', $s, 'number', array( 'step' => '0.1', 'min' => '0', 'max' => '20' ) ) . '<span>%</span></label>', __( 'Of the price; used for the all-in estimate.', 'bytenot-core' ), 'bn-stamp_duty' )
			);
			?>
		</div>
		<div class="bn-save"><button class="bn-btn bn-btn--gold" type="submit"><?php esc_html_e( 'Save setup', 'bytenot-core' ); ?></button></div>
	</form>
	<?php
	bytenot_core_admin_close();
}
