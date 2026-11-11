# Listing page chrome: three directions

Open `design/prototype/listing-chrome.html` (serve the prototype folder; `?v=a|b|c`, keys 1/2/3, or the A/B/C switch). Each works at 1440 and 390.

## A. Editorial index
- Three columns: a numbered chapter index (01–07, hairlines, navy + gold tick when active) sticky in the left margin; the page in the middle; a sticky deal card (price, per sq ft, EMI line, gold Book a visit, call/WhatsApp as text links, advisor) on the right. No pill bar, no bottom bar.
- Phones: the index becomes a quiet text row on the page sheet under the header; the deal card is a one-line bottom sheet (price + Book a visit) that expands on tap.
- Trade-offs: the most "editorial" and the calmest; the content column narrows (~900px at 1440), so figures get smaller; three sticky columns need care with very short viewports.
- Port: `single-listing.php` wraps glance→nearby in the 3-col grid, drops `.chapters`/`.prop-bar`; theme.css gets `.idx`, `.a-deal`, `.a-row`, `.a-sheet` and the ≤1100 rules; theme.js listing block keeps its spy (same `is-active`), adds the sheet toggle.

## B. One bar
- The header keeps one centre pill. Past the hero it crossfades from the site menu to the chapter list; a back arrow (or hovering the logo side) brings the menu back for a few seconds. CTA stays in the right bookmark.
- Bottom bar → a bottom-right advisor chip (round photo + "Book a visit") that expands to Call / WhatsApp / Book a visit on hover or tap.
- Phones: no centre pill there, so the chapters ride as a slim row on the header sheet; chip stays bottom-right.
- Trade-offs: zero extra bars, feels bespoke; the site menu is one step away on this page (acceptable for a page people arrive at from the listings); the chip is small on phones.
- Port: `header.php` gains the `.b-chap` layer inside `.nav` (only on singular listing); theme.css `.b-chap`, `.b-chip`, `.b-row`, the `.is-past-hero` crossfade; theme.js adds past-hero observer + menu timer + chip toggle; `.prop-bar` and `.chapters` removed from `single-listing.php`.

## C. Spine
- A 1px progress line fixed at the left edge with a dot per chapter; labels appear on hover and for the active chapter. Scrolled, the header's pill gives way to a slim strip: title · price on the left, Book a visit on the right.
- Phones: the spine becomes a 3px gold progress bar under the header; the strip docks at the bottom as one row.
- Trade-offs: lightest footprint and a strong sense of place in the page; chapter labels are hidden until hover (less discoverable), and the strip hides the site menu while scrolled.
- Port: `single-listing.php` adds `.spine`, `.c-strip`, `.c-bar`; theme.css the three blocks and the header pill hide; theme.js the progress (`--p`) in the existing scroll handler.

Shared in all three: the spy stays one function painting every `[data-chapter-links] a[data-chap]`; everything arrives on `html.is-past-hero` from one IntersectionObserver on the hero.
