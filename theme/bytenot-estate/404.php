<?php
/**
 * Not found.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<div class="container page-shell">
		<header class="page-head">
			<p class="fig-label">404</p>
			<h1 class="page-title"><?php esc_html_e( 'That page has moved on.', 'bytenot-estate' ); ?></h1>
			<p class="prose page-lede"><?php esc_html_e( 'The home you are looking for may have been sold or the address mistyped. Browse the current listings or search below.', 'bytenot-estate' ); ?></p>
		</header>
		<div class="page-actions">
			<a class="btn btn--dark" href="<?php echo esc_url( bytenot_archive_url() ); ?>"><?php echo esc_html( sprintf( /* translators: %s: what listings are called, e.g. "projects". */ __( 'See all %s', 'bytenot-estate' ), bytenot_noun() ) ); ?> <?php bytenot_the_icon( 'arrow' ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'bytenot-estate' ); ?></a>
		</div>
		<?php get_search_form(); ?>
	</div>
</main>

<?php
get_footer();
