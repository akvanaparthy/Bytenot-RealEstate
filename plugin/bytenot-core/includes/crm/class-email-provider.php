<?php
/**
 * Email provider: always on. Mails the lead to the configured address.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

class Bytenot_Core_Email_Provider implements Bytenot_Core_CRM_Provider {
	/**
	 * Mail the lead.
	 *
	 * @param array<string, mixed> $lead Lead payload.
	 * @return true|WP_Error
	 */
	public function push_lead( array $lead ) {
		$to = bytenot_core_setting( 'email_to' );
		if ( ! is_email( $to ) ) {
			return new WP_Error( 'no_recipient', __( 'No lead email address is configured.', 'bytenot-core' ) );
		}
		$subject = sprintf(
			/* translators: 1: form name, 2: property or site name. */
			__( 'New %1$s enquiry: %2$s', 'bytenot-core' ),
			$lead['form'],
			$lead['property_title'] ?: get_bloginfo( 'name' )
		);
		$lines = array(
			__( 'Name', 'bytenot-core' ) . ': ' . $lead['name'],
			__( 'Phone', 'bytenot-core' ) . ': ' . $lead['phone'],
			__( 'Email', 'bytenot-core' ) . ': ' . $lead['email'],
			__( 'Property', 'bytenot-core' ) . ': ' . $lead['property_title'] . ' ' . $lead['property_url'],
			__( 'Preferred date', 'bytenot-core' ) . ': ' . $lead['preferred_date'],
			__( 'Message', 'bytenot-core' ) . ":\n" . $lead['message'],
			'',
			__( 'Source', 'bytenot-core' ) . ': ' . $lead['source_url'],
			__( 'Attribution', 'bytenot-core' ) . ': ' . $lead['attribution'],
		);
		$sent = wp_mail( $to, $subject, implode( "\n", $lines ) );
		return $sent ? true : new WP_Error( 'mail_failed', __( 'The lead email could not be sent.', 'bytenot-core' ) );
	}

	/**
	 * Configured?
	 *
	 * @return bool
	 */
	public function test() {
		return (bool) is_email( bytenot_core_setting( 'email_to' ) );
	}
}
