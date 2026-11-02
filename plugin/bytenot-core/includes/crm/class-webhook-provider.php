<?php
/**
 * Generic webhook provider: POSTs the lead as JSON to Zoho Flow, Make, n8n or any
 * endpoint. A direct Zoho CRM provider will implement the same interface.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

class Bytenot_Core_Webhook_Provider implements Bytenot_Core_CRM_Provider {
	/**
	 * POST the lead.
	 *
	 * @param array<string, mixed> $lead Lead payload.
	 * @return true|WP_Error
	 */
	public function push_lead( array $lead ) {
		$url = bytenot_core_setting( 'crm_webhook' );
		if ( ! $url ) {
			return new WP_Error( 'no_webhook', __( 'No webhook URL is configured.', 'bytenot-core' ) );
		}
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 10,
				'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
				'body'    => wp_json_encode( $lead ),
			)
		);
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			/* translators: %d: HTTP status code. */
			return new WP_Error( 'webhook_status', sprintf( __( 'Webhook responded with HTTP %d.', 'bytenot-core' ), $code ) );
		}
		return true;
	}

	/**
	 * Configured?
	 *
	 * @return bool
	 */
	public function test() {
		return (bool) bytenot_core_setting( 'crm_webhook' );
	}
}
