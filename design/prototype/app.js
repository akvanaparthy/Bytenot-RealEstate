/* Lavenor Realty — landing page prototype behaviour.
 * Each block below is one self-contained "store" and maps 1:1 onto an
 * Interactivity API store (data-wp-interactive) when ported into the theme.
 */
(() => {
	'use strict';

	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

	/* Listing data shared by the properties page and the property page (→ plugin query / REST). */
	const L = 100000, CR = 10000000;
	const LISTINGS = [
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
	const cityLabel = { hyderabad: 'Hyderabad', bangalore: 'Bangalore' };
	const rupees = (n, rent) => {
		const amount = n >= CR ? `₹${(n / CR).toFixed(n % CR ? 2 : 0).replace(/\.?0+$/, '')} Cr` : n >= L ? `₹${Math.round(n / L)} L` : `₹${n.toLocaleString('en-IN')}`;
		return rent ? `${amount}/mo` : amount;
	};

	/* ------------------------------------------------------------------
	 * Site configuration. In WordPress this is Bytenot Core > Setup: the
	 * agency picks which property types and transactions it deals in, and
	 * the option lists come from the CRM sync (Zoho picklists, synced
	 * localities). An agency that only sells plots never shows "Apartment"
	 * or a BHK filter. Prototype override for demos:
	 *   ?types=plot,commercial&intents=buy
	 * ------------------------------------------------------------------ */
	const TYPES = { apartment: 'Apartment', villa: 'Villa', plot: 'Plot', commercial: 'Commercial' };
	const INTENTS = { buy: 'Buy', rent: 'Rent', 'new-launch': 'New Launch', resale: 'Resale' };
	const SITE = {
		types: ['apartment', 'villa', 'plot', 'commercial'],
		intents: ['buy', 'rent', 'new-launch', 'resale'],
		budgets: [['0-50l', 'Under ₹50 L'], ['50l-1cr', '₹50 L – ₹1 Cr'], ['1cr-2cr', '₹1 Cr – ₹2 Cr'], ['2cr-', 'Above ₹2 Cr']],
		configurations: [['1', '1 BHK'], ['2', '2 BHK'], ['3', '3 BHK'], ['4', '4 BHK+']],
		locations: [...new Set(LISTINGS.map((p) => p.city))].map((c) => [c, cityLabel[c]]),
	};
	const demo = new URLSearchParams(location.search);
	if (demo.has('types')) SITE.types = demo.get('types').split(',').filter((t) => TYPES[t]);
	if (demo.has('intents')) SITE.intents = demo.get('intents').split(',').filter((i) => INTENTS[i]);
	if (!SITE.types.length) SITE.types = Object.keys(TYPES);
	if (!SITE.intents.length) SITE.intents = ['buy'];
	SITE.hasConfig = SITE.types.some((t) => t === 'apartment' || t === 'villa'); // BHK only means something for homes
	SITE.listings = LISTINGS.filter((p) => SITE.types.includes(p.type) && (p.intent === 'rent' ? SITE.intents.includes('rent') : SITE.intents.some((i) => i !== 'rent')));
	const demoQuery = () => ['types', 'intents'].filter((k) => demo.has(k)).map((k) => `${k}=${demo.get(k)}`);

	const fillSelect = (select, items, placeholder) => {
		if (!select) return;
		const keep = select.value;
		select.innerHTML = '';
		if (placeholder !== undefined) select.append(new Option(placeholder, ''));
		items.forEach(([v, label]) => select.append(new Option(label, v)));
		select.value = keep;
		if (select.selectedIndex < 0) select.selectedIndex = 0;
	};
	$$('[data-listing-count]').forEach((el) => { const n = SITE.listings.length; el.textContent = `${n} ${n === 1 ? 'property' : 'properties'}`; });

	/* ------------------------------------------------------------------
	 * Header: transparent over the hero, frosted + compact after scroll.
	 * ------------------------------------------------------------------ */
	// where the menu follows the page, a reload starts at the top: the rendered menu state is then right from
	// the first frame and nothing shifts under a restored position
	if ($('[data-about], [data-inquiry]') && $('[data-header] a[href*="#"]') && 'scrollRestoration' in history && performance.getEntriesByType('navigation')[0]?.type === 'reload' && !location.hash) {
		history.scrollRestoration = 'manual';
		scrollTo({ top: 0, behavior: 'instant' });
	}

	const header = $('[data-header]');
	if (header) {
		const update = () => {
			const y = window.scrollY, on = header.classList.contains('is-scrolled');
			if (y > 40 && !on) header.classList.add('is-scrolled'); else if (y < 8 && on) header.classList.remove('is-scrolled'); // hysteresis: no flutter at rest near the line
		};
		update();
		window.addEventListener('scroll', update, { passive: true });
	}

	/* ------------------------------------------------------------------
	 * Menu follows the page: same-page section links (#about, #inquiry)
	 * light up as their section scrolls into view; the home link while above
	 * the first section.
	 * ------------------------------------------------------------------ */
	const spyLinks = $$('.nav__list a, .drawer__nav a');
	/* a link's section: its hash on this page; or, for a link to another page, a section here whose id matches
	   the link's last path segment (the Properties link lights up over the featured properties section) */
	const sameHash = (a) => {
		const h = a.getAttribute('href') || '';
		const u = new URL(a.href, location.href);
		if (u.pathname === location.pathname) return u.hash && u.hash !== '#' ? u.hash : '';
		if (h === '#') return '';
		const slug = u.pathname.replace(/\/+$/, '').split('/').pop();
		return slug && document.getElementById(slug) ? `#${slug}` : null;
	};
	const spy = spyLinks.map((a) => ({ a, hash: sameHash(a) })).filter((l) => l.hash !== null);
	const spyTargets = [...new Set(spy.map((l) => l.hash).filter(Boolean))].map((hash) => ({ hash, el: $(hash) })).filter((t) => t.el);
	const navList = $('.nav__list');
	const navHl = navList && $('.nav__hl', navList);
	let hlPlaced = false;
	const placeNavHl = () => {
		if (!navHl) return;
		const on = $('.nav__link.is-active', navList);
		if (!on) { navList.classList.remove('is-placed'); hlPlaced = false; return; }
		if (!hlPlaced) navHl.style.transition = 'none';
		navList.style.setProperty('--hl-x', `${on.offsetLeft}px`);
		navList.style.setProperty('--hl-w', `${on.offsetWidth}px`);
		if (!hlPlaced) { void navHl.offsetWidth; navHl.style.transition = ''; hlPlaced = true; navList.classList.add('is-placed'); }
	};
	placeNavHl();
	document.fonts?.ready.then(placeNavHl);
	addEventListener('resize', placeNavHl);
	if (spyTargets.length) {
		let current = null;
		const paint = () => {
			const line = innerHeight * 0.4;
			let hit = '';
			spyTargets.forEach((t) => { if (t.el.getBoundingClientRect().top <= line) hit = t.hash; });
			if (hit === current) return;
			current = hit;
			spy.forEach((l) => {
				const active = l.hash === hit;
				l.a.classList.toggle('is-active', active && l.a.classList.contains('nav__link'));
				if (active) l.a.setAttribute('aria-current', 'location'); else l.a.removeAttribute('aria-current');
			});
			placeNavHl();
		};
		let spyTick = false;
		addEventListener('scroll', () => { if (!spyTick) { spyTick = true; requestAnimationFrame(() => { spyTick = false; paint(); }); } }, { passive: true });
		addEventListener('resize', paint);
		paint();
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
		/* Prototype only: the real switcher is rendered by the multilingual plugin and links to the
		   translated URL (/hi/, /te/). Here the choice is remembered and reflected on <html lang>. */
		const LANGS = { Eng: 'en', 'हिं': 'hi', 'తె': 'te' };
		const apply = (code) => {
			items.forEach((i) => i.setAttribute('aria-checked', String(i.dataset.langCode === code)));
			label.textContent = code;
			document.documentElement.lang = LANGS[code] || 'en';
		};
		try { const saved = localStorage.getItem('lavenor-lang'); if (saved && LANGS[saved]) apply(saved); } catch (e) { /* storage blocked */ }
		items.forEach((item) => item.addEventListener('click', () => {
			const code = item.dataset.langCode;
			apply(code);
			try { localStorage.setItem('lavenor-lang', code); } catch (e) { /* storage blocked */ }
			close(true);
			if (code !== 'Eng') showToast(`${item.textContent} selected. Translated pages appear here once the multilingual plugin and translations are in place.`);
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
		document.addEventListener('keydown', (e) => {
			if (drawer.hidden) return;
			if (e.key === 'Escape') { close(); return; }
			if (e.key !== 'Tab') return;
			// the dialog keeps focus: Tab wraps inside the panel
			const f = $$('a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])', drawer).filter((el) => el.offsetParent);
			if (!f.length) return;
			const first = f[0], last = f[f.length - 1];
			if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); } else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
		});
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

		// Fields and options come from the site configuration, never from markup.
		fillSelect(typeField, SITE.types.map((t) => [t, TYPES[t]]), 'Enter type');
		fillSelect($('#f-budget', form), SITE.budgets, 'Price range');
		fillSelect($('#f-location', form), SITE.locations, 'Location');
		fillSelect($('#f-bhk', form), SITE.configurations, 'Any');
		if (!SITE.hasConfig) $('[data-field="bhk"]', form).hidden = true;
		form.classList.toggle('search--three', !SITE.hasConfig);
		const tagList = $('.tags');
		if (tagList) {
			tagList.innerHTML = '';
			SITE.types.slice(0, 4).forEach((t) => {
				const li = document.createElement('li');
				li.innerHTML = `<button class="tag" type="button" data-type="${t}">${TYPES[t]}</button>`;
				tagList.append(li);
			});
		}
		const intentSet = $('.filters', form);
		if (intentSet) {
			$$('.chip', intentSet).forEach((c) => c.remove());
			SITE.intents.forEach((k, i) => {
				const l = document.createElement('label');
				l.className = 'chip';
				l.innerHTML = `<input type="radio" name="intent" value="${k}"${i === 0 ? ' checked' : ''}><span>${INTENTS[k]}</span>`;
				intentSet.append(l);
			});
			intentSet.hidden = SITE.intents.length < 2;
		}

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
			demoQuery().forEach((q) => { const [k, v] = q.split('='); params.set(k, v); });
			// Same query the theme's search block will build; the listing page reads it.
			window.location.href = `${form.getAttribute('action')}?${params.toString()}`;
		});
	}

	/* ------------------------------------------------------------------
	 * Featured properties: one spotlight, a rail of the rest. `FEATURED`
	 * mirrors the plugin's featured query: listings flagged in WordPress
	 * with an editor-written reason; the facts come from the CRM mirror.
	 * ------------------------------------------------------------------ */
	const FEATURED = [
		{ id: 1, why: "Advisor's pick" },
		{ id: 5, why: 'New launch' },
		{ id: 7, why: 'Price revised' },
		{ id: 2, why: 'Metro in 5 min' },
	].map((f) => ({ ...f, ...SITE.listings.find((p) => p.id === f.id) })).filter((p) => p.title);
	// An agency whose configuration excludes the flagged homes still gets a rail: newest listings fill in.
	SITE.listings.filter((p) => !FEATURED.some((f) => f.id === p.id)).slice(0, Math.max(0, 4 - FEATURED.length)).forEach((p) => FEATURED.push({ ...p, why: 'New this week' }));

	const spot = $('[data-featured]');
	if (spot && FEATURED.length) {
		const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
		const canHover = matchMedia('(hover: hover) and (pointer: fine)').matches;
		const layers = [$('[data-spot-img]', spot), $('[data-spot-img-next]', spot)];
		const el = (k) => $(`[data-spot-${k}]`, spot);
		const rail = $('[data-rail]', spot);
		let index = 0, top = 0;

		FEATURED.forEach((p) => { const i = new Image(); i.src = p.img; });

		const n = (x) => x.toLocaleString('en-IN');
		const facts = (p) => p.type === 'plot'
			? `Plot · ${n(p.sqft)} sq yd · Gated layout`
			: `${p.bhk} BHK ${p.type} · ${n(p.sqft)} sq ft · ${p.status}`;
		const psf = (p) => p.intent === 'rent' ? 'per month' : `₹${n(Math.round(p.price / p.sqft))} per sq ${p.type === 'plot' ? 'yd' : 'ft'}`;

		$$('li', rail).forEach((li) => li.remove()); // the server-rendered rows; rebuilt identically from data
		FEATURED.forEach((p, i) => {
			const li = document.createElement('li');
			li.innerHTML = `<button class="rail__row" type="button" aria-pressed="false">
				<img class="rail__thumb" src="${p.img}" alt="" width="72" height="72" loading="lazy">
				<span class="rail__text"><span class="rail__name">${p.title}</span><span class="rail__meta">${p.area}, ${cityLabel[p.city]} · ${p.type === 'plot' ? `${n(p.sqft)} sq yd` : `${p.bhk} BHK`}</span></span>
				<span class="rail__price">${rupees(p.price, p.intent === 'rent')}<span class="rail__why">${p.why}</span></span></button>`;
			const row = li.firstElementChild;
			// A row switches the stage when pressed (click, tap or Enter); hovering only highlights it.
			row.addEventListener('click', () => { if (i !== index) show(i); });
			rail.append(li);
		});
		const rows = $$('.rail__row', rail);

		/* The corner tab and the price bookmark are shaped entirely in CSS; the script only fixes their
		   width to the longest label so the shape never changes with the content. */
		const widest = (node, labels) => {
			const keep = node.textContent; let w = 0;
			node.style.minWidth = '0';
			labels.forEach((t) => { node.textContent = t; w = Math.max(w, node.getBoundingClientRect().width); });
			node.textContent = keep; node.style.minWidth = `${Math.ceil(w)}px`;
		};
		const fixWidths = () => {
			widest(el('why'), FEATURED.map((p) => p.why));
			widest(el('price'), FEATURED.map((p) => rupees(p.price, p.intent === 'rent')));
			widest(el('psf'), FEATURED.map(psf));
		};
		fixWidths();
		document.fonts?.ready.then(fixWidths);
		let widthTimer; addEventListener('resize', () => { clearTimeout(widthTimer); widthTimer = setTimeout(fixWidths, 150); });

		const hl = $('[data-rail-hl]', rail);
		let hlPlaced = false;
		const moveHl = () => {
			const li = rows[index].parentElement;
			if (!hlPlaced) hl.style.transition = 'none'; // first placement is instant: no grow-in on load
			rail.style.setProperty('--hl-top', `${li.offsetTop}px`); rail.style.setProperty('--hl-h', `${li.offsetHeight}px`);
			if (!hlPlaced) { void hl.offsetHeight; hl.style.transition = ''; hlPlaced = true; rail.classList.add('is-placed'); }
		};
		addEventListener('resize', moveHl);
		if (FEATURED.length < 2) $('.spot__nav', spot).hidden = true;

		const swapImage = (p) => {
			const incoming = layers[1 - top], outgoing = layers[top];
			let done = false;
			const reveal = () => {
				if (done) return; done = true;
				incoming.classList.add('is-on'); outgoing.classList.remove('is-on'); top = 1 - top;
			};
			incoming.alt = `${p.title}, ${p.area}`;
			incoming.onload = reveal;
			incoming.src = p.img;
			if (incoming.complete && incoming.naturalWidth) reveal();
		};
		const fill = (p) => {
			el('why').textContent = p.why;
			el('place').textContent = `${p.area}, ${cityLabel[p.city]}`;
			el('title').textContent = p.title;
			el('line').textContent = facts(p);
			$$('[data-spot-link]', spot).forEach((a) => { a.href = `property.html?id=${p.id}`; });
			el('visit').href = `property.html?id=${p.id}#visit`;
			el('price').textContent = rupees(p.price, p.intent === 'rent');
			el('psf').textContent = psf(p);
			el('count').textContent = `${String(index + 1).padStart(2, '0')} / ${String(FEATURED.length).padStart(2, '0')}`;
			rows.forEach((r, j) => { r.classList.toggle('is-active', j === index); r.setAttribute('aria-pressed', String(j === index)); });
			moveHl();
		};
		const show = (i) => {
			index = (i + FEATURED.length) % FEATURED.length;
			const p = FEATURED[index];
			swapImage(p);
			if (reduced) { fill(p); return; }
			rows.forEach((r, j) => { r.classList.toggle('is-active', j === index); r.setAttribute('aria-pressed', String(j === index)); });
			moveHl();
			spot.classList.add('is-switching');
			setTimeout(() => { fill(p); spot.classList.remove('is-switching'); }, 220);
		};

		$('[data-prev]', spot).addEventListener('click', () => show(index - 1));
		$('[data-next]', spot).addEventListener('click', () => show(index + 1));
		spot.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
			if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
		});

		layers[0].src = FEATURED[0].img; layers[0].alt = `${FEATURED[0].title}, ${FEATURED[0].area}`; layers[0].classList.add('is-on');
		fill(FEATURED[0]);
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
		['pointerdown', 'touchstart', 'wheel'].forEach((ev) => tTrack.addEventListener(ev, () => { cancelAnimationFrame(raf); tTrack.style.scrollSnapType = ''; }, { passive: true }));
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
		const listings = SITE.listings;
		const budgets = { '0-50l': [0, 50 * L], '50l-1cr': [50 * L, 1 * CR], '1cr-2cr': [1 * CR, 2 * CR], '2cr-': [2 * CR, Infinity] };
		const typeLabel = { apartment: 'Apartments', villa: 'Villas', plot: 'Plots', commercial: 'Commercial spaces' };
		const PAGE = 6;

		const form = $('[data-listing-filters]');
		const sortEl = $('[data-sort]');
		const label = $('[data-results-label]');
		const count = $('[data-results-count]');
		const empty = $('[data-results-empty]');
		const more = $('[data-load-more]');
		const tpl = $('#card-template');
		let shown = PAGE;

		// 0. Fields and options from the site configuration
		fillSelect($('select[name=type]', form), SITE.types.map((t) => [t, TYPES[t]]), 'Any type');
		fillSelect($('select[name=location]', form), SITE.locations, 'Anywhere');
		fillSelect($('select[name=budget]', form), SITE.budgets, 'Any budget');
		fillSelect($('select[name=bhk]', form), SITE.configurations, 'Any');
		fillSelect($('select[name=intent]', form), SITE.intents.map((i) => [i, INTENTS[i]]), 'Buy or rent');
		if (!SITE.hasConfig) { $('[data-field="bhk"]', form).hidden = true; form.classList.add('filters-bar--four'); }

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
				const link = $('.card__title a', node); link.textContent = p.title; link.href = `property.html?id=${p.id}`;
				$('.card__media', node).href = `property.html?id=${p.id}`;
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
			demoQuery().forEach((d) => { const [k, v] = d.split('='); q.set(k, v); });
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
	 * About: a pinned stage driven by one scroll progress value (0..1 over
	 * the section's extra height). Words light up in reading order, each
	 * side photo pops in once its threshold is crossed and pops out at the
	 * next, and both the side photos and the inline pills drift toward the
	 * cursor (pills while hovered, side photos when the cursor comes near).
	 * → Interactivity API store with a scroll-derived "progress" state.
	 * ------------------------------------------------------------------ */
	const about = $('[data-about]');
	if (about) {
		const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
		const stage = $('.about__stage', about);
		const statement = $('[data-reveal-words]', about);
		const paras = $$('p', statement);
		(paras.length ? paras : [statement]).forEach((para) => [...para.childNodes].forEach((node) => {
			if (node.nodeType !== Node.TEXT_NODE) { node.classList.add('about__w'); return; }
			const frag = document.createDocumentFragment();
			node.textContent.split(/(\s+)/).forEach((part) => {
				if (!part) return;
				if (/^\s+$/.test(part)) { frag.append(' '); return; }
				const w = document.createElement('span');
				w.className = 'about__w';
				w.textContent = part;
				frag.append(w);
			});
			node.replaceWith(frag);
		}));
		const words = $$('.about__w', statement);
		const floats = $$('[data-float]', about).map((el) => ({
			el, from: +el.dataset.in, out: el.dataset.out ? +el.dataset.out : null,
			x: 0, y: 0, tx: 0, ty: 0, // magnetic drift: current and target offsets
		}));
		const clamp01 = (v) => Math.min(1, Math.max(0, v));
		let lit = -1;
		let last = -1;
		const update = () => {
			const travel = about.offsetHeight - stage.offsetHeight;
			const p = reduced ? 1 : clamp01(-about.getBoundingClientRect().top / travel);
			if (p === last) return;
			last = p;
			// words finish at 90% so the last line is lit a beat before the stage releases
			const n = Math.round(clamp01(p / 0.9) * words.length);
			if (n !== lit) {
				lit = n;
				words.forEach((w, i) => w.classList.toggle('is-on', i < n));
			}
			floats.forEach(({ el, from, out }) => el.classList.toggle('is-in', p >= from && (out === null || p < out)));
		};
		let ticking = false;
		const onScroll = () => {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(() => { ticking = false; update(); });
		};
		update();
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);

		if (matchMedia('(hover: hover) and (pointer: fine)').matches) {
			// side photos: pulled toward the cursor within a radius, eased each frame so the motion feels fluid
			const RADIUS = 320;
			const PULL = 26;
			let raf = 0;
			const settle = () => {
				let moving = false;
				floats.forEach((f) => {
					f.x += (f.tx - f.x) * 0.1;
					f.y += (f.ty - f.y) * 0.1;
					if (Math.abs(f.tx - f.x) > 0.05 || Math.abs(f.ty - f.y) > 0.05) moving = true;
					f.el.style.setProperty('--mx', `${f.x.toFixed(2)}px`);
					f.el.style.setProperty('--my', `${f.y.toFixed(2)}px`);
				});
				raf = moving ? requestAnimationFrame(settle) : 0;
			};
			const aim = (cx, cy) => {
				floats.forEach((f) => {
					if (!f.el.classList.contains('is-in')) { f.tx = f.ty = 0; return; }
					const r = f.el.getBoundingClientRect();
					const dx = cx - (r.left + r.width / 2 - f.x);
					const dy = cy - (r.top + r.height / 2 - f.y);
					const d = Math.hypot(dx, dy);
					const k = d < RADIUS ? (1 - d / RADIUS) * PULL / Math.max(d, 1) : 0;
					f.tx = dx * k;
					f.ty = dy * k;
				});
				if (!raf) raf = requestAnimationFrame(settle);
			};
			let px = 0, py = 0, aimRaf = 0;
			stage.addEventListener('pointermove', (e) => { px = e.clientX; py = e.clientY; if (!aimRaf) aimRaf = requestAnimationFrame(() => { aimRaf = 0; aim(px, py); }); });
			stage.addEventListener('pointerleave', () => aim(-9999, -9999));

			const clamp = (v) => Math.max(-12, Math.min(12, v));
			$$('.about__pill', statement).forEach((pill) => {
				pill.addEventListener('pointermove', (e) => {
					const r = pill.getBoundingClientRect();
					pill.classList.add('is-magnet');
					pill.style.setProperty('--mx', `${clamp((e.clientX - r.left - r.width / 2) * 0.25)}px`);
					pill.style.setProperty('--my', `${clamp((e.clientY - r.top - r.height / 2) * 0.25)}px`);
				});
				pill.addEventListener('pointerleave', () => {
					pill.classList.remove('is-magnet');
					pill.style.setProperty('--mx', '0px');
					pill.style.setProperty('--my', '0px');
				});
			});
		}
	}

	/* ------------------------------------------------------------------
	 * Property page. One listing (?id=) plus its detail record fills a
	 * brochure-style page: opening photo, ledgers, three chapters, a
	 * filmstrip, the plan as a drawing, an editable EMI sentence, a radial
	 * distance plot, papers and a visit form.
	 * → Interactivity API stores in the theme, data from the plugin.
	 * ------------------------------------------------------------------ */
	const propertyRoot = $('[data-property]');
	if (propertyRoot) {
		const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
		const id = Number(new URLSearchParams(location.search).get('id')) || 1;
		const p = LISTINGS.find((l) => l.id === id) || LISTINGS[0];
		const rent = p.intent === 'rent';
		const isPlot = p.type === 'plot';
		const unit = isPlot ? 'sq yd' : 'sq ft';
		const fmt = (n) => Math.round(n).toLocaleString('en-IN');
		const setAll = (sel, text) => $$(sel).forEach((el) => { el.textContent = text; });
		const pool = ['assets/house-large.jpg', 'assets/p1.jpg', 'assets/p2.jpg', 'assets/p3.jpg', 'assets/p4.jpg', 'assets/p5.jpg', 'assets/p6.jpg', 'assets/p7.jpg', 'assets/p8.jpg', 'assets/p9.jpg', 'assets/house-small.jpg'];
		const cityCode = { hyderabad: 'HYD', bangalore: 'BLR' };
		const landmarksByCity = {
			hyderabad: [
				{ name: 'Raidurg Metro', type: 'Metro', km: 2.1, t: '6 min' },
				{ name: 'Financial District', type: 'Work', km: 4.5, t: '10 min' },
				{ name: 'Oakridge International School', type: 'School', km: 3.2, t: '8 min' },
				{ name: 'Continental Hospitals', type: 'Hospital', km: 3.8, t: '9 min' },
				{ name: 'Inorbit Mall', type: 'Shopping', km: 5.6, t: '14 min' },
				{ name: 'RGIA Airport', type: 'Airport', km: 31, t: '40 min' },
			],
			bangalore: [
				{ name: 'Nearest Metro station', type: 'Metro', km: 1.8, t: '5 min' },
				{ name: 'Tech park cluster', type: 'Work', km: 3.9, t: '12 min' },
				{ name: 'International school', type: 'School', km: 2.7, t: '8 min' },
				{ name: 'Multi-speciality hospital', type: 'Hospital', km: 3.1, t: '9 min' },
				{ name: 'Neighbourhood mall', type: 'Shopping', km: 4.4, t: '13 min' },
				{ name: 'Kempegowda Airport', type: 'Airport', km: 38, t: '55 min' },
			],
		};
		const DETAILS = {
			1: {
				ref: 'LAV-HYD-0231', updated: '3 days ago',
				photos: ['assets/house-large.jpg', 'assets/p6.jpg', 'assets/p2.jpg', 'assets/p8.jpg', 'assets/house-small.jpg', 'assets/p4.jpg', 'assets/p9.jpg', 'assets/p5.jpg'],
				chapters: [
					{ title: 'The lawn', photo: 0, body: 'Eleven hundred square feet of lawn runs from the porch to the community green, with the lake behind it. The house faces east, so mornings arrive in the living room and evenings settle on the terrace above.', cap: 'Fig. 1 · East elevation and private lawn, 1,100 sq ft' },
					{ title: 'The light', photo: 2, body: 'A double-height living room, floor-to-ceiling glazing on the east wall, and a courtyard that pulls morning sun through to the dining table. Italian marble below, engineered oak above, teak at every door and window.', cap: 'Fig. 2 · Living room, looking toward the courtyard' },
					{ title: 'Upstairs', photo: 3, body: 'Three bedrooms and a family lounge. The master suite takes the lake view with a walk-in wardrobe and a balcony, and the 420 sq ft terrace is laid out for evenings with people over.', cap: 'Fig. 3 · Master bedroom and terrace, first floor' },
				],
				description: [
					'Lakeview Villa sits at the quiet end of a 42-home gated community in Gachibowli. Downstairs holds the living and dining rooms, a modular kitchen with a separate utility, a guest bedroom and a two-car porch. Upstairs are three bedrooms, a family lounge and the terrace.',
					'The home is brand new and never occupied, with the occupancy certificate in hand and immediate possession.',
				],
				carpet: 3650, builtup: 4015, superBuiltup: 4560,
				specs: { Facing: 'East', Floors: 'G+1', 'Built in': '2025, never occupied', Parking: '2 covered + 1 open', Furnishing: 'Semi-furnished, modular kitchen', Water: 'Municipal + borewell', 'Power backup': '100% DG backup', Ownership: 'Freehold, single owner' },
				amenities: ['clubhouse', 'swimming pool', 'gym', "children's play area", '24x7 security', 'landscaped gardens', 'jogging track', 'indoor games', 'rainwater harvesting', 'solar water heating', 'visitor parking', 'EV charging'],
				rera: 'P02400001234', reraState: 'Telangana RERA', reraUrl: 'https://rera.telangana.gov.in',
				approvals: ['Occupancy certificate received', 'HMDA approved layout', 'Clear title, EC verified for 30 years', 'Vastu compliant'],
				banks: ['SBI', 'HDFC', 'ICICI', 'Axis'],
				advisor: { name: 'Priya Menon', meta: 'Senior advisor, Hyderabad West · RERA agent A02400001234', photo: 'assets/t2.jpg' },
				lat: 17.4401, lng: 78.3489,
			},
		};
		const generic = () => {
			const seed = p.id;
			const photos = [p.img, ...pool.filter((x) => x !== p.img).slice(seed % 5, seed % 5 + 5)];
			const typeWord = isPlot ? 'plot' : p.type === 'villa' ? 'villa' : 'apartment';
			const city = cityLabel[p.city];
			return {
				ref: `LAV-${cityCode[p.city]}-0${String(200 + seed * 7).padStart(3, '0')}`, updated: `${1 + (seed % 6)} days ago`,
				photos,
				chapters: isPlot
					? [
						{ title: 'The land', photo: 0, body: `A ${fmt(p.sqft)} sq yd plot in an approved, gated layout in ${p.area}. Roads, drainage, water and power lines are already in, and the layout holds clear title with encumbrance verified for 30 years.`, cap: `Fig. 1 · Plot frontage, ${p.area}` },
						{ title: 'The layout', photo: 1, body: 'Forty-foot internal roads, a boundary wall, street lighting and a small park at the centre. The layout allows ground plus two floors when you are ready to build.', cap: 'Fig. 2 · Internal road and park' },
						{ title: 'The neighbourhood', photo: 2, body: `${p.area} is seeing steady appreciation on the back of new road and metro links, with schools and hospitals already within a short drive.`, cap: `Fig. 3 · ${p.area}, looking toward the main road` },
					]
					: [
						{ title: 'The home', photo: 0, body: `${p.title} is a ${p.bhk} BHK ${typeWord} in ${p.area}, ${city}, with ${fmt(p.sqft)} sq ft of carpet area across well-proportioned rooms and good cross ventilation.`, cap: `Fig. 1 · ${p.title}, ${p.area}` },
						{ title: 'Inside', photo: 1, body: `${p.baths} bathrooms, a modular kitchen with a separate utility and a ${p.type === 'villa' ? 'private garden' : 'generous balcony'}. Finishes are vitrified tile, laminated wardrobes and branded fittings throughout.`, cap: 'Fig. 2 · Living and dining' },
						{ title: 'Around it', photo: 2, body: `The community has a clubhouse, pool and gym, and ${p.area} puts schools, hospitals and daily shopping within a short drive.`, cap: `Fig. 3 · Community and ${p.area}` },
					],
				description: [`Documents are verified by Lavenor: title, approvals and tax receipts are available on request, and possession is as listed: ${p.status.toLowerCase()}.`],
				carpet: p.sqft, builtup: Math.round(p.sqft * 1.1 / 5) * 5, superBuiltup: Math.round(p.sqft * 1.25 / 5) * 5,
				specs: isPlot
					? { Facing: ['East', 'North', 'West'][seed % 3], 'Plot size': `${fmt(p.sqft)} sq yd`, Approval: 'HMDA / DTCP', Roads: '40 ft internal', 'Boundary wall': 'Yes', Ownership: 'Freehold' }
					: { Facing: ['East', 'North', 'West', 'South-east'][seed % 4], Floors: p.type === 'villa' ? 'G+1' : `${3 + (seed % 12)} of ${12 + (seed % 10)}`, 'Built in': `${2019 + (seed % 7)}`, Parking: p.bhk >= 3 ? '2 covered' : '1 covered', Furnishing: ['Unfurnished', 'Semi-furnished', 'Fully furnished'][seed % 3], Water: 'Municipal + borewell', 'Power backup': 'Full backup', Ownership: 'Freehold' },
				amenities: isPlot
					? ['24x7 security', 'landscaped gardens', 'jogging track', 'rainwater harvesting', 'street lighting', 'underground drainage', "children's play area", 'clubhouse']
					: ['clubhouse', 'swimming pool', 'gym', "children's play area", '24x7 security', 'landscaped gardens', 'jogging track', 'indoor games', 'rainwater harvesting', 'visitor parking', 'lift', 'power backup'].slice(0, 8 + (seed % 5)),
				rera: p.city === 'hyderabad' ? `P0240000${1000 + seed * 37}` : `PRM/KA/RERA/1251/446/PR/${100000 + seed * 911}`,
				reraState: p.city === 'hyderabad' ? 'Telangana RERA' : 'Karnataka RERA',
				reraUrl: p.city === 'hyderabad' ? 'https://rera.telangana.gov.in' : 'https://rera.karnataka.gov.in',
				approvals: isPlot ? ['Approved layout', 'Clear title, EC verified', 'Ready to register'] : ['Clear title, EC verified', 'Approved plan', p.status === 'Ready to move' ? 'Occupancy certificate received' : 'Construction on schedule'],
				banks: ['SBI', 'HDFC', 'ICICI', 'Axis', 'Kotak'].slice(0, 3 + (seed % 3)),
				advisor: p.city === 'hyderabad'
					? { name: 'Priya Menon', meta: 'Senior advisor, Hyderabad West · RERA agent A02400001234', photo: 'assets/t2.jpg' }
					: { name: 'Arjun Rao', meta: 'Senior advisor, Bangalore East · RERA agent A02400001234', photo: 'assets/t1.jpg' },
				lat: p.city === 'hyderabad' ? 17.44 : 12.97, lng: p.city === 'hyderabad' ? 78.35 : 77.64,
			};
		};
		const d = DETAILS[p.id] || generic();
		const city = cityLabel[p.city];
		const psf = Math.round(p.price / p.sqft);
		const first = d.advisor.name.split(' ')[0];
		const waText = encodeURIComponent(`Hi, I'm interested in ${p.title} (${d.ref}) in ${p.area}. Can we arrange a visit?`);

		/* --- text fields --- */
		document.title = `${p.title}, ${p.area} — Lavenor Realty`;
		setAll('[data-title]', p.title);
		setAll('[data-place]', `${p.area}, ${city}`);
		setAll('[data-area]', p.area);
		setAll('[data-price]', rupees(p.price, rent));
		setAll('[data-psf]', rent ? `${fmt(p.sqft)} ${unit} · deposit 2 months` : `₹${fmt(psf)} per ${unit}`);
		setAll('[data-psf-carpet]', rent ? 'per month, maintenance extra' : `₹${fmt(psf)} per ${isPlot ? 'sq yd' : 'carpet sq ft'}`);
		setAll('[data-status]', p.status);
		setAll('[data-ref]', d.ref);
		setAll('[data-updated]', d.updated);
		setAll('[data-advisor-first]', first);
		setAll('[data-call-label]', `Call ${first}`);
		setAll('[data-oneline]', isPlot
			? `${fmt(p.sqft)} sq yd plot · ${d.specs.Facing} facing · ${d.specs.Approval}`
			: `${p.bhk} BHK ${p.type} · ${fmt(p.sqft)} sq ft carpet · ${d.specs.Facing} facing`);
		const crumbCity = $('[data-crumb-city]'); crumbCity.textContent = city; crumbCity.href = `properties.html?location=${p.city}`;
		$('[data-crumb-area]').textContent = p.area;
		const similarLink = $('[data-similar-link]'); similarLink.firstChild.textContent = `See all in ${city} `; similarLink.href = `properties.html?location=${p.city}`;
		$$('[data-wa]').forEach((a) => { a.href = `https://wa.me/919876543210?text=${waText}`; });
		$('[data-ph-img]').src = d.photos[0];
		$('[data-photo-count]').textContent = d.photos.length;

		/* --- opening: the photo eases back into its frame as you scroll --- */
		const ph = $('[data-ph]');
		const phImg = $('[data-ph-img]');
		const zoom = () => {
			const k = Math.min(1, Math.max(0, scrollY / Math.max(1, ph.offsetHeight)));
			phImg.style.setProperty('--ph-zoom', reduced ? 1 : (1.08 - 0.08 * k).toFixed(4));
		};

		/* --- at a glance --- */
		const glance = isPlot
			? [['Plot area', `${fmt(p.sqft)}<small>sq yd</small>`], ['Approval', d.specs.Approval], ['Facing', d.specs.Facing], ['Roads', d.specs.Roads], ['Boundary wall', d.specs['Boundary wall']], ['Status', p.status]]
			: [['Configuration', `${p.bhk} BHK`], [isPlot ? 'Plot area' : 'Carpet area', `${fmt(p.sqft)}<small>sq ft</small>`], ['Bathrooms', String(p.baths)], ['Facing', d.specs.Facing], ['Floors', d.specs.Floors], ['Possession', p.status]];
		$('[data-glance]').innerHTML = glance.map(([k, v]) => `<div><dt>${k}</dt><dd>${v}</dd></div>`).join('');

		/* --- chapters --- */
		const chapterTpl = $('#chapter-template');
		const chapterList = $('[data-chapters-list]');
		d.chapters.forEach((c, i) => {
			const node = chapterTpl.content.cloneNode(true);
			const art = $('.chapter', node);
			if (i % 2) art.classList.add('chapter--flip');
			const img = $('.chapter__img', node); img.src = d.photos[c.photo] || d.photos[0]; img.alt = c.cap;
			if (i === 0) img.loading = 'eager';
			$('.chapter__open', node).dataset.openPhoto = c.photo;
			$('.chapter__num', node).textContent = `0${i + 1}`;
			$('.chapter__title', node).textContent = c.title;
			$('.chapter__body', node).textContent = c.body;
			$('.chapter__cap', node).textContent = c.cap;
			chapterList.append(node);
		});
		const chapterEls = $$('[data-chapter]');
		if (reduced || !('IntersectionObserver' in window)) chapterEls.forEach((c) => c.classList.add('is-in'));
		else {
			const io = new IntersectionObserver((entries) => entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { rootMargin: '0px 0px -18% 0px' });
			chapterEls.forEach((c) => io.observe(c));
		}

		/* --- filmstrip: drag to scroll, counter follows, click opens the lightbox --- */
		const track = $('[data-strip-track]');
		$('[data-strip-total]').textContent = String(d.photos.length).padStart(2, '0');
		track.innerHTML = d.photos.map((src, i) => `<li><button type="button" data-open-photo="${i}" aria-label="Open photo ${i + 1}"><img src="${src}" alt="" loading="lazy"></button></li>`).join('');
		const stripIndex = $('[data-strip-index]');
		track.addEventListener('scroll', () => {
			const li = track.firstElementChild;
			const step = li.offsetWidth + 12;
			stripIndex.textContent = String(Math.min(d.photos.length, Math.round(track.scrollLeft / step) + 1)).padStart(2, '0');
		}, { passive: true });
		let drag = null;
		track.addEventListener('pointerdown', (e) => { if (e.pointerType === 'mouse') { drag = { x: e.clientX, left: track.scrollLeft, moved: false }; track.setPointerCapture(e.pointerId); } });
		track.addEventListener('pointermove', (e) => {
			if (!drag) return;
			const dx = e.clientX - drag.x;
			if (Math.abs(dx) > 6) { drag.moved = true; track.classList.add('is-dragging'); }
			if (drag.moved) track.scrollLeft = drag.left - dx;
		});
		const endDrag = () => { if (drag?.moved) setTimeout(() => track.classList.remove('is-dragging')); drag = null; };
		track.addEventListener('pointerup', endDrag);
		track.addEventListener('pointercancel', endDrag);
		track.addEventListener('click', (e) => { if (track.classList.contains('is-dragging')) e.stopPropagation(); }, true);

		/* --- lightbox --- */
		const lb = $('[data-lightbox]');
		if (lb && typeof lb.showModal === 'function') {
			const img = $('[data-lb-img]', lb);
			const strip = $('[data-lb-strip]', lb);
			const idxEl = $('[data-lb-index]', lb);
			$('[data-lb-total]', lb).textContent = d.photos.length;
			let i = 0;
			const show = (k) => {
				i = (k + d.photos.length) % d.photos.length;
				img.src = d.photos[i];
				img.alt = `${p.title}, photo ${i + 1} of ${d.photos.length}`;
				img.style.animation = 'none'; void img.offsetWidth; img.style.animation = '';
				idxEl.textContent = i + 1;
				thumbs.forEach((t, k2) => t.classList.toggle('is-active', k2 === i));
				thumbs[i].scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
			};
			d.photos.forEach((src, k) => {
				const li = document.createElement('li');
				const b = document.createElement('button');
				b.type = 'button'; b.setAttribute('aria-label', `Photo ${k + 1}`);
				b.innerHTML = `<img src="${src}" alt="" loading="lazy">`;
				b.addEventListener('click', () => show(k));
				li.append(b); strip.append(li);
			});
			const thumbs = $$('button', strip);
			const open = (k) => { show(k); lb.showModal(); document.body.classList.add('is-locked'); };
			document.addEventListener('click', (e) => { const t = e.target.closest('[data-open-photo]'); if (t) open(+t.dataset.openPhoto); });
			$('[data-lb-close]', lb).addEventListener('click', () => lb.close());
			$('[data-lb-prev]', lb).addEventListener('click', () => show(i - 1));
			$('[data-lb-next]', lb).addEventListener('click', () => show(i + 1));
			lb.addEventListener('close', () => document.body.classList.remove('is-locked'));
			lb.addEventListener('click', (e) => { if (e.target === lb) lb.close(); });
			lb.addEventListener('keydown', (e) => { if (e.key === 'ArrowRight') show(i + 1); else if (e.key === 'ArrowLeft') show(i - 1); });
			let x0 = null;
			const stage = $('[data-lb-stage]', lb);
			stage.addEventListener('pointerdown', (e) => { x0 = e.clientX; });
			stage.addEventListener('pointerup', (e) => { if (x0 !== null && Math.abs(e.clientX - x0) > 40) show(e.clientX < x0 ? i + 1 : i - 1); x0 = null; });
		}

		/* --- specification --- */
		$('[data-specs]').innerHTML = Object.entries(d.specs).map(([k, v]) => `<div><dt>${k}</dt><dd>${v}</dd></div>`).join('');
		const am = d.amenities;
		$('[data-amenities-text]').textContent = `${am.slice(0, -1).join(', ')} and ${am[am.length - 1]}.`.replace(/^./, (c) => c.toUpperCase());
		$('[data-description]').innerHTML = d.description.map((t) => `<p>${t}</p>`).join('');

		/* --- the plan --- */
		const planTabs = $$('[data-plan]');
		const sheets = $$('[data-plan-sheet]');
		const roomLists = $$('[data-plan-rooms]');
		const planFig = $('[data-plan-fig]');
		planTabs.forEach((tab) => tab.addEventListener('click', () => {
			const key = tab.dataset.plan;
			planTabs.forEach((t) => { const on = t === tab; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', String(on)); t.tabIndex = on ? 0 : -1; });
			sheets.forEach((sh) => { sh.hidden = sh.dataset.planSheet !== key; });
			roomLists.forEach((rl) => { rl.hidden = rl.dataset.planRooms !== key; });
			planFig.textContent = key === 'ground' ? 'Fig. 1 — Ground floor' : 'Fig. 2 — First floor';
		}));
		$('[data-plan-tabs]').addEventListener('keydown', (e) => {
			const i = planTabs.indexOf(document.activeElement);
			if (i < 0 || (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft')) return;
			e.preventDefault();
			const n = planTabs[(i + (e.key === 'ArrowRight' ? 1 : -1) + planTabs.length) % planTabs.length];
			n.focus(); n.click();
		});
		const hot = (room, on) => $$(`[data-room="${room}"]`).forEach((el) => el.classList.toggle('is-hot', on));
		$$('[data-room]').forEach((el) => {
			el.addEventListener('pointerenter', () => hot(el.dataset.room, true));
			el.addEventListener('pointerleave', () => hot(el.dataset.room, false));
		});
		if (isPlot) { $('#plan').hidden = true; $('.chapters a[href="#plan"]').parentElement.hidden = true; }
		$('[data-brochure]').addEventListener('click', (e) => { e.preventDefault(); showToast(`Brochure for <b>${p.title}</b> would download here. <code>GET /wp-json/bytenot/v1/brochure/${p.id}</code>`); });

		/* --- the numbers: ledger + editable EMI sentence --- */
		const duty = p.price * 0.06;
		const dealRows = rent
			? [['Monthly rent', rupees(p.price)], ['Deposit', rupees(p.price * 2)], ['Maintenance', `₹${fmt(p.sqft * 3.5)}<small>/ month</small>`], ['Carpet area', `${fmt(p.sqft)}<small>sq ft</small>`], ['Per sq ft', `₹${(p.price / p.sqft).toFixed(1)}`], ['Lock-in', '11 months']]
			: isPlot
				? [['Plot area', `${fmt(p.sqft)}<small>sq yd</small>`], ['Per sq yd', `₹${fmt(psf)}`], ['Stamp duty & registration', rupees(Math.round(duty))], ['Development charges', 'Included'], ['Maintenance deposit', rupees(50000)], ['All-in', rupees(Math.round(p.price + duty + 50000)), 'accent']]
				: [['Carpet area', `${fmt(d.carpet)}<small>sq ft</small>`], ['Built-up', `${fmt(d.builtup)}<small>sq ft</small>`], ['Super built-up', `${fmt(d.superBuiltup)}<small>sq ft</small>`], ['Loading', `${Math.round((d.superBuiltup / d.carpet - 1) * 100)}<small>%</small>`], ['Stamp duty & registration', rupees(Math.round(duty))], ['All-in, before loan', rupees(Math.round(p.price + duty)), 'accent']];
		$('[data-deal]').innerHTML = dealRows.map(([k, v, cls]) => `<div${cls ? ` class="is-${cls}"` : ''}><dt>${k}</dt><dd>${v}</dd></div>`).join('');
		const emiP = $('[data-emi-sentence]');
		const pop = $('[data-num-pop]');
		if (rent) { emiP.hidden = true; }
		else {
			const state = { years: 20, rate: 8.5, ltv: 80 };
			const fields = {
				years: { min: 5, max: 30, step: 1, label: 'Tenure', text: (v) => `${v} years` },
				rate: { min: 7, max: 12, step: 0.1, label: 'Interest rate', text: (v) => `${(+v).toFixed(1)}%` },
				ltv: { min: 10, max: 90, step: 5, label: 'Financed', text: (v) => `${v}%` },
			};
			const valueEl = $('[data-emi-value]');
			const tween = (el, to, format) => {
				const from = Number(el.dataset.v || to);
				el.dataset.v = to;
				if (reduced) { el.textContent = format(to); return; }
				const t0 = performance.now();
				const step = (t) => {
					const k = Math.min(1, (t - t0) / 360);
					const e = 1 - Math.pow(1 - k, 3);
					el.textContent = format(from + (to - from) * e);
					if (k < 1) requestAnimationFrame(step);
				};
				requestAnimationFrame(step);
			};
			const calc = () => {
				const loan = p.price * state.ltv / 100;
				const r = state.rate / 1200;
				const n = state.years * 12;
				const emi = loan * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
				tween(valueEl, emi, (v) => v >= L ? `₹${(v / L).toFixed(2)} L` : `₹${fmt(v)}`);
				Object.keys(fields).forEach((k) => { $(`[data-emi-field="${k}"]`).textContent = fields[k].text(state[k]); });
			};
			const range = $('[data-pop-range]', pop);
			const popOut = $('[data-pop-out]', pop);
			const popLabel = $('[data-pop-label]', pop);
			let openBtn = null;
			const closePop = () => { pop.hidden = true; openBtn?.classList.remove('is-open'); openBtn = null; };
			const openPop = (btn) => {
				const k = btn.dataset.emiField;
				const f = fields[k];
				if (openBtn === btn) { closePop(); return; }
				closePop();
				openBtn = btn; btn.classList.add('is-open');
				range.min = f.min; range.max = f.max; range.step = f.step; range.value = state[k];
				popLabel.textContent = f.label; popOut.textContent = f.text(state[k]);
				range.oninput = () => { state[k] = +range.value; popOut.textContent = f.text(state[k]); calc(); };
				pop.hidden = false;
				const card = pop.offsetParent;
				const left = Math.min(btn.offsetLeft + emiP.offsetLeft, card.clientWidth - pop.offsetWidth - 20);
				pop.style.left = `${Math.max(20, left)}px`;
				pop.style.top = `${btn.offsetTop + emiP.offsetTop + btn.offsetHeight + 10}px`;
				range.focus();
			};
			$$('[data-emi-field]').forEach((b) => b.addEventListener('click', () => openPop(b)));
			document.addEventListener('pointerdown', (e) => { if (!pop.hidden && !pop.contains(e.target) && !e.target.closest('[data-emi-field]')) closePop(); });
			document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !pop.hidden) { const b = openBtn; closePop(); b?.focus(); } });
			calc();
		}

		/* --- the place: radial distance plot + ledger --- */
		const lms = landmarksByCity[p.city];
		const R = 236, C = 280;
		const rings = [2, 5, 10, 20, 40];
		const scale = (km) => R * Math.log(1 + km) / Math.log(1 + 40);
		const svgNS = 'http://www.w3.org/2000/svg';
		const radar = $('[data-radar-svg]');
		const el = (tag, attrs, text) => { const n = document.createElementNS(svgNS, tag); Object.entries(attrs).forEach(([k, v]) => n.setAttribute(k, v)); if (text) n.textContent = text; return n; };
		rings.forEach((km) => {
			radar.append(el('circle', { class: 'radar__ring', cx: C, cy: C, r: scale(km) }));
			const la = -102 * Math.PI / 180; // ring labels sit up-left, between the first and last spokes, where no landmark label reaches
			radar.append(el('text', { class: 'radar__ring-label', x: C + Math.cos(la) * scale(km) - 4, y: C + Math.sin(la) * scale(km) - 4, 'text-anchor': 'end' }, `${km} km`));
		});
		const spots = lms.map((l, i) => {
			const a = (-90 + 18 + i * (360 / lms.length)) * Math.PI / 180;
			const r = scale(l.km);
			const x = C + Math.cos(a) * r, y = C + Math.sin(a) * r;
			radar.append(el('line', { class: 'radar__spoke', x1: C, y1: C, x2: x, y2: y }));
			const g = el('g', { class: 'radar__spot', 'data-spot': i });
			g.append(el('circle', { class: 'radar__dot', cx: x, cy: y, r: 6 }));
			const right = Math.cos(a) >= 0;
			const tx = x + (right ? 12 : -12), anchor = right ? 'start' : 'end';
			g.append(el('text', { class: 'radar__label', x: tx, y: y - 2, 'text-anchor': anchor }, l.name));
			g.append(el('text', { class: 'radar__sub', x: tx, y: y + 12, 'text-anchor': anchor }, `${l.km} km · ${l.t}`));
			radar.append(g);
			return g;
		});
		radar.append(el('circle', { class: 'radar__home-ring', cx: C, cy: C, r: 14 }));
		radar.append(el('circle', { class: 'radar__home', cx: C, cy: C, r: 7 }));
		radar.append(el('text', { class: 'radar__label', x: C, y: C + 34, 'text-anchor': 'middle' }, p.title));
		const lmList = $('[data-landmarks]');
		lmList.innerHTML = lms.map((l, i) => `<div data-spot="${i}"><dt>${l.name}<small>${l.type}</small></dt><dd>${l.km} km<small>${l.t} drive</small></dd></div>`).join('');
		const hotSpot = (i, on) => { $$(`[data-spot="${i}"]`).forEach((n) => n.classList.toggle('is-hot', on)); spots[i].querySelector('circle').setAttribute('r', on ? 9 : 6); };
		$$('[data-spot]').forEach((n) => {
			n.addEventListener('pointerenter', () => hotSpot(+n.dataset.spot, true));
			n.addEventListener('pointerleave', () => hotSpot(+n.dataset.spot, false));
		});
		const mapEl = $('[data-map]');
		$('[data-map-load]').addEventListener('click', (e) => {
			const dl = 0.012;
			const bbox = `${d.lng - dl * 1.6},${d.lat - dl},${d.lng + dl * 1.6},${d.lat + dl}`;
			const frame = document.createElement('div');
			frame.className = 'locmap__frame';
			frame.innerHTML = `<iframe title="Map of ${p.area}" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${d.lat},${d.lng}"></iframe>`;
			mapEl.append(frame);
			e.currentTarget.hidden = true;
			$('.locmap__hint', mapEl).hidden = true;
		});

		/* --- papers & people --- */
		const papers = [
			['RERA registration', `<span>${d.rera}</span><a href="${d.reraUrl}" target="_blank" rel="noopener">Verify</a>`],
			['Authority', d.reraState],
			...d.approvals.map((a, i) => [i === 0 ? 'Approvals' : '', a]),
			['Loans pre-approved with', d.banks.join(', ')],
			['Documents on request', 'Sale deed, EC, approved plan, OC, tax receipts'],
		];
		$('[data-papers]').innerHTML = papers.map(([k, v]) => `<div><dt>${k}</dt><dd>${v}</dd></div>`).join('');
		$('[data-advisor-name]').textContent = d.advisor.name;
		$('[data-advisor-meta]').textContent = d.advisor.meta;
		$('[data-advisor-photo]').src = d.advisor.photo;

		/* --- nearby: same city, same intent, closest price --- */
		const similar = LISTINGS.filter((l) => l.id !== p.id && l.city === p.city && l.intent === p.intent)
			.sort((a, b) => Math.abs(a.price - p.price) - Math.abs(b.price - p.price)).slice(0, 3);
		const tpl = $('#card-template');
		const simEl = $('[data-similar]');
		similar.forEach((q) => {
			const node = tpl.content.cloneNode(true);
			const img = $('.card__img', node); img.src = q.img; img.alt = `${q.title}, ${q.area}`;
			$('.card__status', node).textContent = q.status;
			const link = $('.card__title a', node); link.textContent = q.title; link.href = `property.html?id=${q.id}`;
			$('.card__media', node).href = `property.html?id=${q.id}`;
			$('.card__foot .btn', node).href = `property.html?id=${q.id}`;
			$('.card__place span', node).textContent = `${q.area}, ${cityLabel[q.city]}`;
			$('[data-fact=area]', node).textContent = `${fmt(q.sqft)} ${q.type === 'plot' ? 'sq. yd' : 'sq. ft'}`;
			$('[data-fact=beds]', node).textContent = q.bhk ? `${q.bhk} BHK` : 'Plot';
			$('[data-fact=baths]', node).textContent = q.baths ? `${q.baths} Baths` : 'Gated';
			$('.card__price', node).textContent = rupees(q.price, q.intent === 'rent');
			simEl.append(node);
		});
		if (!similar.length) $('#nearby').hidden = true;
		const canHover = matchMedia('(hover: hover)').matches;
		$$('.card', simEl).forEach((card) => {
			if (canHover) {
				card.addEventListener('pointerenter', () => card.classList.add('is-open'));
				card.addEventListener('pointerleave', () => card.classList.remove('is-open'));
			}
			card.addEventListener('focusin', () => card.classList.add('is-open'));
			card.addEventListener('focusout', (e) => { if (!card.contains(e.relatedTarget)) card.classList.remove('is-open'); });
		});

		/* --- chapter nav + compact bar + hero zoom, one scroll handler --- */
		const chapterLinks = $$('[data-chapters] a');
		const sections = chapterLinks.map((a) => $(a.getAttribute('href'))).filter((s) => s && !s.hidden);
		const bar = $('[data-prop-bar]');
		const onScroll = () => {
			zoom();
			const line = innerHeight * 0.34;
			let cur = sections[0];
			sections.forEach((s) => { if (s.getBoundingClientRect().top <= line) cur = s; });
			chapterLinks.forEach((a) => a.classList.toggle('is-active', a.getAttribute('href') === `#${cur.id}`));
			const on = ph.getBoundingClientRect().bottom < 0 || matchMedia('(max-width: 720px)').matches;
			bar.classList.toggle('is-visible', on);
			bar.setAttribute('aria-hidden', String(!on));
		};
		let ticking = false;
		window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(() => { ticking = false; onScroll(); }); } }, { passive: true });
		window.addEventListener('resize', onScroll);
		onScroll();
		$$('[data-go-visit]').forEach((a) => a.addEventListener('click', (e) => {
			e.preventDefault();
			$('#visit').scrollIntoView({ behavior: reduced ? 'instant' : 'smooth', block: 'center' });
			setTimeout(() => $('[data-enquire] [name="name"]').focus({ preventScroll: true }), reduced ? 0 : 650);
		}));

		/* --- save / share --- */
		const save = $('[data-save]');
		const saved = new Set(JSON.parse(localStorage.getItem('lavenor:saved') || '[]'));
		const paintSave = () => { const on = saved.has(p.id); save.setAttribute('aria-pressed', String(on)); $('[data-save-label]').textContent = on ? 'Saved' : 'Save'; };
		save.addEventListener('click', () => {
			saved.has(p.id) ? saved.delete(p.id) : saved.add(p.id);
			localStorage.setItem('lavenor:saved', JSON.stringify([...saved]));
			paintSave();
			showToast(saved.has(p.id) ? `<b>${p.title}</b> saved to your shortlist.` : `<b>${p.title}</b> removed from your shortlist.`);
		});
		paintSave();
		$('[data-share]').addEventListener('click', async () => {
			const data = { title: document.title, text: `${p.title}, ${p.area}: ${rupees(p.price, rent)}`, url: location.href };
			try {
				if (navigator.share) await navigator.share(data);
				else { await navigator.clipboard.writeText(location.href); showToast('Link copied to your clipboard.'); }
			} catch { /* dismissed */ }
		});

		/* --- visit form --- */
		const enq = $('[data-enquire]');
		enq.addEventListener('submit', (e) => {
			e.preventDefault();
			if (!enq.reportValidity()) return;
			const f = new FormData(enq);
			const payload = { property: p.id, ref: d.ref, name: f.get('name'), phone: `+91${f.get('phone')}`, date: f.get('date') || null, source: location.pathname + location.search };
			showToast(`Thanks ${payload.name.split(' ')[0]}, ${first} will call you within the hour about <b>${p.title}</b>.<code>POST /wp-json/bytenot/v1/leads ${JSON.stringify(payload)}</code>`);
			enq.reset();
		});

		/* --- structured data (RealEstateListing) --- */
		const ld = $('[data-jsonld]');
		if (ld) ld.textContent = JSON.stringify({
			'@context': 'https://schema.org', '@type': 'RealEstateListing', name: p.title, url: location.href, datePosted: '2026-10-01',
			image: d.photos, description: d.chapters[0].body,
			about: { '@type': isPlot ? 'Place' : p.type === 'villa' ? 'House' : 'Apartment', numberOfBedrooms: p.bhk || undefined, numberOfBathroomsTotal: p.baths || undefined,
				floorSize: { '@type': 'QuantitativeValue', value: p.sqft, unitCode: isPlot ? 'YDK' : 'FTK' },
				address: { '@type': 'PostalAddress', addressLocality: p.area, addressRegion: city, addressCountry: 'IN' },
				geo: { '@type': 'GeoCoordinates', latitude: d.lat, longitude: d.lng },
				amenityFeature: d.amenities.map((a) => ({ '@type': 'LocationFeatureSpecification', name: a, value: true })) },
			offers: { '@type': 'Offer', price: p.price, priceCurrency: 'INR', availability: 'https://schema.org/InStock', businessFunction: rent ? 'http://purl.org/goodrelations/v1#LeaseOut' : 'http://purl.org/goodrelations/v1#Sell',
				...(rent ? { priceSpecification: { '@type': 'UnitPriceSpecification', price: p.price, priceCurrency: 'INR', unitText: 'MONTH' } } : {}) },
		});
	}

	/* ------------------------------------------------------------------
	 * FAQ accordion: one answer open at a time; the height animates with
	 * CSS grid rows, JS only flips the state.
	 * → core Details block or a tiny Interactivity API store in the theme.
	 * ------------------------------------------------------------------ */
	const faq = $('[data-faq]');
	if (faq) {
		const items = $$('.faq__item', faq);
		// the list reserves room for its tallest answer, so opening and closing never moves the section below
		const reserve = () => {
			const open = items.find((i) => i.classList.contains('is-open'));
			const openInner = open && $('.faq__a-inner', open);
			const openH = openInner ? openInner.offsetHeight : 0;
			const tallest = Math.max(...items.map((i) => $('.faq__a-inner', i).scrollHeight));
			faq.style.minHeight = `${faq.offsetHeight - openH + tallest}px`;
		};
		reserve();
		document.fonts?.ready.then(reserve);
		let reserveTimer; addEventListener('resize', () => { clearTimeout(reserveTimer); reserveTimer = setTimeout(() => { faq.style.minHeight = ''; reserve(); }, 150); });
		items.forEach((item) => {
			$('.faq__q', item).addEventListener('click', () => {
				const open = !item.classList.contains('is-open');
				items.forEach((o) => {
					const on = o === item && open;
					o.classList.toggle('is-open', on);
					$('.faq__q', o).setAttribute('aria-expanded', String(on));
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Inquiry form → lead payload (same endpoint as the visit form).
	 * ------------------------------------------------------------------ */
	const inquiry = $('[data-inquiry]');
	if (inquiry) {
		// Magnetic: when a scroll ends near the band while moving toward it, settle it flush
		// to the viewport. Moving away never pulls back, so it is easy to leave.
		const band = inquiry.closest('.inquiry');
		const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
		let lastTop = null;
		let settleTimer;
		let userScrolled = false; // never on the browser's own scroll (restoring a position, jumping to a hash)
		['wheel', 'touchstart', 'keydown', 'pointerdown'].forEach((ev) => addEventListener(ev, () => { userScrolled = true; }, { passive: true, once: true }));
		const coarse = matchMedia('(pointer: coarse)').matches; // a flick-scrolled phone must never be pulled about
		const settle = () => {
			// never while someone is typing in the form: the browser's own focus scroll is not ours to override
			if (coarse || !userScrolled || inquiry.contains(document.activeElement)) return;
			const top = band.getBoundingClientRect().top;
			const gap = Math.abs(top);
			// nearer than at the last stop and on the same side of it: a reader who scrolled past is never pulled back
			const approaching = lastTop === null ? top > 0 : gap < Math.abs(lastTop) && top * lastTop > 0;
			lastTop = top;
			if (gap > 2 && gap < innerHeight * 0.22 && approaching) {
				lastTop = 0;
				scrollTo({ top: scrollY + top, behavior: reducedMotion ? 'instant' : 'smooth' });
			}
		};
		if ('onscrollend' in window) window.addEventListener('scrollend', settle);
		else window.addEventListener('scroll', () => { clearTimeout(settleTimer); settleTimer = setTimeout(settle, 140); }, { passive: true });

		inquiry.addEventListener('submit', (e) => {
			e.preventDefault();
			if (!inquiry.reportValidity()) return;
			const f = new FormData(inquiry);
			const payload = { name: f.get('name'), email: f.get('email'), phone: `+91${f.get('phone')}`, message: f.get('message') || null, source: `${location.pathname}#inquiry` };
			showToast(`Thanks ${payload.name.split(' ')[0]}, an advisor will reach you within the hour.<code>POST /wp-json/bytenot/v1/leads ${JSON.stringify(payload)}</code>`);
			inquiry.reset();
		});
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
				// upwards when there is no room below, and whenever the field sits in the lower half of the viewport,
				// so the fields of one form all open the same way
				menu.classList.toggle('dd__menu--up', (below < menu.offsetHeight + 12 && r.top > below) || (r.bottom > innerHeight * 0.55 && r.top > menu.offsetHeight + 12));
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
