/**
 * Bytenot Estate: header state.
 *
 * The header turns into "bookmark" pieces once the page has scrolled past the
 * top. A 40px sentinel at the top of the page and an IntersectionObserver report
 * that change only when it happens; there is no per-scroll work. Everything else
 * in the header (menu overlay, links) is core WordPress and needs nothing here.
 */
const header = document.querySelector( '.site-header' );

if ( header && 'IntersectionObserver' in window ) {
	const sentinel = document.createElement( 'div' );
	sentinel.className = 'site-header__sentinel';
	sentinel.setAttribute( 'aria-hidden', 'true' );
	document.body.prepend( sentinel );

	new IntersectionObserver(
		( [ entry ] ) => header.classList.toggle( 'is-scrolled', ! entry.isIntersecting ),
		{ threshold: 0 }
	).observe( sentinel );
}
