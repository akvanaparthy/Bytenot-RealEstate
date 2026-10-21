/* Lavenor Realty — landing page prototype behaviour.
 * Each block below is one self-contained "store" and maps 1:1 onto an
 * Interactivity API store (data-wp-interactive) when ported into the theme.
 */
(() => {
	'use strict';

	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

	/* ------------------------------------------------------------------
	 * Header: transparent over the hero, frosted + compact after scroll.
	 * ------------------------------------------------------------------ */
	const header = $('[data-header]');
	if (header) {
		const update = () => header.classList.toggle('is-scrolled', window.scrollY > 40);
		update();
		window.addEventListener('scroll', update, { passive: true });
	}

	/* ------------------------------------------------------------------
	 * Language switcher (menu button pattern).
	 * ------------------------------------------------------------------ */
	const lang = $('[data-lang]');
	if (lang) {
		const button = $('.lang__button', lang);
		const menu = $('.lang__menu', lang);
		const label = $('[data-lang-label]', lang);
		const items = $$('[data-lang-code]', menu);

		const open = () => { menu.hidden = false; button.setAttribute('aria-expanded', 'true'); items[0].focus(); };
		const close = (refocus = false) => { menu.hidden = true; button.setAttribute('aria-expanded', 'false'); if (refocus) button.focus(); };

		button.addEventListener('click', () => (menu.hidden ? open() : close()));
		items.forEach((item) => item.addEventListener('click', () => {
			items.forEach((i) => i.setAttribute('aria-checked', String(i === item)));
			label.textContent = item.dataset.langCode;
			close(true);
		}));
		document.addEventListener('click', (e) => { if (!lang.contains(e.target)) close(); });
		lang.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && !menu.hidden) { e.preventDefault(); close(true); }
			if (menu.hidden) return;
			const idx = items.indexOf(document.activeElement);
			if (e.key === 'ArrowDown') { e.preventDefault(); items[(idx + 1) % items.length].focus(); }
			if (e.key === 'ArrowUp') { e.preventDefault(); items[(idx - 1 + items.length) % items.length].focus(); }
		});
	}

	/* ------------------------------------------------------------------
	 * Mobile drawer.
	 * ------------------------------------------------------------------ */
	const drawer = $('[data-drawer]');
	if (drawer) {
		const opener = $('[data-drawer-open]');
		const closers = $$('[data-drawer-close]', drawer);
		let lastFocus = null;

		const open = () => {
			lastFocus = opener;
			drawer.hidden = false;
			requestAnimationFrame(() => drawer.classList.add('is-open'));
			document.body.classList.add('is-locked');
			opener.setAttribute('aria-expanded', 'true');
			$('.drawer__close', drawer).focus();
		};
		const close = () => {
			drawer.classList.remove('is-open');
			document.body.classList.remove('is-locked');
			opener.setAttribute('aria-expanded', 'false');
			const done = () => { drawer.hidden = true; lastFocus && lastFocus.focus(); };
			matchMedia('(prefers-reduced-motion: reduce)').matches ? done() : setTimeout(done, 300);
		};

		opener.addEventListener('click', open);
		closers.forEach((c) => c.addEventListener('click', close));
		$$('.drawer__nav a, .drawer__cta', drawer).forEach((a) => a.addEventListener('click', close));
		document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !drawer.hidden) close(); });
	}

	/* ------------------------------------------------------------------
	 * Search: hero tags prefill the form, chips are radios, submit shows
	 * the query URL the real search block will navigate to.
	 * ------------------------------------------------------------------ */
	const form = $('[data-search]');
	const toast = $('[data-toast]');
	let toastTimer;
	const showToast = (html) => {
		if (!toast) return;
		toast.innerHTML = html;
		toast.hidden = false;
		clearTimeout(toastTimer);
		toastTimer = setTimeout(() => { toast.hidden = true; }, 6000);
	};

	if (form) {
		const typeField = $('#f-type', form);

		$$('.tag[data-type]').forEach((tag) => tag.addEventListener('click', () => {
			$$('.tag').forEach((t) => t.classList.toggle('is-active', t === tag));
			typeField.value = tag.dataset.type;
			typeField.dd?.sync();
			const control = typeField.dd?.button || typeField;
			control.classList.add('is-flash');
			setTimeout(() => control.classList.remove('is-flash'), 900);
			control.focus({ preventScroll: true });
		}));
		typeField.addEventListener('change', () => {
			$$('.tag').forEach((t) => t.classList.toggle('is-active', t.dataset.type === typeField.value));
		});

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			const params = new URLSearchParams();
			new FormData(form).forEach((value, key) => { if (value) params.set(key, value); });
			// Same query the theme's search block will build; the listing page reads it.
			window.location.href = `${form.getAttribute('action')}?${params.toString()}`;
		});
	}

	/* ------------------------------------------------------------------
	 * Featured properties carousel. `featured` mirrors what the plugin's
	 * featured-properties query will return (title, excerpt, price, gallery).
	 * ------------------------------------------------------------------ */
	const featured = [
		{
			id: 'lakeview-villa',
			title: 'Great homes begin with the right guidance.',
			blurb: 'From the first site visit to the final paperwork, our advisors walk with you, so the home you choose feels lavish today and wise tomorrow.',
			price: '₹2.4 Cr',
			note: 'Lakeview Villa, Gachibowli — a 4 BHK villa with a private lawn, ten minutes from the Financial District.',
			gallery: ['assets/house-large.jpg', 'assets/p2.jpg', 'assets/p8.jpg'],
			aside: 'assets/house-small.jpg',
			alt: 'Contemporary two-storey villa with a landscaped garden',
			url: '#lakeview-villa',
		},
		{
			id: 'skyline-residences',
			title: 'Big city views, quiet evenings.',
			blurb: 'Skyline Residences pairs double-height living rooms with a rooftop garden and a five-minute walk to the metro.',
			price: '₹95 L',
			note: 'Skyline Residences, Whitefield — 2 and 3 BHK apartments ready for possession this year.',
			gallery: ['assets/p1.jpg', 'assets/p6.jpg', 'assets/p9.jpg'],
			aside: 'assets/p4.jpg',
			alt: 'Modern apartment block lit at dusk under a large tree',
			url: '#skyline-residences',
		},
		{
			id: 'courtyard-homes',
			title: 'Space to grow into, land to hold.',
			blurb: 'Courtyard Homes at Kokapet offers gated villa plots with approved layouts, so you can build when you are ready.',
			price: '₹1.1 Cr',
			note: 'Courtyard Homes, Kokapet — HMDA-approved villa plots from 200 sq. yd, inside a gated community.',
			gallery: ['assets/p3.jpg', 'assets/p5.jpg', 'assets/p7.jpg'],
			aside: 'assets/p1.jpg',
			alt: 'Modern house with timber cladding and a courtyard entrance',
			url: '#courtyard-homes',
		},
	];

	const showcase = $('[data-showcase]');
	if (showcase && featured.length) {
		const mainImg = $('[data-main-img]', showcase);
		const thumbs = $('[data-thumbs]', showcase);
		const asideImg = $('[data-aside-img]', showcase);
		const title = $('[data-title]', showcase);
		const blurb = $('[data-blurb]', showcase);
		const price = $('[data-price]', showcase);
		const note = $('[data-note]', showcase);
		const details = $('[data-details-link]', showcase);
		const prev = $('[data-prev]', showcase);
		const next = $('[data-next]', showcase);
		const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
		let index = 0;

		// Preload every gallery image once so switching never flashes.
		featured.forEach((p) => [...p.gallery, p.aside].forEach((src) => { const i = new Image(); i.src = src; }));

		const renderThumbs = (property, activeIdx) => {
			thumbs.innerHTML = '';
			property.gallery.forEach((src, i) => {
				const li = document.createElement('li');
				const btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'thumb' + (i === activeIdx ? ' is-active' : '');
				btn.setAttribute('aria-label', `Photo ${i + 1} of ${property.gallery.length}`);
				btn.setAttribute('aria-pressed', String(i === activeIdx));
				const img = document.createElement('img');
				img.src = src; img.alt = ''; img.width = 48; img.height = 48;
				btn.append(img);
				btn.addEventListener('click', () => {
					mainImg.src = src;
					$$('.thumb', thumbs).forEach((t, j) => { t.classList.toggle('is-active', j === i); t.setAttribute('aria-pressed', String(j === i)); });
				});
				li.append(btn);
				thumbs.append(li);
			});
		};

		const render = (i) => {
			const p = featured[i];
			mainImg.src = p.gallery[0];
			mainImg.alt = p.alt;
			asideImg.src = p.aside;
			title.textContent = p.title;
			blurb.textContent = p.blurb;
			price.textContent = p.price;
			note.textContent = p.note;
			details.href = p.url;
			renderThumbs(p, 0);
			showcase.setAttribute('aria-label', `Featured properties, ${i + 1} of ${featured.length}`);
		};

		const go = (dir) => {
			index = (index + dir + featured.length) % featured.length;
			if (reduced) { render(index); return; }
			showcase.classList.add('is-switching');
			setTimeout(() => { render(index); showcase.classList.remove('is-switching'); }, 250);
		};

		prev.addEventListener('click', () => go(-1));
		next.addEventListener('click', () => go(1));
		showcase.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); }
			if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
		});

		render(0);
	}

	/* ------------------------------------------------------------------
	 * Partners marquee. Driven by rAF so hover eases the speed down to a
	 * crawl instead of stopping dead. Static under reduced motion.
	 * ------------------------------------------------------------------ */
	const marquee = $('[data-marquee]');
	if (marquee && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
		const inner = $('[data-marquee-inner]', marquee);
		const first = $('.marquee__track', inner);
		const BASE = 56;   // px per second
		const SLOW = 6;    // px per second while hovered / focused
		let speed = BASE, target = BASE, offset = 0, last = performance.now();

		const slow = () => { target = SLOW; };
		const fast = () => { target = BASE; };
		marquee.addEventListener('pointerenter', slow);
		marquee.addEventListener('pointerleave', fast);
		marquee.addEventListener('focusin', slow);
		marquee.addEventListener('focusout', fast);

		const tick = (now) => {
			const dt = Math.min(0.05, (now - last) / 1000);
			last = now;
			speed += (target - speed) * Math.min(1, dt * 3); // ease toward target speed
			offset += speed * dt;
			const loop = first.getBoundingClientRect().width;
			if (loop && offset >= loop) offset -= loop;
			inner.style.transform = `translate3d(${-offset}px, 0, 0)`;
			requestAnimationFrame(tick);
		};
		requestAnimationFrame(tick);
	}

	/* ------------------------------------------------------------------
	 * Testimonials: native scroll-snap track. Arrows move one card by index
	 * (robust against clicks mid-animation) and disable at the ends.
	 * Swipe / drag / keyboard work without JS.
	 * ------------------------------------------------------------------ */
	const tTrack = $('[data-t-track]');
	if (tTrack) {
		const prev = $('[data-t-prev]');
		const next = $('[data-t-next]');
		const cards = $$('.t-card', tTrack);
		const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
		let index = 0;

		const step = () => cards[0].getBoundingClientRect().width + parseFloat(getComputedStyle(tTrack).columnGap || 24);
		const maxScroll = () => tTrack.scrollWidth - tTrack.clientWidth;
		const update = () => {
			const atEnd = tTrack.scrollLeft >= maxScroll() - 1;
			prev.disabled = tTrack.scrollLeft <= 0;
			next.disabled = atEnd || index >= cards.length - 1;
			prev.classList.toggle('is-active', next.disabled && !prev.disabled);
			next.classList.toggle('is-active', !next.disabled);
		};
		// Own easing instead of scrollTo({behavior:'smooth'}): Chrome drops programmatic
		// smooth scrolls that land on a mandatory snap point while another is in flight.
		let raf;
		const animateTo = (left) => {
			cancelAnimationFrame(raf);
			const start = tTrack.scrollLeft, dist = left - start, dur = reduced ? 0 : 450, t0 = performance.now();
			if (!dur || !dist) { tTrack.scrollLeft = left; return; }
			tTrack.style.scrollSnapType = 'none';
			const frame = (now) => {
				const p = Math.min(1, (now - t0) / dur);
				tTrack.scrollLeft = start + dist * (1 - Math.pow(1 - p, 3));
				if (p < 1) raf = requestAnimationFrame(frame); else tTrack.style.scrollSnapType = '';
			};
			raf = requestAnimationFrame(frame);
		};
		const go = (dir) => {
			index = Math.max(0, Math.min(cards.length - 1, index + dir));
			animateTo(Math.min(index * step(), maxScroll()));
			update();
		};
		let idle;
		tTrack.addEventListener('scroll', () => {
			update();
			clearTimeout(idle);
			idle = setTimeout(() => { index = Math.round(tTrack.scrollLeft / step()); update(); }, 120); // sync index after swipes
		}, { passive: true });
		prev.addEventListener('click', () => go(-1));
		next.addEventListener('click', () => go(1));
		window.addEventListener('resize', update);
		update();
	}

	/* ------------------------------------------------------------------
	 * Properties listing. `listings` mirrors the plugin's property query;
	 * filters read/write the URL so results are shareable, exactly as the
	 * Interactivity API store will do in the theme.
	 * ------------------------------------------------------------------ */
	const cardsEl = $('[data-cards]');
	if (cardsEl) {
		const L = 100000, CR = 10000000;
		const listings = [
			{ id: 1,  title: 'Lakeview Villa',          type: 'villa',     city: 'hyderabad', area: 'Gachibowli',      bhk: 4, sqft: 3650, baths: 4, price: 2.4 * CR,  intent: 'buy',  status: 'Ready to move',      img: 'assets/house-large.jpg', added: 20 },
			{ id: 2,  title: 'Skyline Residences',      type: 'apartment', city: 'bangalore', area: 'Whitefield',      bhk: 3, sqft: 1780, baths: 3, price: 95 * L,    intent: 'buy',  status: 'Possession 2026',    img: 'assets/p1.jpg',          added: 19 },
			{ id: 3,  title: 'Courtyard Homes Plot',    type: 'plot',      city: 'hyderabad', area: 'Kokapet',         bhk: 0, sqft: 2400, baths: 0, price: 1.1 * CR,  intent: 'buy',  status: 'HMDA approved',      img: 'assets/p3.jpg',          added: 18 },
			{ id: 4,  title: 'Palm Grove Apartment',    type: 'apartment', city: 'hyderabad', area: 'Kondapur',        bhk: 2, sqft: 1210, baths: 2, price: 78 * L,    intent: 'buy',  status: 'Ready to move',      img: 'assets/p2.jpg',          added: 17 },
			{ id: 5,  title: 'Aralia Garden Villa',     type: 'villa',     city: 'bangalore', area: 'Sarjapur Road',   bhk: 4, sqft: 3100, baths: 4, price: 3.2 * CR,  intent: 'buy',  status: 'New launch',         img: 'assets/p4.jpg',          added: 16 },
			{ id: 6,  title: 'Hebbal Lake Heights',     type: 'apartment', city: 'bangalore', area: 'Hebbal',          bhk: 3, sqft: 1650, baths: 3, price: 1.35 * CR, intent: 'buy',  status: 'Ready to move',      img: 'assets/p5.jpg',          added: 15 },
			{ id: 7,  title: 'Jubilee Hills Duplex',    type: 'villa',     city: 'hyderabad', area: 'Jubilee Hills',   bhk: 4, sqft: 4200, baths: 5, price: 5.5 * CR,  intent: 'buy',  status: 'Resale',             img: 'assets/p6.jpg',          added: 14 },
			{ id: 8,  title: 'Tellapur Meadows',        type: 'apartment', city: 'hyderabad', area: 'Tellapur',        bhk: 2, sqft: 1150, baths: 2, price: 62 * L,    intent: 'buy',  status: 'Possession 2027',    img: 'assets/p7.jpg',          added: 13 },
			{ id: 9,  title: 'Indiranagar Studio',      type: 'apartment', city: 'bangalore', area: 'Indiranagar',     bhk: 1, sqft: 720,  baths: 1, price: 48 * L,    intent: 'buy',  status: 'Ready to move',      img: 'assets/p8.jpg',          added: 12 },
			{ id: 10, title: 'Manikonda Residency',     type: 'apartment', city: 'hyderabad', area: 'Manikonda',       bhk: 3, sqft: 1580, baths: 3, price: 42000,     intent: 'rent', status: 'Available now',      img: 'assets/p9.jpg',          added: 11 },
			{ id: 11, title: 'Electronic City Suites',  type: 'apartment', city: 'bangalore', area: 'Electronic City', bhk: 2, sqft: 1080, baths: 2, price: 28000,     intent: 'rent', status: 'Available 1 Dec',    img: 'assets/p2.jpg',          added: 10 },
			{ id: 12, title: 'Banjara Hills Penthouse', type: 'apartment', city: 'hyderabad', area: 'Banjara Hills',   bhk: 4, sqft: 3900, baths: 4, price: 1.6 * L,   intent: 'rent', status: 'Available now',      img: 'assets/house-small.jpg', added: 9 },
		];
		const budgets = { '0-50l': [0, 50 * L], '50l-1cr': [50 * L, 1 * CR], '1cr-2cr': [1 * CR, 2 * CR], '2cr-': [2 * CR, Infinity] };
		const typeLabel = { apartment: 'Apartments', villa: 'Villas', plot: 'Plots', commercial: 'Commercial spaces' };
		const cityLabel = { hyderabad: 'Hyderabad', bangalore: 'Bangalore' };
		const PAGE = 6;

		const form = $('[data-listing-filters]');
		const sortEl = $('[data-sort]');
		const label = $('[data-results-label]');
		const count = $('[data-results-count]');
		const empty = $('[data-results-empty]');
		const more = $('[data-load-more]');
		const tpl = $('#card-template');
		let shown = PAGE;

		const rupees = (n, rent) => {
			const amount = n >= CR ? `₹${(n / CR).toFixed(n % CR ? 2 : 0).replace(/\.?0+$/, '')} Cr` : n >= L ? `₹${Math.round(n / L)} L` : `₹${n.toLocaleString('en-IN')}`;
			return rent ? `${amount}/mo` : amount;
		};

		// 1. URL → form (deep link from the landing-page search)
		const params = new URLSearchParams(location.search);
		$$('select', form).forEach((sel) => { if (params.has(sel.name)) sel.value = params.get(sel.name); });
		if (params.has('sort')) sortEl.value = params.get('sort');

		const current = () => Object.fromEntries(new FormData(form));
		const filtered = () => {
			const f = current();
			let out = listings.filter((p) =>
				(!f.type || p.type === f.type) &&
				(!f.location || p.city === f.location) &&
				(!f.bhk || (f.bhk === '4' ? p.bhk >= 4 : p.bhk === Number(f.bhk))) &&
				(!f.intent || (f.intent === 'buy' ? p.intent === 'buy' : f.intent === 'rent' ? p.intent === 'rent' : f.intent === 'new-launch' ? /launch|possession/i.test(p.status) : /resale/i.test(p.status))) &&
				(!f.budget || (p.intent === 'buy' && p.price >= budgets[f.budget][0] && p.price < budgets[f.budget][1]))
			);
			const sort = sortEl.value;
			if (sort === 'price-asc') out.sort((a, b) => a.price - b.price);
			if (sort === 'price-desc') out.sort((a, b) => b.price - a.price);
			if (sort === 'newest') out.sort((a, b) => b.added - a.added);
			return out;
		};

		const canHover = matchMedia('(hover: hover)').matches;
		const render = () => {
			const f = current();
			const list = filtered();
			cardsEl.innerHTML = '';
			list.slice(0, shown).forEach((p) => {
				const node = tpl.content.cloneNode(true);
				const img = $('.card__img', node);
				img.src = p.img; img.alt = `${p.title}, ${p.area}`;
				$('.card__status', node).textContent = p.status;
				const link = $('.card__title a', node); link.textContent = p.title; link.href = `#property-${p.id}`;
				$('.card__media', node).href = `#property-${p.id}`;
				$('.card__place span', node).textContent = `${p.area}, ${cityLabel[p.city]}`;
				$('[data-fact=area]', node).textContent = p.type === 'plot' ? `${p.sqft.toLocaleString('en-IN')} sq. yd` : `${p.sqft.toLocaleString('en-IN')} sq. ft`;
				$('[data-fact=beds]', node).textContent = p.bhk ? `${p.bhk} BHK` : 'Plot';
				$('[data-fact=baths]', node).textContent = p.baths ? `${p.baths} Baths` : 'Gated';
				$('.card__price', node).textContent = rupees(p.price, p.intent === 'rent');
				cardsEl.append(node);
			});
			// hover / focus state as one class, so opening and closing can run in different orders
			$$('.card', cardsEl).forEach((card) => {
				if (canHover) {
					card.addEventListener('pointerenter', () => card.classList.add('is-open'));
					card.addEventListener('pointerleave', () => card.classList.remove('is-open'));
				}
				card.addEventListener('focusin', () => card.classList.add('is-open'));
				card.addEventListener('focusout', (e) => { if (!card.contains(e.relatedTarget)) card.classList.remove('is-open'); });
			});
			label.textContent = `${typeLabel[f.type] || 'All properties'}${f.location ? ` in ${cityLabel[f.location]}` : ''}${f.intent === 'rent' ? ' for rent' : ''}`;
			count.textContent = `(${list.length} result${list.length === 1 ? '' : 's'})`;
			empty.hidden = list.length > 0;
			more.hidden = list.length <= shown;
			$$('select', form).forEach((sel) => sel.classList.toggle('is-set', !!sel.value));

			// 2. form → URL (shareable, back-button friendly)
			const q = new URLSearchParams();
			Object.entries(f).forEach(([k, v]) => { if (v) q.set(k, v); });
			if (sortEl.value !== 'featured') q.set('sort', sortEl.value);
			history.replaceState(null, '', q.toString() ? `?${q}` : location.pathname);
		};

		form.addEventListener('change', () => { shown = PAGE; render(); });
		sortEl.addEventListener('change', render);
		more.addEventListener('click', () => { shown += PAGE; render(); });
		$('[data-results-reset]')?.addEventListener('click', () => { form.reset(); shown = PAGE; render(); });
		render();
	}

	/* ------------------------------------------------------------------
	 * Video modal (native <dialog>).
	 * ------------------------------------------------------------------ */
	const modal = $('[data-video-modal]');
	if (modal && typeof modal.showModal === 'function') {
		$$('[data-video-open]').forEach((b) => b.addEventListener('click', () => { modal.showModal(); document.body.classList.add('is-locked'); }));
		$('[data-video-close]', modal).addEventListener('click', () => modal.close());
		modal.addEventListener('click', (e) => { if (e.target === modal) modal.close(); });
		modal.addEventListener('close', () => document.body.classList.remove('is-locked'));
	}

	/* ------------------------------------------------------------------
	 * Dropdowns. Every <select> inside .field__select gets a styled listbox
	 * on mouse/trackpad devices; the select stays in the form as the value
	 * store, so filtering, URL sync and submit work unchanged. Touch keeps
	 * the native picker. Runs last so initial values (URL → form) are read.
	 * → one Interactivity API store reused by every select in the theme.
	 * ------------------------------------------------------------------ */
	if (matchMedia('(hover: hover) and (pointer: fine)').matches) {
		let closeOpen = null;
		let uid = 0;

		$$('.field__select > select').forEach((select) => {
			const wrap = select.parentElement;
			const id = select.id || `dd-${++uid}`;

			const button = document.createElement('button');
			button.type = 'button';
			button.id = `${id}-button`;
			button.className = `${select.className} dd__button`;
			button.setAttribute('aria-haspopup', 'listbox');
			button.setAttribute('aria-expanded', 'false');
			button.setAttribute('aria-controls', `${id}-menu`);

			const menu = document.createElement('div');
			menu.id = `${id}-menu`;
			menu.className = 'dd__menu';
			menu.setAttribute('role', 'listbox');
			menu.tabIndex = -1;
			menu.hidden = true;
			const options = [...select.options].map((opt) => {
				const el = document.createElement('div');
				el.className = 'dd__option';
				el.setAttribute('role', 'option');
				el.tabIndex = -1;
				el.textContent = opt.text;
				menu.append(el);
				return el;
			});

			// accessible name: the field's label, then the current value
			const label = (select.id && $(`label[for="${select.id}"]`)) || wrap.closest('label')?.firstElementChild;
			if (label) {
				label.id ||= `${id}-label`;
				if (label.htmlFor) label.htmlFor = button.id;
				button.setAttribute('aria-labelledby', `${label.id} ${button.id}`);
				menu.setAttribute('aria-labelledby', label.id);
			}

			select.classList.add('dd__native');
			select.tabIndex = -1;
			select.setAttribute('aria-hidden', 'true');
			wrap.classList.add('dd');
			wrap.prepend(button);
			wrap.append(menu);

			const sync = () => {
				const i = select.selectedIndex;
				button.textContent = select.options[i]?.text || '';
				button.classList.toggle('is-set', !!select.value);
				options.forEach((o, k) => o.setAttribute('aria-selected', String(k === i)));
			};

			let hideTimer;
			const close = (refocus = false) => {
				if (menu.hidden) return;
				menu.classList.remove('is-open');
				wrap.classList.remove('is-open');
				button.setAttribute('aria-expanded', 'false');
				hideTimer = setTimeout(() => { menu.hidden = true; }, 160);
				if (closeOpen === close) closeOpen = null;
				if (refocus) button.focus();
			};
			const open = () => {
				if (closeOpen) closeOpen();
				closeOpen = close;
				clearTimeout(hideTimer);
				menu.hidden = false;
				// open upwards when the viewport has no room below (reading offsetHeight also
				// settles the start state so the fade-in transition runs)
				const r = wrap.getBoundingClientRect();
				const below = innerHeight - r.bottom;
				menu.classList.toggle('dd__menu--up', below < menu.offsetHeight + 12 && r.top > below);
				menu.classList.add('is-open');
				wrap.classList.add('is-open');
				button.setAttribute('aria-expanded', 'true');
				options[Math.max(select.selectedIndex, 0)].focus();
			};
			const choose = (i) => {
				if (select.selectedIndex !== i) {
					select.selectedIndex = i;
					select.dispatchEvent(new Event('change', { bubbles: true }));
				}
				sync();
				close(true);
			};

			button.addEventListener('click', () => (menu.hidden ? open() : close(true)));
			button.addEventListener('keydown', (e) => {
				if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); open(); }
			});
			menu.addEventListener('click', (e) => {
				e.preventDefault(); // a click inside the wrapping <label> must not re-activate the button
				const opt = e.target.closest('.dd__option');
				if (opt) choose(options.indexOf(opt));
			});
			let typed = '';
			let typedAt = 0;
			menu.addEventListener('keydown', (e) => {
				const i = options.indexOf(document.activeElement);
				const go = (k) => { e.preventDefault(); options[Math.min(Math.max(k, 0), options.length - 1)].focus(); };
				if (e.key === 'ArrowDown') go(i + 1);
				else if (e.key === 'ArrowUp') go(i - 1);
				else if (e.key === 'Home') go(0);
				else if (e.key === 'End') go(options.length - 1);
				else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); if (i > -1) choose(i); }
				else if (e.key === 'Escape') { e.preventDefault(); close(true); }
				else if (e.key === 'Tab') close();
				else if (e.key.length === 1 && !e.altKey && !e.ctrlKey && !e.metaKey) {
					const now = Date.now();
					typed = now - typedAt > 500 ? e.key : typed + e.key;
					typedAt = now;
					const hit = options.find((o) => o.textContent.toLowerCase().startsWith(typed.toLowerCase()));
					if (hit) hit.focus();
				}
			});
			wrap.addEventListener('focusout', (e) => { if (!wrap.contains(e.relatedTarget)) close(); });
			document.addEventListener('pointerdown', (e) => { if (!wrap.contains(e.target)) close(); });

			// keep in step with the select when something else changes it
			select.addEventListener('change', sync);
			select.form?.addEventListener('reset', () => setTimeout(sync));
			select.dd = { button, sync };
			sync();
		});
	}
})();
