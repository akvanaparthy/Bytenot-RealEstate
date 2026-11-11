<?php
/**
 * One listing: opening photo, chapter nav, glance, story, a project's apartments,
 * specification, plan, numbers, place, papers and people, visit form, nearby, compact
 * bar, lightbox.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$bytenot_d      = bytenot_listing_page( get_the_ID() );
$bytenot_photos = $bytenot_d['photos'];
$bytenot_n      = count( $bytenot_photos );
$bytenot_js     = array(
	'id'        => $bytenot_d['id'],
	'title'     => $bytenot_d['title'],
	'area'      => $bytenot_d['area'],
	'place'     => $bytenot_d['place'],
	'priceText' => $bytenot_d['priceText'],
	'rent'      => $bytenot_d['rent'],
	'photos'    => array_map( static fn( $ph ) => $ph['src'], $bytenot_photos ),
	'landmarks' => $bytenot_d['landmarks'],
	'lat'       => $bytenot_d['lat'],
	'lng'       => $bytenot_d['lng'],
	'emi'       => $bytenot_d['emi'],
	'first'     => $bytenot_d['advisor']['first'],
	'ref'       => $bytenot_d['ref_text'],
);
$bytenot_units_word = $bytenot_d['project'] ? bytenot_noun( true, 'apartment' ) : '';
$bytenot_links      = array_filter(
	array(
		'glance'  => __( 'At a glance', 'bytenot-estate' ),
		'home'    => $bytenot_d['chapters'] ? ( $bytenot_d['project'] ? __( 'The project', 'bytenot-estate' ) : __( 'The home', 'bytenot-estate' ) ) : '',
		'units'   => $bytenot_d['units_list'] ? $bytenot_units_word : '',
		'plan'    => $bytenot_d['plans'] ? __( 'The plan', 'bytenot-estate' ) : '',
		'numbers' => $bytenot_d['deal'] ? __( 'The numbers', 'bytenot-estate' ) : '',
		'place'   => $bytenot_d['landmarks'] || $bytenot_d['lat'] ? __( 'The place', 'bytenot-estate' ) : '',
		'papers'  => __( 'Papers', 'bytenot-estate' ),
		'nearby'  => $bytenot_d['similar'] ? __( 'Nearby', 'bytenot-estate' ) : '',
	)
);
?>

<main id="main" data-listing>
<script type="application/json" data-listing-json><?php echo wp_json_encode( $bytenot_js ); ?></script>

<section class="ph" aria-labelledby="ph-title" data-ph>
	<div class="ph__media">
		<?php foreach ( $bytenot_photos as $bytenot_i => $bytenot_ph ) : ?>
		<img class="ph__img<?php echo 0 === $bytenot_i ? ' is-on' : ''; ?>" src="<?php echo esc_url( $bytenot_ph['src'] ); ?>" alt="" width="2000" height="1333"<?php echo 0 === $bytenot_i ? ' fetchpriority="high"' : ' loading="lazy"'; ?> data-ph-slide>
		<?php endforeach; ?>
		<div class="ph__shade" aria-hidden="true"></div>
		<?php if ( $bytenot_n > 1 ) : ?>
		<div class="ph__tools">
			<button class="arrow arrow--sm ph__arrow" type="button" aria-label="<?php esc_attr_e( 'Previous photo', 'bytenot-estate' ); ?>" data-ph-prev><?php bytenot_the_icon( 'arrow-left' ); ?></button>
			<p class="ph__count"><span data-ph-index>01</span> / <?php echo esc_html( sprintf( '%02d', $bytenot_n ) ); ?></p>
			<button class="arrow arrow--sm ph__arrow" type="button" aria-label="<?php esc_attr_e( 'Next photo', 'bytenot-estate' ); ?>" data-ph-next><?php bytenot_the_icon( 'arrow' ); ?></button>
			<button class="ph__all" type="button" data-open-photo="0">
				<?php bytenot_the_icon( 'grid' ); ?>
				<?php echo esc_html( sprintf( /* translators: %d: photos. */ _n( 'All %d photo', 'All %d photos', $bytenot_n, 'bytenot-estate' ), $bytenot_n ) ); ?>
			</button>
		</div>
		<?php endif; ?>
	</div>
	<div class="container ph__inner">
		<p class="ph__status"><span data-status><?php echo esc_html( $bytenot_d['status'] ); ?></span></p>
		<nav class="ph__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bytenot-estate' ); ?>">
			<ol>
				<?php if ( $bytenot_d['parent'] ) : ?>
				<li><a href="<?php echo esc_url( bytenot_archive_url( 'project' ) ); ?>"><?php echo esc_html( bytenot_noun( true, 'project' ) ); ?></a></li>
				<?php if ( $bytenot_d['cityLabel'] ) : ?>
				<li><a href="<?php echo esc_url( add_query_arg( 'location', $bytenot_d['city'], bytenot_archive_url( 'project' ) ) ); ?>" data-crumb-city><?php echo esc_html( $bytenot_d['cityLabel'] ); ?></a></li>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( $bytenot_d['parent']['url'] ); ?>"><?php echo esc_html( $bytenot_d['parent']['title'] ); ?></a></li>
				<li aria-current="page" data-crumb-area><?php echo esc_html( trim( $bytenot_d['tower'] . ' ' . $bytenot_d['unitNo'] ) ?: $bytenot_d['title'] ); ?></li>
				<?php else : ?>
				<li><a href="<?php echo esc_url( $bytenot_d['archive'] ); ?>"><?php echo esc_html( bytenot_noun( true ) ); ?></a></li>
				<?php if ( $bytenot_d['cityLabel'] ) : ?>
				<li><a href="<?php echo esc_url( $bytenot_d['city_url'] ); ?>" data-crumb-city><?php echo esc_html( $bytenot_d['cityLabel'] ); ?></a></li>
				<?php endif; ?>
				<li aria-current="page" data-crumb-area><?php echo esc_html( $bytenot_d['area'] ?: $bytenot_d['title'] ); ?></li>
				<?php endif; ?>
			</ol>
		</nav>
		<h1 class="ph__title" id="ph-title" data-title><?php echo esc_html( $bytenot_d['title'] ); ?></h1>
		<p class="ph__line" data-oneline><?php echo esc_html( $bytenot_d['oneline'] ); ?></p>
		<?php if ( $bytenot_d['parent'] ) : ?>
		<p class="ph__partof"><?php echo esc_html( sprintf( /* translators: %s: kind, e.g. "project". */ __( 'In the %s', 'bytenot-estate' ), bytenot_kind_one( 'project' ) ) ); ?> <a href="<?php echo esc_url( $bytenot_d['parent']['url'] ); ?>"><?php echo esc_html( $bytenot_d['parent']['title'] ); ?></a></p>
		<?php endif; ?>
	</div>
	<aside class="ph__price" aria-label="<?php esc_attr_e( 'Price', 'bytenot-estate' ); ?>">
		<p class="ph__amount" data-price><?php echo esc_html( $bytenot_d['priceText'] ?: __( 'Price on request', 'bytenot-estate' ) ); ?></p>
		<p class="ph__psf" data-psf><?php echo esc_html( $bytenot_d['psf_long'] ); ?></p>
		<a class="btn btn--dark btn--sm" href="#visit" data-go-visit><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></a>
	</aside>
