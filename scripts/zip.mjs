/**
 * Build the plugin blocks (if any), then zip the theme and plugin separately into dist/.
 *
 * Each zip contains exactly one top-level folder (the slug), as wp-admin expects.
 * Dev-only files are excluded. Pure Node, no third-party deps, and entry names always
 * use forward slashes so the zips extract correctly on Linux hosts.
 *
 * Usage: node scripts/zip.mjs
 */
import { execSync } from 'node:child_process';
import { existsSync, mkdirSync, readdirSync, readFileSync, statSync, writeFileSync } from 'node:fs';
import { basename, join, relative, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { crc32, deflateRawSync } from 'node:zlib';

const root = resolve(fileURLToPath(new URL('..', import.meta.url)));
const distDir = join(root, 'dist');

const packages = [
	{ dir: join(root, 'theme', 'bytenot-estate'), versionFile: 'style.css' },
	{ dir: join(root, 'plugin', 'property-core'), versionFile: 'property-core.php' },
];

// Folders and files that never ship.
const excludedDirs = new Set(['node_modules', 'src', '.git', '.github', '.vscode', 'tests', 'test']);
const excludedFiles = new Set([
	'.DS_Store', 'Thumbs.db', '.gitignore', '.gitattributes', '.editorconfig',
	'package.json', 'package-lock.json', 'composer.json', 'composer.lock',
	'phpcs.xml', 'phpcs.xml.dist', 'phpunit.xml', 'phpunit.xml.dist',
	'webpack.config.js', 'tsconfig.json',
]);
const excludedPatterns = [/^\.eslintrc/, /^\.prettierrc/, /^\.stylelintrc/, /\.map$/, /\.log$/, /\.zip$/];

function isExcluded(name, isDir) {
	if (isDir) return excludedDirs.has(name);
	if (excludedFiles.has(name)) return true;
	return excludedPatterns.some((re) => re.test(name));
}

/** Recursively collect shippable files as [absolutePath, zipEntryName] pairs. */
function collectFiles(dir, prefix) {
	const out = [];
	for (const entry of readdirSync(dir, { withFileTypes: true }).sort((a, b) => a.name.localeCompare(b.name))) {
		if (isExcluded(entry.name, entry.isDirectory())) continue;
		const abs = join(dir, entry.name);
		const name = `${prefix}/${entry.name}`;
		if (entry.isDirectory()) out.push(...collectFiles(abs, name));
		else if (entry.isFile()) out.push([abs, name]);
	}
	return out;
}

function readVersion(pkg) {
	const text = readFileSync(join(pkg.dir, pkg.versionFile), 'utf8');
	const m = text.match(/^\s*\*?\s*Version:\s*(.+)$/m);
	return m ? m[1].trim() : '0.0.0';
}

/** DOS date/time pair for zip headers. */
function dosDateTime(date) {
	const time = (date.getHours() << 11) | (date.getMinutes() << 5) | (date.getSeconds() >> 1);
	const day = ((date.getFullYear() - 1980) << 9) | ((date.getMonth() + 1) << 5) | date.getDate();
	return { time, day };
}

/** Minimal zip writer: deflate every file, forward-slash entry names. */
function writeZip(zipPath, files) {
	const locals = [];
	const centrals = [];
	let offset = 0;

	for (const [abs, name] of files) {
		const data = readFileSync(abs);
		const compressed = deflateRawSync(data, { level: 9 });
		const nameBuf = Buffer.from(name, 'utf8');
		const crc = crc32(data);
		const { time, day } = dosDateTime(statSync(abs).mtime);

		const local = Buffer.alloc(30);
		local.writeUInt32LE(0x04034b50, 0);
		local.writeUInt16LE(20, 4); // version needed
		local.writeUInt16LE(0x0800, 6); // flags: UTF-8 names
		local.writeUInt16LE(8, 8); // deflate
		local.writeUInt16LE(time, 10);
		local.writeUInt16LE(day, 12);
		local.writeUInt32LE(crc, 14);
		local.writeUInt32LE(compressed.length, 18);
		local.writeUInt32LE(data.length, 22);
		local.writeUInt16LE(nameBuf.length, 26);
		local.writeUInt16LE(0, 28);

		const central = Buffer.alloc(46);
		central.writeUInt32LE(0x02014b50, 0);
		central.writeUInt16LE(20, 4); // version made by
		central.writeUInt16LE(20, 6); // version needed
		central.writeUInt16LE(0x0800, 8);
		central.writeUInt16LE(8, 10);
		central.writeUInt16LE(time, 12);
		central.writeUInt16LE(day, 14);
		central.writeUInt32LE(crc, 16);
		central.writeUInt32LE(compressed.length, 20);
		central.writeUInt32LE(data.length, 24);
		central.writeUInt16LE(nameBuf.length, 28);
		central.writeUInt16LE(0, 30); // extra len
		central.writeUInt16LE(0, 32); // comment len
		central.writeUInt16LE(0, 34); // disk
		central.writeUInt16LE(0, 36); // internal attrs
		central.writeUInt32LE(0, 38); // external attrs
		central.writeUInt32LE(offset, 42);

		locals.push(local, nameBuf, compressed);
		centrals.push(central, nameBuf);
		offset += local.length + nameBuf.length + compressed.length;
	}

	const centralSize = centrals.reduce((n, b) => n + b.length, 0);
	const eocd = Buffer.alloc(22);
	eocd.writeUInt32LE(0x06054b50, 0);
	eocd.writeUInt16LE(0, 4);
	eocd.writeUInt16LE(0, 6);
	eocd.writeUInt16LE(files.length, 8);
	eocd.writeUInt16LE(files.length, 10);
	eocd.writeUInt32LE(centralSize, 12);
	eocd.writeUInt32LE(offset, 16);
	eocd.writeUInt16LE(0, 20);

	writeFileSync(zipPath, Buffer.concat([...locals, ...centrals, eocd]));
}

// 1. Build custom blocks, only if the plugin has a src folder.
if (existsSync(join(root, 'plugin', 'property-core', 'src'))) {
	console.log('$ npm run build');
	execSync('npm run build', { stdio: 'inherit', cwd: root });
} else {
	console.log('No plugin/property-core/src folder, skipping block build.');
}

// 2. Zip each package.
mkdirSync(distDir, { recursive: true });

for (const pkg of packages) {
	const slug = basename(pkg.dir);
	const version = readVersion(pkg);
	const files = collectFiles(pkg.dir, slug);
	if (files.length === 0) throw new Error(`Nothing to zip in ${pkg.dir}`);

	const topLevel = new Set(files.map(([, name]) => name.split('/')[0]));
	if (topLevel.size !== 1 || !topLevel.has(slug)) {
		throw new Error(`Expected exactly one top-level folder "${slug}", found: ${[...topLevel].join(', ')}`);
	}

	const zipPath = join(distDir, `${slug}-${version}.zip`);
	writeZip(zipPath, files);
	console.log(`✔ ${relative(root, zipPath).split(sep).join('/')}  (${files.length} files, top-level folder "${slug}")`);
}
