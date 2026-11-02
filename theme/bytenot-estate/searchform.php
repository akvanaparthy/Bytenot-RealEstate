<?php
/**
 * Search form in the design's field style.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;
$bytenot_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="site-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="uf"><span class="uf__label" for="<?php echo esc_attr( $bytenot_id ); ?>"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></span>
		<input class="uf__input" id="<?php echo esc_attr( $bytenot_id ); ?>" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search the site', 'bytenot-estate' ); ?>">
	</label>
	<button class="btn btn--dark btn--sm" type="submit"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></button>
</form>
