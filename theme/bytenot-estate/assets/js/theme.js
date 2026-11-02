/* Bytenot Estate: the prototype's behaviour, one self-contained block per feature.
 * Data comes from the server: window.bytenotConfig (vocabulary, endpoints, strings)
 * and JSON islands inside the sections (featured items, listings, the property).
 */
(() => {
	'use strict';

	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
	const CFG = window.bytenotConfig || {};
	const T = CFG.i18n || {};
	const fmt = (s, ...a) => String(s || '').replace(/%(\d+\$)?[sd]/g, () => a.shift());
	const json = (sel, root = document) => { const el = $(sel, root); if (!el) return null; try { return JSON.parse(el.textContent); } catch (e) { return null; } };
	const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const canHover = matchMedia('(hover: hover) and (pointer: fine)').matches;

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
	 * Menu follows the page: same-page section links (#about, #inquiry)
	 * light up as their section scrolls into view; the home link while above
	 * the first section.
	 * ------------------------------------------------------------------ */
	const spyLinks = $$('.nav__list a, .drawer__nav a');
	const sameHash = (a) => { const h = a.getAttribute('href') || ''; const u = new URL(a.href, location.href); return u.pathname === location.pathname && u.hash && u.hash !== '#' ? u.hash : (h === '#' || (u.pathname === location.pathname && !u.hash) ? '' : null); };
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
				if (active) l.a.setAttribute('aria-current', 'page'); else l.a.removeAttribute('aria-current');
			});
			placeNavHl();
		};
		let spyTick = false;
		addEventListener('scroll', () => { if (!spyTick) { spyTick = true; requestAnimationFrame(() => { spyTick = false; paint(); }); } }, { passive: true });
		addEventListener('resize', paint);
		paint();
	}

	/* ------------------------------------------------------------------
	 * Language switcher (menu button pattern); items are links to the
	 * translated pages, provided by the multilingual plugin.
	 * ------------------------------------------------------------------ */
	const lang = $('[data-lang]');
	if (lang) {
		const button = $('.lang__button', lang);
		const menu = $('.lang__menu', lang);
		const items = $$('[role="menuitemradio"]', menu);
		const open = () => { menu.hidden = false; button.setAttribute('aria-expanded', 'true'); items[0].focus(); };
		const close = (refocus = false) => { menu.hidden = true; button.setAttribute('aria-expanded', 'false'); if (refocus) button.focus(); };
		button.addEventListener('click', () => (menu.hidden ? open() : close()));
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
			reduced ? done() : setTimeout(done, 300);
		};
		opener.addEventListener('click', open);
		closers.forEach((c) => c.addEventListener('click', close));
		$$('.drawer__nav a, .drawer__cta', drawer).forEach((a) => a.addEventListener('click', close));
		document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !drawer.hidden) close(); });
	}

	/* ------------------------------------------------------------------
	 * Toast and the lead endpoint shared by both forms.
	 * ------------------------------------------------------------------ */
	const toast = $('[data-toast]');
	let toastTimer;
	const showToast = (html) => {
		if (!toast) return;
		toast.innerHTML = html;
		toast.hidden = false;
		clearTimeout(toastTimer);
		toastTimer = setTimeout(() => { toast.hidden = true; }, 6000);
	};
	const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
	const postLead = async (form, extra) => {
		const f = new FormData(form);
		const payload = {
			name: f.get('name'), email: f.get('email') || '', phone: `+91${String(f.get('phone') || '').replace(/\D/g, '')}`,
			message: f.get('message') || '', preferred_date: f.get('date') || '', consent: !!f.get('consent'),
			website: f.get('website') || '', started: Number(f.get('started')) || 0, source_url: location.href,
			attribution: document.referrer ? `referrer=${document.referrer}` : '', ...extra,
		};
		form.classList.add('is-busy');
		try {
			const res = await fetch(CFG.leads, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
			const data = await res.json().catch(() => ({}));
			if (!res.ok) throw new Error(data.message || T.error);
			return payload;
		} finally { form.classList.remove('is-busy'); }
	};

	/* ------------------------------------------------------------------
	 * Search: hero tags prefill the form; submit drops empty fields.
	 * ------------------------------------------------------------------ */
	const form = $('[data-search]');
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
			const q = params.toString();
			window.location.href = form.getAttribute('action') + (q ? (form.getAttribute('action').includes('?') ? '&' : '?') + q : '');
		});
	}

	/* ------------------------------------------------------------------
	 * Featured properties: one spotlight, a rail of the rest. The rows are
	 * server-rendered; the items JSON drives the stage.
	 * ------------------------------------------------------------------ */
	const spot = $('[data-featured]');
	const FEATURED = spot ? json('[data-featured-items]', spot) || [] : [];
	if (spot && FEATURED.length) {
		const layers = [$('[data-spot-img]', spot), $('[data-spot-img-next]', spot)];
		const el = (k) => $(`[data-spot-${k}]`, spot);
		const rail = $('[data-rail]', spot);
		let index = 0, top = 0;

		FEATURED.forEach((p) => { const i = new Image(); i.src = p.stage; });

		const rows = $$('.rail__row', rail);
		rows.forEach((row, i) => {
			// a row switches the stage when pressed (click, tap or Enter); hovering only highlights it
			row.addEventListener('click', () => { if (i !== index) show(i); });
		});

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
			widest(el('price'), FEATURED.map((p) => p.priceText));
			widest(el('psf'), FEATURED.map((p) => p.psf));
		};
		fixWidths();
		document.fonts?.ready.then(fixWidths);
		let widthTimer; addEventListener('resize', () => { clearTimeout(widthTimer); widthTimer = setTimeout(fixWidths, 150); });

		const hl = $('[data-rail-hl]', rail);
		let hlPlaced = false;
		const moveHl = () => {
			const li = rows[index].parentElement;
			if (!hlPlaced) hl.style.transition = 'none';
			rail.style.setProperty('--hl-top', `${li.offsetTop}px`); rail.style.setProperty('--hl-h', `${li.offsetHeight}px`);
			if (!hlPlaced) { void hl.offsetHeight; hl.style.transition = ''; hlPlaced = true; rail.classList.add('is-placed'); }
		};
		addEventListener('resize', moveHl);

		const swapImage = (p) => {
			const incoming = layers[1 - top], outgoing = layers[top];
			let done = false;
			const reveal = () => {
				if (done) return; done = true;
				incoming.classList.add('is-on'); outgoing.classList.remove('is-on'); top = 1 - top;
			};
			incoming.alt = p.alt;
			incoming.onload = reveal;
			incoming.src = p.stage;
			if (incoming.complete && incoming.naturalWidth) reveal();
		};
		const fill = (p) => {
			el('why').textContent = p.why;
			el('place').textContent = p.place;
			el('title').textContent = p.title;
			el('line').textContent = p.line;
			$$('[data-spot-link]', spot).forEach((a) => { a.href = p.url; });
			el('visit').href = p.visit;
			el('price').textContent = p.priceText;
			el('psf').textContent = p.psf;
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

		$('[data-prev]', spot)?.addEventListener('click', () => show(index - 1));
		$('[data-next]', spot)?.addEventListener('click', () => show(index + 1));
		spot.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
			if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
		});
		moveHl();
	}

	/* ------------------------------------------------------------------
	 * Partners marquee. Driven by rAF so hover eases the speed down to a
	 * crawl instead of stopping dead. Static under reduced motion.
	 * ------------------------------------------------------------------ */
	const marquee = $('[data-marquee]');
	if (marquee && !reduced) {
		const inner = $('[data-marquee-inner]', marquee);
		const first = $('.marquee__track', inner);
		const BASE = 56, SLOW = 6;
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
			speed += (target - speed) * Math.min(1, dt * 3);
			offset += speed * dt;
			const loop = first.getBoundingClientRect().width;
			if (loop && offset >= loop) offset -= loop;
			inner.style.transform = `translate3d(${-offset}px, 0, 0)`;
			requestAnimationFrame(tick);
		};
		requestAnimationFrame(tick);
	}

	/* ------------------------------------------------------------------
	 * Testimonials: native scroll-snap track with index-based arrows.
	 * ------------------------------------------------------------------ */
	const tTrack = $('[data-t-track]');
	if (tTrack) {
		const prev = $('[data-t-prev]');
		const next = $('[data-t-next]');
		const cards = $$('.t-card', tTrack);
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
			idle = setTimeout(() => { index = Math.round(tTrack.scrollLeft / step()); update(); }, 120);
		}, { passive: true });
		prev.addEventListener('click', () => go(-1));
		next.addEventListener('click', () => go(1));
		window.addEventListener('resize', update);
		update();
	}

	/* ------------------------------------------------------------------
	 * Card hover state as one class (desktop hover, keyboard focus).
	 * ------------------------------------------------------------------ */
	const liveCards = (root) => $$('.card', root).forEach((card) => {
		if (canHover) {
			card.addEventListener('pointerenter', () => card.classList.add('is-open'));
			card.addEventListener('pointerleave', () => card.classList.remove('is-open'));
		}
		card.addEventListener('focusin', () => card.classList.add('is-open'));
		card.addEventListener('focusout', (e) => { if (!card.contains(e.relatedTarget)) card.classList.remove('is-open'); });
	});

	/* ------------------------------------------------------------------
	 * Properties listing: filters read/write the URL so results are
	 * shareable; the full set comes from the JSON island.
	 * ------------------------------------------------------------------ */
	const cardsEl = $('[data-cards]');
	const LISTINGS = cardsEl ? json('[data-listings]') : null;
	if (cardsEl && LISTINGS) {
		const PAGE = CFG.pageSize || 6;
		const form = $('[data-listing-filters]');
		const sortEl = $('[data-sort]');
		const label = $('[data-results-label]');
		const count = $('[data-results-count]');
		const empty = $('[data-results-empty]');
		const more = $('[data-load-more]');
		const tpl = $('#card-template');
		let shown = PAGE;

		const current = () => Object.fromEntries(new FormData(form));
		const filtered = () => {
			const f = current();
			const band = f.budget && CFG.budgets ? CFG.budgets[f.budget] : null;
			let out = LISTINGS.filter((p) =>
				(!f.type || p.type === f.type) &&
				(!f.location || p.city === f.location) &&
				(!f.bhk || (f.bhk === '4' ? p.bhk >= 4 : p.bhk === Number(f.bhk))) &&
				(!f.intent || (f.intent === 'for-sale' ? p.intent === 'buy' : f.intent === 'for-rent' ? p.intent === 'rent' : f.intent === 'new-launch' ? /new-launch|under-construction/.test(p.possession) : p.possession === f.intent)) &&
				(!band || (p.intent === 'buy' && p.price >= band[0] && (band[1] === null || p.price < band[1])))
			);
			const sort = sortEl.value;
			if (sort === 'price-asc') out.sort((a, b) => a.price - b.price);
			if (sort === 'price-desc') out.sort((a, b) => b.price - a.price);
			if (sort === 'newest') out.sort((a, b) => b.added - a.added);
			return out;
		};

		const render = () => {
			const f = current();
			const list = filtered();
			cardsEl.innerHTML = '';
			list.slice(0, shown).forEach((p) => {
				const node = tpl.content.cloneNode(true);
				const img = $('.card__img', node);
				img.src = p.img; img.alt = `${p.title}, ${p.area}`;
				$('.card__status', node).textContent = p.status;
				const link = $('.card__title a', node); link.textContent = p.title; link.href = p.url;
				$('.card__media', node).href = p.url;
				$('.card__foot .btn', node).href = `${p.url}#visit`;
				$('.card__place span', node).textContent = p.place;
				$('[data-fact=area]', node).textContent = p.areaText;
				$('[data-fact=beds]', node).textContent = p.type === 'plot' ? (CFG.types?.plot || 'Plot') : p.bhkLabel;
				$('[data-fact=baths]', node).textContent = p.type === 'plot' ? '' : (p.baths ? `${p.baths} ${p.baths === 1 ? 'Bath' : 'Baths'}` : '');
				$('.card__price', node).textContent = p.priceText;
				cardsEl.append(node);
			});
			liveCards(cardsEl);
			label.textContent = `${(CFG.typePlural && CFG.typePlural[f.type]) || T.allProperties}${f.location && CFG.locations?.[f.location] ? fmt(T.in, CFG.locations[f.location]) : ''}${f.intent === 'for-rent' ? T.forRent : ''}`;
			count.textContent = fmt(list.length === 1 ? T.result : T.results, list.length);
			empty.hidden = list.length > 0;
			more.hidden = list.length <= shown;
			$$('select', form).forEach((sel) => sel.classList.toggle('is-set', !!sel.value));
			const q = new URLSearchParams();
			Object.entries(f).forEach(([k, v]) => { if (v) q.set(k, v); });
			if (sortEl.value !== 'featured') q.set('sort', sortEl.value);
			history.replaceState(null, '', q.toString() ? `?${q}` : location.pathname);
		};

		form.addEventListener('change', () => { shown = PAGE; render(); });
		form.addEventListener('submit', (e) => { e.preventDefault(); shown = PAGE; render(); });
		sortEl.addEventListener('change', render);
		more.addEventListener('click', () => { shown += PAGE; render(); });
		$('[data-results-reset]')?.addEventListener('click', () => { form.reset(); shown = PAGE; render(); });
		render();
	}

	/* ------------------------------------------------------------------
	 * Video modal (native <dialog>); the embed is injected on open so
	 * nothing loads until asked.
	 * ------------------------------------------------------------------ */
	const modal = $('[data-video-modal]');
	if (modal && typeof modal.showModal === 'function') {
		const slot = $('[data-video-slot]', modal);
		const embed = $('[data-video-embed]', modal);
		$$('[data-video-open]').forEach((b) => b.addEventListener('click', () => {
			if (slot && embed && !slot.firstChild) slot.append(embed.content.cloneNode(true));
			modal.showModal(); document.body.classList.add('is-locked');
		}));
		$('[data-video-close]', modal).addEventListener('click', () => modal.close());
		modal.addEventListener('click', (e) => { if (e.target === modal) modal.close(); });
		modal.addEventListener('close', () => { document.body.classList.remove('is-locked'); if (slot) slot.innerHTML = ''; });
	}

	/* ------------------------------------------------------------------
	 * About: a pinned stage driven by one scroll progress value (0..1 over
	 * the section's extra height). Words light up in reading order, each
	 * side photo pops in once its threshold is crossed and pops out at the
	 * next, and both the side photos and the inline pills drift toward the
	 * cursor.
	 * ------------------------------------------------------------------ */
	const about = $('[data-about]');
	if (about) {
		const stage = $('.about__stage', about);
		const statement = $('[data-reveal-words]', about);
		[...statement.childNodes].forEach((node) => {
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
		});
		const words = $$('.about__w', statement);
		const floats = $$('[data-float]', about).map((el) => ({ el, from: +el.dataset.in, out: el.dataset.out ? +el.dataset.out : null, x: 0, y: 0, tx: 0, ty: 0 }));
		const clamp01 = (v) => Math.min(1, Math.max(0, v));
		let lit = -1, last = -1;
		const update = () => {
			const travel = about.offsetHeight - stage.offsetHeight;
			const p = reduced ? 1 : clamp01(-about.getBoundingClientRect().top / travel);
			if (p === last) return;
			last = p;
			const n = Math.round(clamp01(p / 0.9) * words.length);
			if (n !== lit) { lit = n; words.forEach((w, i) => w.classList.toggle('is-on', i < n)); }
			floats.forEach(({ el, from, out }) => el.classList.toggle('is-in', p >= from && (out === null || p < out)));
		};
		let ticking = false;
		const onScroll = () => { if (ticking) return; ticking = true; requestAnimationFrame(() => { ticking = false; update(); }); };
		update();
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll);

		if (canHover) {
			const RADIUS = 320, PULL = 26;
			let raf = 0;
			const settle = () => {
				let moving = false;
				floats.forEach((f) => {
					f.x += (f.tx - f.x) * 0.1; f.y += (f.ty - f.y) * 0.1;
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
					f.tx = dx * k; f.ty = dy * k;
				});
				if (!raf) raf = requestAnimationFrame(settle);
			};
			stage.addEventListener('pointermove', (e) => aim(e.clientX, e.clientY));
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
	 * Property page: everything is server-rendered; the script adds the
	 * opening zoom, chapter reveal, filmstrip, lightbox, plan tabs, the
	 * editable EMI sentence, the radial plot, the map, the compact bar,
	 * save/share and the visit form.
	 * ------------------------------------------------------------------ */
	const propertyRoot = $('[data-property]');
	const P = propertyRoot ? json('[data-property-json]', propertyRoot) : null;
	if (propertyRoot && P) {
		const n = (x) => Math.round(x).toLocaleString('en-IN');
		const L = 100000;

		/* --- opening: the photo eases back into its frame as you scroll --- */
		const ph = $('[data-ph]');
		const phImg = $('[data-ph-img]');
		const zoom = () => {
			if (!phImg) return;
			const k = Math.min(1, Math.max(0, scrollY / Math.max(1, ph.offsetHeight)));
			phImg.style.setProperty('--ph-zoom', reduced ? 1 : (1.08 - 0.08 * k).toFixed(4));
		};

		/* --- chapters --- */
		const chapterEls = $$('[data-chapter]');
		if (reduced || !('IntersectionObserver' in window)) chapterEls.forEach((c) => c.classList.add('is-in'));
		else {
			const io = new IntersectionObserver((entries) => entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }), { rootMargin: '0px 0px -18% 0px' });
			chapterEls.forEach((c) => io.observe(c));
		}

		/* --- filmstrip: drag to scroll, counter follows --- */
		const track = $('[data-strip-track]');
		if (track) {
			const stripIndex = $('[data-strip-index]');
			track.addEventListener('scroll', () => {
				const li = track.firstElementChild;
				const step = li.offsetWidth + 12;
				stripIndex.textContent = String(Math.min(P.photos.length, Math.round(track.scrollLeft / step) + 1)).padStart(2, '0');
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
		}

		/* --- lightbox --- */
		const lb = $('[data-lightbox]');
		if (lb && typeof lb.showModal === 'function' && P.photos.length) {
			const img = $('[data-lb-img]', lb);
			const idxEl = $('[data-lb-index]', lb);
			const thumbs = $$('[data-lb-go]', lb);
			let i = 0;
			const show = (k) => {
				i = (k + P.photos.length) % P.photos.length;
				img.src = P.photos[i];
				img.alt = fmt(T.photo, P.title, i + 1, P.photos.length);
				img.style.animation = 'none'; void img.offsetWidth; img.style.animation = '';
				idxEl.textContent = i + 1;
				thumbs.forEach((t, k2) => t.classList.toggle('is-active', k2 === i));
				thumbs[i]?.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
			};
			thumbs.forEach((b) => b.addEventListener('click', () => show(+b.dataset.lbGo)));
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

		/* --- the plan --- */
		const planTabs = $$('[data-plan]');
		if (planTabs.length) {
			const sheets = $$('[data-plan-sheet]');
			const roomLists = $$('[data-plan-rooms]');
			const planFig = $('[data-plan-fig]');
			planTabs.forEach((tab) => tab.addEventListener('click', () => {
				const key = tab.dataset.plan;
				planTabs.forEach((t) => { const on = t === tab; t.classList.toggle('is-active', on); t.setAttribute('aria-selected', String(on)); t.tabIndex = on ? 0 : -1; });
				sheets.forEach((sh) => { sh.hidden = sh.dataset.planSheet !== key; });
				roomLists.forEach((rl) => { rl.hidden = rl.dataset.planRooms !== key; });
				if (planFig) planFig.textContent = tab.dataset.planFigText || '';
			}));
			$('[data-plan-tabs]')?.addEventListener('keydown', (e) => {
				const i = planTabs.indexOf(document.activeElement);
				if (i < 0 || (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft')) return;
				e.preventDefault();
				const nx = planTabs[(i + (e.key === 'ArrowRight' ? 1 : -1) + planTabs.length) % planTabs.length];
				nx.focus(); nx.click();
			});
		}

		/* --- the numbers: editable EMI sentence --- */
		const emiP = $('[data-emi-sentence]');
		const pop = $('[data-num-pop]');
		if (emiP && pop && P.emi) {
			const state = { years: P.emi.years, rate: P.emi.rate, ltv: P.emi.ltv };
			const fields = {
				years: { min: 5, max: 30, step: 1, label: T.tenure, text: (v) => fmt(T.years, v) },
				rate: { min: 7, max: 12, step: 0.1, label: T.rate, text: (v) => `${(+v).toFixed(1)}%` },
				ltv: { min: 10, max: 90, step: 5, label: T.financed, text: (v) => `${v}%` },
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
				const loan = P.emi.price * state.ltv / 100;
				const r = state.rate / 1200;
				const m = state.years * 12;
				const emi = loan * r * Math.pow(1 + r, m) / (Math.pow(1 + r, m) - 1);
				tween(valueEl, emi, (v) => v >= L ? `₹${(v / L).toFixed(2)} L` : `₹${n(v)}`);
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

		/* --- the place: radial distance plot --- */
		const radar = $('[data-radar-svg]');
		const lms = (P.landmarks || []).filter((l) => l.km !== null);
		if (radar && lms.length) {
			const R = 236, C = 280;
			const maxKm = Math.max(40, ...lms.map((l) => l.km));
			const rings = [2, 5, 10, 20, 40].filter((km) => km <= maxKm);
			const scale = (km) => R * Math.log(1 + km) / Math.log(1 + maxKm);
			const svgNS = 'http://www.w3.org/2000/svg';
			const el = (tag, attrs, text) => { const node = document.createElementNS(svgNS, tag); Object.entries(attrs).forEach(([k, v]) => node.setAttribute(k, v)); if (text) node.textContent = text; return node; };
			rings.forEach((km) => {
				radar.append(el('circle', { class: 'radar__ring', cx: C, cy: C, r: scale(km) }));
				const la = -102 * Math.PI / 180;
				radar.append(el('text', { class: 'radar__ring-label', x: C + Math.cos(la) * scale(km) - 4, y: C + Math.sin(la) * scale(km) - 4, 'text-anchor': 'end' }, fmt(T.km, km)));
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
				g.append(el('text', { class: 'radar__sub', x: tx, y: y + 12, 'text-anchor': anchor }, `${fmt(T.km, l.km)}${l.t ? ` · ${l.t}` : ''}`));
				radar.append(g);
				return g;
			});
			radar.append(el('circle', { class: 'radar__home-ring', cx: C, cy: C, r: 14 }));
			radar.append(el('circle', { class: 'radar__home', cx: C, cy: C, r: 7 }));
			radar.append(el('text', { class: 'radar__label', x: C, y: C + 34, 'text-anchor': 'middle' }, P.title));
			const hotSpot = (i, on) => { $$(`[data-spot="${i}"]`).forEach((node) => node.classList.toggle('is-hot', on)); spots[i]?.querySelector('circle').setAttribute('r', on ? 9 : 6); };
			$$('[data-spot]').forEach((node) => {
				node.addEventListener('pointerenter', () => hotSpot(+node.dataset.spot, true));
				node.addEventListener('pointerleave', () => hotSpot(+node.dataset.spot, false));
			});
		}
		const mapEl = $('[data-map]');
		if (mapEl && P.lat && P.lng) {
			$('[data-map-load]').addEventListener('click', (e) => {
				const dl = 0.012;
				const bbox = `${P.lng - dl * 1.6},${P.lat - dl},${P.lng + dl * 1.6},${P.lat + dl}`;
				const frame = document.createElement('div');
				frame.className = 'locmap__frame';
				frame.innerHTML = `<iframe title="${esc(P.area)}" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${P.lat},${P.lng}"></iframe>`;
				mapEl.append(frame);
				e.currentTarget.hidden = true;
				$('.locmap__hint', mapEl).hidden = true;
			});
		}

		liveCards($('[data-similar]') || propertyRoot);

		/* --- chapter nav + compact bar + hero zoom, one scroll handler --- */
		const chapterLinks = $$('[data-chapters] a');
		const sections = chapterLinks.map((a) => $(a.getAttribute('href'))).filter((s) => s && !s.hidden);
		const bar = $('[data-prop-bar]');
		const onScroll = () => {
			zoom();
			const line = innerHeight * 0.34;
			let cur = sections[0];
			sections.forEach((s) => { if (s.getBoundingClientRect().top <= line) cur = s; });
			if (cur) chapterLinks.forEach((a) => a.classList.toggle('is-active', a.getAttribute('href') === `#${cur.id}`));
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
		let saved = new Set();
		try { saved = new Set(JSON.parse(localStorage.getItem('bytenot:saved') || '[]')); } catch (e) { /* storage blocked */ }
		const paintSave = () => { const on = saved.has(P.id); save.setAttribute('aria-pressed', String(on)); $('[data-save-label]').textContent = on ? T.savedLabel : T.save; };
		save.addEventListener('click', () => {
			saved.has(P.id) ? saved.delete(P.id) : saved.add(P.id);
			try { localStorage.setItem('bytenot:saved', JSON.stringify([...saved])); } catch (e) { /* storage blocked */ }
			paintSave();
			showToast(fmt(saved.has(P.id) ? T.saved : T.removed, `<b>${esc(P.title)}</b>`));
		});
		paintSave();
		$('[data-share]').addEventListener('click', async () => {
			const data = { title: document.title, text: `${P.title}, ${P.area}: ${P.priceText}`, url: location.href };
			try {
				if (navigator.share) await navigator.share(data);
				else { await navigator.clipboard.writeText(location.href); showToast(T.copied); }
			} catch { /* dismissed */ }
		});

		/* --- visit form --- */
		const enq = $('[data-enquire]');
		enq.addEventListener('submit', async (e) => {
			e.preventDefault();
			if (!enq.reportValidity()) return;
			try {
				const payload = await postLead(enq, { form: 'visit', property_id: P.id });
				showToast(fmt(T.thanksVisit, esc(payload.name.split(' ')[0]), esc(P.first), `<b>${esc(P.title)}</b>`));
				enq.reset();
			} catch (err) { showToast(esc(err.message)); }
		});
	}

	/* ------------------------------------------------------------------
	 * FAQ accordion: one answer open at a time; the height animates with
	 * CSS grid rows, JS only flips the state.
	 * ------------------------------------------------------------------ */
	const faq = $('[data-faq]');
	if (faq) {
		const items = $$('.faq__item', faq);
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
	 * Inquiry form → lead endpoint; the band settles flush when a scroll
	 * ends near it while approaching.
	 * ------------------------------------------------------------------ */
	const inquiry = $('[data-inquiry]');
	if (inquiry) {
		const band = inquiry.closest('.inquiry');
		let lastGap = Infinity;
		let settleTimer;
		const settle = () => {
			const top = band.getBoundingClientRect().top;
			const gap = Math.abs(top);
			const approaching = gap < lastGap;
			lastGap = gap;
			if (gap > 2 && gap < innerHeight * 0.22 && approaching) {
				lastGap = 0;
				scrollTo({ top: scrollY + top, behavior: reduced ? 'instant' : 'smooth' });
			}
		};
		if ('onscrollend' in window) window.addEventListener('scrollend', settle);
		else window.addEventListener('scroll', () => { clearTimeout(settleTimer); settleTimer = setTimeout(settle, 140); }, { passive: true });

		inquiry.addEventListener('submit', async (e) => {
			e.preventDefault();
			if (!inquiry.reportValidity()) return;
			try {
				const payload = await postLead(inquiry, { form: 'inquiry', consent: true });
				showToast(fmt(T.thanks, esc(payload.name.split(' ')[0])));
				inquiry.reset();
			} catch (err) { showToast(esc(err.message)); }
		});
	}

	/* ------------------------------------------------------------------
	 * Dropdowns. Every <select> inside .field__select gets a styled listbox
	 * on mouse/trackpad devices; the select stays in the form as the value
	 * store, so filtering, URL sync and submit work unchanged. Touch keeps
	 * the native picker. Runs last so initial values are read.
	 * ------------------------------------------------------------------ */
	if (canHover) {
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
				const o = document.createElement('div');
				o.className = 'dd__option';
				o.setAttribute('role', 'option');
				o.tabIndex = -1;
				o.textContent = opt.text;
				menu.append(o);
				return o;
			});
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
			button.addEventListener('keydown', (e) => { if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); open(); } });
			menu.addEventListener('click', (e) => {
				e.preventDefault();
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
			select.addEventListener('change', sync);
			select.form?.addEventListener('reset', () => setTimeout(sync));
			select.dd = { button, sync };
			sync();
		});
	}
})();
