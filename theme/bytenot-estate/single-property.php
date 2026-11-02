<?php
/**
 * One property: opening photo, chapter nav, glance, story, specification, plan,
 * numbers, place, papers and people, visit form, nearby, compact bar, lightbox.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$d       = bytenot_property( get_the_ID() );
$photos  = $d['photos'];
$n       = count( $photos );
$archive = get_post_type_archive_link( 'property' );
$js      = array(
	'id'        => $d['id'],
	'title'     => $d['title'],
	'area'      => $d['area'],
	'place'     => $d['place'],
	'priceText' => $d['priceText'],
	'rent'      => $d['rent'],
	'photos'    => array_map( static fn( $ph ) => $ph['src'], $photos ),
	'landmarks' => $d['landmarks'],
	'lat'       => $d['lat'],
	'lng'       => $d['lng'],
	'emi'       => $d['emi'],
	'first'     => $d['advisor']['first'],
	'ref'       => $d['ref_text'],
);
$chapter_links = array_filter(
	array(
		'glance'  => __( 'At a glance', 'bytenot-estate' ),
		'home'    => $d['chapters'] ? __( 'The home', 'bytenot-estate' ) : '',
		'plan'    => $d['plans'] ? __( 'The plan', 'bytenot-estate' ) : '',
		'numbers' => $d['deal'] ? __( 'The numbers', 'bytenot-estate' ) : '',
		'place'   => $d['landmarks'] || $d['lat'] ? __( 'The place', 'bytenot-estate' ) : '',
		'papers'  => __( 'Papers', 'bytenot-estate' ),
		'nearby'  => $d['similar'] ? __( 'Nearby', 'bytenot-estate' ) : '',
	)
);
?>

<main id="main" data-property>
<script type="application/json" data-property-json><?php echo wp_json_encode( $js ); ?></script>

<section class="ph" aria-labelledby="ph-title" data-ph>
	<div class="ph__media">
		<?php if ( $photos ) : ?>
		<img class="ph__img" src="<?php echo esc_url( $photos[0]['src'] ); ?>" alt="" width="2000" height="1333" fetchpriority="high" data-ph-img>
		<?php endif; ?>
		<div class="ph__shade" aria-hidden="true"></div>
		<?php if ( $n > 1 ) : ?>
		<button class="ph__all" type="button" data-open-photo="0">
			<?php bytenot_the_icon( 'grid' ); ?>
			<?php echo esc_html( sprintf( /* translators: %d: photos. */ _n( 'All %d photo', 'All %d photos', $n, 'bytenot-estate' ), $n ) ); ?>
		</button>
		<?php endif; ?>
	</div>
	<div class="container ph__inner">
		<p class="ph__status"><span class="ph__status-dot" aria-hidden="true"></span><span data-status><?php echo esc_html( $d['status'] ); ?></span></p>
		<nav class="ph__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bytenot-estate' ); ?>">
			<ol>
				<li><a href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'Properties', 'bytenot-estate' ); ?></a></li>
				<?php if ( $d['cityLabel'] ) : ?>
				<li><a href="<?php echo esc_url( $d['city_url'] ); ?>" data-crumb-city><?php echo esc_html( $d['cityLabel'] ); ?></a></li>
				<?php endif; ?>
				<li aria-current="page" data-crumb-area><?php echo esc_html( $d['area'] ?: $d['title'] ); ?></li>
			</ol>
		</nav>
		<h1 class="ph__title" id="ph-title" data-title><?php echo esc_html( $d['title'] ); ?></h1>
		<p class="ph__line" data-oneline><?php echo esc_html( $d['oneline'] ); ?></p>
	</div>
	<aside class="ph__price" aria-label="<?php esc_attr_e( 'Price', 'bytenot-estate' ); ?>">
		<p class="ph__amount" data-price><?php echo esc_html( $d['priceText'] ?: __( 'Price on request', 'bytenot-estate' ) ); ?></p>
		<p class="ph__psf" data-psf><?php echo esc_html( $d['psf_long'] ); ?></p>
		<a class="btn btn--dark btn--sm" href="#visit" data-go-visit><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></a>
	</aside>