</section>

<nav class="spine" aria-label="<?php esc_attr_e( 'On this page', 'bytenot-estate' ); ?>" data-chapters data-spine>
	<span class="spine__track" aria-hidden="true"><span class="spine__fill"></span></span>
	<ol>
		<?php $bytenot_i = 0; foreach ( $bytenot_links as $bytenot_slug => $bytenot_label ) : ?>
		<li><a href="#<?php echo esc_attr( $bytenot_slug ); ?>"<?php echo 0 === $bytenot_i ? ' class="is-active"' : ''; ?>><span class="spine__dot" aria-hidden="true"></span><span class="spine__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', ++$bytenot_i ) ); ?></span><span class="spine__label"><?php echo esc_html( $bytenot_label ); ?></span></a></li>
		<?php endforeach; ?>
	</ol>
</nav>
<div class="spine-bar" aria-hidden="true" data-spine-bar><i></i></div>

<section class="glance container" id="glance" aria-labelledby="glance-title">
	<div class="meta-line">
		<p><?php esc_html_e( 'Ref', 'bytenot-estate' ); ?> <b data-ref><?php echo esc_html( $bytenot_d['ref_text'] ); ?></b> <span class="dot" aria-hidden="true">·</span> <span class="meta-line__ok"><?php bytenot_the_icon( 'check', 12 ); ?><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( 'Verified by %s', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?></span> <span class="dot" aria-hidden="true">·</span> <?php esc_html_e( 'Updated', 'bytenot-estate' ); ?> <span data-updated><?php echo esc_html( $bytenot_d['updated'] ); ?></span></p>
		<p class="meta-line__tools">
			<button class="link-btn" type="button" data-save aria-pressed="false"><span data-save-label><?php esc_html_e( 'Save', 'bytenot-estate' ); ?></span></button>
			<button class="link-btn" type="button" data-share><?php esc_html_e( 'Share', 'bytenot-estate' ); ?></button>
		</p>
	</div>
	<h2 class="visually-hidden" id="glance-title"><?php esc_html_e( 'At a glance', 'bytenot-estate' ); ?></h2>
	<dl class="ledger ledger--glance" data-glance>
		<?php foreach ( $bytenot_d['glance'] as $bytenot_row ) : ?>
		<div><dt><?php echo esc_html( $bytenot_row[0] ); ?></dt><dd><?php echo wp_kses( $bytenot_row[1], array( 'small' => array() ) ); ?></dd></div>
		<?php endforeach; ?>
	</dl>
</section>

<?php if ( $bytenot_d['chapters'] ) : ?>
<section class="story container" id="home" aria-labelledby="home-title">
	<div class="sec-head">
		<p class="fig-label"><?php echo esc_html( sprintf( '01 — %02d', count( $bytenot_d['chapters'] ) ) ); ?></p>
		<h2 class="sec-head__title" id="home-title"><?php echo esc_html( $bytenot_d['project'] ? __( 'The project', 'bytenot-estate' ) : __( 'The home', 'bytenot-estate' ) ); ?></h2>
	</div>
	<div data-chapters-list>
		<?php foreach ( $bytenot_d['chapters'] as $bytenot_i => $bytenot_ch ) : ?>
		<article class="chapter<?php echo $bytenot_i % 2 ? ' chapter--flip' : ''; ?>" data-chapter>
			<?php if ( $bytenot_ch['photo'] ) : ?>
			<figure class="chapter__fig">
				<button class="chapter__open" type="button" aria-label="<?php esc_attr_e( 'Open photo', 'bytenot-estate' ); ?>" data-open-photo="<?php echo (int) min( $bytenot_i, max( 0, $bytenot_n - 1 ) ); ?>">
					<img class="chapter__img" src="<?php echo esc_url( $bytenot_ch['photo']['large'] ); ?>" alt="<?php echo esc_attr( $bytenot_ch['cap'] ); ?>" width="1200" height="900"<?php echo $bytenot_i ? ' loading="lazy"' : ''; ?>>
				</button>
			</figure>
			<?php endif; ?>
			<div class="chapter__text">
				<p class="chapter__num fig-label"><?php echo esc_html( sprintf( '%02d', $bytenot_i + 1 ) ); ?></p>
				<h3 class="chapter__title"><?php echo esc_html( $bytenot_ch['title'] ); ?></h3>
				<div class="chapter__body"><?php echo wp_kses_post( $bytenot_ch['body'] ); ?></div>
				<p class="chapter__cap"><?php echo esc_html( $bytenot_ch['cap'] ); ?></p>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
	<?php if ( $bytenot_n > 1 ) : ?>
	<div class="strip" data-strip>
		<div class="strip__head">
			<p class="fig-label"><?php esc_html_e( 'All photos', 'bytenot-estate' ); ?></p>
			<div class="strip__nav">
				<p class="strip__count"><span data-strip-index>01</span> / <span data-strip-total><?php echo esc_html( sprintf( '%02d', $bytenot_n ) ); ?></span></p>
				<button class="arrow arrow--sm" type="button" aria-label="<?php esc_attr_e( 'Previous photo', 'bytenot-estate' ); ?>" data-strip-prev><?php bytenot_the_icon( 'arrow-left' ); ?></button>
				<button class="arrow arrow--sm" type="button" aria-label="<?php esc_attr_e( 'Next photo', 'bytenot-estate' ); ?>" data-strip-next><?php bytenot_the_icon( 'arrow' ); ?></button>
			</div>
		</div>
		<ul class="strip__track" data-strip-track>
			<?php foreach ( $bytenot_photos as $bytenot_i => $bytenot_ph ) : ?>
			<li><button type="button" data-open-photo="<?php echo (int) $bytenot_i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: photo number. */ __( 'Open photo %d', 'bytenot-estate' ), $bytenot_i + 1 ) ); ?>"><img src="<?php echo esc_url( $bytenot_ph['thumb'] ); ?>" alt="" loading="lazy"></button></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<?php if ( $bytenot_d['units_list'] ) : ?>
<section class="units container" id="units" aria-labelledby="units-title">
	<div class="sec-head">
		<p class="fig-label"><?php echo esc_html( sprintf( /* translators: %d: units listed. */ _n( '%d listed', '%d listed', count( $bytenot_d['units_list'] ), 'bytenot-estate' ), count( $bytenot_d['units_list'] ) ) ); ?></p>
		<h2 class="sec-head__title" id="units-title"><?php echo esc_html( sprintf( /* translators: %s: apartments, plural. */ __( 'The %s on offer', 'bytenot-estate' ), mb_strtolower( $bytenot_units_word ) ) ); ?></h2>
	</div>
	<table class="units__table">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Unit', 'bytenot-estate' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Configuration', 'bytenot-estate' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Carpet area', 'bytenot-estate' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Floor', 'bytenot-estate' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Facing', 'bytenot-estate' ); ?></th>
				<th scope="col" class="units__num"><?php esc_html_e( 'Price', 'bytenot-estate' ); ?></th>
				<th scope="col"><span class="visually-hidden"><?php esc_html_e( 'Open', 'bytenot-estate' ); ?></span></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $bytenot_d['units_list'] as $bytenot_u ) : ?>
			<tr class="units__row<?php echo $bytenot_u['closed'] ? ' is-closed' : ''; ?>">
				<td><a class="units__name" href="<?php echo esc_url( $bytenot_u['url'] ); ?>"><?php echo esc_html( $bytenot_u['where'] ?: $bytenot_u['title'] ); ?></a><?php if ( $bytenot_u['where'] ) : ?><small><?php echo esc_html( $bytenot_u['title'] ); ?></small><?php endif; ?></td>
				<td><?php echo esc_html( $bytenot_u['bhkLabel'] ); ?></td>
				<td><?php echo esc_html( $bytenot_u['areaText'] ); ?></td>
				<td><?php echo esc_html( $bytenot_u['floorNo'] ? $bytenot_u['floorNo'] : '—' ); ?></td>
				<td><?php echo esc_html( $bytenot_u['facing'] ?: '—' ); ?></td>
				<td class="units__num"><?php echo esc_html( $bytenot_u['closed'] ? $bytenot_u['status'] : $bytenot_u['priceText'] ); ?></td>
				<td class="units__go"><a class="btn btn--outline btn--sm" href="<?php echo esc_url( $bytenot_u['url'] ); ?>"><?php esc_html_e( 'View', 'bytenot-estate' ); ?> <?php bytenot_the_icon( 'arrow' ); ?></a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</section>
