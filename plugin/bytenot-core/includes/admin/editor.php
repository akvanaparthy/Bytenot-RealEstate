<?php
/**
 * The listing editor: a proper "Listing details" panel on property, project and advisor
 * edit screens, in the plugin's design. Fields are defined once (meta.php) and drawn
 * here in groups; media pickers, a landmarks repeater and the relations selects come
 * from assets/admin/editor.js.
 *
 * @package bytenot-core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Select options reused across groups.
 *
 * @param string $set Which set.
 * @return array<string, string>
 */
function bytenot_core_editor_options( $set ) {
	$sets = array(
		'area_type'  => array( 'carpet' => __( 'Carpet', 'bytenot-core' ), 'built-up' => __( 'Built-up', 'bytenot-core' ), 'super-built-up' => __( 'Super built-up', 'bytenot-core' ), 'plot' => __( 'Plot', 'bytenot-core' ) ),
		'area_unit'  => array( 'sqft' => __( 'sq ft', 'bytenot-core' ), 'sqyd' => __( 'sq yd', 'bytenot-core' ), 'sqm' => __( 'sq m', 'bytenot-core' ), 'acre' => __( 'acres', 'bytenot-core' ) ),
		'facing'     => array( '' => __( '—', 'bytenot-core' ), 'East' => __( 'East', 'bytenot-core' ), 'West' => __( 'West', 'bytenot-core' ), 'North' => __( 'North', 'bytenot-core' ), 'South' => __( 'South', 'bytenot-core' ), 'North-east' => __( 'North-east', 'bytenot-core' ), 'North-west' => __( 'North-west', 'bytenot-core' ), 'South-east' => __( 'South-east', 'bytenot-core' ), 'South-west' => __( 'South-west', 'bytenot-core' ) ),
		'furnishing' => array( '' => __( '—', 'bytenot-core' ), 'Unfurnished' => __( 'Unfurnished', 'bytenot-core' ), 'Semi-furnished' => __( 'Semi-furnished', 'bytenot-core' ), 'Fully furnished' => __( 'Fully furnished', 'bytenot-core' ) ),
		'rent'       => array( 'month' => __( 'per month', 'bytenot-core' ), 'year' => __( 'per year', 'bytenot-core' ) ),
		'landmark'   => array( 'metro' => __( 'Metro', 'bytenot-core' ), 'work' => __( 'Work', 'bytenot-core' ), 'school' => __( 'School', 'bytenot-core' ), 'hospital' => __( 'Hospital', 'bytenot-core' ), 'shopping' => __( 'Shopping', 'bytenot-core' ), 'airport' => __( 'Airport', 'bytenot-core' ), 'park' => __( 'Park', 'bytenot-core' ), 'other' => __( 'Other', 'bytenot-core' ) ),
		'unit'       => array( 'km' => 'km', 'm' => 'm', 'min' => 'min' ),
	);
	return $sets[ $set ];
}

/**
 * The editor layout per post type: tab => [ [ key, label, control, extra ], ... ].
 * Controls: text, number, money, select, toggle, textarea, month, date, url, media, file, landmarks, posts, readonly.
 *
 * @param string $type Post type.
 * @return array<string, array<int, array>>
 */
