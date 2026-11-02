<?php
/**
 * Everything the property page shows, assembled from Bytenot Core's fields.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Split the content into chapters (one per h2/h3) and the remaining prose.
 *
 * @param string $html Rendered content.
 * @return array{chapters: array<int, array{title:string, body:string}>, prose: string}
 */
function bytenot_chapters_from_content( $html ) {
	$parts    = preg_split( '/(?=<h[23][\s>])/i', (string) $html, -1, PREG_SPLIT_NO_EMPTY );
	$chapters = array();
	$prose    = array();
	foreach ( $parts as $part ) {
		if ( preg_match( '/^<h[23][^>]*>(.*?)<\/h[23]>(.*)$/is', trim( $part ), $m ) && count( $chapters ) < 3 ) {
			$chapters[] = array( 'title' => wp_strip_all_tags( $m[1] ), 'body' => trim( $m[2] ) );
		} elseif ( ! $chapters ) {
			$prose[] = trim( $part ); // what comes before the first chapter is the specification prose
		}
	}
	$prose = trim( implode( "\n", $prose ) );
	if ( ! $chapters && $prose ) {
		// no headings: the first paragraph opens the story, the rest stays as prose
		if ( preg_match( '/^(\s*<p[^>]*>.*?<\/p>)(.*)$/is', $prose, $m ) ) {
			$chapters[] = array( 'title' => __( 'The home', 'bytenot-estate' ), 'body' => trim( $m[1] ) );
			$prose      = trim( $m[2] );
		}
	}
	return array( 'chapters' => $chapters, 'prose' => $prose );
}

/**
 * The property page's data.
 *
 * @param int $id Property ID.
 * @return array<string, mixed>
 */
