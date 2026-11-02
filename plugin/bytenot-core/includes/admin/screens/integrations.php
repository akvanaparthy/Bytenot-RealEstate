<?php
/**
 * Integrations: where leads go after they are stored here.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_integrations() {
	$s     = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	$stats = bytenot_core_lead_stats();
	bytenot_core_admin_open( 'bytenot-integrations' );
	?>
	<div class="bn-grid bn-grid--2">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" class="bn-form">
			<?php settings_fields( 'bytenot_core' ); ?>
			<input type="hidden" name="bytenot_core_settings[_screen]" value="bytenot-integrations">
			<?php
			$provider = '<div class="bn-radios">'
				. '<label class="bn-radio"><input type="radio" name="bytenot_core_settings[crm_provider]" value="email" ' . checked( $s['crm_provider'], 'email', false ) . '><span><b>' . esc_html__( 'Email only', 'bytenot-core' ) . '</b><small>' . esc_html__( 'Every lead is stored here and emailed to the setup address.', 'bytenot-core' ) . '</small></span></label>'
				. '<label class="bn-radio"><input type="radio" name="bytenot_core_settings[crm_provider]" value="webhook" ' . checked( $s['crm_provider'], 'webhook', false ) . '><span><b>' . esc_html__( 'Email + webhook', 'bytenot-core' ) . '</b><small>' . esc_html__( 'Also POSTed as JSON to a URL: Zoho Flow, Make, n8n, Zapier or your own endpoint. Retried three times.', 'bytenot-core' ) . '</small></span></label>'
				. '</div>';
			bytenot_core_card(
				__( 'Where leads go', 'bytenot-core' ),
				bytenot_core_field( __( 'Provider', 'bytenot-core' ), $provider )
				. bytenot_core_field( __( 'Webhook URL', 'bytenot-core' ), bytenot_core_input( 'crm_webhook', $s, 'url', array( 'placeholder' => 'https://' ) ), __( 'The lead is sent as a JSON object: name, phone, email, message, form, property title, URL and reference, source URL, attribution.', 'bytenot-core' ), 'bn-crm_webhook' )
				. '<div class="bn-save bn-save--inline"><button class="bn-btn bn-btn--gold" type="submit">' . esc_html__( 'Save', 'bytenot-core' ) . '</button></div>'
			);
			?>
		</form>
		<div>
			<?php
			$html = '<div class="bn-minis">'
				. '<div class="bn-mini"><b>' . esc_html( number_format_i18n( $stats['sent'] ) ) . '</b><span>' . esc_html__( 'delivered', 'bytenot-core' ) . '</span></div>'
				. '<div class="bn-mini"><b>' . esc_html( number_format_i18n( $stats['pending'] ) ) . '</b><span>' . esc_html__( 'pending', 'bytenot-core' ) . '</span></div>'
				. '<div class="bn-mini bn-mini--bad"><b>' . esc_html( number_format_i18n( $stats['failed'] ) ) . '</b><span>' . esc_html__( 'failed', 'bytenot-core' ) . '</span></div>'
				. '</div>'
				. '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="bn-actions"><input type="hidden" name="action" value="bytenot_core_test_lead">' . wp_nonce_field( 'bytenot_core_test_lead', '_wpnonce', true, false )
				. '<button class="bn-btn" type="submit">' . esc_html__( 'Send a test lead', 'bytenot-core' ) . '</button>'
				. '<a class="bn-btn bn-btn--ghost" href="' . esc_url( admin_url( 'admin.php?page=bytenot-leads&crm=failed' ) ) . '">' . esc_html__( 'See failed', 'bytenot-core' ) . '</a></form>'
				. '<p class="bn-card__note">' . esc_html__( 'The test goes to the lead email and, if set, the webhook. It is not stored as an enquiry.', 'bytenot-core' ) . '</p>';
			bytenot_core_card( __( 'Delivery', 'bytenot-core' ), $html );
			bytenot_core_card(
				__( 'Zoho CRM', 'bytenot-core' ),
				'<p class="bn-card__note">' . esc_html__( 'A direct connection (lead → Zoho Leads module, listing sync from Zoho) plugs into the same provider interface. Until then, Zoho Flow with the webhook above does the job.', 'bytenot-core' ) . '</p><span class="bn-badge bn-badge--muted">' . esc_html__( 'Planned', 'bytenot-core' ) . '</span>'
			);
			?>
		</div>
	</div>
	<?php
	bytenot_core_admin_close();
}
