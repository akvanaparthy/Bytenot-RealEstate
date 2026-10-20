# Project: Bytenot Estate — WordPress theme

Read `%USERPROFILE%\.claude\skills\wordpress-skill\SKILL.md` before working here, then load
the `real-estate-web` skill (it extends wordpress-skill with the property/listing domain).
Load `seo-aeo-geo` when work touches metadata, schema, sitemaps or page structure.

## What this is
- Block theme `bytenot-estate` in `theme/bytenot-estate`. Text domain `bytenot-estate`.
- Companion plugin `property-core` in `plugin/property-core` for custom post types /
  fields / blocks (listings, projects, localities, leads).
- Target WordPress `7.1.3` / PHP `8.1`.
- Audience / purpose: real-estate company website (listings, projects, localities, leads)
  that must also work as a sellable, reusable theme.
- Reusable by other sites: yes. No brand data hard-coded; everything via `theme.json`,
  site blocks, settings, style variations.

## Environment
- Local WordPress: LocalWP at `http://lavenorrealty.local` (site name `lavenorrealty`),
  files at `C:\Users\aksha\Local Sites\lavenorrealty\app\public`. Admin login: use the
  "WP Admin" button in the Local app (credentials are not stored in this repo).
- WP-CLI: via Local's site shell ("Open site shell" in the Local app) — `wp …`.
  Claude cannot run `wp` directly; it gives the commands or writes `demo/seed.php`.
- Live reload: browser-sync proxy on `http://localhost:3000` (`npm run dev`).
- Screenshots: Playwright MCP against `http://localhost:3000` at ~1440px and ~390px.
- Validator: `node %USERPROFILE%\.claude\skills\wordpress-skill\scripts\validate_theme.mjs theme\bytenot-estate --plugin plugin\property-core`.

## Design input
- Reference images live in `design/references/`. Treat them as specs: note what you
  see, build, screenshot at ~1440px and ~390px, score 0–10, iterate ≤ 3 times, list gaps.
- Extracted design tokens are recorded in `design/tokens.md` and implemented in
  `theme.json`. New colours/sizes go into presets first, never raw values in markup.

## Rules
1. Core blocks first; custom blocks only for interactivity or data processing.
2. Data in the plugin, looks in the theme.
3. All strings translatable; all output escaped; all input sanitized.
4. Don't duplicate what an active SEO plugin outputs.
5. Ask before decisions that are expensive to reverse (URL structure, content model);
   otherwise decide, note it in `DECISIONS.md`, continue.
6. Run the validator and take screenshots before saying a task is done.

## Repo layout
```
design/references/        reference images (design specs)
theme/bytenot-estate/     the block theme
plugin/property-core/     the companion plugin (CPTs, fields, blocks)
qa/screenshots/           Playwright screenshots for visual checks
demo/                     seed content (demo/seed.php, run via Local's shell)
scripts/                  repo tooling (zip.mjs)
DECISIONS.md
QA.md
```

## How this project is set up
- **Customer vs product.** The first customer site is Lavenor Realty (`lavenorrealty.local`).
  "Bytenot Estate" is the reusable theme that will be sold to other companies. Lavenor's
  name, logo, phone, RERA number and similar details are site content or settings only.
  They must never appear in theme or plugin code.
- **Where WordPress lives.** WordPress runs in LocalWP at
  `C:\Users\aksha\Local Sites\lavenorrealty\app\public`, served at
  `http://lavenorrealty.local`. `wp-config.php` already enables `WP_DEBUG`,
  `WP_DEBUG_DISPLAY`, `SCRIPT_DEBUG` and `WP_DEVELOPMENT_MODE=theme`, so theme.json and
  pattern changes show up without cache clearing.
- **What is in git.** Only this repo is version-controlled. WordPress core, LocalWP files
  and the database are not. The theme and plugin folders here are junction-linked into
  Local's `wp-content/themes` and `wp-content/plugins`, so editing a file in this repo
  changes the running site immediately. Never edit files on the Local side.
- **Remote and releases.** GitHub repo `akvanaparthy/Bytenot-RealEstate` (origin added by
  the owner). Branch `main`. One commit per feature. Releases are tagged `vX.Y.Z`, and the
  `Version:` headers in `style.css` and `property-core.php` are bumped in the same commit.
- **Live reload.** `npm run dev` starts browser-sync on `localhost:3000`, proxying the
  Local site. The owner keeps it running in one terminal and Claude Code in another.
  Templates, patterns, theme.json and CSS need no build step. Only custom blocks in the
  plugin use `wp-scripts` (`npm run blocks` for watch, `npm run build` for production).
- **Visual checks.** Use Playwright MCP to screenshot `localhost:3000` at roughly 1440px
  and 390px wide, and compare against the images in `design/references/`.
- **Content seeding.** Content is seeded through WP-CLI in Local's site shell. Claude
  cannot run `wp` itself: it gives the owner the commands to paste, or writes
  `demo/seed.php` for the owner to run.
- **Deployment.** `npm run zip` builds and produces one zip each for the theme and plugin
  in `dist/`. For now the owner uploads both in wp-admin. Git or SSH deploy comes later.
- **Skills.** `wordpress-skill`, `seo-aeo-geo` and `real-estate-web` are installed
  globally in `%USERPROFILE%\.claude\skills` (from `github.com/akvanaparthy/aks-skills`).
  The official WordPress skills (`wp-block-themes`, `wp-block-development`,
  `wp-interactivity-api`, `wp-plugin-development`, `wp-performance`) are there too.