function bytenot_core_editor_layout( $type ) {
	$listing = array(
		__( 'Pricing', 'bytenot-core' )  => array(
			array( 'price', __( 'Price', 'bytenot-core' ), 'money', array( 'help' => __( 'Whole rupees. For rentals, the monthly rent.', 'bytenot-core' ) ) ),
			array( 'price_on_request', __( 'Price on request', 'bytenot-core' ), 'toggle' ),
			array( 'rent_period', __( 'Rent period', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'rent' ), 'help' => __( 'Only for rentals (status "For rent").', 'bytenot-core' ) ) ),
			array( 'deposit', __( 'Deposit', 'bytenot-core' ), 'money' ),
			array( 'maintenance', __( 'Maintenance', 'bytenot-core' ), 'money', array( 'help' => __( 'Per month.', 'bytenot-core' ) ) ),
		),
		__( 'Size', 'bytenot-core' )     => array(
			array( 'bedrooms', __( 'Bedrooms', 'bytenot-core' ), 'number', array( 'step' => '0.5' ) ),
			array( 'bedroom_label', __( 'Configuration label', 'bytenot-core' ), 'text', array( 'placeholder' => __( '3 BHK, Studio, Penthouse', 'bytenot-core' ) ) ),
			array( 'bathrooms', __( 'Bathrooms', 'bytenot-core' ), 'number', array( 'step' => '0.5' ) ),
			array( 'area_primary', __( 'Area', 'bytenot-core' ), 'number', array( 'pair' => array( 'area_primary_type' => bytenot_core_editor_options( 'area_type' ), 'area_unit' => bytenot_core_editor_options( 'area_unit' ) ), 'help' => __( 'The headline area: carpet for homes, plot for land.', 'bytenot-core' ) ) ),
			array( 'area_secondary', __( 'Second area', 'bytenot-core' ), 'number', array( 'pair' => array( 'area_secondary_type' => bytenot_core_editor_options( 'area_type' ) ), 'help' => __( 'Optional: built-up or super built-up, shown in the numbers.', 'bytenot-core' ) ) ),
		),
		__( 'Building', 'bytenot-core' ) => array(
			array( 'floor', __( 'Floor', 'bytenot-core' ), 'number', array( 'pair_label' => __( 'of', 'bytenot-core' ), 'pair_number' => 'total_floors' ) ),
			array( 'facing', __( 'Facing', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'facing' ) ) ),
			array( 'parking', __( 'Parking', 'bytenot-core' ), 'text', array( 'placeholder' => __( '2 covered + 1 open', 'bytenot-core' ) ) ),
			array( 'year_built', __( 'Built in', 'bytenot-core' ), 'number', array( 'min' => '1900', 'max' => '2100' ) ),
			array( 'furnishing', __( 'Furnishing', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'furnishing' ) ) ),
			array( 'ownership', __( 'Ownership', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Freehold', 'bytenot-core' ) ) ),
			array( 'possession_date', __( 'Possession / available from', 'bytenot-core' ), 'month', array( 'help' => __( 'For under-construction homes and future rentals. YYYY-MM or a full date.', 'bytenot-core' ) ) ),
		),
		__( 'Legal', 'bytenot-core' )    => array(
			array( 'rera_number', __( 'RERA number', 'bytenot-core' ), 'text' ),
			array( 'rera_state', __( 'Authority', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Telangana RERA', 'bytenot-core' ) ) ),
			array( 'rera_url', __( 'Verify at', 'bytenot-core' ), 'url', array( 'placeholder' => 'https://' ) ),
			array( 'approvals', __( 'Approvals', 'bytenot-core' ), 'textarea', array( 'help' => __( 'One per line: Occupancy certificate received, HMDA approved layout…', 'bytenot-core' ) ) ),
			array( 'banks', __( 'Loans pre-approved with', 'bytenot-core' ), 'text', array( 'placeholder' => 'SBI, HDFC, ICICI' ) ),
		),
		__( 'Location', 'bytenot-core' ) => array(
			array( 'address_line', __( 'Address line', 'bytenot-core' ), 'text', array( 'help' => __( 'The locality and city come from the Localities box on the right.', 'bytenot-core' ) ) ),
			array( 'lat', __( 'Latitude', 'bytenot-core' ), 'number', array( 'step' => 'any', 'pair_label' => __( 'longitude', 'bytenot-core' ), 'pair_number' => 'lng', 'help' => __( 'Right-click a spot in Google Maps and copy the two numbers.', 'bytenot-core' ) ) ),
			array( 'landmarks', __( 'Nearby', 'bytenot-core' ), 'landmarks', array( 'help' => __( 'What is close by and how far: drawn as the radial plot on the listing page.', 'bytenot-core' ) ) ),
		),
		__( 'Media', 'bytenot-core' )    => array(
			array( 'gallery', __( 'Photos', 'bytenot-core' ), 'media', array( 'help' => __( 'The featured image opens the page; these follow it in the story and the filmstrip.', 'bytenot-core' ) ) ),
			array( 'floor_plans', __( 'Floor plans', 'bytenot-core' ), 'media', array( 'help' => __( 'One image per floor. Put the room schedule in the image\'s Description, one per line: Living | 18\' × 14\'.', 'bytenot-core' ) ) ),
			array( 'brochure_id', __( 'Brochure', 'bytenot-core' ), 'file' ),
			array( 'video_url', __( 'Video', 'bytenot-core' ), 'url', array( 'placeholder' => 'https://youtube.com/…' ) ),
			array( 'tour_url', __( '3D tour', 'bytenot-core' ), 'url', array( 'placeholder' => 'https://' ) ),
		),
		__( 'People & flags', 'bytenot-core' ) => array(
			array( 'agent_id', __( 'Advisor', 'bytenot-core' ), 'posts', array( 'post_type' => 'agent' ) ),
			array( 'project_id', __( 'Project', 'bytenot-core' ), 'posts', array( 'post_type' => 'project' ) ),
			array( 'featured', __( 'Featured on the home page', 'bytenot-core' ), 'toggle' ),
			array( 'featured_reason', __( 'Why featured', 'bytenot-core' ), 'text', array( 'placeholder' => __( "Advisor's pick, Price revised, Metro in 5 min", 'bytenot-core' ) ) ),
			array( 'reference_id', __( 'Reference', 'bytenot-core' ), 'text', array( 'placeholder' => 'HYD-0231' ) ),
			array( 'source', __( 'Source', 'bytenot-core' ), 'readonly', array( 'pair_text' => 'source_id' ) ),
		),
	);
	if ( 'project' === $type ) {
		$listing = array_merge(
			array(
				__( 'Project', 'bytenot-core' ) => array(
					array( 'developer', __( 'Developer', 'bytenot-core' ), 'text' ),
					array( 'price_min', __( 'Price from', 'bytenot-core' ), 'money', array( 'pair_label' => __( 'to', 'bytenot-core' ), 'pair_number' => 'price_max' ) ),
					array( 'units_total', __( 'Units', 'bytenot-core' ), 'number', array( 'pair_label' => __( 'towers', 'bytenot-core' ), 'pair_number' => 'towers' ) ),
					array( 'launch_date', __( 'Launched', 'bytenot-core' ), 'month', array( 'pair_label' => __( 'completion', 'bytenot-core' ), 'pair_month' => 'completion_date' ) ),
				),
			),
			$listing
		);
		unset( $listing[ __( 'People & flags', 'bytenot-core' ) ][1] ); // a project has no parent project
	}
	if ( 'agent' === $type ) {
		return array(
			__( 'Advisor', 'bytenot-core' ) => array(
				array( 'role', __( 'Role', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Senior advisor, Hyderabad West', 'bytenot-core' ) ) ),
				array( 'phone', __( 'Phone', 'bytenot-core' ), 'text' ),
				array( 'whatsapp', __( 'WhatsApp', 'bytenot-core' ), 'text', array( 'help' => __( 'Digits with the country code.', 'bytenot-core' ) ) ),
				array( 'email', __( 'Email', 'bytenot-core' ), 'text' ),
				array( 'rera_number', __( 'RERA agent number', 'bytenot-core' ), 'text' ),
				array( 'languages', __( 'Languages', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'English, Telugu, Hindi', 'bytenot-core' ) ) ),
			),
		);
	}
	return $listing;
}

/**
 * Register the panel.
 */
function bytenot_core_editor_boxes() {
	add_meta_box( 'bytenot_listing', __( 'Listing details', 'bytenot-core' ), 'bytenot_core_editor_render', 'property', 'normal', 'high' );
	add_meta_box( 'bytenot_listing', __( 'Project details', 'bytenot-core' ), 'bytenot_core_editor_render', 'project', 'normal', 'high' );
	add_meta_box( 'bytenot_listing', __( 'Advisor details', 'bytenot-core' ), 'bytenot_core_editor_render', 'agent', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'bytenot_core_editor_boxes' );

/**
 * Media library and the editor script on those screens.
 *
 * @param string $hook Hook suffix.
 */
function bytenot_core_editor_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'property', 'project', 'agent' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.css', array(), BYTENOT_CORE_VERSION );
	wp_enqueue_script( 'bytenot-core-editor', BYTENOT_CORE_URL . 'assets/admin/editor.js', array( 'jquery' ), BYTENOT_CORE_VERSION, true );
	wp_localize_script( 'bytenot-core-editor', 'bytenotEditor', array( 'choose' => __( 'Choose', 'bytenot-core' ), 'remove' => __( 'Remove', 'bytenot-core' ), 'photos' => __( 'Choose photos', 'bytenot-core' ), 'file' => __( 'Choose a file', 'bytenot-core' ) ) );
}
add_action( 'admin_enqueue_scripts', 'bytenot_core_editor_assets' );

/**
 * Draw the panel.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_editor_render( $post ) {
	$layout = bytenot_core_editor_layout( $post->post_type );
	$get    = static fn( $k ) => get_post_meta( $post->ID, 'pc_' . $k, true );
	wp_nonce_field( 'bytenot_core_editor', 'bytenot_core_editor_nonce' );
	echo '<div class="bn bn-editor">';
	if ( count( $layout ) > 1 ) {
		echo '<nav class="bn-editor__tabs" role="tablist">';
		$i = 0;
		foreach ( array_keys( $layout ) as $tab ) {
			echo '<button type="button" class="bn-editor__tab' . ( $i ? '' : ' is-active' ) . '" role="tab" aria-selected="' . ( $i ? 'false' : 'true' ) . '" data-tab="' . esc_attr( sanitize_title( $tab ) ) . '">' . esc_html( $tab ) . '</button>';
			$i++;
		}
		echo '</nav>';
	}
	$i = 0;
	foreach ( $layout as $tab => $fields ) {
		echo '<div class="bn-editor__panel' . ( $i ? '' : ' is-active' ) . '" data-panel="' . esc_attr( sanitize_title( $tab ) ) . '"' . ( $i ? ' hidden' : '' ) . '>';
		foreach ( $fields as $f ) {
			echo bytenot_core_editor_field( $f, $get ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
		}
		echo '</div>';
		$i++;
	}
	echo '</div>';
}

/**
 * One field row.
 *
 * @param array    $f   [ key, label, control, extra ].
 * @param callable $get Meta reader.
 * @return string
 */
function bytenot_core_editor_field( $f, $get ) {
	list( $key, $label, $control ) = $f;
	$x    = $f[3] ?? array();
	$id   = 'pc-' . $key;
	$name = 'pc[' . $key . ']';
	$val  = $get( $key );
	$attr = static function ( $a ) {
		$s = '';
		foreach ( $a as $k => $v ) {
			$s .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		return $s;
	};
	$input = static fn( $n, $v, $type = 'text', $a = array() ) => '<input class="bn-input" type="' . esc_attr( $type ) . '" name="' . esc_attr( $n ) . '" value="' . esc_attr( (string) $v ) . '"' . $attr( $a ) . '>';
	$select = static function ( $n, $v, $options, $a = array() ) use ( $attr ) {
		$h = '<select class="bn-input bn-input--select" name="' . esc_attr( $n ) . '"' . $attr( $a ) . '>';
		foreach ( $options as $ov => $ol ) {
			$h .= '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $v, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
		}
		return $h . '</select>';
	};
	$help = ! empty( $x['help'] ) ? '<p class="bn-field__help">' . esc_html( $x['help'] ) . '</p>' : '';
	$row  = static fn( $control_html, $for = true ) => '<div class="bn-field">' . ( $for ? '<label class="bn-field__label" for="' . esc_attr( $id ) . '">' : '<span class="bn-field__label">' ) . esc_html( $label ) . ( $for ? '</label>' : '</span>' ) . '<div class="bn-field__control">' . $control_html . $help . '</div></div>';

	switch ( $control ) {
		case 'toggle':
			return $row( '<label class="bn-switch"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( (bool) $val, true, false ) . '><span class="bn-switch__track" aria-hidden="true"></span><span class="bn-switch__label">' . esc_html__( 'Yes', 'bytenot-core' ) . '</span></label>', false );
		case 'select':
			return $row( $select( $name, $val, $x['options'], array( 'id' => $id ) ) );
		case 'textarea':
			return $row( '<textarea class="bn-input" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="4">' . esc_textarea( (string) $val ) . '</textarea>' );
		case 'readonly':
			$pair = ! empty( $x['pair_text'] ) ? ' <span class="bn-muted">' . esc_html( (string) $get( $x['pair_text'] ) ) . '</span>' : '';
			return $row( '<p class="bn-editor__static">' . ( $val ? esc_html( (string) $val ) . $pair : '<span class="bn-muted">' . esc_html__( 'Added here', 'bytenot-core' ) . '</span>' ) . '</p>', false );
		case 'money':
		case 'number':
		case 'month':
		case 'url':
		case 'text':
			$type = in_array( $control, array( 'money', 'number' ), true ) ? 'number' : ( 'month' === $control ? 'text' : $control );
			$a    = array( 'id' => $id );
			foreach ( array( 'step', 'min', 'max', 'placeholder' ) as $k ) {
				if ( isset( $x[ $k ] ) ) {
					$a[ $k ] = $x[ $k ];
				}
			}
			if ( 'money' === $control ) {
				$a['min'] = '0';
				$a['step'] = '1';
			}
			if ( 'month' === $control ) {
				$a['placeholder'] = $a['placeholder'] ?? 'YYYY-MM';
			}
			$html = 'money' === $control ? '<span class="bn-money"><span class="bn-money__sign">₹</span>' . $input( $name, $val, $type, $a ) . '</span>' : $input( $name, $val, $type, $a );
			if ( ! empty( $x['pair'] ) ) {
				foreach ( $x['pair'] as $pk => $opts ) {
					$html .= $select( 'pc[' . $pk . ']', $get( $pk ), $opts );
				}
				$html = '<div class="bn-row">' . $html . '</div>';
			} elseif ( ! empty( $x['pair_number'] ) ) {
				$pn   = $x['pair_number'];
				$html = '<div class="bn-row bn-row--pair">' . $html . '<span class="bn-row__sep">' . esc_html( $x['pair_label'] ) . '</span>' . ( 'money' === $control ? '<span class="bn-money"><span class="bn-money__sign">₹</span>' . $input( 'pc[' . $pn . ']', $get( $pn ), 'number', array( 'min' => '0', 'step' => isset( $x['step'] ) ? $x['step'] : '1' ) ) . '</span>' : $input( 'pc[' . $pn . ']', $get( $pn ), 'number', array( 'step' => isset( $x['step'] ) ? $x['step'] : '1' ) ) ) . '</div>';
			} elseif ( ! empty( $x['pair_month'] ) ) {
				$html = '<div class="bn-row bn-row--pair">' . $html . '<span class="bn-row__sep">' . esc_html( $x['pair_label'] ) . '</span>' . $input( 'pc[' . $x['pair_month'] . ']', $get( $x['pair_month'] ), 'text', array( 'placeholder' => 'YYYY-MM' ) ) . '</div>';
			}
			return $row( $html );
		case 'media':
			$ids  = array_values( array_filter( array_map( 'absint', (array) $val ) ) );
			$html = '<div class="bn-media" data-media="multiple"><ul class="bn-media__list">';
			foreach ( $ids as $aid ) {
				$html .= '<li data-id="' . (int) $aid . '">' . wp_get_attachment_image( $aid, 'thumbnail' ) . '<button type="button" class="bn-media__remove" aria-label="' . esc_attr__( 'Remove', 'bytenot-core' ) . '">×</button></li>';
			}
			$html .= '</ul><input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '"><button type="button" class="bn-btn bn-btn--ghost bn-btn--small bn-media__add">' . esc_html__( 'Add photos', 'bytenot-core' ) . '</button></div>';
			return $row( $html, false );
		case 'file':
			$aid  = absint( $val );
			$html = '<div class="bn-media bn-media--file" data-media="file"><input type="hidden" name="' . esc_attr( $name ) . '" value="' . ( $aid ? $aid : '' ) . '"><span class="bn-media__name">' . ( $aid ? esc_html( basename( (string) get_attached_file( $aid ) ) ) : '<span class="bn-muted">' . esc_html__( 'None', 'bytenot-core' ) . '</span>' ) . '</span> <button type="button" class="bn-btn bn-btn--ghost bn-btn--small bn-media__add">' . esc_html__( 'Choose', 'bytenot-core' ) . '</button> <button type="button" class="bn-btn bn-btn--ghost bn-btn--small bn-media__clear"' . ( $aid ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'bytenot-core' ) . '</button></div>';
			return $row( $html, false );
		case 'posts':
			$posts   = get_posts( array( 'post_type' => $x['post_type'], 'posts_per_page' => 200, 'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true ) );
			$options = array( '' => __( '—', 'bytenot-core' ) );
			foreach ( $posts as $pp ) {
				$options[ $pp->ID ] = $pp->post_title;
			}
			return $row( $select( $name, $val, $options, array( 'id' => $id ) ) );
		case 'landmarks':
			$rows  = is_array( $val ) ? $val : array();
			$types = bytenot_core_editor_options( 'landmark' );
			$units = bytenot_core_editor_options( 'unit' );
			$line  = static function ( $i, $r ) use ( $select, $input, $types, $units ) {
				$n = 'pc[landmarks][' . $i . ']';
				return '<li class="bn-lm">' . $input( $n . '[name]', $r['name'] ?? '', 'text', array( 'placeholder' => __( 'Raidurg Metro', 'bytenot-core' ) ) ) . $select( $n . '[type]', $r['type'] ?? 'other', $types ) . $input( $n . '[distance]', $r['distance'] ?? '', 'number', array( 'step' => '0.1', 'min' => '0', 'placeholder' => '2.1' ) ) . $select( $n . '[unit]', $r['unit'] ?? 'km', $units ) . $input( $n . '[time]', $r['time'] ?? '', 'text', array( 'placeholder' => __( '6 min', 'bytenot-core' ) ) ) . '<button type="button" class="bn-media__remove bn-lm__remove" aria-label="' . esc_attr__( 'Remove', 'bytenot-core' ) . '">×</button></li>';
			};
			$html = '<div class="bn-landmarks"><div class="bn-lm bn-lm--head"><span>' . esc_html__( 'Name', 'bytenot-core' ) . '</span><span>' . esc_html__( 'Type', 'bytenot-core' ) . '</span><span>' . esc_html__( 'Distance', 'bytenot-core' ) . '</span><span></span><span>' . esc_html__( 'Drive', 'bytenot-core' ) . '</span><span></span></div><ul class="bn-landmarks__list">';
			foreach ( array_values( $rows ) as $i => $r ) {
				$html .= $line( $i, $r );
			}
			$html .= '</ul><template class="bn-landmarks__tpl">' . $line( '__i__', array() ) . '</template><button type="button" class="bn-btn bn-btn--ghost bn-btn--small bn-landmarks__add">' . esc_html__( 'Add a place', 'bytenot-core' ) . '</button></div>';
			return $row( $html, false );
	}
	return '';
}

/**
 * Save the panel.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
function bytenot_core_editor_save( $post_id, $post ) {
	if ( ! isset( $_POST['bytenot_core_editor_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bytenot_core_editor_nonce'] ), 'bytenot_core_editor' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$fields = bytenot_core_fields()[ $post->post_type ] ?? array();
	$in     = isset( $_POST['pc'] ) ? wp_unslash( (array) $_POST['pc'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each value goes through its field's sanitiser below.
	$shown  = array();
	foreach ( bytenot_core_editor_layout( $post->post_type ) as $group ) {
		foreach ( $group as $f ) {
			$shown[] = $f[0];
			$x       = $f[3] ?? array();
			foreach ( array( 'pair_number', 'pair_month', 'pair_text' ) as $pk ) {
				if ( ! empty( $x[ $pk ] ) ) {
					$shown[] = $x[ $pk ];
				}
			}
			if ( ! empty( $x['pair'] ) ) {
				$shown = array_merge( $shown, array_keys( $x['pair'] ) );
			}
		}
	}
	foreach ( $shown as $key ) {
		if ( ! isset( $fields[ $key ] ) || in_array( $key, array( 'source', 'source_id', 'sold_at' ), true ) ) {
			continue;
		}
		$raw = $in[ $key ] ?? '';
		if ( 'array' === $fields[ $key ]['type'] && ! is_array( $raw ) ) {
			$raw = array_filter( array_map( 'trim', explode( ',', (string) $raw ) ) );
		}
		if ( 'landmarks' === $key && is_array( $raw ) ) {
			$raw = array_values( array_filter( $raw, static fn( $r ) => is_array( $r ) && '' !== trim( (string) ( $r['name'] ?? '' ) ) ) );
		}
		$clean = call_user_func( $fields[ $key ]['sanitize_callback'], $raw );
		if ( 'boolean' === $fields[ $key ]['type'] ) {
			$clean = ! empty( $raw );
		}
		if ( '' === $clean || null === $clean || ( is_array( $clean ) && ! $clean ) ) {
			delete_post_meta( $post_id, 'pc_' . $key );
		} else {
			update_post_meta( $post_id, 'pc_' . $key, $clean );
		}
	}
}
add_action( 'save_post_property', 'bytenot_core_editor_save', 10, 2 );
add_action( 'save_post_project', 'bytenot_core_editor_save', 10, 2 );
add_action( 'save_post_agent', 'bytenot_core_editor_save', 10, 2 );

/**
 * Useful columns on the listings list.
 *
 * @param array $columns Columns.
 * @return array
 */
function bytenot_core_property_columns( $columns ) {
	$out = array();
	foreach ( $columns as $k => $v ) {
		$out[ $k ] = $v;
		if ( 'title' === $k ) {
			$out['pc_price'] = __( 'Price', 'bytenot-core' );
			$out['pc_size']  = __( 'Size', 'bytenot-core' );
			$out['pc_flags'] = __( 'Flags', 'bytenot-core' );
		}
	}
	unset( $out['author'] );
	return $out;
}
add_filter( 'manage_property_posts_columns', 'bytenot_core_property_columns' );

/**
 * Column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function bytenot_core_property_column( $column, $post_id ) {
	if ( 'pc_price' === $column ) {
		echo esc_html( bytenot_core_get( $post_id, 'price_on_request' ) ? __( 'On request', 'bytenot-core' ) : bytenot_core_format_price( bytenot_core_get( $post_id, 'price' ), bytenot_core_is_rent( $post_id ) ) );
	} elseif ( 'pc_size' === $column ) {
		echo esc_html( trim( bytenot_core_get( $post_id, 'bedroom_label' ) . ' · ' . bytenot_core_format_area( bytenot_core_get( $post_id, 'area_primary' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' ), ' ·' ) );
	} elseif ( 'pc_flags' === $column ) {
		if ( bytenot_core_get( $post_id, 'featured' ) ) {
			echo '<span class="bn-badge bn-badge--warn">' . esc_html__( 'Featured', 'bytenot-core' ) . '</span> ';
		}
		if ( has_term( bytenot_core_closed_statuses(), 'listing_status', $post_id ) ) {
			echo '<span class="bn-badge bn-badge--muted">' . esc_html__( 'Closed', 'bytenot-core' ) . '</span>';
		}
	}
}
add_action( 'manage_property_posts_custom_column', 'bytenot_core_property_column', 10, 2 );
