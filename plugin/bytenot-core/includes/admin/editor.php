<?php
/**
 * The listing editor: a "Listing details" panel on listing and advisor edit screens, in
 * the plugin's design. The kind (project, apartment, property) is chosen at the top and
 * decides which groups show; fields are defined once (meta.php) and drawn here in
 * groups. Media pickers, the landmarks repeater and the kind switch come from
 * assets/admin/editor.js.
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
		'rent'       => array( '' => __( 'Not a rental', 'bytenot-core' ), 'month' => __( 'per month', 'bytenot-core' ), 'year' => __( 'per year', 'bytenot-core' ) ),
		'landmark'   => array( 'metro' => __( 'Metro', 'bytenot-core' ), 'work' => __( 'Work', 'bytenot-core' ), 'school' => __( 'School', 'bytenot-core' ), 'hospital' => __( 'Hospital', 'bytenot-core' ), 'shopping' => __( 'Shopping', 'bytenot-core' ), 'airport' => __( 'Airport', 'bytenot-core' ), 'park' => __( 'Park', 'bytenot-core' ), 'other' => __( 'Other', 'bytenot-core' ) ),
		'unit'       => array( 'km' => 'km', 'm' => 'm', 'min' => 'min' ),
	);
	return $sets[ $set ];
}

/**
 * The editor layout: groups of fields, each tagged with the kinds it applies to.
 * Controls: text, number, money, select, toggle, textarea, month, url, email, media, file, landmarks, posts, readonly.
 *
 * @param string $post_type listing | agent.
 * @return array<int, array{id:string,label:string,kinds:string[],fields:array<int, array>}>
 */
