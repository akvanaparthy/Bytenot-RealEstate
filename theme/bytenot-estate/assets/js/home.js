/**
 * Bytenot Estate: home section behaviour.
 *
 * - About statement: words light up against the pinned scroll progress, side photos pop
 *   in and out, and photos drift toward the cursor. The pointer part cannot be CSS; the
 *   scroll part updates only when the lit count changes.
 * - Testimonials: arrow buttons step a scroll-snap track (swipe and keyboard work without this).
 * - Inquiry band: when a scroll ends near the band while approaching it, settle it flush.
 */
const $ = ( s, r = document ) => r.querySelector( s );
const $$ = ( s, r = document ) => [ ...r.querySelectorAll( s ) ];
const reduced = matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
const fine = matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

/* ---------- about ---------- */
const about = $( '[data-about]' );
if ( about ) {
	const stage = $( '.about__stage', about );
	const statement = $( '[data-reveal-words]', about );
	[ ...statement.childNodes ].forEach( ( node ) => {
		if ( node.nodeType !== Node.TEXT_NODE ) { node.classList.add( 'about__w' ); return; }
		const frag = document.createDocumentFragment();
		node.textContent.split( /(\s+)/ ).forEach( ( part ) => {
			if ( ! part ) return;
			if ( /^\s+$/.test( part ) ) { frag.append( ' ' ); return; }
			const w = document.createElement( 'span' );
			w.className = 'about__w';
			w.textContent = part;
			frag.append( w );
		} );
		node.replaceWith( frag );
	} );
	const words = $$( '.about__w', statement );
	const floats = $$( '[data-float]', about ).map( ( el ) => ( { el, from: +el.dataset.in, out: el.dataset.out ? +el.dataset.out : null, x: 0, y: 0, tx: 0, ty: 0 } ) );
	const clamp01 = ( v ) => Math.min( 1, Math.max( 0, v ) );
	let lit = -1, last = -1, ticking = false;
	const update = () => {
		const travel = about.offsetHeight - stage.offsetHeight;
		const p = reduced ? 1 : clamp01( -about.getBoundingClientRect().top / travel );
		if ( p === last ) return;
		last = p;
		const n = Math.round( clamp01( p / 0.9 ) * words.length );
		if ( n !== lit ) { lit = n; words.forEach( ( w, i ) => w.classList.toggle( 'is-on', i < n ) ); }
		floats.forEach( ( { el, from, out } ) => el.classList.toggle( 'is-in', p >= from && ( out === null || p < out ) ) );
	};
	const onScroll = () => { if ( ticking ) return; ticking = true; requestAnimationFrame( () => { ticking = false; update(); } ); };
	update();
	addEventListener( 'scroll', onScroll, { passive: true } );
	addEventListener( 'resize', onScroll );

	if ( fine ) {
		const RADIUS = 320, PULL = 26;
		let raf = 0;
		const settle = () => {
			let moving = false;
			floats.forEach( ( f ) => {
				f.x += ( f.tx - f.x ) * 0.1; f.y += ( f.ty - f.y ) * 0.1;
				if ( Math.abs( f.tx - f.x ) > 0.05 || Math.abs( f.ty - f.y ) > 0.05 ) moving = true;
				f.el.style.setProperty( '--mx', `${ f.x.toFixed( 2 ) }px` );
				f.el.style.setProperty( '--my', `${ f.y.toFixed( 2 ) }px` );
			} );
			raf = moving ? requestAnimationFrame( settle ) : 0;
		};
		const aim = ( cx, cy ) => {
			floats.forEach( ( f ) => {
				if ( ! f.el.classList.contains( 'is-in' ) ) { f.tx = f.ty = 0; return; }
				const r = f.el.getBoundingClientRect();
				const dx = cx - ( r.left + r.width / 2 - f.x ), dy = cy - ( r.top + r.height / 2 - f.y );
				const d = Math.hypot( dx, dy );
				const k = d < RADIUS ? ( 1 - d / RADIUS ) * PULL / Math.max( d, 1 ) : 0;
				f.tx = dx * k; f.ty = dy * k;
			} );
			if ( ! raf ) raf = requestAnimationFrame( settle );
		};
		stage.addEventListener( 'pointermove', ( e ) => aim( e.clientX, e.clientY ) );
		stage.addEventListener( 'pointerleave', () => aim( -9999, -9999 ) );
		const clamp = ( v ) => Math.max( -12, Math.min( 12, v ) );
		$$( '.about__pill', statement ).forEach( ( pill ) => {
			pill.addEventListener( 'pointermove', ( e ) => {
				const r = pill.getBoundingClientRect();
				pill.classList.add( 'is-magnet' );
				pill.style.setProperty( '--mx', `${ clamp( ( e.clientX - r.left - r.width / 2 ) * 0.25 ) }px` );
				pill.style.setProperty( '--my', `${ clamp( ( e.clientY - r.top - r.height / 2 ) * 0.25 ) }px` );
			} );
			pill.addEventListener( 'pointerleave', () => { pill.classList.remove( 'is-magnet' ); pill.style.setProperty( '--mx', '0px' ); pill.style.setProperty( '--my', '0px' ); } );
		} );
	}
}

