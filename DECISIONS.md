# Decisions

Short log of choices that are expensive to reverse. Newest first.

## 2026-11-02 — Bytenot Core: one plugin, its own admin

- The companion plugin is Bytenot Core (`plugin/bytenot-core`, text domain `bytenot-core`, prefix
  `bytenot_core_`, REST `bytenot/v1`, option `bytenot_core_settings`). Post types, taxonomies and
  the `pc_` field keys did not change.
- It draws its own admin under a top-level "Bytenot" menu: Dashboard, Enquiries, the content types
  (listings, projects, advisors, testimonials, FAQs, partners), Setup, Integrations, Tools. WordPress
  keeps the outer frame; the screens use the plugin's stylesheet (Figtree, navy and gold). Settings
  still save through the Settings API; each screen posts only its own keys.
- Listings, projects and advisors get a "Listing details" panel (tabs: pricing, size, building,
  legal, location with a landmarks repeater, media pickers, people and flags). The raw Custom
  Fields panel is off for those types.
- Private preview lives under Tools and as a one-click toggle on the dashboard.
- The browser-sync proxy (localhost:3000) corrupts the block editor's settings JSON by rewriting a
  URL inside it; use lavenorrealty.local for wp-admin, the proxy for the front end only.

## 2026-11-02 — Production build hides the design's structure

- `npm run build` (scripts/build.mjs) writes `build/bytenot-estate/`: every design class name becomes
  a short hash in the stylesheet, the script and the templates; CSS and JS are minified with no
  source maps or comments; `inc/classmap.php` carries the map for the few names composed at runtime
  (`bytenot_class()`). `npm run zip` packages that build. The source theme stays readable and is what
  Local runs. IDs and `data-*` hooks stay readable (anchors and script hooks).
- Crawlers are not blocked (AI-search visibility matters). Instead the credit and a friendly note
  pointing to bytenot.com go out as a head comment, an `X-Design-By` header, `/humans.txt` and
  lines in `robots.txt` (inc/credits.php), and the CSS/JS banner.
- Licence: PHP GPL-2.0-or-later as WordPress requires; design, stylesheet, script and images (c)
  Bytenot, licensed to the site owner.

## 2026-11-01 — Theme rebuilt as a classic PHP theme from the prototype

- The block-theme build (templates, parts, patterns, core Navigation/Cover) drifted from the
  prototype in the header, footer and listing pages. Rebuilt as a classic theme: the prototype's
  HTML lives in `header.php`, `footer.php`, `front-page.php` + `template-parts/home/*`,
  `archive-property.php` and `single-property.php`; `assets/css/theme.css` is the prototype
  stylesheet verbatim (tokens come from `theme.json` presets); `assets/js/theme.js` is the
  prototype script with data read from JSON islands instead of constants.
- `theme.json` stays for colours, fonts and spacing (the CSS uses the preset variables) and the
  block editor is used for page and listing content only.
- Texts and photos are Customizer settings (Customize › Bytenot Estate); testimonials, FAQs and
  partners are plugin post types; menus are classic menu locations. Defaults are brand-neutral;
  the demo installer sets Lavenor's.
- Listing stories: chapters are the h2/h3 sections of the listing's content (up to three), each
  paired with a gallery photo; the remaining paragraphs become the specification prose. Floor
  plans are images (`pc_floor_plans`) with rooms listed in the attachment description as
  `Label | size` lines.
- The language switcher renders only when a multilingual plugin supplies languages through the
  `bytenot_languages` filter.
- Hero search: the configuration field defaults to "Any" (the prototype preselected 2 BHK, which
  would narrow every search by default).
- JSON-LD (RealEstateListing, RealEstateAgent) is emitted by the plugin unless an SEO plugin is
  active.

## 2026-11-01 — Theme port: URLs, lifecycle, localities, languages

**Status:** assumed defaults, taken when the owner said "proceed" without answering; overturn any of them before content is entered.

- **URLs.** `/properties/{slug}/`, `/projects/{slug}/`, `/advisors/{slug}/`, `/localities/{slug}/`. Property type, status, amenity and possession terms are filters, not landing pages (`/type/`, `/status/`, `/amenity/`, `/possession/` exist but are not linked from navigation).
- **Localities are a taxonomy, not a post type.** `location` is hierarchical (City › Locality) and attached to properties, projects and advisors. Each term is a page with its own description and term meta, rendered by `taxonomy-location.html`. One entity instead of a post type plus a term kept in sync.
- **Sold, let and withdrawn listings keep their page.** They leave archives, locality pages, search and sitemaps; the page stays published, marked, with `noindex, follow`; nothing is deleted or redirected to the home page. `pc_sold_at` is stamped automatically when the status changes.
- **What an agency offers is a setting.** Property types, transactions (sale, rent), budget bands, phone, WhatsApp, RERA, EMI defaults and the lead destination live in Properties › Setup. Search forms and filters build from it; a plots-only agency never shows a BHK filter.
- **Leads are stored first, pushed second.** Every form posts to `bytenot/v1/leads`; the enquiry is saved as a private post, emailed at once, then pushed to the CRM through a provider interface in the background with three retries. Providers shipped: email, generic webhook. A direct Zoho CRM provider implements the same interface later.
- **No multilingual plugin assumed.** The prototype's language switcher is not in the theme. If Hindi and Telugu are wanted, Polylang or WPML supplies the switcher and translated URLs.
- **Figtree is bundled, not loaded from Google.** Two variable WOFF2 files under `assets/fonts/`; the theme falls back to the system sans-serif until they are added.

