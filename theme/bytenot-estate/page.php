<?php
/**
 * Pages: title plus block content.
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
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header>
		<div class="prose entry-content">
			<?php the_content(); ?>
		</div>
	</article>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
