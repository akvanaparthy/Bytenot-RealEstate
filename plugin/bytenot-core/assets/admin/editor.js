/* Bytenot Core listing editor: tabs, media pickers, the landmarks repeater. */
(() => {
	const t = window.bytenotEditor || {};
	const root = document.querySelector('.bn-editor');
	if (!root) return;

	// tabs
	const tabs = [...root.querySelectorAll('.bn-editor__tab')];
	const panels = [...root.querySelectorAll('.bn-editor__panel')];
	tabs.forEach((tab) => tab.addEventListener('click', () => {
		tabs.forEach((x) => { const on = x === tab; x.classList.toggle('is-active', on); x.setAttribute('aria-selected', String(on)); });
		panels.forEach((p) => { const on = p.dataset.panel === tab.dataset.tab; p.classList.toggle('is-active', on); p.hidden = !on; });
	}));

	// media: photo lists and single files
	root.querySelectorAll('.bn-media').forEach((box) => {
		const hidden = box.querySelector('input[type=hidden]');
		const list = box.querySelector('.bn-media__list');
		const multiple = box.dataset.media === 'multiple';
		const sync = () => { if (list) hidden.value = [...list.querySelectorAll('li')].map((li) => li.dataset.id).join(','); };
		box.querySelector('.bn-media__add').addEventListener('click', () => {
			if (!window.wp || !wp.media) return;
			const frame = wp.media({ title: multiple ? (t.photos || 'Choose photos') : (t.file || 'Choose a file'), multiple, library: multiple ? { type: 'image' } : {}, button: { text: t.choose || 'Choose' } });
			frame.on('select', () => {
				const items = frame.state().get('selection').toJSON();
				if (multiple) {
					items.forEach((a) => {
						if (list.querySelector(`li[data-id="${a.id}"]`)) return;
						const li = document.createElement('li');
						li.dataset.id = a.id;
						const src = (a.sizes && (a.sizes.thumbnail || a.sizes.medium || a.sizes.full) || {}).url || a.url;
						li.innerHTML = `<img src="${src}" alt=""><button type="button" class="bn-media__remove" aria-label="${t.remove || 'Remove'}">×</button>`;
						list.append(li);
					});
					sync();
				} else if (items[0]) {
					hidden.value = items[0].id;
					box.querySelector('.bn-media__name').textContent = items[0].filename || items[0].title;
					box.querySelector('.bn-media__clear').hidden = false;
				}
			});
			frame.open();
		});
		box.addEventListener('click', (e) => {
			if (e.target.closest('.bn-media__remove')) { e.target.closest('li').remove(); sync(); }
			if (e.target.closest('.bn-media__clear')) { hidden.value = ''; box.querySelector('.bn-media__name').innerHTML = '<span class="bn-muted">—</span>'; e.target.closest('.bn-media__clear').hidden = true; }
		});
	});

	// landmarks
	root.querySelectorAll('.bn-landmarks').forEach((box) => {
		const list = box.querySelector('.bn-landmarks__list');
		const tpl = box.querySelector('.bn-landmarks__tpl');
		box.querySelector('.bn-landmarks__add').addEventListener('click', () => {
			const i = Date.now();
			const html = tpl.innerHTML.replace(/__i__/g, i);
			list.insertAdjacentHTML('beforeend', html);
			list.lastElementChild.querySelector('input').focus();
		});
		list.addEventListener('click', (e) => { if (e.target.closest('.bn-lm__remove')) e.target.closest('li').remove(); });
	});
})();
