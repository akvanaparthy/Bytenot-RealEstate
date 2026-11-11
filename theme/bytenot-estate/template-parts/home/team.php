<?php
/**
 * The people: up to three portrait cards under the about statement. A card shows when it
 * has a name, a photo or a role; without a photo it carries a placeholder frame so the row
 * reads right before the portraits are in.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_people = array();
foreach ( array( 1, 2, 3 ) as $bytenot_i ) {
	$bytenot_person = array(
		'photo' => (int) bytenot_mod( 'team_' . $bytenot_i . '_photo' ),
		'name'  => trim( (string) bytenot_mod( 'team_' . $bytenot_i . '_name' ) ),
		'role'  => trim( (string) bytenot_mod( 'team_' . $bytenot_i . '_role' ) ),
	);
	if ( $bytenot_person['photo'] || '' !== $bytenot_person['name'] || '' !== $bytenot_person['role'] ) {
		$bytenot_people[] = $bytenot_person;
	}
}
$bytenot_team_title = trim( (string) bytenot_mod( 'team_title' ) );
if ( ! $bytenot_people || '' === $bytenot_team_title ) {
	return;
}
?>
<section class="team" aria-labelledby="team-title">
	<div class="container">
		<div class="team__head">
			<p class="team__eyebrow"><?php echo esc_html( bytenot_mod( 'team_eyebrow' ) ); ?></p>
			<h2 class="team__title" id="team-title"><?php echo bytenot_inline( $bytenot_team_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		</div>
		<ul class="team__grid <?php echo esc_attr( bytenot_class( 'team__grid--' . count( $bytenot_people ) ) ); ?>">
			<?php foreach ( $bytenot_people as $bytenot_person ) : ?>
			<li class="team__card">
				<figure class="team__fig<?php echo $bytenot_person['photo'] ? '' : ' ' . esc_attr( bytenot_class( 'team__fig--empty' ) ); ?>">
					<?php if ( $bytenot_person['photo'] ) : ?>
						<?php echo wp_get_attachment_image( $bytenot_person['photo'], 'bytenot-portrait', false, array( 'alt' => $bytenot_person['name'], 'loading' => 'lazy', 'width' => 600, 'height' => 700 ) ); ?>
					<?php else : ?>
						<svg class="team__silhouette" viewBox="0 0 120 140" aria-hidden="true" focusable="false"><circle cx="60" cy="48" r="26" fill="currentColor"/><path d="M12 140c0-30 21-48 48-48s48 18 48 48Z" fill="currentColor"/></svg>
					<?php endif; ?>
				</figure>
				<p class="team__name"><?php echo esc_html( '' !== $bytenot_person['name'] ? $bytenot_person['name'] : $bytenot_person['role'] ); ?></p>
				<?php if ( '' !== $bytenot_person['name'] && '' !== $bytenot_person['role'] ) : ?>
				<p class="team__role"><?php echo esc_html( $bytenot_person['role'] ); ?></p>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