</section>

<nav class="chapters chapters--page" aria-label="<?php esc_attr_e( 'On this page', 'bytenot-estate' ); ?>" data-chapters>
	<ul>
		<?php $first = true; foreach ( $chapter_links as $slug => $label ) : ?>
		<li><a href="#<?php echo esc_attr( $slug ); ?>"<?php echo $first ? ' class="is-active"' : ''; $first = false; ?>><?php echo esc_html( $label ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>

<section class="glance container" id="glance" aria-labelledby="glance-title">
	<div class="meta-line">
		<p><?php esc_html_e( 'Ref', 'bytenot-estate' ); ?> <b data-ref><?php echo esc_html( $d['ref_text'] ); ?></b> <span class="dot" aria-hidden="true">·</span> <span class="meta-line__ok"><?php bytenot_the_icon( 'check', 12 ); ?><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( 'Verified by %s', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?></span> <span class="dot" aria-hidden="true">·</span> <?php esc_html_e( 'Updated', 'bytenot-estate' ); ?> <span data-updated><?php echo esc_html( $d['updated'] ); ?></span></p>
		<p class="meta-line__tools">
			<button class="link-btn" type="button" data-save aria-pressed="false"><span data-save-label><?php esc_html_e( 'Save', 'bytenot-estate' ); ?></span></button>
			<button class="link-btn" type="button" data-share><?php esc_html_e( 'Share', 'bytenot-estate' ); ?></button>
		</p>
	</div>
	<h2 class="visually-hidden" id="glance-title"><?php esc_html_e( 'At a glance', 'bytenot-estate' ); ?></h2>
	<dl class="ledger ledger--glance" data-glance>
		<?php foreach ( $d['glance'] as $row ) : ?>
		<div><dt><?php echo esc_html( $row[0] ); ?></dt><dd><?php echo wp_kses( $row[1], array( 'small' => array() ) ); ?></dd></div>
		<?php endforeach; ?>
	</dl>
</section>

<?php if ( $d['chapters'] ) : ?>
<section class="story container" id="home" aria-labelledby="home-title">
	<div class="sec-head">
		<p class="fig-label"><?php echo esc_html( sprintf( '01 — %02d', count( $d['chapters'] ) ) ); ?></p>
		<h2 class="sec-head__title" id="home-title"><?php esc_html_e( 'The home', 'bytenot-estate' ); ?></h2>
	</div>
	<div data-chapters-list>
		<?php foreach ( $d['chapters'] as $i => $ch ) : ?>
		<article class="chapter<?php echo $i % 2 ? ' chapter--flip' : ''; ?>" data-chapter>
			<?php if ( $ch['photo'] ) : ?>
			<figure class="chapter__fig">
				<button class="chapter__open" type="button" aria-label="<?php esc_attr_e( 'Open photo', 'bytenot-estate' ); ?>" data-open-photo="<?php echo (int) min( $i, max( 0, $n - 1 ) ); ?>">
					<img class="chapter__img" src="<?php echo esc_url( $ch['photo']['large'] ); ?>" alt="<?php echo esc_attr( $ch['cap'] ); ?>" width="1200" height="900"<?php echo $i ? ' loading="lazy"' : ''; ?>>
				</button>
			</figure>
			<?php endif; ?>
			<div class="chapter__text">
				<p class="chapter__num fig-label"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
				<h3 class="chapter__title"><?php echo esc_html( $ch['title'] ); ?></h3>
				<div class="chapter__body"><?php echo wp_kses_post( $ch['body'] ); ?></div>
				<p class="chapter__cap"><?php echo esc_html( $ch['cap'] ); ?></p>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
	<?php if ( $n > 1 ) : ?>
	<div class="strip" data-strip>
		<div class="strip__head">
			<p class="fig-label"><?php esc_html_e( 'All photos', 'bytenot-estate' ); ?></p>
			<p class="strip__count"><span data-strip-index>01</span> / <span data-strip-total><?php echo esc_html( sprintf( '%02d', $n ) ); ?></span></p>
		</div>
		<ul class="strip__track" data-strip-track>
			<?php foreach ( $photos as $i => $ph ) : ?>
			<li><button type="button" data-open-photo="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: photo number. */ __( 'Open photo %d', 'bytenot-estate' ), $i + 1 ) ); ?>"><img src="<?php echo esc_url( $ph['thumb'] ); ?>" alt="" loading="lazy"></button></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<?php if ( $d['specs'] || $d['amenities'] || $d['prose'] ) : ?>
<section class="spec container" id="spec" aria-labelledby="spec-title">
	<div class="sec-head">
		<p class="fig-label"><?php esc_html_e( 'Specification', 'bytenot-estate' ); ?></p>
		<h2 class="sec-head__title" id="spec-title"><?php echo esc_html( $d['rent'] ? __( 'What you are renting', 'bytenot-estate' ) : __( 'What you are buying', 'bytenot-estate' ) ); ?></h2>
	</div>
	<div class="spec__grid">
		<dl class="ledger ledger--spec" data-specs>
			<?php foreach ( $d['specs'] as $k => $v ) : ?>
			<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<div class="spec__also">
			<?php if ( $d['amenities'] ) : ?>
			<p class="fig-label"><?php esc_html_e( 'Also', 'bytenot-estate' ); ?></p>
			<p data-amenities-text><?php echo esc_html( $d['amenities'] ); ?></p>
			<?php endif; ?>
			<?php if ( $d['prose'] ) : ?>
			<div class="prose prose--spec" data-description><?php echo wp_kses_post( $d['prose'] ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $d['plans'] ) : ?>
<section class="planx container" id="plan" aria-labelledby="plan-title">
	<div class="sec-head">
		<p class="fig-label" data-plan-fig><?php echo esc_html( $d['plans'][0]['fig'] ); ?></p>
		<h2 class="sec-head__title" id="plan-title"><?php esc_html_e( 'The plan', 'bytenot-estate' ); ?></h2>
		<?php if ( count( $d['plans'] ) > 1 ) : ?>
		<div class="toggle" role="tablist" aria-label="<?php esc_attr_e( 'Floors', 'bytenot-estate' ); ?>" data-plan-tabs>
			<?php foreach ( $d['plans'] as $i => $pl ) : ?>
			<button class="toggle__btn<?php echo $i ? '' : ' is-active'; ?>" role="tab" aria-selected="<?php echo $i ? 'false' : 'true'; ?>" aria-controls="<?php echo esc_attr( $pl['key'] ); ?>" id="tab-<?php echo esc_attr( $pl['key'] ); ?>" type="button" data-plan="<?php echo esc_attr( $pl['key'] ); ?>" data-plan-fig-text="<?php echo esc_attr( $pl['fig'] ); ?>"<?php echo $i ? ' tabindex="-1"' : ''; ?>><?php echo esc_html( $pl['title'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="drawing">
		<div class="drawing__paper">
			<?php foreach ( $d['plans'] as $i => $pl ) : ?>
			<div class="drawing__sheet<?php echo $i ? '' : ' is-active'; ?>" id="<?php echo esc_attr( $pl['key'] ); ?>" role="tabpanel" aria-labelledby="tab-<?php echo esc_attr( $pl['key'] ); ?>" data-plan-sheet="<?php echo esc_attr( $pl['key'] ); ?>"<?php echo $i ? ' hidden' : ''; ?>>
				<img class="drawing__img" src="<?php echo esc_url( $pl['src'] ); ?>" alt="<?php echo esc_attr( $pl['title'] ); ?>" loading="lazy">
			</div>
			<?php endforeach; ?>
		</div>
		<div class="drawing__side">
			<?php foreach ( $d['plans'] as $i => $pl ) : ?>
				<?php if ( $pl['rooms'] ) : ?>
			<dl class="ledger ledger--rooms" data-plan-rooms="<?php echo esc_attr( $pl['key'] ); ?>"<?php echo $i ? ' hidden' : ''; ?>>
				<?php foreach ( $pl['rooms'] as $room ) : ?>
				<div><dt><?php echo esc_html( $room[0] ); ?></dt><dd><?php echo esc_html( $room[1] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ( $d['brochure'] ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $d['brochure'] ); ?>" download><?php esc_html_e( 'Download the brochure', 'bytenot-estate' ); ?>
				<?php bytenot_the_icon( 'download' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( $d['deal'] ) : ?>
<section class="deal container" id="numbers" aria-labelledby="numbers-title">
	<div class="deal__card">
		<div class="deal__top">
			<div>
				<p class="fig-label fig-label--light"><?php esc_html_e( 'The numbers', 'bytenot-estate' ); ?></p>
				<h2 class="deal__title" id="numbers-title"><?php esc_html_e( 'What it costs, honestly', 'bytenot-estate' ); ?></h2>
			</div>
			<p class="deal__price"><b data-price><?php echo esc_html( $d['priceText'] ); ?></b><span data-psf-carpet><?php echo esc_html( $d['psf_carpet'] ); ?></span></p>
		</div>
		<dl class="ledger ledger--dark" data-deal>
			<?php foreach ( $d['deal'] as $row ) : ?>
			<div<?php echo isset( $row[2] ) ? ' class="is-' . esc_attr( $row[2] ) . '"' : ''; ?>><dt><?php echo esc_html( $row[0] ); ?></dt><dd><?php echo wp_kses( $row[1], array( 'small' => array() ) ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<?php if ( $d['emi'] ) : ?>
		<p class="deal__emi" data-emi-sentence>
			<?php
			printf(
				/* translators: 1: EMI amount, 2: tenure button, 3: rate button, 4: financed share button. */
				esc_html__( 'Roughly %1$s a month over %2$s at %3$s, with %4$s of the price financed.', 'bytenot-estate' ),
				'<b class="num num--static" data-emi-value></b>',
				'<button class="num" type="button" data-emi-field="years" aria-haspopup="dialog">' . esc_html( sprintf( __( '%s years', 'bytenot-estate' ), $d['emi']['years'] ) ) . '</button>',
				'<button class="num" type="button" data-emi-field="rate" aria-haspopup="dialog">' . esc_html( $d['emi']['rate'] . '%' ) . '</button>',
				'<button class="num" type="button" data-emi-field="ltv" aria-haspopup="dialog">' . esc_html( $d['emi']['ltv'] . '%' ) . '</button>'
			);
			?>
			<span class="deal__hint"><?php esc_html_e( 'Tap a number to change it.', 'bytenot-estate' ); ?></span>
		</p>
		<div class="num-pop" data-num-pop hidden>
			<label><span data-pop-label></span><output data-pop-out></output></label>
			<input type="range" data-pop-range>
		</div>
		<?php endif; ?>
		<p class="deal__note"><?php echo esc_html( bytenot_mod( 'deal_note' ) ); ?></p>
	</div>
</section>
<?php endif; ?>

<?php if ( $d['landmarks'] || $d['lat'] ) : ?>
<section class="place container" id="place" aria-labelledby="place-title">
	<div class="sec-head">
		<p class="fig-label"><?php esc_html_e( 'The place', 'bytenot-estate' ); ?></p>
		<h2 class="sec-head__title" id="place-title"><?php echo esc_html( sprintf( /* translators: %s: locality. */ __( '%s, from the front door', 'bytenot-estate' ), $d['area'] ?: $d['cityLabel'] ) ); ?></h2>
	</div>
	<div class="place__grid">
		<?php if ( $d['landmarks'] ) : ?>
		<div class="radar" data-radar>
			<svg viewBox="0 0 560 560" class="radar__svg" aria-hidden="true" data-radar-svg></svg>
		</div>
		<?php endif; ?>
		<div class="place__side">
			<dl class="ledger ledger--places" data-landmarks>
				<?php foreach ( $d['landmarks'] as $i => $l ) : ?>
				<div data-spot="<?php echo (int) $i; ?>"><dt><?php echo esc_html( $l['name'] ); ?><?php if ( $l['type'] ) : ?><small><?php echo esc_html( $l['type'] ); ?></small><?php endif; ?></dt><dd><?php echo esc_html( null !== $l['km'] ? sprintf( __( '%s km', 'bytenot-estate' ), $l['km'] ) : $l['t'] ); ?><?php if ( $l['t'] && null !== $l['km'] ) : ?><small><?php echo esc_html( sprintf( /* translators: %s: drive time. */ __( '%s drive', 'bytenot-estate' ), $l['t'] ) ); ?></small><?php endif; ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<?php if ( $d['lat'] && $d['lng'] ) : ?>
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
			<?php foreach ( $d['papers'] as $row ) : ?>
			<div><dt><?php echo esc_html( $row[0] ); ?></dt><dd><?php echo wp_kses( $row[1], array( 'span' => array(), 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<div class="signature">
			<?php if ( $d['advisor']['photo'] ) : ?>
			<?php echo wp_get_attachment_image( $d['advisor']['photo'], 'thumbnail', false, array( 'class' => 'signature__photo', 'alt' => '', 'width' => 160, 'height' => 160, 'loading' => 'lazy' ) ); ?>
			<?php endif; ?>
			<div class="signature__body">
				<p class="fig-label"><?php esc_html_e( 'Your advisor', 'bytenot-estate' ); ?></p>
				<p class="signature__name" data-advisor-name><?php echo esc_html( $d['advisor']['name'] ); ?></p>
				<?php if ( $d['advisor']['meta'] ) : ?>
				<p class="signature__meta" data-advisor-meta><?php echo esc_html( $d['advisor']['meta'] ); ?></p>
				<?php endif; ?>
				<div class="signature__actions">
					<?php if ( $d['advisor']['phone'] ) : ?>
					<a class="btn btn--dark btn--sm" href="<?php echo esc_attr( bytenot_tel( $d['advisor']['phone'] ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s: first name. */ __( 'Call %s', 'bytenot-estate' ), $d['advisor']['first'] ) ); ?></a>
					<?php endif; ?>
					<?php if ( $d['wa'] ) : ?>
					<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $d['wa'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'bytenot-estate' ); ?></a>
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
			<p class="visit__lede"><?php echo esc_html( sprintf( /* translators: %s: advisor first name. */ __( 'Free, no obligation. %s confirms within the hour and meets you at the gate.', 'bytenot-estate' ), $d['advisor']['first'] ) ); ?></p>
		</div>
		<form class="visit__form" method="post" action="<?php echo esc_url( rest_url( 'property-core/v1/leads' ) ); ?>" data-enquire data-lead="visit" data-property="<?php echo (int) $d['id']; ?>">
			<label class="vf">
				<span class="vf__label"><?php esc_html_e( 'Your name', 'bytenot-estate' ); ?></span>
				<input class="vf__input" type="text" name="name" autocomplete="name" required>
			</label>
			<label class="vf">
				<span class="vf__label"><?php esc_html_e( 'Phone', 'bytenot-estate' ); ?></span>
				<span class="vf__phone"><span class="vf__prefix">+91</span><input class="vf__input" type="tel" name="phone" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" autocomplete="tel-national" required></span>
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
				<span><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( '%s may contact me about this property. No spam, and I can opt out any time.', 'bytenot-estate' ), get_bloginfo( 'name' ) ) ); ?></span>
			</label>
			<?php bytenot_lead_fields(); ?>
		</form>
	</div>
</section>

<?php if ( $d['similar'] ) : ?>
<section class="nearby container" id="nearby" aria-labelledby="nearby-title">
	<div class="sec-head sec-head--row">
		<div>
			<p class="fig-label"><?php esc_html_e( 'Nearby', 'bytenot-estate' ); ?></p>
			<h2 class="sec-head__title" id="nearby-title"><?php esc_html_e( 'In the same budget, close by', 'bytenot-estate' ); ?></h2>
		</div>
		<a class="link-arrow" href="<?php echo esc_url( $d['city_url'] ); ?>" data-similar-link><?php echo esc_html( sprintf( /* translators: %s: city. */ __( 'See all in %s', 'bytenot-estate' ), $d['cityLabel'] ) ); ?>
			<?php bytenot_the_icon( 'arrow' ); ?>
		</a>
	</div>
	<ul class="cards cards--similar" data-similar>
		<?php foreach ( $d['similar'] as $q ) : ?>
			<?php bytenot_card( $q, 'view' ); ?>
		<?php endforeach; ?>
	</ul>
</section>
<?php endif; ?>

</main>

<div class="prop-bar" data-prop-bar aria-hidden="true">
	<div class="prop-bar__info">
		<b data-title><?php echo esc_html( $d['title'] ); ?></b>
		<span><span data-price><?php echo esc_html( $d['priceText'] ); ?></span> · <span data-place><?php echo esc_html( $d['place'] ); ?></span></span>
	</div>
	<div class="prop-bar__actions">
		<?php if ( $d['advisor']['phone'] ) : ?>
		<a class="btn btn--outline btn--sm prop-bar__call" href="<?php echo esc_attr( bytenot_tel( $d['advisor']['phone'] ) ); ?>" aria-label="<?php esc_attr_e( 'Call', 'bytenot-estate' ); ?>">
			<?php bytenot_the_icon( 'phone', 18 ); ?>
		</a>
		<?php endif; ?>
		<?php if ( $d['wa'] ) : ?>
		<a class="btn btn--outline btn--sm" href="<?php echo esc_url( $d['wa'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'WhatsApp', 'bytenot-estate' ); ?></a>
		<?php endif; ?>
		<a class="btn btn--accent btn--sm" href="#visit" data-go-visit><?php esc_html_e( 'Book a visit', 'bytenot-estate' ); ?></a>
	</div>
</div>

<?php if ( $photos ) : ?>
<dialog class="lightbox" data-lightbox aria-label="<?php esc_attr_e( 'Photo gallery', 'bytenot-estate' ); ?>">
	<button class="lightbox__close" type="button" aria-label="<?php esc_attr_e( 'Close gallery', 'bytenot-estate' ); ?>" data-lb-close>
		<?php bytenot_the_icon( 'close', 22 ); ?>
	</button>
	<p class="lightbox__count"><span data-lb-index>1</span> / <span data-lb-total><?php echo (int) $n; ?></span></p>
	<div class="lightbox__stage" data-lb-stage>
		<img class="lightbox__img" src="" alt="" data-lb-img>
	</div>
	<button class="lightbox__arrow lightbox__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous photo', 'bytenot-estate' ); ?>" data-lb-prev>
		<?php bytenot_the_icon( 'arrow-left', 22 ); ?>
	</button>
	<button class="lightbox__arrow lightbox__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next photo', 'bytenot-estate' ); ?>" data-lb-next>
		<?php bytenot_the_icon( 'arrow', 22 ); ?>
	</button>
	<ul class="lightbox__strip" data-lb-strip>
		<?php foreach ( $photos as $i => $ph ) : ?>
		<li><button type="button" data-lb-go="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: photo number. */ __( 'Photo %d', 'bytenot-estate' ), $i + 1 ) ); ?>"><img src="<?php echo esc_url( $ph['thumb'] ); ?>" alt="" loading="lazy"></button></li>
		<?php endforeach; ?>
	</ul>
</dialog>
<?php endif; ?>

<?php
get_footer();
