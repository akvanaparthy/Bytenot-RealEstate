<?php
/**
 * Home: hero with search and partners, about, featured, testimonials, FAQ, inquiry.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">

<?php
get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/about' );
get_template_part( 'template-parts/home/featured' );
get_template_part( 'template-parts/home/testimonials' );
?>

</main>

<?php
get_template_part( 'template-parts/home/faq' );
get_template_part( 'template-parts/home/inquiry' );

get_footer();
