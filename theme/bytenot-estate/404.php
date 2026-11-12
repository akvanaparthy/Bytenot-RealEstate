<?php
/**
 * Not found. Two faces, by what was asked for:
 *  - under a listings path, and with three homes to show: "404" set across the page, each
 *    numeral filled with a listing's photo and linking to it;
 *  - anywhere else, or on a site with fewer than three listings: an architect's floor plan
 *    with one room never built.
 *
 * @package bytenot-estate
 */

defined( 'ABSPATH' ) || exit;

$bytenot_path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( rawurldecode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- shown escaped, as text only.
$bytenot_host = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
$bytenot_more = bytenot_featured_items( 3 );

$bytenot_under_listings = false;
if ( function_exists( 'bytenot_core_kinds' ) ) {
	foreach ( array_keys( bytenot_core_kinds( true ) ) as $bytenot_kind ) {
		$bytenot_base = rtrim( (string) wp_parse_url( bytenot_archive_url( $bytenot_kind ), PHP_URL_PATH ), '/' );
		if ( '' !== $bytenot_base && 0 === strpos( $bytenot_path, $bytenot_base . '/' ) ) {
			$bytenot_under_listings = true;
		}
	}
}
$bytenot_homes = $bytenot_under_listings && count( $bytenot_more ) >= 3;
$bytenot_noun  = bytenot_noun();

get_header();
?>

<main id="main" class="lost <?php echo esc_attr( bytenot_class( $bytenot_homes ? 'lost--homes' : 'lost--plan' ) ); ?>">
	<?php if ( $bytenot_homes ) : ?>
	<section class="container lost__homes" aria-labelledby="lost-title">
		<div class="lost__nums" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: what listings are called, e.g. "projects". */ __( 'Three %s on the market', 'bytenot-estate' ), $bytenot_noun ) ); ?>">
			<?php foreach ( array( '4', '0', '4' ) as $bytenot_i => $bytenot_digit ) : $bytenot_p = $bytenot_more[ $bytenot_i ]; ?>
			<a class="lost__num" href="<?php echo esc_url( $bytenot_p['url'] ); ?>" style="background-image:url('<?php echo esc_url( $bytenot_p['stage'] ?: $bytenot_p['img'] ); ?>')" aria-label="<?php echo esc_attr( $bytenot_p['title'] . ', ' . $bytenot_p['place'] ); ?>"><?php echo esc_html( $bytenot_digit ); ?><span class="lost__cap" aria-hidden="true"><b><?php echo esc_html( $bytenot_p['title'] ); ?></b><?php echo esc_html( $bytenot_p['place'] . ( $bytenot_p['priceText'] ? ' · ' . $bytenot_p['priceText'] : '' ) ); ?></span></a>
			<?php endforeach; ?>
		</div>
		<div class="lost__under">
			<div class="lost__copy">
				<h1 class="lost__title" id="lost-title"><?php esc_html_e( 'Nothing lives at that address. These three do.', 'bytenot-estate' ); ?></h1>
				<p class="lost__lede"><?php esc_html_e( 'The page may have been sold or the link mistyped. The numbers above are real homes on the market this week: point at one to see it, click to walk through.', 'bytenot-estate' ); ?></p>
				<?php if ( '' !== $bytenot_path && '/' !== $bytenot_path ) : ?>
				<p class="lost__path"><?php esc_html_e( 'Asked for', 'bytenot-estate' ); ?> <b><?php echo esc_html( $bytenot_host . mb_strimwidth( $bytenot_path, 0, 80, '…' ) ); ?></b></p>
				<?php endif; ?>
			</div>
			<div class="lost__actions">
				<a class="btn btn--accent" href="<?php echo esc_url( bytenot_archive_url() ); ?>"><?php echo esc_html( sprintf( /* translators: %s: what listings are called, e.g. "projects". */ __( 'See all %s', 'bytenot-estate' ), $bytenot_noun ) ); ?> <?php bytenot_the_icon( 'arrow', 18 ); ?></a>
				<form role="search" method="get" class="lost__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="uf lost__field"><span class="uf__label" for="lost-search"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></span>
						<input class="uf__input" id="lost-search" type="search" name="s" placeholder="<?php esc_attr_e( 'Project or locality', 'bytenot-estate' ); ?>">
					</label>
					<button class="btn btn--dark btn--sm" type="submit"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></button>
				</form>
			</div>
		</div>
	</section>
	<?php else : ?>
	<section class="container lost__plan" aria-labelledby="lost-title">
		<div class="lost__copy">
			<h1 class="lost__title" id="lost-title"><?php esc_html_e( "This room isn't on the plan.", 'bytenot-estate' ); ?></h1>
			<p class="lost__lede"><?php esc_html_e( 'The page you asked for was never built, or it has been sold and taken down. Everything we have on the market is drawn up and ready to walk through.', 'bytenot-estate' ); ?></p>
			<?php if ( '' !== $bytenot_path && '/' !== $bytenot_path ) : ?>
			<p class="lost__path"><?php esc_html_e( 'Asked for', 'bytenot-estate' ); ?> <b><?php echo esc_html( $bytenot_host . mb_strimwidth( $bytenot_path, 0, 80, '…' ) ); ?></b></p>
			<?php endif; ?>
			<div class="lost__actions">
				<a class="btn btn--accent" href="<?php echo esc_url( bytenot_archive_url() ); ?>"><?php echo esc_html( sprintf( /* translators: %s: what listings are called, e.g. "projects". */ __( 'See all %s', 'bytenot-estate' ), $bytenot_noun ) ); ?> <?php bytenot_the_icon( 'arrow', 18 ); ?></a>
				<form role="search" method="get" class="lost__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="uf lost__field"><span class="uf__label" for="lost-search"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></span>
						<input class="uf__input" id="lost-search" type="search" name="s" placeholder="<?php esc_attr_e( 'Project or locality', 'bytenot-estate' ); ?>">
					</label>
					<button class="btn btn--dark btn--sm" type="submit"><?php esc_html_e( 'Search', 'bytenot-estate' ); ?></button>
				</form>
			</div>
		</div>
		<figure class="lost__sheet" aria-hidden="true">
			<svg viewBox="-40 -40 720 540" focusable="false">
				<defs><pattern id="lost-grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M20 0H0V20" fill="none" stroke="currentColor" stroke-opacity=".07" stroke-width=".6"/></pattern></defs>
				<rect x="-40" y="-40" width="720" height="540" fill="url(#lost-grid)"/>
				<g class="lost__dim"><path d="M0 -22H640M0 -27V-17M640 -27V-17M-22 0V440M-27 0H-17M-27 440H-17"/></g>
				<text class="lost__size" x="320" y="-30" text-anchor="middle">64' 0"</text>
				<text class="lost__size" x="-30" y="224" text-anchor="middle" transform="rotate(-90 -30 224)">44' 0"</text>
				<rect class="lost__wall" x="0" y="0" width="640" height="440"/>
				<path class="lost__wall" d="M300 0V240M300 160H460M460 0V240M0 240H400M260 240V440M400 240V440"/>
				<path class="lost__thin" d="M60 -4H200M60 4H200M500 -4H600M500 4H600M-4 300V400M4 300V400M636 60V180M644 60V180"/>
				<path class="lost__door" d="M300 60V110A50 50 0 0 1 250 60M460 60V110A50 50 0 0 0 510 60M120 240H170A50 50 0 0 1 120 290M330 160V210A50 50 0 0 1 380 160M260 300V350A50 50 0 0 1 310 300M400 420H350A50 50 0 0 1 400 370"/>
				<path class="lost__gap" d="M310 440H370"/>
				<path class="lost__door" d="M310 440V380A60 60 0 0 1 370 440"/>
				<text class="lost__label" x="150" y="118" text-anchor="middle"><?php esc_html_e( 'Living', 'bytenot-estate' ); ?></text><text class="lost__size" x="150" y="136" text-anchor="middle">18' × 14'</text>
				<text class="lost__label" x="380" y="76" text-anchor="middle"><?php esc_html_e( 'Kitchen', 'bytenot-estate' ); ?></text><text class="lost__size" x="380" y="94" text-anchor="middle">12' × 9'</text>
				<text class="lost__label" x="380" y="196" text-anchor="middle"><?php esc_html_e( 'Bath', 'bytenot-estate' ); ?></text><text class="lost__size" x="380" y="214" text-anchor="middle">8' × 6'</text>
				<text class="lost__label" x="550" y="118" text-anchor="middle"><?php esc_html_e( 'Bedroom', 'bytenot-estate' ); ?></text><text class="lost__size" x="550" y="136" text-anchor="middle">14' × 12'</text>
				<text class="lost__label" x="130" y="336" text-anchor="middle"><?php esc_html_e( 'Bedroom 2', 'bytenot-estate' ); ?></text><text class="lost__size" x="130" y="354" text-anchor="middle">13' × 12'</text>
				<text class="lost__label" x="330" y="330" text-anchor="middle"><?php esc_html_e( 'Passage', 'bytenot-estate' ); ?></text>
				<rect class="lost__missing" x="400" y="240" width="240" height="200"/>
				<text class="lost__missing-num" x="520" y="356" text-anchor="middle">404</text>
				<text class="lost__missing-label" x="520" y="392" text-anchor="middle"><?php esc_html_e( 'Not built', 'bytenot-estate' ); ?></text>
				<g transform="translate(615 -10)"><path class="lost__north" d="M0 -14L6 6L0 2L-6 6Z"/><text class="lost__size" x="0" y="-18" text-anchor="middle">N</text></g>
			</svg>
			<figcaption class="lost__note"><span><?php esc_html_e( 'Sheet 1 of 1 · 1:100', 'bytenot-estate' ); ?></span><span><?php esc_html_e( 'Drawn from the address you asked for', 'bytenot-estate' ); ?></span></figcaption>
		</figure>
	</section>
	<?php if ( $bytenot_more ) : ?>
	<section class="container lost__more" aria-labelledby="lost-more-title">
		<div class="lost__head">
			<h2 class="lost__h2" id="lost-more-title"><?php esc_html_e( 'On the market now', 'bytenot-estate' ); ?></h2>
			<a class="link-arrow" href="<?php echo esc_url( bytenot_archive_url() ); ?>"><?php echo esc_html( sprintf( /* translators: %s: what listings are called, e.g. "projects". */ __( 'All %s', 'bytenot-estate' ), $bytenot_noun ) ); ?> <?php bytenot_the_icon( 'arrow' ); ?></a>
		</div>
		<ul class="cards" data-live-cards>
			<?php foreach ( $bytenot_more as $bytenot_p ) : ?>
				<?php bytenot_card( $bytenot_p ); ?>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>
	<?php endif; ?>
</main>

<?php
get_footer();