## 2026-10-23 — Property data: Zoho owns the facts, WordPress owns the presentation

**Status:** proposed, to be confirmed with the owner. Not yet implemented.

**Context.** Lavenor plans to run inventory and leads in Zoho CRM. Zoho is a
horizontal CRM: properties only exist there as a custom module whose fields the
firm defines. The property page prototype needs structured facts, media,
editorial copy and locality data, and sends leads back.

**Decision.** Ownership is split by field, not by system, and sync runs one way
per direction. No two-way sync of property records.

| Zoho owns; WordPress mirrors read-only | WordPress owns; never overwritten by sync |
|---|---|
| Reference ID, title, type, status, price, rent, deposit | Chapters, captions, long description |
| Carpet, built-up, super built-up, BHK, baths, facing, floor, possession | Photo order and crops, floor plan, brochure |
| Locality, address, coordinates, RERA number | Locality page content, landmarks |
| Assigned advisor | SEO fields, slug, featured flag |
| Photos as the initial import only | Anything edited after import |

**Mechanics.**
- The `property` post type in `bytenot-core` is the canonical record for the
  site and must be fully editable without any CRM, because the theme is sold to
  other firms. When a sync is connected, CRM-owned fields are locked read-only.
- Match on the Zoho record ID stored as post meta, with the human reference ID
  as fallback. Never match on title.
- Zoho notification webhooks push changes; a nightly reconcile catches misses.
  Zoho wins for its columns, WordPress wins for its own.
- A record deleted or marked sold in Zoho never deletes the post. It moves to
  sold or archived and the lifecycle URL rules apply. New Zoho records arrive
  as drafts, or publish with the generated fallback copy the page supports.
- Photos import once into the media library and re-import only on file change.
  Zoho image downloads are authenticated; the site never hotlinks them.
- Leads go WordPress to Zoho through the CRM provider interface, with property
  reference, URL, source and UTM attached. The local `enquiry` copy is written
  first so a Zoho outage never loses a lead.

**Action for Lavenor.** Define the Zoho custom module to match the left column
above, with picklists for type, status, facing and possession and a lookup to
the advisor user, so the field map is one-to-one.

**Alternatives rejected.**
- WordPress as master for everything: the sales team works in Zoho, so prices
  and sold status would go stale and be double-entered.
- Zoho as master for everything: no media pipeline and no place for editorial
  or locality content; forces bad content into CRM fields.
- Two-way sync of property records: conflict handling costs more than it saves.

## 2026-11-11 — One listing post type with kinds; audit clean-up

**Context.** Lavenor sells developers' projects and the apartments inside them,
and may add standalone land or houses later. The plugin had a `property` type
the theme rendered and a `project` type nothing rendered, and the site's noun
("Properties"/"Projects") was a Customizer text.

**Decision.** One post type, `listing`, with a fixed `listing_kind` term:
`project` (a developer's venture), `apartment` (one unit, child of a project via
`post_parent`) or `property` (standalone). Bytenot › Setup switches kinds on,
names them, sets each kind's URL base and its sub-types (`listing_type` terms).
The first kind switched on is the primary kind: the menu, the home search and
the featured rail point to it. Addresses: `/{base}/{slug}/` per kind and
`/projects/{project}/{unit}/` for an apartment inside a project. A project's
price range, configurations, area range and unit count roll up from its
apartments whenever it has any. Meta keys moved from `pc_` to `bn_`; the dead
block layer (`blocks/`, bindings) is gone since the theme is classic PHP.

**Also settled by the audit.** Budget bands skip rentals on both server and
client; closed listings leave the sitemap; export and retry need
`manage_options`; CSV cells are formula-safe; the demo never overwrites a site
name, settings or menus it did not create; the phone country code is a Setup
setting, not `+91` in the theme; the inquiry form carries a real consent box.

**Alternatives rejected.** A separate post type per kind (three editors, three
archives, no shared fields or filters); apartments as rows inside a project
without their own pages (no unit-level URLs, enquiries or SEO).

## 2026-11-11 — Motion without a library; the people row as Customizer content

**Decision.** Page changes use the browser's cross-document View Transitions (a
`@view-transition` rule, the header pinned, a card photo growing into the listing
opening) and parallax uses CSS scroll-driven animations (`animation-timeline`),
both in `theme.css` only. No GSAP: its licence is not GPL-compatible, which would
keep the theme off WordPress.org, and it adds ~30 KB of script for effects the
browser now composites itself. Firefox and older browsers load pages and lay
them out exactly as before. Everything is off under `prefers-reduced-motion` and
the parallax is off under 721px.

**Decision.** The three people cards under the about statement are Customizer
settings (photo, name, role × 3), like the rest of the about section, rather
than Advisor posts: they are brand copy for the home page, not records the site
queries. A card shows when it has any of the three; a blank photo renders a
placeholder frame.

## 2026-11-11 — Listing page chrome: spine and advisor chip

**Decision.** The chapter pill bar under the header and the bottom bar are gone.
Chapters live on a spine down the left margin (a line that fills in gold as you
read, a dot and number per chapter, the active chapter named, the others named
on hover); the listing page's content keeps a 148px margin either side at 1100px
and up so the spine never overlaps it. Under 1100px the spine is a 3px progress
line at the top of the viewport. The call to action is an advisor chip bottom
right (photo, "Book a visit", the advisor's first name) that opens to call,
WhatsApp and Book a visit; on phones it opens upward. Chosen by the owner from
three prototypes in `design/prototype/listing-chrome.html` (the spine from C,
the chip from B).
