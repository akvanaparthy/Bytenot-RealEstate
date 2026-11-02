<?php
/**
 * Block Bindings source `bytenot-core/field`: formatted values for templates.
 *
 * Templates bind a Paragraph, Heading, Button or Image to {"source":"bytenot-core/field",
 * "args":{"key":"price"}} and get the formatted string; raw meta is still available via
 * core/post-meta with the pc_ prefix.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the source.
 */
function bytenot_core_register_bindings() {
	register_block_bindings_source(
		'bytenot/field',
		array(
			'label'              => __( 'Property field', 'bytenot-core' ),
			'get_value_callback' => 'bytenot_core_binding_value',
			'uses_context'       => array( 'postId' ),
		)
	);
}
add_action( 'init', 'bytenot_core_register_bindings' );

/**
 * Resolve a formatted field.
 *
 * @param array    $args  Binding args: key.
 * @param WP_Block $block Block instance (for context).
 * @return string
 */
function bytenot_core_binding_value( array $args, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	$key     = $args['key'] ?? '';
	if ( ! $post_id || ! $key ) {
		return '';
	}
	switch ( $key ) {
		case 'price':
			if ( bytenot_core_get( $post_id, 'price_on_request' ) ) {
				return __( 'Price on request', 'bytenot-core' );
			}
			return bytenot_core_format_price( bytenot_core_get( $post_id, 'price' ), bytenot_core_is_rent( $post_id ) );
		case 'psf':
			return bytenot_core_format_psf( $post_id );
		case 'area':
			return bytenot_core_format_area( bytenot_core_get( $post_id, 'area_primary' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' );
		case 'area_secondary':
			return bytenot_core_format_area( bytenot_core_get( $post_id, 'area_secondary' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' );
		case 'line':
			return bytenot_core_format_line( $post_id );
		case 'place':
			return bytenot_core_format_place( $post_id );
		case 'bedrooms':
			return (string) ( bytenot_core_get( $post_id, 'bedroom_label' ) ?: '' );
		case 'bathrooms':
			$n = (float) bytenot_core_get( $post_id, 'bathrooms' );
			/* translators: %s: number of bathrooms. */
			return $n > 0 ? sprintf( _n( '%s bath', '%s baths', (int) ceil( $n ), 'bytenot-core' ), $n ) : '';
		case 'status':
			$terms = get_the_terms( $post_id, 'listing_status' );
			return $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
		case 'possession':
			$terms = get_the_terms( $post_id, 'possession' );
			return $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
		case 'featured_reason':
			return (string) bytenot_core_get( $post_id, 'featured_reason' );
		case 'reference':
			return (string) bytenot_core_get( $post_id, 'reference_id' );
		case 'rera':
			return (string) bytenot_core_get( $post_id, 'rera_number' );
		case 'rera_url':
			return (string) bytenot_core_get( $post_id, 'rera_url' );
		case 'whatsapp_url':
			$number = bytenot_core_setting( 'whatsapp' );
			if ( ! $number ) {
				return '';
			}
			/* translators: 1: property title, 2: property URL. */
			$text = sprintf( __( 'Hi, I am interested in %1$s (%2$s).', 'bytenot-core' ), get_the_title( $post_id ), get_permalink( $post_id ) );
			return 'https://wa.me/' . rawurlencode( $number ) . '?text=' . rawurlencode( $text );
		case 'phone_url':
			$phone = bytenot_core_setting( 'phone' );
			return $phone ? 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) : '';
		default:
			return (string) bytenot_core_get( $post_id, sanitize_key( $key ) );
	}
}
