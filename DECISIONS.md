# Decisions

Short log of choices that are expensive to reverse. Newest first.

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
- The `property` post type in `property-core` is the canonical record for the
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
