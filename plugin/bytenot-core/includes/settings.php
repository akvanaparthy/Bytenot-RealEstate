<?php
/**
 * Site configuration: what this agency deals in, how prices read, where leads go.
 *
 * The theme and the blocks read it through bytenot_core_setting(); nothing about one
 * company is hard-coded anywhere else. The screens live in includes/admin.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults.
 *
 * @return array<string, mixed>
 */
function bytenot_core_default_settings() {
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
		'stamp_duty'   => 6,
		'crm_provider' => 'email',
		'crm_webhook'  => '',
		'preview_mode' => false,
		'preview_code' => '',
	);
}

/**
 * Read one setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function bytenot_core_setting( $key ) {
	$settings = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	return $settings[ $key ] ?? null;
}

/**
 * Budget bands as [ [min, max|null, label], ... ].
 *
 * @return array<int, array{0:int,1:int|null,2:string}>
 */
function bytenot_core_budget_bands() {
	$out = array();
	foreach ( explode( "\n", (string) bytenot_core_setting( 'budget_bands' ) ) as $line ) {
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
 * Sanitise the settings array. Each admin screen posts only its own keys (with a
 * `_screen` marker), so the keys it did not post keep their saved values.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function bytenot_core_sanitize_settings( $input ) {
	$input   = (array) $input;
	$current = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$screens = array(
		'bytenot-setup'        => array( 'types', 'intents', 'budget_bands', 'phone', 'whatsapp', 'email_to', 'rera_number', 'rera_state', 'emi_rate', 'emi_years', 'emi_ltv', 'stamp_duty' ),
		'bytenot-integrations' => array( 'crm_provider', 'crm_webhook' ),
		'bytenot-tools'        => array( 'preview_mode', 'preview_code' ),
	);
	$screen = isset( $input['_screen'] ) ? sanitize_key( $input['_screen'] ) : '';
	$raw    = $current;
	foreach ( isset( $screens[ $screen ] ) ? $screens[ $screen ] : array_keys( bytenot_core_default_settings() ) as $key ) {
		$raw[ $key ] = $input[ $key ] ?? '';
	}
	$vocab  = bytenot_core_vocabularies();
	$types  = array_values( array_intersect( array_map( 'sanitize_key', (array) $raw['types'] ), array_keys( $vocab['property_type'] ) ) );
	$intent = array_values( array_intersect( array_map( 'sanitize_key', (array) $raw['intents'] ), array( 'for-sale', 'for-rent' ) ) );
	return array(
		'types'        => $types ? $types : array_keys( $vocab['property_type'] ),
		'intents'      => $intent ? $intent : array( 'for-sale' ),
		'budget_bands' => sanitize_textarea_field( (string) $raw['budget_bands'] ),
		'phone'        => sanitize_text_field( (string) $raw['phone'] ),
		'whatsapp'     => preg_replace( '/\D+/', '', (string) $raw['whatsapp'] ),
		'email_to'     => sanitize_email( (string) $raw['email_to'] ),
		'rera_number'  => sanitize_text_field( (string) $raw['rera_number'] ),
		'rera_state'   => sanitize_text_field( (string) $raw['rera_state'] ),
		'emi_rate'     => (float) ( $raw['emi_rate'] ?: 8.5 ),
		'emi_years'    => absint( $raw['emi_years'] ?: 20 ),
		'emi_ltv'      => min( 100, absint( $raw['emi_ltv'] ?: 80 ) ),
		'stamp_duty'   => min( 20, (float) ( '' === $raw['stamp_duty'] ? 6 : $raw['stamp_duty'] ) ),
		'crm_provider' => in_array( $raw['crm_provider'], array( 'email', 'webhook' ), true ) ? $raw['crm_provider'] : 'email',
		'crm_webhook'  => esc_url_raw( (string) $raw['crm_webhook'] ),
		'preview_mode' => ! empty( $raw['preview_mode'] ),
		'preview_code' => sanitize_text_field( (string) $raw['preview_code'] ),
	);
}

/**
 * Register the option.
 */
function bytenot_core_settings_init() {
	register_setting(
		'bytenot_core',
		'bytenot_core_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bytenot_core_sanitize_settings',
			'default'           => bytenot_core_default_settings(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'bytenot_core_settings_init' );
