<?php
/**
 * Everything a listing page shows, assembled from Bytenot Core's fields. A project adds
 * its apartments and a price range; an apartment inherits its project's place, legal
 * details and amenities when its own are empty.
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
 * A field of the listing, falling back to its project's when it is an apartment.
 *
 * @param int    $id      Listing ID.
 * @param int    $project Project ID or 0.
 * @param string $key     Field key.
 * @return mixed
 */
function bytenot_inherit( $id, $project, $key ) {
	$own = bytenot_core_get( $id, $key );
	if ( '' !== $own && null !== $own && array() !== $own && 0 !== $own && '0' !== $own ) {
		return $own;
	}
	return $project ? bytenot_core_get( $project, $key ) : $own;
}

/**
 * The listing page's data.
 *
 * @param int $id Listing ID.
 * @return array<string, mixed>
 */
function bytenot_listing_page( $id ) {
	$p       = bytenot_listing( $id );
	$kind    = $p['kind'];
	$project = 'project' === $kind;
	$parent  = $p['parent'] ? $p['parent']['id'] : 0;
	$get     = static fn( $k ) => bytenot_core_get( $id, $k );
	$inh     = static fn( $k ) => bytenot_inherit( $id, $parent, $k );
	$rent    = 'rent' === $p['intent'];
	$land    = ! $project && $p['type'] && preg_match( '/plot|land/i', $p['type'] );
	$unit    = bytenot_core_area_unit_label( $p['unit'] );
	$fmt     = static fn( $n ) => bytenot_core_format_indian( $n );
	$site    = get_bloginfo( 'name' );

	// photos: featured image first, then the gallery
	$photo_ids = array_values( array_unique( array_filter( array_merge( array( get_post_thumbnail_id( $id ) ), (array) $get( 'gallery' ) ) ) ) );
	$photos    = array();
	foreach ( $photo_ids as $pid ) {
		$src = wp_get_attachment_image_url( $pid, 'full' );
		if ( $src ) {
			$photos[] = array( 'id' => $pid, 'src' => $src, 'large' => wp_get_attachment_image_url( $pid, 'large' ) ?: $src, 'thumb' => wp_get_attachment_image_url( $pid, 'bytenot-card' ) ?: $src, 'caption' => wp_get_attachment_caption( $pid ) );
		}
	}

	$facing = (string) $get( 'facing' );
	$floor  = (int) $get( 'floor' );
	$total  = (int) $inh( 'total_floors' );
	$floors = '';
	if ( $total && ! $project ) {
		$floors = $floor ? sprintf( /* translators: 1: floor, 2: total floors. */ __( '%1$d of %2$d', 'bytenot-estate' ), $floor, $total ) : sprintf( /* translators: %d: floors. */ __( '%d floors', 'bytenot-estate' ), $total );
	}
	$area_type  = (string) $get( 'area_primary_type' );
	$area_word  = array( 'carpet' => __( 'Carpet area', 'bytenot-estate' ), 'built-up' => __( 'Built-up area', 'bytenot-estate' ), 'super-built-up' => __( 'Super built-up area', 'bytenot-estate' ), 'plot' => __( 'Plot area', 'bytenot-estate' ) );
	$area_label = $area_word[ $area_type ] ?? ( $land ? $area_word['plot'] : $area_word['carpet'] );
	$area_html  = $p['sqft'] ? $fmt( $p['sqft'] ) . '<small>' . esc_html( $unit ) . '</small>' : '';
	$psf        = ! $rent && ! $project && $p['sqft'] && $p['price'] ? '₹' . $fmt( $p['price'] / $p['sqft'] ) : '';

	// one line under the title
	$line = array();
	if ( $project ) {
		$line[] = $p['line'];
	} elseif ( $land ) {
		$line[] = $p['sqft'] ? sprintf( /* translators: 1: area, 2: unit. */ __( '%1$s %2$s plot', 'bytenot-estate' ), $fmt( $p['sqft'] ), $unit ) : $p['typeLabel'];
	} else {
		$line[] = trim( $p['bhkLabel'] . ' ' . mb_strtolower( $p['typeLabel'] ?: bytenot_kind_one( $kind ) ) );
		if ( $p['sqft'] ) {
			$line[] = $fmt( $p['sqft'] ) . ' ' . $unit . ( 'carpet' === $area_type ? ' ' . __( 'carpet', 'bytenot-estate' ) : '' );
		}
	}
	if ( $facing && ! $project ) {
		/* translators: %s: compass direction. */
		$line[] = sprintf( __( '%s facing', 'bytenot-estate' ), $facing );
	}

	// at a glance
	$glance = array();
	if ( $project ) {
		if ( $p['developer'] ) {
			$glance[] = array( __( 'Developer', 'bytenot-estate' ), esc_html( $p['developer'] ) );
		}
		if ( $p['facts'][1] ) {
			$glance[] = array( __( 'Configurations', 'bytenot-estate' ), esc_html( $p['facts'][1] ) );
		}
		if ( $p['facts'][0] ) {
			$glance[] = array( __( 'Sizes', 'bytenot-estate' ), esc_html( $p['facts'][0] ) );
		}
		if ( $p['units'] ) {
			$glance[] = array( __( 'Units', 'bytenot-estate' ), esc_html( $fmt( $p['units'] ) ) . ( (int) $get( 'towers' ) ? '<small>' . esc_html( sprintf( /* translators: %d: towers. */ _n( '%d tower', '%d towers', (int) $get( 'towers' ), 'bytenot-estate' ), (int) $get( 'towers' ) ) ) . '</small>' : '' ) );
		}
		if ( $get( 'launch_date' ) ) {
			$glance[] = array( __( 'Launched', 'bytenot-estate' ), esc_html( wp_date( 'M Y', strtotime( $get( 'launch_date' ) ) ) ) );
		}
	} elseif ( $land ) {
		$glance[] = array( __( 'Plot area', 'bytenot-estate' ), $area_html );
		if ( $inh( 'rera_state' ) ) {
			$glance[] = array( __( 'Approval', 'bytenot-estate' ), esc_html( $inh( 'rera_state' ) ) );
		}
	} else {
		if ( $p['bhkLabel'] ) {
			$glance[] = array( __( 'Configuration', 'bytenot-estate' ), esc_html( $p['bhkLabel'] ) );
		}
		$glance[] = array( $area_label, $area_html );
		if ( $p['baths'] ) {
			$glance[] = array( __( 'Bathrooms', 'bytenot-estate' ), esc_html( bytenot_trim_number( $p['baths'] ) ) );
		}
	}
	if ( $facing && ! $project ) {
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
			__( 'Facing', 'bytenot-estate' )     => $project ? '' : $facing,
			__( 'Floors', 'bytenot-estate' )     => $floors,
			__( 'Built in', 'bytenot-estate' )   => (string) ( (int) $inh( 'year_built' ) ?: '' ),
			__( 'Parking', 'bytenot-estate' )    => (string) $get( 'parking' ),
			__( 'Furnishing', 'bytenot-estate' ) => (string) $get( 'furnishing' ),
			__( 'Ownership', 'bytenot-estate' )  => (string) $inh( 'ownership' ),
			__( 'Towers', 'bytenot-estate' )     => $project && (int) $get( 'towers' ) ? (string) (int) $get( 'towers' ) : '',
			__( 'Completion', 'bytenot-estate' ) => $project && $get( 'completion_date' ) ? wp_date( 'M Y', strtotime( $get( 'completion_date' ) ) ) : '',
		)
	);
	$features = get_the_terms( $id, 'feature' );
	if ( ( ! $features || is_wp_error( $features ) ) && $parent ) {
		$features = get_the_terms( $parent, 'feature' );
	}
	$features     = $features && ! is_wp_error( $features ) ? wp_list_pluck( $features, 'name' ) : array();
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
	foreach ( (array) $inh( 'floor_plans' ) as $i => $pid ) {
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
		/* translators: %d: plan number. */
		$title   = $att->post_title ?: sprintf( __( 'Plan %d', 'bytenot-estate' ), $i + 1 );
		$plans[] = array( 'key' => 'plan-' . ( $i + 1 ), 'title' => $title, 'src' => $src, 'rooms' => $rooms, 'fig' => sprintf( /* translators: 1: figure number, 2: plan title. */ __( 'Fig. %1$d — %2$s', 'bytenot-estate' ), $i + 1, $title ) );
	}
	$brochure = (int) $inh( 'brochure_id' ) ? wp_get_attachment_url( (int) $inh( 'brochure_id' ) ) : '';
	$video    = (string) $inh( 'video_url' );

	// numbers
	$duty_pct = (float) bytenot_core_setting( 'stamp_duty' );
	$base     = $project ? $p['priceMin'] : $p['price'];
	$duty     = round( $base * $duty_pct / 100 );
	$deal     = array();
	if ( $project ) {
		if ( $p['priceMin'] ) {
			$deal[] = array( __( 'Price from', 'bytenot-estate' ), bytenot_core_format_price( $p['priceMin'] ) );
		}
		if ( $p['priceMax'] > $p['priceMin'] ) {
			$deal[] = array( __( 'Price up to', 'bytenot-estate' ), bytenot_core_format_price( $p['priceMax'] ) );
		}
		if ( $p['facts'][0] ) {
			$deal[] = array( __( 'Sizes', 'bytenot-estate' ), esc_html( $p['facts'][0] ) );
		}
		if ( $base ) {
			$deal[] = array( __( 'Stamp duty & registration, from', 'bytenot-estate' ), bytenot_core_format_price( $duty ) );
			$deal[] = array( __( 'All-in, from', 'bytenot-estate' ), bytenot_core_format_price( $base + $duty ), 'accent' );
		}
	} elseif ( $rent ) {
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
	} elseif ( $land ) {
		$deal[] = array( __( 'Plot area', 'bytenot-estate' ), $area_html );
		if ( $psf ) {
			/* translators: %s: unit. */
			$deal[] = array( sprintf( __( 'Per %s', 'bytenot-estate' ), $unit ), $psf );
		}
		$deal[] = array( __( 'Stamp duty & registration', 'bytenot-estate' ), bytenot_core_format_price( $duty ) );
		$deal[] = array( __( 'All-in', 'bytenot-estate' ), bytenot_core_format_price( $p['price'] + $duty ), 'accent' );
	} else {
		$deal[]    = array( $area_label, $area_html );
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
	$emi  = ! $rent && $base ? array(
		'price' => $base,
		'rate'  => (float) bytenot_core_setting( 'emi_rate' ),
		'years' => (int) bytenot_core_setting( 'emi_years' ),
		'ltv'   => (int) bytenot_core_setting( 'emi_ltv' ),
	) : null;

	// the place
	$landmarks = array();
	foreach ( (array) $inh( 'landmarks' ) as $l ) {
		$km          = 'm' === $l['unit'] ? $l['distance'] / 1000 : ( 'km' === $l['unit'] ? (float) $l['distance'] : null );
		$landmarks[] = array(
			'name' => $l['name'],
			'type' => ucfirst( str_replace( '-', ' ', (string) $l['type'] ) ),
			'km'   => null === $km ? null : round( $km, 1 ),
			't'    => (string) ( $l['time'] ?? ( 'min' === $l['unit'] ? sprintf( /* translators: %s: minutes. */ __( '%s min', 'bytenot-estate' ), $l['distance'] ) : '' ) ),
		);
	}
	$lat = (float) $inh( 'lat' );
	$lng = (float) $inh( 'lng' );

	// papers
	$papers = array();
	if ( $inh( 'rera_number' ) ) {
		$papers[] = array( __( 'RERA registration', 'bytenot-estate' ), '<span>' . esc_html( $inh( 'rera_number' ) ) . '</span>' . ( $inh( 'rera_url' ) ? '<a href="' . esc_url( $inh( 'rera_url' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Verify', 'bytenot-estate' ) . '</a>' : '' ) );
	}
	if ( $inh( 'rera_state' ) ) {
		$papers[] = array( __( 'Authority', 'bytenot-estate' ), esc_html( $inh( 'rera_state' ) ) );
	}
	foreach ( array_values( array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string) $inh( 'approvals' ) ) ) ) ) as $i => $a ) {
		$papers[] = array( 0 === $i ? __( 'Approvals', 'bytenot-estate' ) : '', esc_html( $a ) );
	}
	if ( $inh( 'banks' ) ) {
		$papers[] = array( __( 'Loans pre-approved with', 'bytenot-estate' ), esc_html( $inh( 'banks' ) ) );
	}
	if ( bytenot_mod( 'papers_documents' ) ) {
		$papers[] = array( __( 'Documents on request', 'bytenot-estate' ), esc_html( bytenot_mod( 'papers_documents' ) ) );
	}

	// the advisor
	$agent_id = (int) $inh( 'agent_id' );
	$agent    = $agent_id && 'agent' === get_post_type( $agent_id ) ? get_post( $agent_id ) : null;
	$advisor  = array(
		'name'     => $agent ? $agent->post_title : $site,
		'first'    => $agent ? explode( ' ', $agent->post_title )[0] : $site,
		'meta'     => $agent ? trim( bytenot_core_get( $agent_id, 'role' ) . ( bytenot_core_get( $agent_id, 'rera_number' ) ? ' · ' . sprintf( /* translators: %s: agent RERA number. */ __( 'RERA agent %s', 'bytenot-estate' ), bytenot_core_get( $agent_id, 'rera_number' ) ) : '' ), ' ·' ) : '',
		'photo'    => $agent ? get_post_thumbnail_id( $agent_id ) : 0,
		'phone'    => ( $agent ? bytenot_core_get( $agent_id, 'phone' ) : '' ) ?: bytenot_contact( 'phone' ),
		'whatsapp' => ( $agent ? bytenot_core_get( $agent_id, 'whatsapp' ) : '' ) ?: bytenot_contact( 'whatsapp' ),
	);
	/* translators: 1: listing title, 2: reference, 3: locality. */
	$wa_text = sprintf( __( "Hi, I'm interested in %1\$s (%2\$s) in %3\$s. Can we arrange a visit?", 'bytenot-estate' ), $p['title'], $p['ref'] ?: '#' . $id, $p['area'] );

	// a project's apartments
	$units = array();
	if ( $project ) {
		foreach ( bytenot_core_units_of( $id ) as $uid ) {
			$u             = bytenot_listing( $uid );
			$u['floorNo']  = (int) bytenot_core_get( $uid, 'floor' );
			$u['facing']   = (string) bytenot_core_get( $uid, 'facing' );
			$u['closed']   = has_term( bytenot_core_closed_statuses(), 'listing_status', $uid );
			$u['where']    = trim( $u['tower'] . ' ' . $u['unitNo'] );
			$units[]       = $u;
		}
	}

	// nearby: same kind, same city, same transaction, closest price
	$similar = array();
	if ( $p['city'] ) {
		$ids = get_posts(
			array(
				'post_type'      => 'listing',
				'post_status'    => 'publish',
				'posts_per_page' => 40,
				'post__not_in'   => array_merge( array( $id ), $parent ? array( $parent ) : array() ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array( 'taxonomy' => 'listing_kind', 'field' => 'slug', 'terms' => $kind ),
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
			'project'     => $project,
			'rent'        => $rent,
			'land'        => $land,
			'unit_label'  => $unit,
			'photos'      => $photos,
			'oneline'     => implode( ' · ', array_filter( $line ) ),
			'psf_long'    => $project ? $p['psf'] : ( $rent ? ( $p['sqft'] ? $fmt( $p['sqft'] ) . ' ' . $unit . ( (int) $get( 'deposit' ) ? ' · ' . sprintf( /* translators: %s: deposit. */ __( 'deposit %s', 'bytenot-estate' ), bytenot_core_format_price( (int) $get( 'deposit' ) ) ) : '' ) : __( 'per month', 'bytenot-estate' ) ) : ( $psf ? sprintf( /* translators: 1: price, 2: unit. */ __( '%1$s per %2$s', 'bytenot-estate' ), $psf, $unit ) : '' ) ),
			'psf_carpet'  => $project ? ( $p['facts'][0] ? sprintf( /* translators: %s: area range. */ __( '%s of carpet area', 'bytenot-estate' ), $p['facts'][0] ) : '' ) : ( $rent ? __( 'per month, maintenance extra', 'bytenot-estate' ) : ( $psf ? sprintf( /* translators: 1: price, 2: unit. */ __( '%1$s per %2$s', 'bytenot-estate' ), $psf, $land ? $unit : sprintf( /* translators: %s: unit. */ __( 'carpet %s', 'bytenot-estate' ), $unit ) ) : '' ) ),
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
			'video'       => $video,
			'deal'        => $deal,
			'emi'         => $emi,
			'landmarks'   => $landmarks,
			'lat'         => $lat,
			'lng'         => $lng,
			'papers'      => $papers,
			'advisor'     => $advisor,
			'wa'          => bytenot_wa( $advisor['whatsapp'], $wa_text ),
			'units_list'  => $units,
			'similar'     => $similar,
			'archive'     => bytenot_archive_url( $kind ),
			'city_url'    => $p['city'] ? add_query_arg( 'location', $p['city'], bytenot_archive_url( $kind ) ) : bytenot_archive_url( $kind ),
		)
	);
}
