<?php
/**
 * Site configuration: what this agency deals in, how prices read, where leads go.
 *
 * This is the WordPress home of the prototype's SITE object. The theme and the
 * blocks read it through property_core_setting(); nothing about one company is
 * hard-coded anywhere else.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults.
 *
 * @return array<string, mixed>
 */
function property_core_default_settings() {
	return array(
		'types'        => array( 'apartment', 'villa', 'plot', 'commercial' ),
		'intents'      => array( 'for-sale', 'for-rent' ),
		'budget_bands' => "0-5000000|Under ₹50 L\n5000000-10000000|₹50 L – ₹1 Cr\n10000000-20000000|₹1 Cr – ₹2 Cr\n20000000-|Above ₹2 Cr",
		'phone'        => '',
		'whatsapp'     => '',
		'email_to'     => get_option( 'admin_email' ),
		'rera_number'  => '',
		'rera_state'   => '',
		'emi_rate'     => 8.5,
		'emi_years'    => 20,
		'emi_ltv'      => 80,
		'crm_provider' => 'email',
		'crm_webhook'  => '',
	);
}

/**
 * Read one setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function property_core_setting( $key ) {
	$settings = wp_parse_args( (array) get_option( 'property_core_settings', array() ), property_core_default_settings() );
	return $settings[ $key ] ?? null;
}

/**
 * Budget bands as [ [min, max|null, label], ... ].
 *
 * @return array<int, array{0:int,1:int|null,2:string}>
 */
