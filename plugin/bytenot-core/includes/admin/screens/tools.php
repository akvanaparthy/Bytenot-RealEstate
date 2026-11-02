<?php
/**
 * Tools: the private preview lock, demo content and exports.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_tools() {
	$s = wp_parse_args( (array) get_option( 'bytenot_core_settings', array() ), bytenot_core_default_settings() );
	bytenot_core_admin_open( 'bytenot-tools' );
	?>
	<div class="bn-grid bn-grid--2">
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" class="bn-form">
			<?php settings_fields( 'bytenot_core' ); ?>
			<input type="hidden" name="bytenot_core_settings[_screen]" value="bytenot-tools">
			<?php
			$link = home_url( '/?preview_code=' . ( $s['preview_code'] ? $s['preview_code'] : 'CODE' ) );
			$lock = '<label class="bn-switch"><input type="checkbox" name="bytenot_core_settings[preview_mode]" value="1" ' . checked( ! empty( $s['preview_mode'] ), true, false ) . '><span class="bn-switch__track" aria-hidden="true"></span><span class="bn-switch__label">' . esc_html__( 'Only logged-in users can see the site', 'bytenot-core' ) . '</span></label>';
			bytenot_core_card(
				__( 'Private preview', 'bytenot-core' ),
				bytenot_core_field( __( 'Lock', 'bytenot-core' ), $lock, __( 'Everyone else sees a holding page with a 503 status and a noindex header. Feeds, the REST API and sitemaps are closed and robots.txt disallows everything. Nothing is indexed until you switch this off.', 'bytenot-core' ) )
				. bytenot_core_field( __( 'Preview code', 'bytenot-core' ), '<div class="bn-row">' . bytenot_core_input( 'preview_code', $s, 'text', array( 'autocomplete' => 'off' ) ) . '<button class="bn-btn bn-btn--ghost" type="button" data-copy="' . esc_attr( $link ) . '">' . esc_html__( 'Copy link', 'bytenot-core' ) . '</button></div>', sprintf( /* translators: %s: link. */ __( 'Optional. Share %s with someone who has no account; it unlocks their browser for 30 days.', 'bytenot-core' ), $link ), 'bn-preview_code' )
				. '<div class="bn-save bn-save--inline"><button class="bn-btn bn-btn--gold" type="submit">' . esc_html__( 'Save', 'bytenot-core' ) . '</button></div>'
			);
			?>
		</form>
		<div>
			<?php
			$installed = get_option( 'bytenot_core_demo_installed' );
			$html = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="bn-actions" data-confirm-demo>'
				. '<input type="hidden" name="action" value="bytenot_core_install_demo">' . wp_nonce_field( 'bytenot_core_install_demo', '_wpnonce', true, false )
				. '<button class="bn-btn" type="submit">' . ( $installed ? esc_html__( 'Reinstall demo content', 'bytenot-core' ) : esc_html__( 'Install demo content', 'bytenot-core' ) ) . '</button></form>'
				. '<p class="bn-card__note">' . esc_html__( 'Twelve sample listings with photos, two advisors, localities, testimonials, FAQs, partner logos, the menus and the home page texts and photos, so the site looks like the design. Running it again updates the same records; your own records are untouched.', 'bytenot-core' )
				. ( $installed ? ' ' . esc_html( sprintf( /* translators: %s: date. */ __( 'Last installed %s.', 'bytenot-core' ), wp_date( get_option( 'date_format' ), (int) $installed ) ) ) : '' ) . '</p>';
			bytenot_core_card( __( 'Demo content', 'bytenot-core' ), $html );
			$html = '<div class="bn-actions"><a class="bn-btn bn-btn--ghost" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bytenot_core_export_leads' ), 'bytenot_core_export_leads' ) ) . '">' . esc_html__( 'Export enquiries (CSV)', 'bytenot-core' ) . '</a></div>'
				. '<p class="bn-card__note">' . esc_html__( 'Every enquiry with its contact details, form, property, message, source and CRM result.', 'bytenot-core' ) . '</p>';
			bytenot_core_card( __( 'Export', 'bytenot-core' ), $html );
			?>
		</div>
	</div>
	<?php
	bytenot_core_admin_close();
}
