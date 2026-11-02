/**
 * Featured spotlight: rail rows switch the stage (hover previews on pointer devices, tap or
 * Enter elsewhere), arrows and arrow keys step, the active highlight glides between rows, and
 * the corner tab and price bookmark are sized once to their longest label so the shapes never
 * change. The markup is server-rendered; this only switches.
 */
( function () {
	const $ = ( s, r = document ) => r.querySelector( s );
	const $$ = ( s, r = document ) => [ ...r.querySelectorAll( s ) ];
	const reduced = matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const canHover = matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

	$$( '[data-featured]' ).forEach( ( spot ) => {
		let items;
		try { items = JSON.parse( $( '[data-featured-items]', spot ).textContent ); } catch ( e ) { return; }
		if ( ! items.length ) return;
		const el = ( k ) => $( `[data-spot-${ k }]`, spot );
		const layers = [ el( 'img' ), el( 'img-next' ) ];
		const rail = $( '[data-rail]', spot );
		const rows = $$( '.rail__row', rail );
		const hl = $( '[data-rail-hl]', rail );
		let index = 0, top = 0, hoverTimer, hlPlaced = false;

		items.forEach( ( p ) => { if ( p.img ) { const i = new Image(); i.src = p.img; } } );

		const widest = ( node, labels ) => {
			const keep = node.textContent; let w = 0;
			node.style.minWidth = '0';
			labels.forEach( ( t ) => { node.textContent = t; w = Math.max( w, node.getBoundingClientRect().width ); } );
			node.textContent = keep; node.style.minWidth = `${ Math.ceil( w ) }px`;
		};
		const fixWidths = () => {
			widest( el( 'why' ), items.map( ( p ) => p.why ) );
			widest( el( 'price' ), items.map( ( p ) => p.price ) );
			widest( el( 'psf' ), items.map( ( p ) => p.psf ) );
		};
		fixWidths();
		document.fonts?.ready.then( fixWidths );
		let widthTimer; addEventListener( 'resize', () => { clearTimeout( widthTimer ); widthTimer = setTimeout( fixWidths, 150 ); } );

		const moveHl = () => {
			const li = rows[ index ].parentElement;
			if ( ! hlPlaced ) hl.style.transition = 'none';
			rail.style.setProperty( '--hl-top', `${ li.offsetTop }px` ); rail.style.setProperty( '--hl-h', `${ li.offsetHeight }px` );
			if ( ! hlPlaced ) { void hl.offsetHeight; hl.style.transition = ''; hlPlaced = true; rail.classList.add( 'is-placed' ); }
		};
		addEventListener( 'resize', moveHl );

		const swapImage = ( p ) => {
			const incoming = layers[ 1 - top ], outgoing = layers[ top ];
			let done = false;
			const reveal = () => { if ( done ) return; done = true; incoming.classList.add( 'is-on' ); outgoing.classList.remove( 'is-on' ); top = 1 - top; };
			incoming.alt = p.alt; incoming.onload = reveal; incoming.src = p.img;
			if ( incoming.complete && incoming.naturalWidth ) reveal();
		};
		const fill = ( p ) => {
			el( 'why' ).textContent = p.why;
			el( 'place' ).textContent = p.place;
			el( 'title' ).textContent = p.title;
			el( 'line' ).textContent = p.line;
			$$( '[data-spot-link]', spot ).forEach( ( a ) => { a.href = p.url; } );
			el( 'visit' ).href = p.visit;
			el( 'price' ).textContent = p.price;
			el( 'psf' ).textContent = p.psf;
			el( 'count' ).textContent = `${ String( index + 1 ).padStart( 2, '0' ) } / ${ String( items.length ).padStart( 2, '0' ) }`;
			rows.forEach( ( r, j ) => { r.classList.toggle( 'is-active', j === index ); r.setAttribute( 'aria-pressed', String( j === index ) ); } );
			moveHl();
		};
		const show = ( i ) => {
			index = ( i + items.length ) % items.length;
			const p = items[ index ];
			swapImage( p );
			if ( reduced ) { fill( p ); return; }
			rows.forEach( ( r, j ) => { r.classList.toggle( 'is-active', j === index ); r.setAttribute( 'aria-pressed', String( j === index ) ); } );
			moveHl();
			spot.classList.add( 'is-switching' );
			setTimeout( () => { fill( p ); spot.classList.remove( 'is-switching' ); }, 220 );
		};

		rows.forEach( ( row, i ) => {
			row.addEventListener( 'click', () => { if ( i !== index ) show( i ); } );
			if ( canHover ) {
				row.addEventListener( 'pointerenter', () => { clearTimeout( hoverTimer ); if ( i !== index ) hoverTimer = setTimeout( () => show( i ), 140 ); } );
				row.addEventListener( 'pointerleave', () => clearTimeout( hoverTimer ) );
			}
		} );
		$( '[data-prev]', spot )?.addEventListener( 'click', () => show( index - 1 ) );
		$( '[data-next]', spot )?.addEventListener( 'click', () => show( index + 1 ) );
		spot.addEventListener( 'keydown', ( e ) => {
			if ( e.key === 'ArrowLeft' ) { e.preventDefault(); show( index - 1 ); }
			if ( e.key === 'ArrowRight' ) { e.preventDefault(); show( index + 1 ); }
		} );
		moveHl();
	} );
} )();
