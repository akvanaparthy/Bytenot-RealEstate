<?php
/**
 * Enquiries: every lead the site captured, with its CRM result, a detail view and a
 * retry button.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render.
 */
function bytenot_core_screen_leads() {
	bytenot_core_admin_open( 'bytenot-leads' );
	$lead = isset( $_GET['lead'] ) ? absint( $_GET['lead'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $lead && 'enquiry' === get_post_type( $lead ) ) {
		bytenot_core_lead_detail( $lead );
	} else {
		bytenot_core_lead_list();
	}
	bytenot_core_admin_close();
}

/**
 * The list.
 */
function bytenot_core_lead_list() {
	$crm   = isset( $_GET['crm'] ) ? sanitize_key( wp_unslash( $_GET['crm'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$paged = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$args  = array( 'post_type' => 'enquiry', 'post_status' => 'private', 'posts_per_page' => 20, 'paged' => $paged );
	if ( in_array( $crm, array( 'pending', 'sent', 'failed' ), true ) ) {
		$args['meta_query'] = array( array( 'key' => 'bn_crm_status', 'value' => $crm ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}
	$q     = new WP_Query( $args );
	$stats = bytenot_core_lead_stats();
	$base  = admin_url( 'admin.php?page=bytenot-leads' );
	$tabs  = array( '' => __( 'All', 'bytenot-core' ) . ' ' . $stats['total'], 'pending' => __( 'Pending', 'bytenot-core' ) . ' ' . $stats['pending'], 'failed' => __( 'Failed', 'bytenot-core' ) . ' ' . $stats['failed'], 'sent' => __( 'Sent', 'bytenot-core' ) . ' ' . $stats['sent'] );
	?>
	<div class="bn-toolbar">
		<nav class="bn-subtabs" aria-label="<?php esc_attr_e( 'Filter enquiries', 'bytenot-core' ); ?>">
			<?php foreach ( $tabs as $k => $label ) : ?>
			<a class="bn-subtab<?php echo $k === $crm ? ' is-active' : ''; ?>" href="<?php echo esc_url( $k ? add_query_arg( 'crm', $k, $base ) : $base ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if ( current_user_can( 'manage_options' ) ) : ?><a class="bn-btn bn-btn--ghost" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bytenot_core_export_leads' ), 'bytenot_core_export_leads' ) ); ?>"><?php esc_html_e( 'Export CSV', 'bytenot-core' ); ?></a><?php endif; ?>
	</div>
	<section class="bn-card bn-card--wide bn-card--flush">
	<?php if ( $q->have_posts() ) : ?>
		<table class="bn-table">
			<thead><tr><th><?php esc_html_e( 'Lead', 'bytenot-core' ); ?></th><th><?php esc_html_e( 'Form', 'bytenot-core' ); ?></th><th><?php esc_html_e( 'Listing', 'bytenot-core' ); ?></th><th><?php esc_html_e( 'Received', 'bytenot-core' ); ?></th><th><?php esc_html_e( 'CRM', 'bytenot-core' ); ?></th><th></th></tr></thead>
			<tbody>
			<?php
			foreach ( $q->posts as $post ) :
				$id     = $post->ID;
				$p      = (int) bytenot_core_get( $id, 'listing_id' );
				$status = (string) bytenot_core_get( $id, 'crm_status' );
				$error  = (string) bytenot_core_get( $id, 'crm_error' );
				?>
				<tr>
					<td><a href="<?php echo esc_url( add_query_arg( 'lead', $id, $base ) ); ?>"><b><?php echo esc_html( bytenot_core_get( $id, 'name' ) ); ?></b></a><br><span class="bn-muted"><?php echo esc_html( bytenot_core_get( $id, 'phone' ) ); ?><?php echo bytenot_core_get( $id, 'email' ) ? ' · ' . esc_html( bytenot_core_get( $id, 'email' ) ) : ''; ?></span></td>
					<td><?php echo esc_html( bytenot_core_get( $id, 'form' ) ); ?></td>
					<td><?php echo $p ? '<a href="' . esc_url( get_edit_post_link( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>' : '<span class="bn-muted">—</span>'; ?></td>
					<td><?php echo esc_html( get_post_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), false, $id ) ); ?></td>
					<td><?php echo bytenot_core_badge( $status ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $error ? '<br><span class="bn-muted bn-small">' . esc_html( $error ) . '</span>' : ''; ?></td>
					<td class="bn-table__actions"><?php if ( 'sent' !== $status && 'email' !== bytenot_core_setting( 'crm_provider' ) && current_user_can( 'manage_options' ) ) : ?><a class="bn-btn bn-btn--small" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bytenot_core_retry_lead&id=' . $id ), 'bytenot_core_retry_' . $id ) ); ?>"><?php esc_html_e( 'Push again', 'bytenot-core' ); ?></a><?php endif; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		$pages = (int) $q->max_num_pages;
		if ( $pages > 1 ) {
			echo '<div class="bn-pager">' . wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%', $crm ? add_query_arg( 'crm', $crm, $base ) : $base ), 'format' => '', 'current' => $paged, 'total' => $pages, 'prev_text' => '‹', 'next_text' => '›' ) ) ) . '</div>';
		}
		?>
	<?php else : ?>
		<p class="bn-empty"><?php esc_html_e( 'Nothing here yet.', 'bytenot-core' ); ?></p>
	<?php endif; ?>
	</section>
	<?php
	wp_reset_postdata();
}

/**
 * One lead.
 *
 * @param int $id Enquiry ID.
 */
function bytenot_core_lead_detail( $id ) {
	$get = static fn( $k ) => (string) bytenot_core_get( $id, $k );
	$p   = (int) $get( 'listing_id' );
	$rows = array(
		__( 'Name', 'bytenot-core' )           => esc_html( $get( 'name' ) ),
		__( 'Phone', 'bytenot-core' )          => '<a href="tel:' . esc_attr( $get( 'phone' ) ) . '">' . esc_html( $get( 'phone' ) ) . '</a>',
		__( 'Email', 'bytenot-core' )          => $get( 'email' ) ? '<a href="mailto:' . esc_attr( $get( 'email' ) ) . '">' . esc_html( $get( 'email' ) ) . '</a>' : '—',
		__( 'Form', 'bytenot-core' )           => esc_html( $get( 'form' ) ),
		__( 'Listing', 'bytenot-core' )        => $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '" target="_blank" rel="noopener">' . esc_html( get_the_title( $p ) ) . '</a>' : '—',
		__( 'Preferred date', 'bytenot-core' ) => esc_html( $get( 'preferred_date' ) ?: '—' ),
		__( 'Message', 'bytenot-core' )        => nl2br( esc_html( $get( 'message' ) ?: '—' ) ),
		__( 'Consent', 'bytenot-core' )        => $get( 'consent' ) ? esc_html__( 'Yes', 'bytenot-core' ) : esc_html__( 'No', 'bytenot-core' ),
		__( 'Received', 'bytenot-core' )       => esc_html( get_post_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), false, $id ) ),
		__( 'Source', 'bytenot-core' )         => $get( 'source_url' ) ? '<a href="' . esc_url( $get( 'source_url' ) ) . '" target="_blank" rel="noopener">' . esc_html( $get( 'source_url' ) ) . '</a>' : '—',
		__( 'Attribution', 'bytenot-core' )    => esc_html( $get( 'attribution' ) ?: '—' ),
		__( 'CRM', 'bytenot-core' )            => bytenot_core_badge( $get( 'crm_status' ) ) . ( $get( 'crm_error' ) ? ' <span class="bn-muted">' . esc_html( $get( 'crm_error' ) ) . '</span>' : '' ) . ( $get( 'crm_id' ) ? ' <span class="bn-muted">#' . esc_html( $get( 'crm_id' ) ) . '</span>' : '' ),
	);
	$html = '<dl class="bn-dl">';
	foreach ( $rows as $k => $v ) {
		$html .= '<div><dt>' . esc_html( $k ) . '</dt><dd>' . $v . '</dd></div>';
	}
	$html .= '</dl><div class="bn-actions">'
		. '<a class="bn-btn bn-btn--ghost" href="' . esc_url( admin_url( 'admin.php?page=bytenot-leads' ) ) . '">' . esc_html__( 'Back to enquiries', 'bytenot-core' ) . '</a>'
		. ( 'sent' !== $get( 'crm_status' ) && 'email' !== bytenot_core_setting( 'crm_provider' ) && current_user_can( 'manage_options' ) ? '<a class="bn-btn" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=bytenot_core_retry_lead&id=' . $id ), 'bytenot_core_retry_' . $id ) ) . '">' . esc_html__( 'Push to the CRM again', 'bytenot-core' ) . '</a>' : '' )
		. '<a class="bn-btn bn-btn--ghost" href="' . esc_url( get_delete_post_link( $id, '', true ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Delete this enquiry permanently?', 'bytenot-core' ) ) . '\')">' . esc_html__( 'Delete', 'bytenot-core' ) . '</a>'
		. '</div>';
	/* translators: %s: lead name. */
	bytenot_core_card( sprintf( __( 'Enquiry from %s', 'bytenot-core' ), $get( 'name' ) ), $html, '', 'bn-card--wide' );
}
