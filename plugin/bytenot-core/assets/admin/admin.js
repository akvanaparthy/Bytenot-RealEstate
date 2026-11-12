/* Bytenot Core admin: copy buttons and the demo confirmation. */
(() => {
	const t = window.bytenotAdmin || {};
	document.querySelectorAll('[data-copy]').forEach((b) => b.addEventListener('click', async () => {
		try {
			await navigator.clipboard.writeText(b.dataset.copy);
			const was = b.textContent;
			b.textContent = t.copied || 'Copied';
			setTimeout(() => { b.textContent = was; }, 1500);
		} catch (e) { /* clipboard blocked */ }
	}));
	document.querySelectorAll('[data-confirm-demo]').forEach((f) => f.addEventListener('submit', (e) => {
		if (!window.confirm(t.confirmDemo || 'Reinstall the demo content?')) e.preventDefault();
	}));
})();

/* a single-image picker (the sharing image on the setup screen) */
(() => {
	const t = window.bytenotAdmin || {};
	document.querySelectorAll('[data-media="image"]').forEach((box) => {
		const hidden = box.querySelector('input[type=hidden]');
		const thumb = box.querySelector('.bn-media__thumb');
		const name = box.querySelector('.bn-media__name');
		const clear = box.querySelector('.bn-media__clear');
		box.querySelector('.bn-media__add').addEventListener('click', () => {
			if (!window.wp || !wp.media) return;
			const frame = wp.media({ title: t.choose || 'Choose image', multiple: false, library: { type: 'image' }, button: { text: t.choose || 'Choose image' } });
			frame.on('select', () => {
				const a = frame.state().get('selection').first()?.toJSON();
				if (!a) return;
				hidden.value = a.id;
				thumb.src = (a.sizes && (a.sizes.medium || a.sizes.full) || {}).url || a.url;
				thumb.hidden = false;
				name.textContent = a.filename || a.title;
				clear.hidden = false;
			});
			frame.open();
		});
		clear.addEventListener('click', () => { hidden.value = ''; thumb.hidden = true; thumb.removeAttribute('src'); name.innerHTML = `<span class="bn-muted">${t.none || 'None'}</span>`; clear.hidden = true; });
	});
})();