function property_core_budget_bands() {
	$out = array();
	foreach ( explode( "\n", (string) property_core_setting( 'budget_bands' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line || false === strpos( $line, '|' ) ) {
			continue;
		}
		list( $range, $label ) = array_map( 'trim', explode( '|', $line, 2 ) );
		list( $min, $max )     = array_pad( explode( '-', $range, 2 ), 2, '' );
		$out[]                 = array( (int) $min, '' === $max ? null : (int) $max, $label );
	}
	return $out;
}

/**
 * Sanitise the settings array.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function property_core_sanitize_settings( $input ) {
	$input  = (array) $input;
	$vocab  = property_core_vocabularies();
	$types  = array_values( array_intersect( array_map( 'sanitize_key', (array) ( $input['types'] ?? array() ) ), array_keys( $vocab['property_type'] ) ) );
	$intent = array_values( array_intersect( array_map( 'sanitize_key', (array) ( $input['intents'] ?? array() ) ), array( 'for-sale', 'for-rent' ) ) );
	return array(
		'types'        => $types ? $types : array_keys( $vocab['property_type'] ),
		'intents'      => $intent ? $intent : array( 'for-sale' ),
		'budget_bands' => sanitize_textarea_field( $input['budget_bands'] ?? '' ),
		'phone'        => sanitize_text_field( $input['phone'] ?? '' ),
		'whatsapp'     => preg_replace( '/\D+/', '', (string) ( $input['whatsapp'] ?? '' ) ),
		'email_to'     => sanitize_email( $input['email_to'] ?? '' ),
		'rera_number'  => sanitize_text_field( $input['rera_number'] ?? '' ),
		'rera_state'   => sanitize_text_field( $input['rera_state'] ?? '' ),
		'emi_rate'     => (float) ( $input['emi_rate'] ?? 8.5 ),
		'emi_years'    => absint( $input['emi_years'] ?? 20 ),
		'emi_ltv'      => min( 100, absint( $input['emi_ltv'] ?? 80 ) ),
		'crm_provider' => in_array( $input['crm_provider'] ?? 'email', array( 'email', 'webhook' ), true ) ? $input['crm_provider'] : 'email',
		'crm_webhook'  => esc_url_raw( $input['crm_webhook'] ?? '' ),
	);
}

/**
 * Settings page under Properties.
 */
function property_core_settings_init() {
	register_setting(
		'property_core',
		'property_core_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'property_core_sanitize_settings',
			'default'           => property_core_default_settings(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'property_core_settings_init' );

/**
 * Menu entry.
 */
function property_core_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=property',
		__( 'Property Core setup', 'property-core' ),
		__( 'Setup', 'property-core' ),
		'manage_options',
		'property-core',
		'property_core_settings_page'
	);
}
add_action( 'admin_menu', 'property_core_settings_menu' );

/**
 * Render the settings page.
 */
function property_core_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s     = wp_parse_args( (array) get_option( 'property_core_settings', array() ), property_core_default_settings() );
	$vocab = property_core_vocabularies();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Property Core setup', 'property-core' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'property_core' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Property types offered', 'property-core' ); ?></th>
					<td>
						<?php foreach ( $vocab['property_type'] as $slug => $name ) : ?>
							<label style="display:block"><input type="checkbox" name="property_core_settings[types][]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( in_array( $slug, $s['types'], true ) ); ?>> <?php echo esc_html( $name ); ?></label>
						<?php endforeach; ?>
						<p class="description"><?php esc_html_e( 'Only these appear in search forms and filters. Configuration (BHK) filters show only when apartments or villas are offered.', 'property-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Transactions', 'property-core' ); ?></th>
					<td>
						<label style="display:block"><input type="checkbox" name="property_core_settings[intents][]" value="for-sale" <?php checked( in_array( 'for-sale', $s['intents'], true ) ); ?>> <?php esc_html_e( 'Sale', 'property-core' ); ?></label>
						<label style="display:block"><input type="checkbox" name="property_core_settings[intents][]" value="for-rent" <?php checked( in_array( 'for-rent', $s['intents'], true ) ); ?>> <?php esc_html_e( 'Rent', 'property-core' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-budget"><?php esc_html_e( 'Budget bands', 'property-core' ); ?></label></th>
					<td>
						<textarea id="pc-budget" name="property_core_settings[budget_bands]" rows="5" class="large-text code"><?php echo esc_textarea( $s['budget_bands'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One per line: min-max|Label, in whole rupees. Leave max empty for "and above".', 'property-core' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-phone"><?php esc_html_e( 'Phone', 'property-core' ); ?></label></th>
					<td><input id="pc-phone" type="text" name="property_core_settings[phone]" value="<?php echo esc_attr( $s['phone'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-wa"><?php esc_html_e( 'WhatsApp number (digits, with country code)', 'property-core' ); ?></label></th>
					<td><input id="pc-wa" type="text" name="property_core_settings[whatsapp]" value="<?php echo esc_attr( $s['whatsapp'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-email"><?php esc_html_e( 'Send leads to', 'property-core' ); ?></label></th>
					<td><input id="pc-email" type="email" name="property_core_settings[email_to]" value="<?php echo esc_attr( $s['email_to'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-rera"><?php esc_html_e( 'Agency RERA number', 'property-core' ); ?></label></th>
					<td>
						<input id="pc-rera" type="text" name="property_core_settings[rera_number]" value="<?php echo esc_attr( $s['rera_number'] ); ?>" class="regular-text">
						<input type="text" name="property_core_settings[rera_state]" value="<?php echo esc_attr( $s['rera_state'] ); ?>" placeholder="<?php esc_attr_e( 'State authority', 'property-core' ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'EMI defaults', 'property-core' ); ?></th>
					<td>
						<label><?php esc_html_e( 'Rate %', 'property-core' ); ?> <input type="number" step="0.1" name="property_core_settings[emi_rate]" value="<?php echo esc_attr( $s['emi_rate'] ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'Years', 'property-core' ); ?> <input type="number" name="property_core_settings[emi_years]" value="<?php echo esc_attr( $s['emi_years'] ); ?>" class="small-text"></label>
						<label><?php esc_html_e( 'Loan %', 'property-core' ); ?> <input type="number" name="property_core_settings[emi_ltv]" value="<?php echo esc_attr( $s['emi_ltv'] ); ?>" class="small-text"></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pc-crm"><?php esc_html_e( 'Leads go to', 'property-core' ); ?></label></th>
					<td>
						<select id="pc-crm" name="property_core_settings[crm_provider]">
							<option value="email" <?php selected( $s['crm_provider'], 'email' ); ?>><?php esc_html_e( 'Email only', 'property-core' ); ?></option>
							<option value="webhook" <?php selected( $s['crm_provider'], 'webhook' ); ?>><?php esc_html_e( 'Email + webhook (Zoho Flow, Make, n8n)', 'property-core' ); ?></option>
						</select>
						<input type="url" name="property_core_settings[crm_webhook]" value="<?php echo esc_attr( $s['crm_webhook'] ); ?>" class="regular-text" placeholder="https://">
						<p class="description"><?php esc_html_e( 'Every lead is stored here first and emailed; the webhook is pushed afterwards with retries. A direct Zoho CRM provider plugs into the same interface.', 'property-core' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		<?php do_action( 'property_core_settings_after' ); ?>
	</div>
	<?php
}
