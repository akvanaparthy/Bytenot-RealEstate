<?php
/**
 * CRM provider contract. Every lead is stored locally first; providers only push.
 *
 * @package property-core
 */

defined( 'ABSPATH' ) || exit;

interface Property_Core_CRM_Provider {
	/**
	 * Push one lead.
	 *
	 * @param array<string, mixed> $lead Lead payload (see property_core_lead_payload()).
	 * @return true|WP_Error True on success, WP_Error with a message otherwise.
	 */
	public function push_lead( array $lead );

	/**
	 * Is the provider configured and reachable?
	 *
	 * @return bool
	 */
	public function test();
}