function bytenot_core_editor_layout( $post_type ) {
	if ( 'agent' === $post_type ) {
		return array(
			array(
				'id'     => 'advisor',
				'label'  => __( 'Advisor', 'bytenot-core' ),
				'kinds'  => array(),
				'fields' => array(
					array( 'role', __( 'Role', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Senior advisor, west side', 'bytenot-core' ) ) ),
					array( 'phone', __( 'Phone', 'bytenot-core' ), 'text' ),
					array( 'whatsapp', __( 'WhatsApp', 'bytenot-core' ), 'text', array( 'help' => __( 'Digits with the country code.', 'bytenot-core' ) ) ),
					array( 'email', __( 'Email', 'bytenot-core' ), 'email' ),
					array( 'rera_number', __( 'RERA agent number', 'bytenot-core' ), 'text' ),
				),
			),
		);
	}
	$all   = array( 'project', 'apartment', 'property' );
	$homes = array( 'apartment', 'property' );
	return array(
		array(
			'id'     => 'project',
			'label'  => __( 'Project', 'bytenot-core' ),
			'kinds'  => array( 'project' ),
			'fields' => array(
				array( 'developer_id', __( 'Developer', 'bytenot-core' ), 'posts', array( 'post_type' => 'partner', 'help' => __( 'A partner from Content › Partners, or type a name below.', 'bytenot-core' ) ) ),
				array( 'developer', __( 'Developer name', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'If the developer is not a partner', 'bytenot-core' ) ) ),
				array( 'price_min', __( 'Price from', 'bytenot-core' ), 'money', array( 'pair_label' => __( 'to', 'bytenot-core' ), 'pair_number' => 'price_max', 'help' => __( 'Filled in from the apartments listed under the project whenever it has any.', 'bytenot-core' ) ) ),
				array( 'config_label', __( 'Configurations', 'bytenot-core' ), 'text', array( 'placeholder' => __( '2, 3 BHK', 'bytenot-core' ), 'help' => __( 'Also filled in from the apartments.', 'bytenot-core' ) ) ),
				array( 'area_min', __( 'Area from', 'bytenot-core' ), 'number', array( 'pair_label' => __( 'to', 'bytenot-core' ), 'pair_number' => 'area_max', 'pair' => array( 'area_unit' => bytenot_core_editor_options( 'area_unit' ) ) ) ),
				array( 'units_total', __( 'Units', 'bytenot-core' ), 'number', array( 'pair_label' => __( 'towers', 'bytenot-core' ), 'pair_number' => 'towers' ) ),
				array( 'launch_date', __( 'Launched', 'bytenot-core' ), 'month', array( 'pair_label' => __( 'completion', 'bytenot-core' ), 'pair_month' => 'completion_date' ) ),
				array( 'possession_date', __( 'Possession', 'bytenot-core' ), 'month', array( 'help' => __( 'YYYY-MM or a full date; shown on the card while the project is under construction.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'pricing',
			'label'  => __( 'Pricing', 'bytenot-core' ),
			'kinds'  => $homes,
			'fields' => array(
				array( 'price', __( 'Price', 'bytenot-core' ), 'money', array( 'help' => __( 'Whole rupees. For rentals, the monthly rent.', 'bytenot-core' ) ) ),
				array( 'price_on_request', __( 'Price on request', 'bytenot-core' ), 'toggle' ),
				array( 'rent_period', __( 'Rent period', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'rent' ), 'help' => __( 'Only for rentals (status "For rent").', 'bytenot-core' ) ) ),
				array( 'deposit', __( 'Deposit', 'bytenot-core' ), 'money' ),
				array( 'maintenance', __( 'Maintenance', 'bytenot-core' ), 'money', array( 'help' => __( 'Per month.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'size',
			'label'  => __( 'Size', 'bytenot-core' ),
			'kinds'  => $homes,
			'fields' => array(
				array( 'bedrooms', __( 'Bedrooms', 'bytenot-core' ), 'number', array( 'step' => '0.5' ) ),
				array( 'bedroom_label', __( 'Configuration label', 'bytenot-core' ), 'text', array( 'placeholder' => __( '3 BHK, Studio, Penthouse', 'bytenot-core' ) ) ),
				array( 'bathrooms', __( 'Bathrooms', 'bytenot-core' ), 'number', array( 'step' => '0.5' ) ),
				array( 'area_primary', __( 'Area', 'bytenot-core' ), 'number', array( 'pair' => array( 'area_primary_type' => bytenot_core_editor_options( 'area_type' ), 'area_unit' => bytenot_core_editor_options( 'area_unit' ) ), 'help' => __( 'The headline area: carpet for homes, plot for land.', 'bytenot-core' ) ) ),
				array( 'area_secondary', __( 'Second area', 'bytenot-core' ), 'number', array( 'pair' => array( 'area_secondary_type' => bytenot_core_editor_options( 'area_type' ) ), 'help' => __( 'Optional: built-up or super built-up, shown in the numbers.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'building',
			'label'  => __( 'Building', 'bytenot-core' ),
			'kinds'  => $homes,
			'fields' => array(
				array( 'tower', __( 'Tower', 'bytenot-core' ), 'text', array( 'kinds' => array( 'apartment' ), 'placeholder' => __( 'Tower B', 'bytenot-core' ), 'pair_text' => 'unit_no', 'pair_label' => __( 'unit', 'bytenot-core' ) ) ),
				array( 'floor', __( 'Floor', 'bytenot-core' ), 'number', array( 'pair_label' => __( 'of', 'bytenot-core' ), 'pair_number' => 'total_floors' ) ),
				array( 'facing', __( 'Facing', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'facing' ) ) ),
				array( 'parking', __( 'Parking', 'bytenot-core' ), 'text', array( 'placeholder' => __( '2 covered + 1 open', 'bytenot-core' ) ) ),
				array( 'year_built', __( 'Built in', 'bytenot-core' ), 'number', array( 'min' => '1900', 'max' => '2100' ) ),
				array( 'furnishing', __( 'Furnishing', 'bytenot-core' ), 'select', array( 'options' => bytenot_core_editor_options( 'furnishing' ) ) ),
				array( 'ownership', __( 'Ownership', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Freehold', 'bytenot-core' ) ) ),
				array( 'possession_date', __( 'Possession / available from', 'bytenot-core' ), 'month', array( 'help' => __( 'For under-construction homes and future rentals. YYYY-MM or a full date.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'legal',
			'label'  => __( 'Legal', 'bytenot-core' ),
			'kinds'  => $all,
			'fields' => array(
				array( 'rera_number', __( 'RERA number', 'bytenot-core' ), 'text', array( 'help' => __( 'An apartment inherits its project\'s legal details when its own are empty.', 'bytenot-core' ) ) ),
				array( 'rera_state', __( 'Authority', 'bytenot-core' ), 'text', array( 'placeholder' => __( 'Telangana RERA', 'bytenot-core' ) ) ),
				array( 'rera_url', __( 'Verify at', 'bytenot-core' ), 'url', array( 'placeholder' => 'https://' ) ),
				array( 'approvals', __( 'Approvals', 'bytenot-core' ), 'textarea', array( 'help' => __( 'One per line: Occupancy certificate received, HMDA approved layout…', 'bytenot-core' ) ) ),
				array( 'banks', __( 'Loans pre-approved with', 'bytenot-core' ), 'text', array( 'placeholder' => 'SBI, HDFC, ICICI' ) ),
			),
		),
		array(
			'id'     => 'location',
			'label'  => __( 'Location', 'bytenot-core' ),
			'kinds'  => $all,
			'fields' => array(
				array( 'address_line', __( 'Address line', 'bytenot-core' ), 'text', array( 'help' => __( 'The locality and city come from the Localities box on the right. An apartment inherits its project\'s place and landmarks when its own are empty.', 'bytenot-core' ) ) ),
				array( 'lat', __( 'Latitude', 'bytenot-core' ), 'number', array( 'step' => 'any', 'pair_label' => __( 'longitude', 'bytenot-core' ), 'pair_number' => 'lng', 'help' => __( 'Right-click a spot in Google Maps and copy the two numbers.', 'bytenot-core' ) ) ),
				array( 'landmarks', __( 'Nearby', 'bytenot-core' ), 'landmarks', array( 'help' => __( 'What is close by and how far: drawn as the radial plot on the listing page.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'media',
			'label'  => __( 'Media', 'bytenot-core' ),
			'kinds'  => $all,
			'fields' => array(
				array( 'gallery', __( 'Photos', 'bytenot-core' ), 'media', array( 'help' => __( 'The featured image opens the page; these follow it in the story and the filmstrip.', 'bytenot-core' ) ) ),
				array( 'floor_plans', __( 'Floor plans', 'bytenot-core' ), 'media', array( 'help' => __( 'One image per floor. Put the room schedule in the image\'s Description, one per line: Living | 18\' × 14\'.', 'bytenot-core' ) ) ),
				array( 'brochure_id', __( 'Brochure', 'bytenot-core' ), 'file' ),
				array( 'video_url', __( 'Video', 'bytenot-core' ), 'url', array( 'placeholder' => 'https://youtube.com/…', 'help' => __( 'Linked beside the brochure.', 'bytenot-core' ) ) ),
			),
		),
		array(
			'id'     => 'people',
			'label'  => __( 'People & flags', 'bytenot-core' ),
			'kinds'  => $all,
			'fields' => array(
				array( 'agent_id', __( 'Advisor', 'bytenot-core' ), 'posts', array( 'post_type' => 'agent' ) ),
				array( 'featured', __( 'Featured on the home page', 'bytenot-core' ), 'toggle' ),
				array( 'featured_reason', __( 'Why featured', 'bytenot-core' ), 'text', array( 'placeholder' => __( "Advisor's pick, Price revised, Metro in 5 min", 'bytenot-core' ) ) ),
				array( 'reference_id', __( 'Reference', 'bytenot-core' ), 'text', array( 'placeholder' => 'HYD-0231' ) ),
				array( 'source', __( 'Source', 'bytenot-core' ), 'readonly', array( 'pair_text' => 'source_id' ) ),
			),
		),
	);
}

/**
 * Register the panel.
 */
function bytenot_core_editor_boxes() {
	add_meta_box( 'bytenot_listing', __( 'Listing details', 'bytenot-core' ), 'bytenot_core_editor_render', 'listing', 'normal', 'high' );
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
	if ( ! $screen || ! in_array( $screen->post_type, array( 'listing', 'agent' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'bytenot-core-admin', BYTENOT_CORE_URL . 'assets/admin/admin.css', array(), BYTENOT_CORE_VERSION );
	wp_enqueue_script( 'bytenot-core-editor', BYTENOT_CORE_URL . 'assets/admin/editor.js', array( 'jquery' ), BYTENOT_CORE_VERSION, true );
	wp_localize_script( 'bytenot-core-editor', 'bytenotEditor', array( 'choose' => __( 'Choose', 'bytenot-core' ), 'remove' => __( 'Remove', 'bytenot-core' ), 'photos' => __( 'Choose photos', 'bytenot-core' ), 'file' => __( 'Choose a file', 'bytenot-core' ) ) );
}
add_action( 'admin_enqueue_scripts', 'bytenot_core_editor_assets' );

/**
 * The kind an edit screen starts with: the saved one, ?kind= on a new listing, or the primary.
 *
 * @param WP_Post $post Post.
 * @return string
 */
function bytenot_core_editor_kind( $post ) {
	$kinds = bytenot_core_kinds( true );
	$terms = get_the_terms( $post->ID, 'listing_kind' );
	if ( $terms && ! is_wp_error( $terms ) && isset( bytenot_core_kinds()[ $terms[0]->slug ] ) ) {
		return $terms[0]->slug;
	}
	$asked = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return isset( $kinds[ $asked ] ) ? $asked : bytenot_core_primary_kind();
}

/**
 * Draw the panel.
 *
 * @param WP_Post $post Post.
 */
function bytenot_core_editor_render( $post ) {
	$layout = bytenot_core_editor_layout( $post->post_type );
	$get    = static fn( $k ) => get_post_meta( $post->ID, BYTENOT_CORE_META_PREFIX . $k, true );
	$kind   = 'listing' === $post->post_type ? bytenot_core_editor_kind( $post ) : '';
	wp_nonce_field( 'bytenot_core_editor', 'bytenot_core_editor_nonce' );
	echo '<div class="bn bn-editor" data-kind="' . esc_attr( $kind ) . '">';

	if ( $kind ) {
		bytenot_core_editor_kind_row( $post, $kind );
	}

	$tabs = static fn( $g ) => $g['kinds'] ? ' data-kinds="' . esc_attr( implode( ' ', $g['kinds'] ) ) . '"' : '';
	if ( count( $layout ) > 1 ) {
		echo '<nav class="bn-editor__tabs" role="tablist">';
		foreach ( $layout as $g ) {
			echo '<button type="button" class="bn-editor__tab" role="tab" aria-selected="false" data-tab="' . esc_attr( $g['id'] ) . '"' . $tabs( $g ) . '>' . esc_html( $g['label'] ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
		echo '</nav>';
	}
	foreach ( $layout as $g ) {
		echo '<div class="bn-editor__panel" data-panel="' . esc_attr( $g['id'] ) . '"' . $tabs( $g ) . ' hidden>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		foreach ( $g['fields'] as $f ) {
			echo bytenot_core_editor_field( $f, $get ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside.
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * The row above the tabs: the kind, its sub-type, and the project an apartment belongs to.
 *
 * @param WP_Post $post Post.
 * @param string  $kind Current kind.
 */
function bytenot_core_editor_kind_row( $post, $kind ) {
	$kinds   = bytenot_core_kinds( true );
	$current = get_the_terms( $post->ID, 'listing_type' );
	$current = $current && ! is_wp_error( $current ) ? $current[0]->slug : '';
	echo '<div class="bn-editor__kind">';
	echo '<div class="bn-field"><span class="bn-field__label">' . esc_html__( 'This is a', 'bytenot-core' ) . '</span><div class="bn-field__control"><div class="bn-chips bn-chips--radio">';
	foreach ( $kinds as $slug => $k ) {
		echo '<label class="bn-chip"><input type="radio" name="bn_kind" value="' . esc_attr( $slug ) . '" ' . checked( $slug, $kind, false ) . '><span>' . esc_html( $k['singular'] ) . '</span></label>';
	}
	if ( ! isset( $kinds[ $kind ] ) ) {
		echo '<label class="bn-chip"><input type="radio" name="bn_kind" value="' . esc_attr( $kind ) . '" checked><span>' . esc_html( bytenot_core_kind_label( $kind, false ) ) . '</span></label>';
	}
	echo '</div></div></div>';
	foreach ( array_keys( $kinds ) as $slug ) {
		$subtypes = bytenot_core_subtypes( $slug );
		if ( ! $subtypes ) {
			continue;
		}
		echo '<div class="bn-field" data-kinds="' . esc_attr( $slug ) . '"><label class="bn-field__label" for="bn-subtype-' . esc_attr( $slug ) . '">' . esc_html__( 'Type', 'bytenot-core' ) . '</label><div class="bn-field__control"><select class="bn-input bn-input--select" id="bn-subtype-' . esc_attr( $slug ) . '" name="bn_subtype[' . esc_attr( $slug ) . ']"><option value="">' . esc_html__( '—', 'bytenot-core' ) . '</option>';
		foreach ( $subtypes as $ts => $tl ) {
			echo '<option value="' . esc_attr( $ts ) . '"' . selected( $current, $ts, false ) . '>' . esc_html( $tl ) . '</option>';
		}
		echo '</select></div></div>';
	}
	if ( isset( $kinds['apartment'] ) ) {
		$projects = get_posts( array( 'post_type' => 'listing', 'posts_per_page' => 300, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => array( 'publish', 'draft', 'pending' ), 'no_found_rows' => true, 'tax_query' => array( array( 'taxonomy' => 'listing_kind', 'field' => 'slug', 'terms' => 'project' ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		echo '<div class="bn-field" data-kinds="apartment"><label class="bn-field__label" for="bn-parent">' . esc_html__( 'In the project', 'bytenot-core' ) . '</label><div class="bn-field__control"><select class="bn-input bn-input--select" id="bn-parent" name="bn_parent"><option value="">' . esc_html__( '— none —', 'bytenot-core' ) . '</option>';
		foreach ( $projects as $pr ) {
			echo '<option value="' . (int) $pr->ID . '"' . selected( (int) $post->post_parent, $pr->ID, false ) . '>' . esc_html( $pr->post_title ) . '</option>';
		}
		echo '</select><p class="bn-field__help">' . esc_html__( 'The apartment appears on the project\'s page and under its address.', 'bytenot-core' ) . '</p></div></div>';
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
	$id   = 'bn-' . $key;
	$name = 'bn[' . $key . ']';
	$val  = $get( $key );
	$attr = static function ( $a ) {
		$s = '';
		foreach ( $a as $k => $v ) {
			$s .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		return $s;
	};
	$input  = static fn( $n, $v, $type = 'text', $a = array() ) => '<input class="bn-input" type="' . esc_attr( $type ) . '" name="' . esc_attr( $n ) . '" value="' . esc_attr( (string) $v ) . '"' . $attr( $a ) . '>';
	$select = static function ( $n, $v, $options, $a = array() ) use ( $attr ) {
		$h = '<select class="bn-input bn-input--select" name="' . esc_attr( $n ) . '"' . $attr( $a ) . '>';
		foreach ( $options as $ov => $ol ) {
			$h .= '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $v, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
		}
		return $h . '</select>';
	};
	$help  = ! empty( $x['help'] ) ? '<p class="bn-field__help">' . esc_html( $x['help'] ) . '</p>' : '';
	$kinds = ! empty( $x['kinds'] ) ? ' data-kinds="' . esc_attr( implode( ' ', $x['kinds'] ) ) . '"' : '';
	$row   = static fn( $control_html, $for = true ) => '<div class="bn-field"' . $kinds . '>' . ( $for ? '<label class="bn-field__label" for="' . esc_attr( $id ) . '">' : '<span class="bn-field__label">' ) . esc_html( $label ) . ( $for ? '</label>' : '</span>' ) . '<div class="bn-field__control">' . $control_html . $help . '</div></div>';

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
		case 'email':
		case 'text':
			$type = in_array( $control, array( 'money', 'number' ), true ) ? 'number' : ( 'month' === $control ? 'text' : $control );
			$a    = array( 'id' => $id );
			foreach ( array( 'step', 'min', 'max', 'placeholder' ) as $k ) {
				if ( isset( $x[ $k ] ) ) {
					$a[ $k ] = $x[ $k ];
				}
			}
			if ( 'money' === $control ) {
				$a['min']  = '0';
				$a['step'] = '1';
			}
			if ( 'month' === $control ) {
				$a['placeholder'] = $a['placeholder'] ?? 'YYYY-MM';
			}
			$html = 'money' === $control ? '<span class="bn-money"><span class="bn-money__sign">₹</span>' . $input( $name, $val, $type, $a ) . '</span>' : $input( $name, $val, $type, $a );
			if ( ! empty( $x['pair_number'] ) ) {
				$pn   = $x['pair_number'];
				$html = '<div class="bn-row bn-row--pair">' . $html . '<span class="bn-row__sep">' . esc_html( $x['pair_label'] ) . '</span>' . ( 'money' === $control ? '<span class="bn-money"><span class="bn-money__sign">₹</span>' . $input( 'bn[' . $pn . ']', $get( $pn ), 'number', array( 'min' => '0', 'step' => '1' ) ) . '</span>' : $input( 'bn[' . $pn . ']', $get( $pn ), 'number', array( 'step' => isset( $x['step'] ) ? $x['step'] : '1' ) ) ) . '</div>';
			} elseif ( ! empty( $x['pair_month'] ) ) {
				$html = '<div class="bn-row bn-row--pair">' . $html . '<span class="bn-row__sep">' . esc_html( $x['pair_label'] ) . '</span>' . $input( 'bn[' . $x['pair_month'] . ']', $get( $x['pair_month'] ), 'text', array( 'placeholder' => 'YYYY-MM' ) ) . '</div>';
			} elseif ( ! empty( $x['pair_text'] ) && 'readonly' !== $control ) {
				$html = '<div class="bn-row bn-row--pair">' . $html . '<span class="bn-row__sep">' . esc_html( $x['pair_label'] ) . '</span>' . $input( 'bn[' . $x['pair_text'] . ']', $get( $x['pair_text'] ), 'text', array( 'placeholder' => '1204' ) ) . '</div>';
			}
			if ( ! empty( $x['pair'] ) ) {
				$selects = '';
				foreach ( $x['pair'] as $pk => $opts ) {
					$selects .= $select( 'bn[' . $pk . ']', $get( $pk ), $opts );
				}
				$html = '<div class="bn-row">' . $html . $selects . '</div>';
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
				$n = 'bn[landmarks][' . $i . ']';
				return '<li class="bn-lm">' . $input( $n . '[name]', $r['name'] ?? '', 'text', array( 'placeholder' => __( 'Metro station', 'bytenot-core' ) ) ) . $select( $n . '[type]', $r['type'] ?? 'other', $types ) . $input( $n . '[distance]', $r['distance'] ?? '', 'number', array( 'step' => '0.1', 'min' => '0', 'placeholder' => '2.1' ) ) . $select( $n . '[unit]', $r['unit'] ?? 'km', $units ) . $input( $n . '[time]', $r['time'] ?? '', 'text', array( 'placeholder' => __( '6 min', 'bytenot-core' ) ) ) . '<button type="button" class="bn-media__remove bn-lm__remove" aria-label="' . esc_attr__( 'Remove', 'bytenot-core' ) . '">×</button></li>';
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
 * An apartment's parent comes from the panel, not from WordPress's own attributes box.
 *
 * @param array $data    Post data about to be saved.
 * @param array $postarr Raw post array.
 * @return array
 */
function bytenot_core_editor_parent( $data, $postarr ) {
	if ( 'listing' !== $data['post_type'] || ! isset( $_POST['bytenot_core_editor_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bytenot_core_editor_nonce'] ), 'bytenot_core_editor' ) ) {
		return $data;
	}
	$kind                = isset( $_POST['bn_kind'] ) ? sanitize_key( wp_unslash( $_POST['bn_kind'] ) ) : '';
	$parent              = isset( $_POST['bn_parent'] ) ? absint( $_POST['bn_parent'] ) : 0;
	$data['post_parent'] = 'apartment' === $kind && $parent && 'listing' === get_post_type( $parent ) && $parent !== (int) ( $postarr['ID'] ?? 0 ) ? $parent : 0;
	return $data;
}
add_filter( 'wp_insert_post_data', 'bytenot_core_editor_parent', 10, 2 );

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
	$kind = '';
	if ( 'listing' === $post->post_type ) {
		$kind = isset( $_POST['bn_kind'] ) ? sanitize_key( wp_unslash( $_POST['bn_kind'] ) ) : '';
		if ( ! isset( bytenot_core_kinds()[ $kind ] ) ) {
			$kind = bytenot_core_primary_kind();
		}
		wp_set_object_terms( $post_id, $kind, 'listing_kind' );
		$subtype = isset( $_POST['bn_subtype'][ $kind ] ) ? sanitize_title( wp_unslash( $_POST['bn_subtype'][ $kind ] ) ) : '';
		wp_set_object_terms( $post_id, $subtype && isset( bytenot_core_subtypes( $kind )[ $subtype ] ) ? $subtype : array(), 'listing_type' );
	}
	$fields = bytenot_core_fields()[ $post->post_type ] ?? array();
	$in     = isset( $_POST['bn'] ) ? wp_unslash( (array) $_POST['bn'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each value goes through its field's sanitiser below.
	$shown  = array();
	foreach ( bytenot_core_editor_layout( $post->post_type ) as $g ) {
		if ( $kind && $g['kinds'] && ! in_array( $kind, $g['kinds'], true ) ) {
			continue;
		}
		foreach ( $g['fields'] as $f ) {
			$x = $f[3] ?? array();
			if ( $kind && ! empty( $x['kinds'] ) && ! in_array( $kind, $x['kinds'], true ) ) {
				continue;
			}
			$shown[] = $f[0];
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
		if ( ( 'number' === $fields[ $key ]['type'] || 'integer' === $fields[ $key ]['type'] ) && '' === trim( (string) $raw ) ) {
			$clean = '';
		}
		bytenot_core_set( $post_id, $key, $clean );
	}
	if ( 'project' === $kind ) {
		bytenot_core_rollup( $post_id );
	}
}
add_action( 'save_post_listing', 'bytenot_core_editor_save', 10, 2 );
add_action( 'save_post_agent', 'bytenot_core_editor_save', 10, 2 );

/**
 * Useful columns on the listings list.
 *
 * @param array $columns Columns.
 * @return array
 */
function bytenot_core_listing_columns( $columns ) {
	$out = array();
	foreach ( $columns as $k => $v ) {
		$out[ $k ] = $v;
		if ( 'title' === $k ) {
			$out['bn_kind']  = __( 'Kind', 'bytenot-core' );
			$out['bn_price'] = __( 'Price', 'bytenot-core' );
			$out['bn_size']  = __( 'Size', 'bytenot-core' );
			$out['bn_flags'] = __( 'Flags', 'bytenot-core' );
		}
	}
	unset( $out['author'] );
	return $out;
}
add_filter( 'manage_listing_posts_columns', 'bytenot_core_listing_columns' );

/**
 * Column values.
 *
 * @param string $column  Column.
 * @param int    $post_id Post ID.
 */
function bytenot_core_listing_column( $column, $post_id ) {
	$kind = bytenot_core_kind_of( $post_id );
	if ( 'bn_kind' === $column ) {
		echo esc_html( bytenot_core_kind_label( $kind, false ) );
		$project = bytenot_core_project_of( $post_id );
		if ( $project ) {
			echo '<br><span class="bn-muted">' . esc_html( get_the_title( $project ) ) . '</span>';
		}
	} elseif ( 'bn_price' === $column ) {
		if ( 'project' === $kind ) {
			echo esc_html( bytenot_core_format_range( bytenot_core_get( $post_id, 'price_min' ), bytenot_core_get( $post_id, 'price_max' ) ) );
		} else {
			echo esc_html( bytenot_core_get( $post_id, 'price_on_request' ) ? __( 'On request', 'bytenot-core' ) : bytenot_core_format_price( bytenot_core_get( $post_id, 'price' ), bytenot_core_is_rent( $post_id ) ) );
		}
	} elseif ( 'bn_size' === $column ) {
		if ( 'project' === $kind ) {
			echo esc_html( trim( bytenot_core_get( $post_id, 'config_label' ) . ' · ' . bytenot_core_format_area_range( bytenot_core_get( $post_id, 'area_min' ), bytenot_core_get( $post_id, 'area_max' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' ), ' ·' ) );
		} else {
			echo esc_html( trim( bytenot_core_get( $post_id, 'bedroom_label' ) . ' · ' . bytenot_core_format_area( bytenot_core_get( $post_id, 'area_primary' ), bytenot_core_get( $post_id, 'area_unit' ) ?: 'sqft' ), ' ·' ) );
		}
	} elseif ( 'bn_flags' === $column ) {
		if ( bytenot_core_get( $post_id, 'featured' ) ) {
			echo '<span class="bn-badge bn-badge--warn">' . esc_html__( 'Featured', 'bytenot-core' ) . '</span> ';
		}
		if ( has_term( bytenot_core_closed_statuses(), 'listing_status', $post_id ) ) {
			echo '<span class="bn-badge bn-badge--muted">' . esc_html__( 'Closed', 'bytenot-core' ) . '</span>';
		}
	}
}
add_action( 'manage_listing_posts_custom_column', 'bytenot_core_listing_column', 10, 2 );

/**
 * "Add listing" buttons per kind above the list, and a kind filter.
 */
function bytenot_core_listing_list_tools() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit' !== $screen->base || 'listing' !== $screen->post_type ) {
		return;
	}
	$current = isset( $_GET['listing_kind'] ) ? sanitize_key( wp_unslash( $_GET['listing_kind'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<div class="bn bn--bar bn-kindbar"><nav class="bn-subtabs" aria-label="' . esc_attr__( 'Kinds', 'bytenot-core' ) . '">';
	echo '<a class="bn-subtab' . ( $current ? '' : ' is-active' ) . '" href="' . esc_url( admin_url( 'edit.php?post_type=listing' ) ) . '">' . esc_html__( 'All', 'bytenot-core' ) . '</a>';
	foreach ( bytenot_core_kinds( true ) as $slug => $k ) {
		echo '<a class="bn-subtab' . ( $slug === $current ? ' is-active' : '' ) . '" href="' . esc_url( admin_url( 'edit.php?post_type=listing&listing_kind=' . $slug ) ) . '">' . esc_html( $k['plural'] ) . '</a>';
	}
	echo '</nav><div class="bn-actions">';
	foreach ( bytenot_core_kinds( true ) as $slug => $k ) {
		/* translators: %s: kind, e.g. "project". */
		echo '<a class="bn-btn bn-btn--small" href="' . esc_url( admin_url( 'post-new.php?post_type=listing&kind=' . $slug ) ) . '">' . esc_html( sprintf( __( 'Add %s', 'bytenot-core' ), mb_strtolower( $k['singular'] ) ) ) . '</a>';
	}
	echo '</div></div>';
}
add_action( 'all_admin_notices', 'bytenot_core_listing_list_tools', 2 );