function bytenot_property( $id ) {
	$p    = bytenot_listing( $id );
	$get  = static fn( $k ) => bytenot_core_get( $id, $k );
	$rent = 'rent' === $p['intent'];
	$plot = 'plot' === $p['type'];
	$unit = 'sqyd' === $p['unit'] ? __( 'sq yd', 'bytenot-estate' ) : ( 'sqm' === $p['unit'] ? __( 'sq m', 'bytenot-estate' ) : __( 'sq ft', 'bytenot-estate' ) );
	$fmt  = static fn( $n ) => bytenot_core_format_indian( $n );
	$site = get_bloginfo( 'name' );

	// photos: featured image first, then the gallery
	$photo_ids = array_values( array_unique( array_filter( array_merge( array( get_post_thumbnail_id( $id ) ), (array) $get( 'gallery' ) ) ) ) );
	$photos    = array();
	foreach ( $photo_ids as $pid ) {
		$src = wp_get_attachment_image_url( $pid, 'full' );
		if ( $src ) {
			$photos[] = array( 'id' => $pid, 'src' => $src, 'large' => wp_get_attachment_image_url( $pid, 'large' ) ?: $src, 'thumb' => wp_get_attachment_image_url( $pid, 'bytenot-card' ) ?: $src, 'caption' => wp_get_attachment_caption( $pid ) );
		}
	}

	$facing  = (string) $get( 'facing' );
	$floor   = (int) $get( 'floor' );
	$total   = (int) $get( 'total_floors' );
	$floors  = '';
	if ( $total ) {
		$floors = 'villa' === $p['type'] ? 'G+' . max( 0, $total - 1 ) : ( $floor ? sprintf( /* translators: 1: floor, 2: total floors. */ __( '%1$d of %2$d', 'bytenot-estate' ), $floor, $total ) : sprintf( /* translators: %d: floors. */ __( '%d floors', 'bytenot-estate' ), $total ) );
	}
	$area_type = (string) $get( 'area_primary_type' );
	$area_word = array( 'carpet' => __( 'Carpet area', 'bytenot-estate' ), 'built-up' => __( 'Built-up area', 'bytenot-estate' ), 'super-built-up' => __( 'Super built-up area', 'bytenot-estate' ), 'plot' => __( 'Plot area', 'bytenot-estate' ) );
	$area_label = $area_word[ $area_type ] ?? ( $plot ? $area_word['plot'] : $area_word['carpet'] );
	$area_html  = $p['sqft'] ? $fmt( $p['sqft'] ) . '<small>' . esc_html( $unit ) . '</small>' : '';
	$psf        = ! $rent && $p['sqft'] && $p['price'] ? '₹' . $fmt( $p['price'] / $p['sqft'] ) : '';

	// one line under the title
	$line = array();
	if ( $plot ) {
		$line[] = $p['sqft'] ? sprintf( /* translators: 1: area, 2: unit. */ __( '%1$s %2$s plot', 'bytenot-estate' ), $fmt( $p['sqft'] ), $unit ) : __( 'Plot', 'bytenot-estate' );
	} else {
		$line[] = trim( $p['bhkLabel'] . ' ' . strtolower( $p['typeLabel'] ) );
		if ( $p['sqft'] ) {
			$line[] = $fmt( $p['sqft'] ) . ' ' . $unit . ( 'carpet' === $area_type ? ' ' . __( 'carpet', 'bytenot-estate' ) : '' );
		}
	}
	if ( $facing ) {
		/* translators: %s: compass direction. */
		$line[] = sprintf( __( '%s facing', 'bytenot-estate' ), $facing );
	}

	// at a glance
	$glance = array();
	if ( $plot ) {
		$glance[] = array( __( 'Plot area', 'bytenot-estate' ), $area_html );
		if ( $get( 'rera_state' ) ) {
			$glance[] = array( __( 'Approval', 'bytenot-estate' ), esc_html( $get( 'rera_state' ) ) );
		}
	} else {
		if ( $p['bhkLabel'] ) {
			$glance[] = array( __( 'Configuration', 'bytenot-estate' ), esc_html( $p['bhkLabel'] ) );
		}
		$glance[] = array( $area_label, $area_html );
		if ( $p['baths'] ) {
			$glance[] = array( __( 'Bathrooms', 'bytenot-estate' ), esc_html( rtrim( rtrim( number_format( $p['baths'], 1, '.', '' ), '0' ), '.' ) ) );
		}
	}
	if ( $facing ) {
		$glance[] = array( __( 'Facing', 'bytenot-estate' ), esc_html( $facing ) );
	}
	if ( $floors ) {
		$glance[] = array( __( 'Floors', 'bytenot-estate' ), esc_html( $floors ) );
	}
	$glance[] = array( $rent ? __( 'Availability', 'bytenot-estate' ) : __( 'Possession', 'bytenot-estate' ), esc_html( $p['status'] ) );
	$glance   = array_values( array_filter( $glance, static fn( $r ) => '' !== $r[1] ) );

	// specification
	$specs = array_filter(
		array(
			__( 'Facing', 'bytenot-estate' )     => $facing,
			__( 'Floors', 'bytenot-estate' )     => $floors,
			__( 'Built in', 'bytenot-estate' )   => (string) ( (int) $get( 'year_built' ) ?: '' ),
			__( 'Parking', 'bytenot-estate' )    => (string) $get( 'parking' ),
			__( 'Furnishing', 'bytenot-estate' ) => (string) $get( 'furnishing' ),
			__( 'Ownership', 'bytenot-estate' )  => (string) $get( 'ownership' ),
		)
	);
	$features = get_the_terms( $id, 'feature' );
	$features = $features && ! is_wp_error( $features ) ? wp_list_pluck( $features, 'name' ) : array();
	$amenity_text = '';
	if ( $features ) {
		$last         = array_pop( $features );
		$amenity_text = $features ? sprintf( /* translators: 1: list, 2: last item. */ __( '%1$s and %2$s.', 'bytenot-estate' ), implode( ', ', $features ), $last ) : $last . '.';
		$amenity_text = ucfirst( $amenity_text );
	}

	// story
	$post    = get_post( $id );
	$content = bytenot_chapters_from_content( apply_filters( 'the_content', $post->post_content ) );
	foreach ( $content['chapters'] as $i => &$ch ) {
		$ch['photo'] = $photos[ $i ] ?? ( $photos[0] ?? null );
		$ch['cap']   = $ch['photo'] && $ch['photo']['caption'] ? $ch['photo']['caption'] : sprintf( /* translators: 1: figure number, 2: chapter title, 3: locality. */ __( 'Fig. %1$d · %2$s, %3$s', 'bytenot-estate' ), $i + 1, $ch['title'], $p['area'] );
	}
	unset( $ch );

	// plans
	$plans = array();
	foreach ( (array) $get( 'floor_plans' ) as $i => $pid ) {
		$src = wp_get_attachment_image_url( $pid, 'full' );
		if ( ! $src ) {
			continue;
		}
		$att   = get_post( $pid );
		$rooms = array();
		foreach ( preg_split( '/\r?\n/', (string) ( $att->post_content ?? '' ) ) as $row ) {
			if ( false !== strpos( $row, '|' ) ) {
				list( $k, $v ) = array_map( 'trim', explode( '|', $row, 2 ) );
				$rooms[]       = array( $k, $v );
			}
		}
		$plans[] = array( 'key' => 'plan-' . ( $i + 1 ), 'title' => $att->post_title ?: sprintf( /* translators: %d: plan number. */ __( 'Plan %d', 'bytenot-estate' ), $i + 1 ), 'src' => $src, 'rooms' => $rooms, 'fig' => sprintf( /* translators: 1: figure number, 2: plan title. */ __( 'Fig. %1$d — %2$s', 'bytenot-estate' ), $i + 1, $att->post_title ?: sprintf( __( 'Plan %d', 'bytenot-estate' ), $i + 1 ) ) );
	}
	$brochure = (int) $get( 'brochure_id' ) ? wp_get_attachment_url( (int) $get( 'brochure_id' ) ) : '';

	// numbers
	$duty_pct = (float) ( function_exists( 'bytenot_core_setting' ) ? bytenot_core_setting( 'stamp_duty' ) : 6 );
	$duty     = round( $p['price'] * $duty_pct / 100 );
	$deal     = array();
	if ( $rent ) {
		$deal[] = array( __( 'Monthly rent', 'bytenot-estate' ), bytenot_core_format_price( $p['price'] ) );
		if ( (int) $get( 'deposit' ) ) {
			$deal[] = array( __( 'Deposit', 'bytenot-estate' ), bytenot_core_format_price( (int) $get( 'deposit' ) ) );
		}
		if ( (int) $get( 'maintenance' ) ) {
			$deal[] = array( __( 'Maintenance', 'bytenot-estate' ), '₹' . $fmt( (int) $get( 'maintenance' ) ) . '<small>' . esc_html__( '/ month', 'bytenot-estate' ) . '</small>' );
		}
		if ( $area_html ) {
			$deal[] = array( $area_label, $area_html );
		}
		if ( $p['sqft'] && $p['price'] ) {
			$deal[] = array( sprintf( /* translators: %s: unit. */ __( 'Per %s', 'bytenot-estate' ), $unit ), '₹' . number_format( $p['price'] / $p['sqft'], 1 ) );
		}
	} elseif ( $plot ) {
		$deal[] = array( __( 'Plot area', 'bytenot-estate' ), $area_html );
		if ( $psf ) {
			$deal[] = array( sprintf( __( 'Per %s', 'bytenot-estate' ), $unit ), $psf );
		}
		$deal[] = array( __( 'Stamp duty & registration', 'bytenot-estate' ), bytenot_core_format_price( $duty ) );
		$deal[] = array( __( 'All-in', 'bytenot-estate' ), bytenot_core_format_price( $p['price'] + $duty ), 'accent' );
	} else {
		$deal[] = array( $area_label, $area_html );
		$secondary = (float) $get( 'area_secondary' );
		if ( $secondary ) {
			$deal[] = array( $area_word[ (string) $get( 'area_secondary_type' ) ] ?? __( 'Built-up area', 'bytenot-estate' ), $fmt( $secondary ) . '<small>' . esc_html( $unit ) . '</small>' );
			if ( $p['sqft'] && $secondary > $p['sqft'] ) {
				$deal[] = array( __( 'Loading', 'bytenot-estate' ), round( ( $secondary / $p['sqft'] - 1 ) * 100 ) . '<small>%</small>' );
			}
		}
		$deal[] = array( __( 'Stamp duty & registration', 'bytenot-estate' ), bytenot_core_format_price( $duty ) );
		$deal[] = array( __( 'All-in, before loan', 'bytenot-estate' ), bytenot_core_format_price( $p['price'] + $duty ), 'accent' );
	}
	$deal = array_values( array_filter( $deal, static fn( $r ) => '' !== $r[1] ) );
	$emi  = ! $rent && $p['price'] ? array(
		'price' => $p['price'],
		'rate'  => (float) bytenot_core_setting( 'emi_rate' ),
		'years' => (int) bytenot_core_setting( 'emi_years' ),
		'ltv'   => (int) bytenot_core_setting( 'emi_ltv' ),
	) : null;

	// the place
	$landmarks = array();
	foreach ( (array) $get( 'landmarks' ) as $l ) {
		$km = 'm' === $l['unit'] ? $l['distance'] / 1000 : ( 'km' === $l['unit'] ? (float) $l['distance'] : null );
		$landmarks[] = array(
			'name' => $l['name'],
			'type' => ucfirst( str_replace( '-', ' ', (string) $l['type'] ) ),
			'km'   => null === $km ? null : round( $km, 1 ),
			't'    => (string) ( $l['time'] ?? ( 'min' === $l['unit'] ? sprintf( /* translators: %s: minutes. */ __( '%s min', 'bytenot-estate' ), $l['distance'] ) : '' ) ),
		);
	}
	$lat = (float) $get( 'lat' );
	$lng = (float) $get( 'lng' );

	// papers
	$papers = array();
	if ( $get( 'rera_number' ) ) {
		$papers[] = array( __( 'RERA registration', 'bytenot-estate' ), '<span>' . esc_html( $get( 'rera_number' ) ) . '</span>' . ( $get( 'rera_url' ) ? '<a href="' . esc_url( $get( 'rera_url' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Verify', 'bytenot-estate' ) . '</a>' : '' ) );
	}
	if ( $get( 'rera_state' ) ) {
		$papers[] = array( __( 'Authority', 'bytenot-estate' ), esc_html( $get( 'rera_state' ) ) );
	}
	foreach ( array_values( array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) $get( 'approvals' ) ) ) ) ) as $i => $a ) {
		$papers[] = array( 0 === $i ? __( 'Approvals', 'bytenot-estate' ) : '', esc_html( $a ) );
	}
	if ( $get( 'banks' ) ) {
		$papers[] = array( __( 'Loans pre-approved with', 'bytenot-estate' ), esc_html( $get( 'banks' ) ) );
	}
	if ( bytenot_mod( 'papers_documents' ) ) {
		$papers[] = array( __( 'Documents on request', 'bytenot-estate' ), esc_html( bytenot_mod( 'papers_documents' ) ) );
	}

	// the advisor
	$agent_id = (int) $get( 'agent_id' );
	$agent    = $agent_id && 'agent' === get_post_type( $agent_id ) ? get_post( $agent_id ) : null;
	$advisor  = array(
		'name'     => $agent ? $agent->post_title : $site,
		'first'    => $agent ? explode( ' ', $agent->post_title )[0] : $site,
		'meta'     => $agent ? trim( get_post_meta( $agent_id, 'pc_role', true ) . ( get_post_meta( $agent_id, 'pc_rera_number', true ) ? ' · ' . sprintf( /* translators: %s: agent RERA number. */ __( 'RERA agent %s', 'bytenot-estate' ), get_post_meta( $agent_id, 'pc_rera_number', true ) ) : '' ), ' ·' ) : '',
		'photo'    => $agent ? get_post_thumbnail_id( $agent_id ) : 0,
		'phone'    => ( $agent ? get_post_meta( $agent_id, 'pc_phone', true ) : '' ) ?: bytenot_contact( 'phone' ),
		'whatsapp' => ( $agent ? get_post_meta( $agent_id, 'pc_whatsapp', true ) : '' ) ?: bytenot_contact( 'whatsapp' ),
	);
	/* translators: 1: property title, 2: reference, 3: locality. */
	$wa_text = sprintf( __( "Hi, I'm interested in %1\$s (%2\$s) in %3\$s. Can we arrange a visit?", 'bytenot-estate' ), $p['title'], $p['ref'] ?: '#' . $id, $p['area'] );

	// nearby: same city, same transaction, closest price
	$similar = array();
	if ( $p['city'] ) {
		$ids = get_posts(
			array(
				'post_type'      => 'property',
				'post_status'    => 'publish',
				'posts_per_page' => 40,
				'post__not_in'   => array( $id ),
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'tax_query'      => array(
					array( 'taxonomy' => 'location', 'field' => 'slug', 'terms' => $p['city'], 'include_children' => true ),
					array( 'taxonomy' => 'listing_status', 'field' => 'slug', 'terms' => $rent ? array( 'for-rent' ) : array( 'for-sale', 'under-offer' ) ),
				),
			)
		);
		$similar = array_map( 'bytenot_listing', $ids );
		usort( $similar, static fn( $a, $b ) => abs( $a['price'] - $p['price'] ) <=> abs( $b['price'] - $p['price'] ) );
		$similar = array_slice( $similar, 0, 3 );
	}

	$modified = get_post_modified_time( 'U', true, $id );
	return array_merge(
		$p,
		array(
			'rent'        => $rent,
			'plot'        => $plot,
			'unit_label'  => $unit,
			'photos'      => $photos,
			'oneline'     => implode( ' · ', array_filter( $line ) ),
			'psf_long'    => $rent ? ( $p['sqft'] ? $fmt( $p['sqft'] ) . ' ' . $unit . ( (int) $get( 'deposit' ) ? ' · ' . sprintf( /* translators: %s: deposit. */ __( 'deposit %s', 'bytenot-estate' ), bytenot_core_format_price( (int) $get( 'deposit' ) ) ) : '' ) : __( 'per month', 'bytenot-estate' ) ) : ( $psf ? sprintf( /* translators: 1: price, 2: unit. */ __( '%1$s per %2$s', 'bytenot-estate' ), $psf, $unit ) : '' ),
			'psf_carpet'  => $rent ? __( 'per month, maintenance extra', 'bytenot-estate' ) : ( $psf ? sprintf( __( '%1$s per %2$s', 'bytenot-estate' ), $psf, $plot ? $unit : sprintf( /* translators: %s: unit. */ __( 'carpet %s', 'bytenot-estate' ), $unit ) ) : '' ),
			'ref_text'    => $p['ref'] ?: '#' . $id,
			/* translators: %s: human time. */
			'updated'     => sprintf( __( '%s ago', 'bytenot-estate' ), human_time_diff( $modified ) ),
			'glance'      => $glance,
			'chapters'    => $content['chapters'],
			'prose'       => $content['prose'],
			'specs'       => $specs,
			'amenities'   => $amenity_text,
			'plans'       => $plans,
			'brochure'    => $brochure,
			'deal'        => $deal,
			'emi'         => $emi,
			'landmarks'   => $landmarks,
			'lat'         => $lat,
			'lng'         => $lng,
			'papers'      => $papers,
			'advisor'     => $advisor,
			'wa'          => bytenot_wa( $advisor['whatsapp'], $wa_text ),
			'similar'     => $similar,
			'city_url'    => $p['city'] ? add_query_arg( 'location', $p['city'], get_post_type_archive_link( 'property' ) ) : get_post_type_archive_link( 'property' ),
		)
	);
}
