/**
 * Property search: quick-type tags prefill the form; every <select> inside .field__select
 * gets a styled listbox on pointer devices (the select stays as the value store, so submit
 * and URL sync work unchanged; touch keeps the native picker).
 */
( function () {
	const $ = ( s, r = document ) => r.querySelector( s );
	const $$ = ( s, r = document ) => [ ...r.querySelectorAll( s ) ];

	/* ---- tags → type field ---- */
	const form = $( '[data-search]' );
	const typeField = form && $( '#f-type', form );
	if ( typeField ) {
		$$( '[data-search-tags] .tag' ).forEach( ( tag ) => tag.addEventListener( 'click', () => {
			$$( '[data-search-tags] .tag' ).forEach( ( t ) => t.classList.toggle( 'is-active', t === tag ) );
			typeField.value = tag.dataset.type;
			typeField.dd?.sync();
			const control = typeField.dd?.button || typeField;
			control.classList.add( 'is-flash' );
			setTimeout( () => control.classList.remove( 'is-flash' ), 900 );
			control.focus( { preventScroll: true } );
		} ) );
		typeField.addEventListener( 'change', () => $$( '[data-search-tags] .tag' ).forEach( ( t ) => t.classList.toggle( 'is-active', t.dataset.type === typeField.value ) ) );
		// drop empty params so the archive URL stays clean
		form.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			const params = new URLSearchParams();
			new FormData( form ).forEach( ( v, k ) => { if ( v ) params.set( k, v ); } );
			window.location.href = form.action + ( params.toString() ? '?' + params : '' );
		} );
	}

	/* ---- styled dropdowns ---- */
	if ( ! matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) return;
	let uid = 0;
	$$( '.field__select > select' ).forEach( ( select ) => {
		if ( select.dd ) return;
		const wrap = select.parentElement;
		const id = select.id || `dd-${ ++uid }`;
		const button = document.createElement( 'button' );
		button.type = 'button'; button.id = `${ id }-button`;
		button.className = `${ select.className } dd__button`;
		button.setAttribute( 'aria-haspopup', 'listbox' ); button.setAttribute( 'aria-expanded', 'false' ); button.setAttribute( 'aria-controls', `${ id }-menu` );
		const menu = document.createElement( 'div' );
		menu.id = `${ id }-menu`; menu.className = 'dd__menu'; menu.setAttribute( 'role', 'listbox' ); menu.tabIndex = -1; menu.hidden = true;
		const options = [ ...select.options ].map( ( opt ) => {
			const el = document.createElement( 'div' );
			el.className = 'dd__option'; el.setAttribute( 'role', 'option' ); el.tabIndex = -1; el.textContent = opt.text;
			menu.append( el ); return el;
		} );
		const label = ( select.id && $( `label[for="${ select.id }"]` ) ) || wrap.closest( 'label' )?.firstElementChild;
		if ( label ) {
			label.id ||= `${ id }-label`;
			if ( label.htmlFor ) label.htmlFor = button.id;
			button.setAttribute( 'aria-labelledby', `${ label.id } ${ button.id }` );
			menu.setAttribute( 'aria-labelledby', label.id );
		}
		select.classList.add( 'dd__native' ); select.tabIndex = -1; select.setAttribute( 'aria-hidden', 'true' );
		wrap.classList.add( 'dd' ); wrap.append( button, menu );

		const sync = () => {
			const i = select.selectedIndex;
			button.textContent = select.options[ i ]?.text || '';
			options.forEach( ( o, j ) => o.setAttribute( 'aria-selected', String( j === i ) ) );
			button.classList.toggle( 'is-set', !! select.value );
		};
		const open = () => {
			menu.hidden = false;
			const room = innerHeight - button.getBoundingClientRect().bottom;
			menu.classList.toggle( 'dd__menu--up', room < menu.offsetHeight + 24 );
			requestAnimationFrame( () => menu.classList.add( 'is-open' ) );
			button.setAttribute( 'aria-expanded', 'true' );
			( options[ select.selectedIndex ] || options[ 0 ] )?.focus();
		};
		const close = ( refocus = false ) => {
			if ( menu.hidden ) return;
			menu.classList.remove( 'is-open' );
			setTimeout( () => { menu.hidden = true; }, 160 );
			button.setAttribute( 'aria-expanded', 'false' );
			if ( refocus ) button.focus();
		};
		const choose = ( i ) => {
			if ( select.selectedIndex !== i ) { select.selectedIndex = i; select.dispatchEvent( new Event( 'change', { bubbles: true } ) ); }
			sync(); close( true );
		};
		button.addEventListener( 'click', () => ( menu.hidden ? open() : close( true ) ) );
		button.addEventListener( 'keydown', ( e ) => { if ( e.key === 'ArrowDown' || e.key === 'ArrowUp' ) { e.preventDefault(); open(); } } );
		menu.addEventListener( 'click', ( e ) => { e.preventDefault(); const opt = e.target.closest( '.dd__option' ); if ( opt ) choose( options.indexOf( opt ) ); } );
		let typed = '', typedAt = 0;
		menu.addEventListener( 'keydown', ( e ) => {
			const i = options.indexOf( document.activeElement );
			const go = ( k ) => { e.preventDefault(); options[ Math.min( Math.max( k, 0 ), options.length - 1 ) ].focus(); };
			if ( e.key === 'ArrowDown' ) go( i + 1 );
			else if ( e.key === 'ArrowUp' ) go( i - 1 );
			else if ( e.key === 'Home' ) go( 0 );
			else if ( e.key === 'End' ) go( options.length - 1 );
			else if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); if ( i > -1 ) choose( i ); }
			else if ( e.key === 'Escape' ) { e.preventDefault(); close( true ); }
			else if ( e.key === 'Tab' ) close();
			else if ( e.key.length === 1 && ! e.altKey && ! e.ctrlKey && ! e.metaKey ) {
				const now = Date.now();
				typed = now - typedAt > 500 ? e.key : typed + e.key; typedAt = now;
				const hit = options.find( ( o ) => o.textContent.toLowerCase().startsWith( typed.toLowerCase() ) );
				if ( hit ) hit.focus();
			}
		} );
		wrap.addEventListener( 'focusout', ( e ) => { if ( ! wrap.contains( e.relatedTarget ) ) close(); } );
		document.addEventListener( 'pointerdown', ( e ) => { if ( ! wrap.contains( e.target ) ) close(); } );
		select.addEventListener( 'change', sync );
		select.form?.addEventListener( 'reset', () => setTimeout( sync ) );
		select.dd = { button, sync };
		sync();
	} );
} )();
