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
		'show_in_menu'        => 'bytenot',
		'exclude_from_search' => true,
		'rewrite'             => false,
		'has_archive'         => false,
	);
	register_post_type(
		'testimonial',
		array_merge(
			$common,
			array(
				'labels'      => array(
					'name'                  => _x( 'Testimonials', 'post type general name', 'bytenot-core' ),
					'singular_name'         => _x( 'Testimonial', 'post type singular name', 'bytenot-core' ),
					'add_new_item'          => __( 'Add new testimonial', 'bytenot-core' ),
					'edit_item'             => __( 'Edit testimonial', 'bytenot-core' ),
					'featured_image'        => __( 'Photo', 'bytenot-core' ),
					'set_featured_image'    => __( 'Set photo', 'bytenot-core' ),
					'remove_featured_image' => __( 'Remove photo', 'bytenot-core' ),
					'use_featured_image'    => __( 'Use as photo', 'bytenot-core' ),
				),
				'description' => __( 'A client quote: the title is the name, the content the quote, the photo their picture.', 'bytenot-core' ),
				'supports'    => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		)
	);
	register_post_type(
		'faq',
		array_merge(
			$common,
			array(
				'labels'      => array(
					'name'          => _x( 'FAQs', 'post type general name', 'bytenot-core' ),
					'singular_name' => _x( 'FAQ', 'post type singular name', 'bytenot-core' ),
					'add_new_item'  => __( 'Add new question', 'bytenot-core' ),
					'edit_item'     => __( 'Edit question', 'bytenot-core' ),
				),
				'description' => __( 'A question (title) and its answer (content). Order sets the position.', 'bytenot-core' ),
				'supports'    => array( 'title', 'editor', 'page-attributes' ),
			)
		)
	);
	register_post_type(
		'partner',
		array_merge(
			$common,
			array(
				'labels'      => array(
					'name'                  => _x( 'Partners', 'post type general name', 'bytenot-core' ),
					'singular_name'         => _x( 'Partner', 'post type singular name', 'bytenot-core' ),
					'add_new_item'          => __( 'Add new partner', 'bytenot-core' ),
					'edit_item'             => __( 'Edit partner', 'bytenot-core' ),
					'featured_image'        => __( 'Logo', 'bytenot-core' ),
					'set_featured_image'    => __( 'Set logo', 'bytenot-core' ),
					'remove_featured_image' => __( 'Remove logo', 'bytenot-core' ),
					'use_featured_image'    => __( 'Use as logo', 'bytenot-core' ),
				),
				'description' => __( 'A developer or channel partner: the name and the logo. Projects can name a partner as their developer.', 'bytenot-core' ),
				'supports'    => array( 'title', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_meta( 'testimonial', 'bn_role', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => 'bytenot_core_can_edit_meta', 'description' => __( 'What they did, e.g. "Bought a 3 BHK in Gachibowli".', 'bytenot-core' ) ) );
	register_post_meta( 'partner', 'bn_shape', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'bytenot_core_sanitize_shape', 'auth_callback' => 'bytenot_core_can_edit_meta', 'description' => __( 'Logo shape override: tall, wide or mark (icon plus name). Empty means: read it from the image.', 'bytenot-core' ) ) );
	register_post_meta( 'partner', 'bn_url', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'esc_url_raw', 'auth_callback' => 'bytenot_core_can_edit_meta', 'description' => __( 'Where the logo links to, if anywhere.', 'bytenot-core' ) ) );
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
 * Side boxes for the fields these types carry.
 */
function bytenot_core_site_content_boxes() {
	add_meta_box( 'bytenot_core_testimonial', __( 'Details', 'bytenot-core' ), 'bytenot_core_testimonial_box', 'testimonial', 'side' );
	add_meta_box( 'bytenot_core_partner', __( 'Details', 'bytenot-core' ), 'bytenot_core_partner_box', 'partner', 'side' );
}
add_action( 'add_meta_boxes', 'bytenot_core_site_content_boxes' );

/**
 * Testimonial: the role line.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_testimonial_box( $post ) {
	wp_nonce_field( 'bytenot_core_site_content', 'bytenot_core_site_content_nonce' );
	echo '<p><label for="bn_role">' . esc_html__( 'What they did', 'bytenot-core' ) . '</label><input id="bn_role" class="widefat" type="text" name="bn_role" value="' . esc_attr( get_post_meta( $post->ID, 'bn_role', true ) ) . '" placeholder="' . esc_attr__( 'Bought a 3 BHK in …', 'bytenot-core' ) . '"></p>';
}

/**
 * Partner: logo shape and link.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_partner_box( $post ) {
	wp_nonce_field( 'bytenot_core_site_content', 'bytenot_core_site_content_nonce' );
	$shape  = get_post_meta( $post->ID, 'bn_shape', true );
	$shapes = array( '' => __( 'From the image', 'bytenot-core' ), 'tall' => __( 'Tall', 'bytenot-core' ), 'wide' => __( 'Wide', 'bytenot-core' ), 'mark' => __( 'Mark (icon with the name beside it)', 'bytenot-core' ) );
	echo '<p><label for="bn_shape">' . esc_html__( 'Logo shape', 'bytenot-core' ) . '</label><select id="bn_shape" class="widefat" name="bn_shape">';
	foreach ( $shapes as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $shape, $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></p>';
	echo '<p><label for="bn_url">' . esc_html__( 'Link', 'bytenot-core' ) . '</label><input id="bn_url" class="widefat" type="url" name="bn_url" value="' . esc_attr( get_post_meta( $post->ID, 'bn_url', true ) ) . '" placeholder="https://"></p>';
}

/**
 * Save the boxes.
 *
 * @param int $post_id Post ID.
 */
function bytenot_core_site_content_save( $post_id ) {
	if ( ! isset( $_POST['bytenot_core_site_content_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bytenot_core_site_content_nonce'] ), 'bytenot_core_site_content' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$fields = array( 'bn_role' => 'sanitize_text_field', 'bn_shape' => 'bytenot_core_sanitize_shape', 'bn_url' => 'esc_url_raw' );
	foreach ( $fields as $key => $fn ) {
		if ( isset( $_POST[ $key ] ) ) {
			$clean = call_user_func( $fn, wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised by $fn.
			if ( '' === $clean ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $clean );
			}
		}
	}
}
add_action( 'save_post', 'bytenot_core_site_content_save' );
