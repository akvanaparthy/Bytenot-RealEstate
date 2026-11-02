<?php
/**
 * Posts, projects and advisors: title, date, featured image and content.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
	<article <?php post_class( 'container page-shell' ); ?>>
		<header class="page-head">
			<?php if ( 'post' === get_post_type() ) : ?>
			<p class="fig-label"><?php echo esc_html( get_the_date() ); ?></p>
			<?php elseif ( get_post_meta( get_the_ID(), 'pc_role', true ) ) : ?>
			<p class="fig-label"><?php echo esc_html( get_post_meta( get_the_ID(), 'pc_role', true ) ); ?></p>
			<?php endif; ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
		<figure class="page-media"><?php the_post_thumbnail( 'large' ); ?></figure>
		<?php endif; ?>
		<div class="prose entry-content">
			<?php the_content(); ?>
		</div>
	</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>

<?php
get_footer();
