<?php
/**
 * Site configuration: what this agency lists, how prices read, where leads go.
 *
 * The theme reads it through bytenot_core_setting(); nothing about one company is
 * hard-coded anywhere else. The screens live in includes/admin.
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
		'kinds'        => array(),   // per-kind overrides; see kinds.php for the shape and defaults
		'intents'      => array( 'for-sale', 'for-rent' ),
		'budget_bands' => "0-5000000|Under ₹50 L\n5000000-10000000|₹50 L – ₹1 Cr\n10000000-20000000|₹1 Cr – ₹2 Cr\n20000000-|Above ₹2 Cr",
		'phone'        => '',
		'phone_prefix' => '+91',
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
		'bytenot-setup'        => array( 'kinds', 'intents', 'budget_bands', 'phone', 'phone_prefix', 'whatsapp', 'email_to', 'rera_number', 'rera_state', 'emi_rate', 'emi_years', 'emi_ltv', 'stamp_duty' ),
		'bytenot-integrations' => array( 'crm_provider', 'crm_webhook' ),
		'bytenot-tools'        => array( 'preview_mode', 'preview_code' ),
	);
	$screen = isset( $input['_screen'] ) ? sanitize_key( $input['_screen'] ) : '';
	$raw    = $current;
	foreach ( isset( $screens[ $screen ] ) ? $screens[ $screen ] : array_keys( bytenot_core_default_settings() ) as $key ) {
		$raw[ $key ] = $input[ $key ] ?? '';
	}

	// kinds: on/off, labels, URL base (unique), sub-types one per line
	$kinds = array();
	$bases = array();
	foreach ( bytenot_core_kind_defaults() as $slug => $d ) {
		$k    = isset( $raw['kinds'][ $slug ] ) ? (array) $raw['kinds'][ $slug ] : array();
		$base = sanitize_title( (string) ( $k['base'] ?? '' ) ) ?: $d['base'];
		if ( in_array( $base, $bases, true ) ) {
			$base = $d['base'];
		}
		$bases[]        = $base;
		$kinds[ $slug ] = array(
			'on'       => empty( $k['on'] ) ? 0 : 1,
			'singular' => sanitize_text_field( (string) ( $k['singular'] ?? '' ) ),
			'plural'   => sanitize_text_field( (string) ( $k['plural'] ?? '' ) ),
			'base'     => $base,
			'subtypes' => sanitize_textarea_field( (string) ( $k['subtypes'] ?? '' ) ),
		);
	}
	if ( ! array_filter( array_column( $kinds, 'on' ) ) ) {
		$kinds['property']['on'] = 1;
	}

	$intent = array_values( array_intersect( array_map( 'sanitize_key', (array) $raw['intents'] ), array( 'for-sale', 'for-rent' ) ) );
	return array(
		'kinds'        => $kinds,
		'intents'      => $intent ? $intent : array( 'for-sale' ),
		'budget_bands' => sanitize_textarea_field( (string) $raw['budget_bands'] ),
		'phone'        => sanitize_text_field( (string) $raw['phone'] ),
		'phone_prefix' => preg_replace( '/[^\d+]/', '', (string) $raw['phone_prefix'] ),
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

/**
 * After the settings change: sub-type terms exist and the URL bases are routed.
 *
 * @param mixed $old Old value.
 * @param mixed $new New value.
 */
function bytenot_core_settings_saved( $old, $new ) {
	$old_kinds = isset( $old['kinds'] ) ? wp_json_encode( $old['kinds'] ) : '';
	$new_kinds = isset( $new['kinds'] ) ? wp_json_encode( $new['kinds'] ) : '';
	if ( $old_kinds !== $new_kinds ) {
		set_transient( 'bytenot_core_flush', 1, HOUR_IN_SECONDS ); // the kinds cache is per request; flush on the next load
	}
}
add_action( 'update_option_bytenot_core_settings', 'bytenot_core_settings_saved', 10, 2 );

/**
 * Flush rewrite rules and sync sub-type terms once, on the request after a kinds change.
 */
function bytenot_core_flush_after_save() {
	if ( ! get_transient( 'bytenot_core_flush' ) ) {
		return;
	}
	delete_transient( 'bytenot_core_flush' );
	bytenot_core_sync_subtype_terms();
	flush_rewrite_rules();
}
add_action( 'init', 'bytenot_core_flush_after_save', 99 );

/**
 * Options for the search form and the listing filters, from settings and synced terms.
 *
 * @return array<string, mixed>
 */
function bytenot_core_search_options() {
	$kinds = array();
	$types = array();
	foreach ( bytenot_core_kinds( true ) as $slug => $k ) {
		$kinds[ $slug ] = $k['plural'];
		$types[ $slug ] = bytenot_core_subtypes( $slug );
	}
	$intents = array();
	foreach ( (array) bytenot_core_setting( 'intents' ) as $slug ) {
		$intents[ $slug ] = 'for-rent' === $slug ? __( 'Rent', 'bytenot-core' ) : __( 'Buy', 'bytenot-core' );
	}
	// possession facets ride along with the transaction chips, as in the prototype
	$intents['new-launch'] = __( 'New Launch', 'bytenot-core' );
	$intents['resale']     = __( 'Resale', 'bytenot-core' );

	$locations = array();
	$cities    = get_terms( array( 'taxonomy' => 'location', 'parent' => 0, 'hide_empty' => false ) );
	if ( ! is_wp_error( $cities ) ) {
		foreach ( $cities as $city ) {
			$locations[ $city->slug ] = $city->name;
		}
	}
	$budgets = array();
	foreach ( bytenot_core_budget_bands() as $band ) {
		$budgets[ $band[0] . '-' . ( null === $band[1] ? '' : $band[1] ) ] = $band[2];
	}
	$primary = bytenot_core_primary_kind();
	return array(
		'kinds'          => $kinds,
		'primary'        => $primary,
		'types'          => $types[ $primary ] ?? array(),
		'typesByKind'    => $types,
		'intents'        => $intents,
		'locations'      => $locations,
		'budgets'        => $budgets,
		'configurations' => array( '1' => __( '1 BHK', 'bytenot-core' ), '2' => __( '2 BHK', 'bytenot-core' ), '3' => __( '3 BHK', 'bytenot-core' ), '4' => __( '4 BHK+', 'bytenot-core' ) ),
		'has_config'     => isset( $kinds['apartment'] ) || isset( $kinds['property'] ) || isset( $kinds['project'] ),
	);
}