/* ---------- testimonials ---------- */
const track = $( '[data-t-track]' );
if ( track ) {
	const prev = $( '[data-t-prev]' ), next = $( '[data-t-next]' );
	const cards = $$( '.t-card', track );
	let index = 0, raf, idle;
	const step = () => cards[ 0 ].getBoundingClientRect().width + parseFloat( getComputedStyle( track ).columnGap || 24 );
	const maxScroll = () => track.scrollWidth - track.clientWidth;
	const update = () => {
		const atEnd = track.scrollLeft >= maxScroll() - 1;
		prev.disabled = track.scrollLeft <= 0;
		next.disabled = atEnd || index >= cards.length - 1;
		prev.classList.toggle( 'is-active', next.disabled && ! prev.disabled );
		next.classList.toggle( 'is-active', ! next.disabled );
	};
	// own easing: Chrome drops programmatic smooth scrolls that land on a mandatory snap point mid-flight
	const animateTo = ( left ) => {
		cancelAnimationFrame( raf );
		const start = track.scrollLeft, dist = left - start, dur = reduced ? 0 : 450, t0 = performance.now();
		if ( ! dur || ! dist ) { track.scrollLeft = left; return; }
		track.style.scrollSnapType = 'none';
		const frame = ( now ) => {
			const p = Math.min( 1, ( now - t0 ) / dur );
			track.scrollLeft = start + dist * ( 1 - Math.pow( 1 - p, 3 ) );
			if ( p < 1 ) raf = requestAnimationFrame( frame ); else track.style.scrollSnapType = '';
		};
		raf = requestAnimationFrame( frame );
	};
	const go = ( dir ) => { index = Math.max( 0, Math.min( cards.length - 1, index + dir ) ); animateTo( Math.min( index * step(), maxScroll() ) ); update(); };
	track.addEventListener( 'scroll', () => { update(); clearTimeout( idle ); idle = setTimeout( () => { index = Math.round( track.scrollLeft / step() ); update(); }, 120 ); }, { passive: true } );
	prev.addEventListener( 'click', () => go( -1 ) );
	next.addEventListener( 'click', () => go( 1 ) );
	addEventListener( 'resize', update );
	update();
}

/* ---------- inquiry band ---------- */
const band = $( '[data-settle]' );
if ( band ) {
	let lastGap = Infinity, settleTimer;
	const settle = () => {
		const top = band.getBoundingClientRect().top, gap = Math.abs( top ), approaching = gap < lastGap;
		lastGap = gap;
		if ( gap > 2 && gap < innerHeight * 0.22 && approaching ) { lastGap = 0; scrollTo( { top: scrollY + top, behavior: reduced ? 'instant' : 'smooth' } ); }
	};
	if ( 'onscrollend' in window ) addEventListener( 'scrollend', settle );
	else addEventListener( 'scroll', () => { clearTimeout( settleTimer ); settleTimer = setTimeout( settle, 140 ); }, { passive: true } );
}
