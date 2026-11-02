<?php
/**
 * Site content types the theme shows beside listings: testimonials, FAQs and channel
 * partners. Not public on their own (no single pages); edited in wp-admin.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the three post types.
 */
function bytenot_core_register_site_content() {
	$common = array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'show_in_menu'        => 'edit.php?post_type=property',
		'exclude_from_search' => true,
		'rewrite'             => false,
		'has_archive'         => false,
	);
	register_post_type(
		'testimonial',
		array_merge(
			$common,
			array(
				'labels'   => array(
					'name'          => _x( 'Testimonials', 'post type general name', 'bytenot-core' ),
					'singular_name' => _x( 'Testimonial', 'post type singular name', 'bytenot-core' ),
					'add_new_item'  => __( 'Add new testimonial', 'bytenot-core' ),
					'edit_item'     => __( 'Edit testimonial', 'bytenot-core' ),
				),
				'description' => __( 'A client quote: the title is the name, the content the quote, the featured image the photo.', 'bytenot-core' ),
				'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
			)
		)
	);
	register_post_type(
		'faq',
		array_merge(
			$common,
			array(
				'labels'   => array(
					'name'          => _x( 'FAQs', 'post type general name', 'bytenot-core' ),
					'singular_name' => _x( 'FAQ', 'post type singular name', 'bytenot-core' ),
					'add_new_item'  => __( 'Add new question', 'bytenot-core' ),
					'edit_item'     => __( 'Edit question', 'bytenot-core' ),
				),
				'description' => __( 'A question (title) and its answer (content). Order sets the position.', 'bytenot-core' ),
				'supports' => array( 'title', 'editor', 'page-attributes' ),
			)
		)
	);
	register_post_type(
		'partner',
		array_merge(
			$common,
			array(
				'labels'   => array(
					'name'          => _x( 'Partners', 'post type general name', 'bytenot-core' ),
					'singular_name' => _x( 'Partner', 'post type singular name', 'bytenot-core' ),
					'add_new_item'  => __( 'Add new partner', 'bytenot-core' ),
					'edit_item'     => __( 'Edit partner', 'bytenot-core' ),
				),
				'description' => __( 'A developer or channel partner whose logo runs in the strip under the hero.', 'bytenot-core' ),
				'supports' => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			)
		)
	);

	register_post_meta( 'testimonial', 'pc_role', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => 'bytenot_core_can_edit_meta', 'description' => __( 'What they did, e.g. "Bought a 3 BHK in Gachibowli".', 'bytenot-core' ) ) );
	register_post_meta( 'partner', 'pc_shape', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'bytenot_core_sanitize_shape', 'auth_callback' => 'bytenot_core_can_edit_meta', 'description' => __( 'Logo shape: tall, wide or mark (icon plus name).', 'bytenot-core' ) ) );
	register_post_meta( 'partner', 'pc_url', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'esc_url_raw', 'auth_callback' => 'bytenot_core_can_edit_meta' ) );
}
add_action( 'init', 'bytenot_core_register_site_content' );

/**
 * Partner logo shape.
 *
 * @param mixed $value Raw.
 * @return string
 */
function bytenot_core_sanitize_shape( $value ) {
	return in_array( $value, array( 'tall', 'wide', 'mark' ), true ) ? $value : '';
}

/**
 * Simple meta boxes for the fields these types carry.
 */
function bytenot_core_site_content_boxes() {
	add_meta_box( 'pc_testimonial', __( 'Details', 'bytenot-core' ), 'bytenot_core_testimonial_box', 'testimonial', 'side' );
	add_meta_box( 'pc_partner', __( 'Logo', 'bytenot-core' ), 'bytenot_core_partner_box', 'partner', 'side' );
}
add_action( 'add_meta_boxes', 'bytenot_core_site_content_boxes' );

/**
 * Testimonial: role line.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_testimonial_box( $post ) {
	wp_nonce_field( 'pc_site_content', 'pc_site_content_nonce' );
	echo '<p><label for="pc_role">' . esc_html__( 'What they did', 'bytenot-core' ) . '</label><input id="pc_role" class="widefat" type="text" name="pc_role" value="' . esc_attr( get_post_meta( $post->ID, 'pc_role', true ) ) . '" placeholder="' . esc_attr__( 'Bought a 3 BHK in …', 'bytenot-core' ) . '"></p>';
}

/**
 * Partner: shape and link.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_partner_box( $post ) {
	wp_nonce_field( 'pc_site_content', 'pc_site_content_nonce' );
	$shape = get_post_meta( $post->ID, 'pc_shape', true );
	echo '<p><label for="pc_shape">' . esc_html__( 'Shape', 'bytenot-core' ) . '</label><select id="pc_shape" name="pc_shape" class="widefat">';
	foreach ( array( '' => __( 'Normal', 'bytenot-core' ), 'tall' => __( 'Tall', 'bytenot-core' ), 'wide' => __( 'Wide', 'bytenot-core' ), 'mark' => __( 'Mark + name', 'bytenot-core' ) ) as $k => $label ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $shape, $k, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
	echo '<p><label for="pc_url">' . esc_html__( 'Website', 'bytenot-core' ) . '</label><input id="pc_url" class="widefat" type="url" name="pc_url" value="' . esc_attr( get_post_meta( $post->ID, 'pc_url', true ) ) . '"></p>';
}

/**
 * Save the boxes.
 *
 * @param int $post_id Post ID.
 */
function bytenot_core_site_content_save( $post_id ) {
	if ( ! isset( $_POST['pc_site_content_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pc_site_content_nonce'] ), 'pc_site_content' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'pc_role' => 'sanitize_text_field', 'pc_shape' => 'bytenot_core_sanitize_shape', 'pc_url' => 'esc_url_raw' ) as $key => $fn ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $fn, wp_unslash( $_POST[ $key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised by $fn.
		}
	}
}
add_action( 'save_post', 'bytenot_core_site_content_save' );
