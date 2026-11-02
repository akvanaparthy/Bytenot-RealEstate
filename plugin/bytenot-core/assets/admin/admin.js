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
