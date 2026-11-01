# Design tokens

Source: `Lavenor Realty - Brand Guidelines.pdf` (October 2026). Implemented in
`design/prototype/style.css` `:root`; the same names go into `theme.json` when the theme is ported.
Brand values below are Lavenor's site content and must stay out of theme and plugin code.

## Colour

| Preset | Value | Role |
|---|---|---|
| `contrast`, `ink` | `#142F3F` | Brand blue. Dark surfaces, buttons, headings, body text |
| `contrast-hover` | `#1D3F54` | Hover on blue buttons |
| `primary` | `#F9EBB2` | Light gold. Solid accent fills, accent text on blue |
| `primary-dark` | `#897749` | Dark gold. Accent text and strokes on white (4.4:1, pair small links with underline) |
| gradient `brand-gold` | `135deg #F9EBB2 → #897749` | The logo gradient. Accent button hover, status dot |
| gradient `brand-gold-soft` | `135deg #F9EBB2 → #CDB97A` | Accent button at rest |
| `base` | `#FFFFFF` | Page |
| `ink-soft` / `ink-muted` | `#3F5465` / `#6B7F8C` | Secondary and tertiary text, blue-tinted |
| `surface` / `input` / `border` | `#F3F6F7` / `#F0F3F5` / `#DFE6EA` | Panels, fields, hairlines |

Photo shades use `rgba(20, 47, 63, …)` rather than black so overlays read as brand blue.

## Typography

Figtree variable (Google Fonts), `300..900` upright and italic.

| Weight | Guideline role | Prototype use |
|---|---|---|
| 300 Light | Small metadata | Italic accents in display lines |
| 400 Regular | Captions | Body copy (guide says SemiBold; kept Regular for screen readability) |
| 500 Medium | Labels | Labels, nav, buttons, ledger values |
| 600 SemiBold | Body | Headings and display titles |
| 700 Bold | Heading | Wordmark, active footer links |

## Logo

`design/prototype/assets/logo-mark.svg` is a traced placeholder of the gold logomark until the real
files arrive. Lockup: mark + "LAVENOR" 700 / "REALTY" 400 letter-spaced; wordmark blue on white,
white over blue, black or imagery. The mark doubles as favicon and as the faint `.brand-motif`
texture on blue panels.