<?php endif; ?>

<?php if ( $bytenot_d['specs'] || $bytenot_d['amenities'] || $bytenot_d['prose'] ) : ?>
<section class="spec container" id="spec" aria-labelledby="spec-title">
	<div class="sec-head">
		<p class="fig-label"><?php esc_html_e( 'Specification', 'bytenot-estate' ); ?></p>
		<h2 class="sec-head__title" id="spec-title"><?php echo esc_html( $bytenot_d['rent'] ? __( 'What you are renting', 'bytenot-estate' ) : __( 'What you are buying', 'bytenot-estate' ) ); ?></h2>
	</div>
	<div class="spec__grid">
		<dl class="ledger ledger--spec" data-specs>
			<?php foreach ( $bytenot_d['specs'] as $bytenot_k => $bytenot_v ) : ?>
			<div><dt><?php echo esc_html( $bytenot_k ); ?></dt><dd><?php echo esc_html( $bytenot_v ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<div class="spec__also">
			<?php if ( $bytenot_d['amenities'] ) : ?>
			<p class="fig-label"><?php esc_html_e( 'Also', 'bytenot-estate' ); ?></p>
			<p data-amenities-text><?php echo esc_html( $bytenot_d['amenities'] ); ?></p>
			<?php endif; ?>
			<?php if ( $bytenot_d['prose'] ) : ?>
			<div class="prose prose--spec" data-description><?php echo wp_kses_post( $bytenot_d['prose'] ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $bytenot_d['plans'] ) : ?>
<section class="planx container" id="plan" aria-labelledby="plan-title">
	<div class="sec-head">
		<p class="fig-label" data-plan-fig><?php echo esc_html( $bytenot_d['plans'][0]['fig'] ); ?></p>
		<h2 class="sec-head__title" id="plan-title"><?php esc_html_e( 'The plan', 'bytenot-estate' ); ?></h2>
		<?php if ( count( $bytenot_d['plans'] ) > 1 ) : ?>
		<div class="toggle" role="tablist" aria-label="<?php esc_attr_e( 'Floors', 'bytenot-estate' ); ?>" data-plan-tabs>
			<?php foreach ( $bytenot_d['plans'] as $bytenot_i => $bytenot_pl ) : ?>
			<button class="toggle__btn<?php echo $bytenot_i ? '' : ' is-active'; ?>" role="tab" aria-selected="<?php echo $bytenot_i ? 'false' : 'true'; ?>" aria-controls="<?php echo esc_attr( $bytenot_pl['key'] ); ?>" id="tab-<?php echo esc_attr( $bytenot_pl['key'] ); ?>" type="button" data-plan="<?php echo esc_attr( $bytenot_pl['key'] ); ?>" data-plan-fig-text="<?php echo esc_attr( $bytenot_pl['fig'] ); ?>"<?php echo $bytenot_i ? ' tabindex="-1"' : ''; ?>><?php echo esc_html( $bytenot_pl['title'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="drawing">
		<div class="drawing__paper">
			<?php foreach ( $bytenot_d['plans'] as $bytenot_i => $bytenot_pl ) : ?>
			<div class="drawing__sheet<?php echo $bytenot_i ? '' : ' is-active'; ?>" id="<?php echo esc_attr( $bytenot_pl['key'] ); ?>" role="tabpanel" aria-labelledby="tab-<?php echo esc_attr( $bytenot_pl['key'] ); ?>" data-plan-sheet="<?php echo esc_attr( $bytenot_pl['key'] ); ?>"<?php echo $bytenot_i ? ' hidden' : ''; ?>>
				<?php echo wp_get_attachment_image( $bytenot_pl['id'], 'large', false, array( 'class' => 'drawing__img', 'alt' => $bytenot_pl['title'], 'loading' => 'lazy' ) ); ?>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="drawing__side">
			<?php foreach ( $bytenot_d['plans'] as $bytenot_i => $bytenot_pl ) : ?>
				<?php if ( $bytenot_pl['rooms'] ) : ?>
			<dl class="ledger ledger--rooms" data-plan-rooms="<?php echo esc_attr( $bytenot_pl['key'] ); ?>"<?php echo $bytenot_i ? ' hidden' : ''; ?>>
				<?php foreach ( $bytenot_pl['rooms'] as $bytenot_room ) : ?>
				<div><dt><?php echo esc_html( $bytenot_room[0] ); ?></dt><dd><?php echo esc_html( $bytenot_room[1] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ( $bytenot_d['brochure'] ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $bytenot_d['brochure'] ); ?>" download><?php esc_html_e( 'Download the brochure', 'bytenot-estate' ); ?>
				<?php bytenot_the_icon( 'download' ); ?>
			</a>
			<?php endif; ?>
			<?php if ( $bytenot_d['video'] ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $bytenot_d['video'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Watch the video', 'bytenot-estate' ); ?>
				<?php bytenot_the_icon( 'play' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $bytenot_d['deal'] ) : ?>
<section class="deal container" id="numbers" aria-labelledby="numbers-title">
	<div class="deal__card">
		<div class="deal__top">
			<div>
				<p class="fig-label fig-label--light"><?php esc_html_e( 'The numbers', 'bytenot-estate' ); ?></p>
				<h2 class="deal__title" id="numbers-title"><?php esc_html_e( 'What it costs, honestly', 'bytenot-estate' ); ?></h2>
			</div>
			<p class="deal__price"><b data-price><?php echo esc_html( $bytenot_d['priceText'] ); ?></b><span data-psf-carpet><?php echo esc_html( $bytenot_d['psf_carpet'] ); ?></span></p>
		</div>
		<dl class="ledger ledger--dark" data-deal>
			<?php foreach ( $bytenot_d['deal'] as $bytenot_row ) : ?>
			<div<?php echo isset( $bytenot_row[2] ) ? ' class="' . esc_attr( bytenot_class( 'is-' . $bytenot_row[2] ) ) . '"' : ''; ?>><dt><?php echo esc_html( $bytenot_row[0] ); ?></dt><dd><?php echo wp_kses( $bytenot_row[1], array( 'small' => array() ) ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<?php if ( $bytenot_d['emi'] ) : ?>
		<p class="deal__emi" data-emi-sentence>
			<?php
			printf(
				/* translators: 1: EMI amount, 2: tenure button, 3: rate button, 4: financed share button. */
				esc_html__( 'Roughly %1$s a month over %2$s at %3$s, with %4$s of the price financed.', 'bytenot-estate' ),
				'<b class="num num--static" data-emi-value></b>',
				'<button class="num" type="button" data-emi-field="years" aria-haspopup="dialog">' . esc_html( sprintf( /* translators: %s: years. */ __( '%s years', 'bytenot-estate' ), $bytenot_d['emi']['years'] ) ) . '</button>',
				'<button class="num" type="button" data-emi-field="rate" aria-haspopup="dialog">' . esc_html( $bytenot_d['emi']['rate'] . '%' ) . '</button>',
				'<button class="num" type="button" data-emi-field="ltv" aria-haspopup="dialog">' . esc_html( $bytenot_d['emi']['ltv'] . '%' ) . '</button>'
			);
			?>
			<span class="deal__hint"><?php esc_html_e( 'Tap a number to change it.', 'bytenot-estate' ); ?></span>
		</p>
		<div class="num-pop" data-num-pop hidden>
			<label for="emi-range"><span data-pop-label></span><output data-pop-out></output></label>
			<input type="range" id="emi-range" data-pop-range>
		</div>
		<?php endif; ?>
		<p class="deal__note"><?php echo esc_html( bytenot_mod( 'deal_note' ) ); ?></p>
	</div>
</section>
<?php endif; ?>

<?php if ( $bytenot_d['landmarks'] || $bytenot_d['lat'] ) : ?>
<section class="place container" id="place" aria-labelledby="place-title">
	<div class="sec-head">
		<p class="fig-label"><?php esc_html_e( 'The place', 'bytenot-estate' ); ?></p>
		<h2 class="sec-head__title" id="place-title"><?php echo esc_html( sprintf( /* translators: %s: locality. */ __( '%s, from the front door', 'bytenot-estate' ), $bytenot_d['area'] ?: $bytenot_d['cityLabel'] ) ); ?></h2>
	</div>
	<div class="place__grid">
		<?php if ( $bytenot_d['landmarks'] ) : ?>
		<div class="radar" data-radar>
			<svg viewBox="0 0 560 560" class="radar__svg" aria-hidden="true" data-radar-svg></svg>
		</div>
		<?php endif; ?>
		<div class="place__side">
			<dl class="ledger ledger--places" data-landmarks>
				<?php foreach ( $bytenot_d['landmarks'] as $bytenot_i => $bytenot_l ) : ?>
				<div data-spot="<?php echo (int) $bytenot_i; ?>"><dt><?php echo esc_html( $bytenot_l['name'] ); ?><?php if ( $bytenot_l['type'] ) : ?><small><?php echo esc_html( $bytenot_l['type'] ); ?></small><?php endif; ?></dt><dd><?php echo esc_html( null !== $bytenot_l['km'] ? sprintf( /* translators: %s: kilometres. */ __( '%s km', 'bytenot-estate' ), $bytenot_l['km'] ) : $bytenot_l['t'] ); ?><?php if ( $bytenot_l['t'] && null !== $bytenot_l['km'] ) : ?><small><?php echo esc_html( sprintf( /* translators: %s: drive time. */ __( '%s drive', 'bytenot-estate' ), $bytenot_l['t'] ) ); ?></small><?php endif; ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<?php if ( $bytenot_d['lat'] && $bytenot_d['lng'] ) : ?>
			<div class="locmap locmap--inline" data-map>
				<button class="link-arrow" type="button" data-map-load><?php esc_html_e( 'Load the map', 'bytenot-estate' ); ?>
					<?php bytenot_the_icon( 'arrow' ); ?>
				</button>
				<p class="locmap__hint"><?php esc_html_e( 'Loads on request, so nothing is tracked until you ask.', 'bytenot-estate' ); ?></p>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="papers container" id="papers" aria-labelledby="papers-title">
	<div class="sec-head">
		<p class="fig-label"><?php esc_html_e( 'Papers & people', 'bytenot-estate' ); ?></p>
		<h2 class="sec-head__title" id="papers-title"><?php esc_html_e( 'Everything checked, one person to call', 'bytenot-estate' ); ?></h2>
	</div>
	<div class="papers__grid">
		<dl class="ledger ledger--papers" data-papers>
			<?php foreach ( $bytenot_d['papers'] as $bytenot_row ) : ?>
			<div><dt><?php echo esc_html( $bytenot_row[0] ); ?></dt><dd><?php echo wp_kses( $bytenot_row[1], array( 'span' => array(), 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<div class="signature">
			<?php if ( $bytenot_d['advisor']['photo'] ) : ?>
			<?php echo wp_get_attachment_image( $bytenot_d['advisor']['photo'], 'thumbnail', false, array( 'class' => 'signature__photo', 'alt' => '', 'width' => 160, 'height' => 160, 'loading' => 'lazy' ) ); ?>
			<?php endif; ?>
			<div class="signature__body">
				<p class="fig-label"><?php esc_html_e( 'Your advisor', 'bytenot-estate' ); ?></p>
				<p class="signature__name" data-advisor-name><?php echo esc_html( $bytenot_d['advisor']['name'] ); ?></p>
				<?php if ( $bytenot_d['advisor']['meta'] ) : ?>
				<p class="signature__meta" data-advisor-meta><?php echo esc_html( $bytenot_d['advisor']['meta'] ); ?></p>
				<?php endif; ?>
				<div class="signature__actions">
					<?php if ( $bytenot_d['advisor']['phone'] ) : ?>
					<a class="btn btn--dark btn--sm" href="<?php echo esc_url( bytenot_tel( $bytenot_d['advisor']['phone'] ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s: first name. */ __( 'Call %s', 'bytenot-estate' ), $bytenot_d['advisor']['first'] ) ); ?></a>
					<?php endif; ?>
					<?php if ( $bytenot_d['wa'] ) : ?>
					<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $bytenot_d['wa'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'bytenot-estate' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="visit" id="visit" aria-labelledby="visit-title">
	<div class="brand-motif" aria-hidden="true"></div>
	<div class="container visit__inner">
		<div class="visit__head">
			<p class="fig-label"><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></p>
			<h2 class="visit__title" id="visit-title"><?php esc_html_e( 'Walk through it this week.', 'bytenot-estate' ); ?></h2>
			<p class="visit__lede"><?php echo esc_html( sprintf( /* translators: %s: advisor first name. */ __( 'Free, no obligation. %s confirms within the hour and meets you at the gate.', 'bytenot-estate' ), $bytenot_d['advisor']['first'] ) ); ?></p>
		</div>
		<form class="visit__form" method="post" action="<?php echo esc_url( rest_url( 'bytenot/v1/leads' ) ); ?>" data-enquire data-lead="visit" data-listing-id="<?php echo (int) $bytenot_d['id']; ?>">
			<label class="vf">
				<span class="vf__label"><?php esc_html_e( 'Your name', 'bytenot-estate' ); ?></span>
				<input class="vf__input" type="text" name="name" autocomplete="name" required>
			</label>
			<label class="vf">
				<span class="vf__label"><?php esc_html_e( 'Phone', 'bytenot-estate' ); ?></span>
				<?php bytenot_phone_input( 'vf' ); ?>
			</label>
			<label class="vf">
				<span class="vf__label"><?php esc_html_e( 'Preferred date', 'bytenot-estate' ); ?></span>
				<input class="vf__input" type="date" name="date">
			</label>
			<button class="btn btn--accent visit__submit" type="submit"><?php esc_html_e( 'Request a visit', 'bytenot-estate' ); ?>
				<?php bytenot_the_icon( 'arrow', 18 ); ?>
			</button>
			<label class="consent visit__consent">
				<input type="checkbox" name="consent" required>
				<span><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( '%s may contact me about this listing. No spam, and I can opt out any time.', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?></span>
			</label>
			<?php bytenot_lead_fields(); ?>
		</form>
	</div>
</section>

<?php if ( $bytenot_d['similar'] ) : ?>
<section class="nearby container" id="nearby" aria-labelledby="nearby-title">
	<div class="sec-head sec-head--row">
		<div>
			<p class="fig-label"><?php esc_html_e( 'Nearby', 'bytenot-estate' ); ?></p>
			<h2 class="sec-head__title" id="nearby-title"><?php esc_html_e( 'In the same budget, close by', 'bytenot-estate' ); ?></h2>
		</div>
		<a class="link-arrow" href="<?php echo esc_url( $bytenot_d['city_url'] ); ?>" data-similar-link><?php echo esc_html( sprintf( /* translators: %s: city. */ __( 'See all in %s', 'bytenot-estate' ), $bytenot_d['cityLabel'] ) ); ?>
			<?php bytenot_the_icon( 'arrow' ); ?>
		</a>
	</div>
	<ul class="cards cards--similar" data-similar>
		<?php foreach ( $bytenot_d['similar'] as $bytenot_q ) : ?>
			<?php bytenot_card( $bytenot_q ); ?>
		<?php endforeach; ?>
	</ul>
</section>
<?php endif; ?>

</main>

<div class="advisor" data-prop-bar data-advisor aria-hidden="true" inert>
	<button class="advisor__face" type="button" aria-expanded="false" aria-controls="advisor-actions" data-advisor-toggle>
		<?php if ( $bytenot_d['advisor']['photo'] ) : ?>
		<?php echo wp_get_attachment_image( $bytenot_d['advisor']['photo'], 'thumbnail', false, array( 'class' => 'advisor__photo', 'alt' => '', 'width' => 88, 'height' => 88, 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
		<span class="advisor__photo advisor__photo--initial" aria-hidden="true"><?php echo esc_html( mb_substr( $bytenot_d['advisor']['first'], 0, 1 ) ); ?></span>
		<?php endif; ?>
		<span class="advisor__text"><b><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></b><small><?php echo esc_html( sprintf( /* translators: %s: advisor first name. */ __( 'with %s, your advisor', 'bytenot-estate' ), $bytenot_d['advisor']['first'] ) ); ?></small></span>
	</button>
	<div class="advisor__acts" id="advisor-actions"><div>
		<?php if ( $bytenot_d['advisor']['phone'] ) : ?>
		<a class="btn btn--outline btn--sm advisor__call" href="<?php echo esc_url( bytenot_tel( $bytenot_d['advisor']['phone'] ) ); ?>" aria-label="<?php esc_attr_e( 'Call', 'bytenot-estate' ); ?>"><?php bytenot_the_icon( 'phone', 18 ); ?></a>
		<?php endif; ?>
		<?php if ( $bytenot_d['wa'] ) : ?>
		<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $bytenot_d['wa'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'bytenot-estate' ); ?></a>
		<?php endif; ?>
		<a class="btn btn--accent btn--sm" href="#visit" data-go-visit><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></a>
	</div></div>
</div>

<?php if ( $bytenot_photos ) : ?>
<dialog class="lightbox" data-lightbox aria-label="<?php esc_attr_e( 'Photo gallery', 'bytenot-estate' ); ?>">
	<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close gallery', 'bytenot-estate' ); ?>" data-lb-close>
		<?php bytenot_the_icon( 'close', 22 ); ?>
	</button>
	<p class="lightbox__count"><span data-lb-index>1</span> / <span data-lb-total><?php echo (int) $bytenot_n; ?></span></p>
	<div class="lightbox__stage" data-lb-stage>
		<img class="lightbox__img" alt="" data-lb-img>
	</div>
	<button class="lightbox__arrow lightbox__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous photo', 'bytenot-estate' ); ?>" data-lb-prev>
		<?php bytenot_the_icon( 'arrow-left', 22 ); ?>
	</button>
	<button class="lightbox__arrow lightbox__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next photo', 'bytenot-estate' ); ?>" data-lb-next>
		<?php bytenot_the_icon( 'arrow', 22 ); ?>
	</button>
	<ul class="lightbox__strip" data-lb-strip>
		<?php foreach ( $bytenot_photos as $bytenot_i => $bytenot_ph ) : ?>
		<li><button type="button" data-lb-go="<?php echo (int) $bytenot_i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: photo number. */ __( 'Photo %d', 'bytenot-estate' ), $bytenot_i + 1 ) ); ?>"><img src="<?php echo esc_url( $bytenot_ph['thumb'] ); ?>" alt="" loading="lazy"></button></li>
		<?php endforeach; ?>
	</ul>
</dialog>
<?php endif; ?>

<?php
get_footer();
