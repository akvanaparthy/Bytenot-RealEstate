/**
 * Production build of the theme into build/bytenot-estate/.
 *
 * The source theme stays readable for development (Local runs it through a junction).
 * The built copy is what ships: every design class name is replaced by a short hash in
 * the stylesheet, the script and the templates; the stylesheet and script are minified
 * with no source maps and no comments except the Bytenot banner; a class map is written
 * for the few class names the templates compose at runtime.
 *
 * Usage: node scripts/build.mjs
 */
import { createHash } from 'node:crypto';
import { cpSync, existsSync, mkdirSync, readdirSync, readFileSync, rmSync, statSync, writeFileSync } from 'node:fs';
import { join, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { buildSync } from 'esbuild';

const root = resolve(fileURLToPath(new URL('..', import.meta.url)));
const src = join(root, 'theme', 'bytenot-estate');
const out = join(root, 'build', 'bytenot-estate');

const BANNER = '/*! Bytenot Estate. Design, styles and scripts (c) Bytenot, https://bytenot.com. Love this interface? Come and have yours made: premium branding, websites, AI and automation. You are very welcome. */';

/* Class names WordPress, the editor or plugins emit, which the stylesheet targets and must keep. */
const KEEP_PREFIXES = ['wp-', 'has-', 'is-style-', 'align', 'page-template', 'post-', 'type-', 'status-', 'format-', 'tax-', 'term-', 'category-', 'tag-', 'menu-item', 'current', 'logged-', 'admin-', 'customize-', 'no-', 'js', 'hentry', 'sticky', 'bypostauthor', 'gallery', 'screen-reader', 'nav-links', 'page-numbers', 'prev', 'next', 'dots', 'editor-', 'block-', 'rtl', 'single', 'archive', 'blog', 'home', 'search', 'error404', 'attachment', 'author', 'date', 'paged', 'privacy-policy', 'property-template', 'postid-', 'page-id-', 'parent-pageid-'];
const keep = (name) => KEEP_PREFIXES.some((p) => name === p || name.startsWith(p));

/* ---------- 1. the class map, from both stylesheets ---------- */
const stripComments = (css) => css.replace(/\/\*[\s\S]*?\*\//g, '');

function cssClassNames(css) {
	const names = new Set();
	forEachSelector(stripComments(css), (prelude) => {
		for (const m of prelude.matchAll(/\.(-?[A-Za-z_][\w-]*)/g)) names.add(m[1]);
	});
	return names;
}

/** Call fn(prelude) for every rule prelude (the selector text before "{"), skipping at-rule preludes and declarations. */
function forEachSelector(css, fn, replace = false) {
	let outCss = '';
	let buf = '';
	let i = 0;
	const n = css.length;
	let inString = null;
	let depth = 0;
	let declDepth = -1; // depth at which the current block holds declarations
	while (i < n) {
		const ch = css[i];
		if (inString) {
			buf += ch;
			if (ch === '\\') { buf += css[i + 1] || ''; i += 2; continue; }
			if (ch === inString) inString = null;
			i++;
			continue;
		}
		if (ch === '/' && css[i + 1] === '*') { const end = css.indexOf('*/', i + 2); buf += css.slice(i, end + 2); i = end + 2; continue; }
		if (ch === '"' || ch === "'") { inString = ch; buf += ch; i++; continue; }
		if (ch === '{') {
			const prelude = buf;
			const trimmed = prelude.trim();
			const isAt = trimmed.startsWith('@');
			if (!isAt && declDepth === -1) {
				outCss += replace ? fn(prelude) : (fn(prelude), prelude);
				declDepth = depth + 1;
			} else {
				outCss += prelude;
			}
			outCss += '{';
			depth++;
			buf = '';
			i++;
			continue;
		}
		if (ch === '}') {
			outCss += buf + '}';
			buf = '';
			if (declDepth === depth) declDepth = -1;
			depth--;
			i++;
			continue;
		}
		if (ch === ';' && declDepth === -1) { outCss += buf + ';'; buf = ''; i++; continue; }
		buf += ch;
		i++;
	}
	return outCss + buf;
}

const themeCss = readFileSync(join(src, 'assets', 'css', 'theme.css'), 'utf8');
const wpCss = readFileSync(join(src, 'assets', 'css', 'wp.css'), 'utf8');
const walkSrc = (dir) => readdirSync(dir).flatMap((f) => { const p = join(dir, f); return statSync(p).isDirectory() ? walkSrc(p) : [p]; });
const markupClassNames = new Set();
for (const file of walkSrc(src).filter((f) => f.endsWith('.php'))) {
	const text = readFileSync(file, 'utf8');
	for (const m of text.matchAll(/class="([^"]*)"/g)) {
		for (const part of m[1].split(/<\?php[\s\S]*?\?>/)) for (const tok of part.split(/\s+/)) if (/^-?[A-Za-z_][\w-]*$/.test(tok)) markupClassNames.add(tok);
	}
}
const names = [...new Set([...cssClassNames(themeCss), ...cssClassNames(wpCss), ...markupClassNames])].filter((n) => !keep(n)).sort((a, b) => b.length - a.length);
const used = new Set(names);
const map = {};
for (const name of names) {
	let h = createHash('sha1').update('bytenot:' + name).digest('base64url').replace(/[^a-z0-9]/gi, '').toLowerCase();
	let short = 'b' + h.slice(0, 4);
	let k = 4;
	while (used.has(short)) short = 'b' + h.slice(0, ++k);
	used.add(short);
	map[name] = short;
}
const mapName = (name) => map[name] || name;
const nameRe = new RegExp('(?<![\\w-])(' + names.map((s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|') + ')(?![\\w-])', 'g');
const mapTokens = (text) => (names.length ? text.replace(nameRe, (m) => mapName(m)) : text);

/* ---------- 2. stylesheet: hashed selectors, minified ---------- */
function rewriteCss(css) {
	const hashed = forEachSelector(stripComments(css), (prelude) => prelude.replace(/\.(-?[A-Za-z_][\w-]*)/g, (m, c) => '.' + mapName(c)), true);
	return hashed;
}

/* ---------- 3. script: hashed class names inside string literals ---------- */
function rewriteJs(code) {
	let outJs = '';
	let i = 0;
	const n = code.length;
	let lastSig = '';
	const regexBefore = new Set(['(', ',', '=', ':', '[', '!', '&', '|', '?', '{', '}', ';', '+', '-', '*', '%', '<', '>', '~', '^']);
	const mapString = (text) => {
		// selector references: ".name"
		let t = text.replace(/\.(-?[A-Za-z_][\w-]*)/g, (m, c) => (map[c] ? '.' + map[c] : m));
		return t;
	};
	const wholeTokens = (parts) => parts.every((p) => p.trim().split(/\s+/).filter(Boolean).every((tok) => map[tok]));
	const classContext = () => /(?:classList\.(?:add|remove|toggle|contains|replace)\(|className\s*=|\bclass\s*:)\s*$/.test(outJs);
	const mapParts = (parts) => parts.map((p) => p.replace(/(-?[A-Za-z_][\w-]*)/g, (m) => mapName(m)));
	while (i < n) {
		const ch = code[i];
		if (ch === '/' && code[i + 1] === '/') { const end = code.indexOf('\n', i); const e = end === -1 ? n : end; outJs += code.slice(i, e); i = e; continue; }
		if (ch === '/' && code[i + 1] === '*') { const end = code.indexOf('*/', i + 2); outJs += code.slice(i, end + 2); i = end + 2; continue; }
		if (ch === '"' || ch === "'") {
			let j = i + 1;
			let text = '';
			while (j < n && code[j] !== ch) { if (code[j] === '\\') { text += code[j] + code[j + 1]; j += 2; continue; } text += code[j]; j++; }
			const mapped = classContext() && wholeTokens([text]) ? mapParts([text])[0] : mapString(text);
			outJs += ch + mapped + ch;
			i = j + 1;
			lastSig = ch;
			continue;
		}
		if (ch === '`') {
			const { end, parts, exprs } = readTemplate(code, i);
			const texts = parts.map((p) => p);
			const mappedTexts = classContext() && wholeTokens(texts) ? mapParts(texts) : texts.map(mapString);
			let s = '`';
			for (let k = 0; k < mappedTexts.length; k++) {
				s += mappedTexts[k];
				if (k < exprs.length) s += '${' + rewriteJs(exprs[k]) + '}';
			}
			outJs += s + '`';
			i = end + 1;
			lastSig = '`';
			continue;
		}
		if (ch === '/' && (lastSig === '' || regexBefore.has(lastSig) || /\breturn$/.test(outJs.trimEnd()))) {
			let j = i + 1;
			let inClass = false;
			while (j < n) {
				if (code[j] === '\\') { j += 2; continue; }
				if (code[j] === '[') inClass = true;
				else if (code[j] === ']') inClass = false;
				else if (code[j] === '/' && !inClass) break;
				j++;
			}
			j++;
			while (j < n && /[a-z]/.test(code[j])) j++;
			outJs += code.slice(i, j);
			i = j;
			lastSig = '/';
			continue;
		}
		outJs += ch;
		if (!/\s/.test(ch)) lastSig = ch;
		i++;
	}
	return outJs;
}

/** Read a template literal starting at code[start] === '`'; returns text parts and raw expressions. */
function readTemplate(code, start) {
	const parts = [];
	const exprs = [];
	let text = '';
	let i = start + 1;
	while (i < code.length) {
		const ch = code[i];
		if (ch === '\\') { text += ch + code[i + 1]; i += 2; continue; }
		if (ch === '`') { parts.push(text); return { end: i, parts, exprs }; }
		if (ch === '$' && code[i + 1] === '{') {
			parts.push(text);
			text = '';
			let depth = 1;
			let j = i + 2;
			let inStr = null;
			while (j < code.length && depth) {
				const c = code[j];
				if (inStr) { if (c === '\\') { j += 2; continue; } if (c === inStr) inStr = null; j++; continue; }
				if (c === '"' || c === "'") { inStr = c; j++; continue; }
				if (c === '`') { const t = readTemplate(code, j); j = t.end + 1; continue; }
				if (c === '{') depth++;
				else if (c === '}') depth--;
				j++;
			}
			exprs.push(code.slice(i + 2, j - 1));
			i = j;
			continue;
		}
		text += ch;
		i++;
	}
	throw new Error('Unterminated template literal');
}

/* ---------- 4. templates: class attributes and the known string contexts ---------- */
const allClasses = (v) => v.trim().split(/\s+/).filter(Boolean).every((tok) => map[tok]);
const mapAttr = (v) => v.split(/(<\?php[\s\S]*?\?>)/).map((part) => {
	if (!part.startsWith('<?php')) return mapTokens(part);
	return part.replace(/(bytenot_class\(\s*)?'([^']*)'/g, (m, fn, lit) => (fn || !allClasses(lit) ? m : "'" + mapTokens(lit) + "'"));
}).join('');
function rewritePhp(php) {
	let s = php.replace(/class="([^"]*)"/g, (m, v) => 'class="' + mapAttr(v) + '"');
	s = s.replace(/class='([^']*)'/g, (m, v) => "class='" + mapAttr(v) + "'");
	s = s.replace(/('(?:menu_class|class)'\s*=>\s*')([^']*)(')/g, (m, a, v, b) => a + mapTokens(v) + b);
	s = s.replace(/'([^']*)'(\s*===\s*(?:\(\s*\$args->menu_class|\$args\['menu_class'\]))/g, (m, v, rest) => "'" + mapTokens(v) + "'" + rest);
	s = s.replace(/(bytenot_brand\(\s*|post_class\(\s*)'([^']*)'/g, (m, fn, v) => fn + "'" + mapTokens(v) + "'");
	return s;
}

/* ---------- run ---------- */
rmSync(out, { recursive: true, force: true });
mkdirSync(out, { recursive: true });
cpSync(src, out, { recursive: true, filter: (p) => !/[\\/](\.git|node_modules)([\\/]|$)/.test(p) });

const walk = (dir) => readdirSync(dir).flatMap((f) => { const p = join(dir, f); return statSync(p).isDirectory() ? walk(p) : [p]; });
let phpCount = 0;
for (const file of walk(out)) {
	if (file.endsWith('.php')) {
		writeFileSync(file, rewritePhp(readFileSync(file, 'utf8')));
		phpCount++;
	}
}

// stylesheet: both files, hashed, minified, one banner
const css = rewriteCss(themeCss) + '\n' + rewriteCss(wpCss);
const cssMin = buildSync({ stdin: { contents: css, loader: 'css' }, minify: true, write: false, legalComments: 'none', logLevel: 'silent' }).outputFiles[0].text;
writeFileSync(join(out, 'assets', 'css', 'theme.css'), BANNER + '\n' + cssMin);
writeFileSync(join(out, 'assets', 'css', 'wp.css'), '/* merged into theme.css */\n');

// script
const js = rewriteJs(readFileSync(join(src, 'assets', 'js', 'theme.js'), 'utf8'));
const jsMin = buildSync({ stdin: { contents: js, loader: 'js' }, minify: true, write: false, legalComments: 'none', target: ['es2020'], logLevel: 'silent' }).outputFiles[0].text;
writeFileSync(join(out, 'assets', 'js', 'theme.js'), BANNER + '\n' + jsMin);

// editor stylesheet keeps WordPress class names only; nothing to hash
// class map for names composed at runtime
const phpMap = '<?php\n/**\n * Generated by scripts/build.mjs: design class name => served name. Do not edit.\n *\n * @package bytenot-estate\n */\n\ndefined( \'ABSPATH\' ) || exit;\n\nreturn array(\n' + Object.entries(map).map(([k, v]) => `\t'${k}' => '${v}',`).join('\n') + '\n);\n';
writeFileSync(join(out, 'inc', 'classmap.php'), phpMap);
writeFileSync(join(root, 'build', 'classmap.json'), JSON.stringify(map, null, '\t'));

console.log(`Built ${relative(root, out)}: ${names.length} class names hashed, ${phpCount} PHP files, css ${(cssMin.length / 1024).toFixed(1)} KB, js ${(jsMin.length / 1024).toFixed(1)} KB`);
