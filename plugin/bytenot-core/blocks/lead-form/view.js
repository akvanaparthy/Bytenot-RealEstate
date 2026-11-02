/**
 * Lead form: posts JSON to the leads endpoint and reports back in place. Without this
 * script the form still posts to the same endpoint as a normal submission.
 */
( function () {
	document.querySelectorAll( '[data-lead-form]' ).forEach( ( form ) => {
		const status = form.querySelector( '.lead-form__status' );
		const button = form.querySelector( 'button[type="submit"]' );
		form.addEventListener( 'submit', async ( e ) => {
			e.preventDefault();
			if ( ! form.reportValidity() ) return;
			const f = new FormData( form );
			const payload = {
				name: f.get( 'name' ),
				email: f.get( 'email' ) || '',
				phone: '+91' + String( f.get( 'phone' ) || '' ).replace( /\D/g, '' ),
				message: f.get( 'message' ) || '',
				preferred_date: f.get( 'preferred_date' ) || '',
				consent: !! f.get( 'consent' ),
				website: f.get( 'website' ) || '',
				started: Number( f.get( 'started' ) ) || 0,
				form: form.dataset.form,
				property_id: Number( form.dataset.property ) || 0,
				source_url: location.href,
				attribution: document.referrer ? 'referrer=' + document.referrer : '',
			};
			status.classList.remove( 'is-error' );
			status.textContent = '';
			button.disabled = true;
			try {
				const res = await fetch( form.dataset.endpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify( payload ) } );
				const data = await res.json();
				if ( ! res.ok ) throw new Error( data.message || 'Request failed' );
				status.textContent = data.message;
				form.reset();
			} catch ( err ) {
				status.classList.add( 'is-error' );
				status.textContent = err.message;
			} finally {
				button.disabled = false;
			}
		} );
	} );
} )();
