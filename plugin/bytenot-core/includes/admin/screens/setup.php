<?php
/**
 * Setup: what the agency lists, how prices read, who to call.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_setup() {
	$s = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	bytenot_core_admin_open( 'bytenot-setup' );
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" class="bn-form">
		<?php settings_fields( 'bytenot_core' ); ?>
		<input type="hidden" name="bytenot_core_settings[_screen]" value="bytenot-setup">
		<?php
		// what you list: the kinds
		$rows = '';
		foreach ( bytenot_core_kinds() as $slug => $k ) {
			$n     = static fn( $f ) => 'bytenot_core_settings[kinds][' . $slug . '][' . $f . ']';
			$id    = static fn( $f ) => 'bn-kind-' . $slug . '-' . $f;
			$rows .= '<div class="bn-kind' . ( $k['on'] ? ' is-on' : '' ) . '" data-kind-row>'
				. '<label class="bn-switch bn-kind__switch"><input type="checkbox" name="' . esc_attr( $n( 'on' ) ) . '" value="1" ' . checked( $k['on'], true, false ) . '><span class="bn-switch__track" aria-hidden="true"></span><span class="bn-switch__label">' . esc_html( bytenot_core_kind_defaults()[ $slug ]['plural'] ) . '</span></label>'
				. '<p class="bn-kind__what">' . esc_html( bytenot_core_kind_blurb( $slug ) ) . '</p>'
				. '<div class="bn-kind__fields">'
				. '<label class="bn-kind__field"><span>' . esc_html__( 'One', 'bytenot-core' ) . '</span><input class="bn-input" type="text" id="' . esc_attr( $id( 'singular' ) ) . '" name="' . esc_attr( $n( 'singular' ) ) . '" value="' . esc_attr( $k['singular'] ) . '"></label>'
				. '<label class="bn-kind__field"><span>' . esc_html__( 'Many', 'bytenot-core' ) . '</span><input class="bn-input" type="text" name="' . esc_attr( $n( 'plural' ) ) . '" value="' . esc_attr( $k['plural'] ) . '"></label>'
				. '<label class="bn-kind__field"><span>' . esc_html__( 'Address', 'bytenot-core' ) . '</span><span class="bn-kind__base"><span class="bn-muted">/</span><input class="bn-input" type="text" name="' . esc_attr( $n( 'base' ) ) . '" value="' . esc_attr( $k['base'] ) . '"><span class="bn-muted">/</span></span></label>'
				. '<label class="bn-kind__field bn-kind__field--wide"><span>' . esc_html__( 'Types', 'bytenot-core' ) . '</span><textarea class="bn-input" name="' . esc_attr( $n( 'subtypes' ) ) . '" rows="2" placeholder="' . esc_attr__( 'One per line, optional', 'bytenot-core' ) . '">' . esc_textarea( implode( "\n", $k['subtypes'] ) ) . '</textarea></label>'
				. '</div></div>';
		}
		bytenot_core_card(
			__( 'What you list', 'bytenot-core' ),
			$rows,
			__( 'Switch on the kinds you deal in. The first one on is what the menu and the home page point to. Apartments belong to projects and appear on the project\'s page.', 'bytenot-core' ),
			'bn-card--wide'
		);

		$trans = '<div class="bn-chips">';
		foreach ( array( 'for-sale' => __( 'Sale', 'bytenot-core' ), 'for-rent' => __( 'Rent', 'bytenot-core' ) ) as $slug => $label ) {
			$trans .= '<label class="bn-chip"><input type="checkbox" name="bytenot_core_settings[intents][]" value="' . esc_attr( $slug ) . '" ' . checked( in_array( $slug, (array) $s['intents'], true ), true, false ) . '><span>' . esc_html( $label ) . '</span></label>';
		}
		$trans .= '</div>';
		echo '<div class="bn-grid bn-grid--2">';
		bytenot_core_card(
			__( 'Search', 'bytenot-core' ),
			bytenot_core_field( __( 'Transactions', 'bytenot-core' ), $trans, __( 'Buy and rent chips on the home page and the status filter on the listing page.', 'bytenot-core' ) )
			. bytenot_core_field( __( 'Budget bands', 'bytenot-core' ), '<textarea class="bn-input bn-input--code" id="bn-budget_bands" name="bytenot_core_settings[budget_bands]" rows="5">' . esc_textarea( $s['budget_bands'] ) . '</textarea>', __( 'One per line: min-max|Label, in whole rupees. Leave max empty for "and above".', 'bytenot-core' ), 'bn-budget_bands' )
		);
		bytenot_core_card(
			__( 'Contact and legal', 'bytenot-core' ),
			bytenot_core_field( __( 'Phone', 'bytenot-core' ), bytenot_core_input( 'phone', $s, 'text', array( 'placeholder' => '+91 98765 43210' ) ), __( 'Shown in the footer and on listing pages.', 'bytenot-core' ), 'bn-phone' )
			. bytenot_core_field( __( 'Country code', 'bytenot-core' ), bytenot_core_input( 'phone_prefix', $s, 'text', array( 'placeholder' => '+91', 'inputmode' => 'tel' ) ), __( 'Shown before the phone field on the forms and added to every number a visitor types. Leave empty to take full numbers.', 'bytenot-core' ), 'bn-phone_prefix' )
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
		echo '</div>';
		?>
		<div class="bn-save"><button class="bn-btn bn-btn--gold" type="submit"><?php esc_html_e( 'Save setup', 'bytenot-core' ); ?></button></div>
	</form>
	<?php
	bytenot_core_admin_close();
}

/**
 * One line explaining a kind.
 *
 * @param string $slug Kind.
 * @return string
 */
function bytenot_core_kind_blurb( $slug ) {
	$blurbs = array(
		'project'   => __( 'A developer\'s venture: a tower, a gated community, a plotted layout. Carries a price range and its apartments.', 'bytenot-core' ),
		'apartment' => __( 'One unit inside a project, with its own floor, facing, area and price.', 'bytenot-core' ),
		'property'  => __( 'Anything standing on its own: land, a house, a villa, a commercial space.', 'bytenot-core' ),
	);
	return $blurbs[ $slug ] ?? '';
}
