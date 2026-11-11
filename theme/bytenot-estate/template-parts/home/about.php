<?php
/**
 * About: a pinned statement whose words light up on scroll, inline photo pills and
 * side photos that pop in and out.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_floats = array(
	array( 'about_float_1', '0.04', '0.4' ),
	array( 'about_float_2', '0.36', '0.72' ),
	array( 'about_float_3', '0.64', '0.92' ),
);
$bytenot_pills  = array_values( array_filter( array( (int) bytenot_mod( 'about_pill_1' ), (int) bytenot_mod( 'about_pill_2' ), (int) bytenot_mod( 'about_pill_3' ) ) ) );
// blank lines split paragraphs; {pill} marks an inline photo, counted across the whole statement
$bytenot_paras = preg_split( '/\R\s*\R/', trim( (string) bytenot_mod( 'about_statement' ) ) );
$bytenot_pill  = 0;
?>
<section class="about" id="about" aria-labelledby="about-title" data-about>
	<div class="about__stage">
		<?php foreach ( $bytenot_floats as $bytenot_n => $bytenot_f ) : ?>
			<?php if ( (int) bytenot_mod( $bytenot_f[0] ) ) : ?>
		<div class="about__float <?php echo esc_attr( bytenot_class( 'about__float--' . ( (int) $bytenot_n + 1 ) ) ); ?>" data-float data-in="<?php echo esc_attr( $bytenot_f[1] ); ?>" data-out="<?php echo esc_attr( $bytenot_f[2] ); ?>" aria-hidden="true"><div class="about__float-in"><?php bytenot_mod_image( $bytenot_f[0], 'bytenot-square', array( 'width' => 560, 'height' => 560, 'loading' => 'lazy' ) ); ?></div></div>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="container about__inner">
			<h2 class="visually-hidden" id="about-title"><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( 'About %s', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?></h2>
			<div class="about__statement" data-reveal-words>
				<?php foreach ( $bytenot_paras as $bytenot_para ) : ?>
				<p>
					<?php
					$bytenot_parts = preg_split( '/\{pill\}/', $bytenot_para );
					foreach ( $bytenot_parts as $bytenot_i => $bytenot_text ) {
						echo esc_html( $bytenot_text );
						if ( $bytenot_i < count( $bytenot_parts ) - 1 && isset( $bytenot_pills[ $bytenot_pill ] ) ) {
							echo ' <span class="about__pill"><span class="about__pill-img">' . wp_get_attachment_image( $bytenot_pills[ $bytenot_pill ], 'bytenot-card', false, array( 'alt' => '', 'width' => 260, 'height' => 176, 'loading' => 'lazy' ) ) . '</span></span> ';
							++$bytenot_pill;
						}
					}
					?>
				</p>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
