<?php
/**
 * Fallback for every archive and the blog: a titled list of entries.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<div class="container page-shell">
		<header class="page-head">
			<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
			<?php elseif ( is_archive() ) : ?>
			<h1 class="page-title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="prose page-lede">', '</div>' ); ?>
			<?php elseif ( is_search() ) : ?>
			<h1 class="page-title"><?php echo esc_html( sprintf( /* translators: %s: search query. */ __( 'Results for “%s”', 'bytenot-estate' ), get_search_query() ) ); ?></h1>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
		<ul class="entries">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
			<li class="entry">
				<?php if ( has_post_thumbnail() ) : ?>
				<a class="entry__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'bytenot-card', array( 'loading' => 'lazy' ) ); ?></a>
				<?php endif; ?>
				<div class="entry__body">
					<p class="fig-label"><?php echo esc_html( get_the_date() ); ?></p>
					<h2 class="entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="prose"><?php the_excerpt(); ?></div>
				</div>
			</li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'bytenot-estate' ), 'next_text' => __( 'Next', 'bytenot-estate' ) ) ); ?>
		<?php else : ?>
		<p class="prose"><?php esc_html_e( 'Nothing here yet.', 'bytenot-estate' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
