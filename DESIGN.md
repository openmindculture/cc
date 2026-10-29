# DESIGN.md — Claudio Canaparo website

Design source of truth for the WordPress block child theme.
Canvas: "Canaparo Website Design", page **v2 · Index**. Version **v2.1**, 29 October 2026.

> For coding sessions: implement from this file, not from screenshots. Values here are exact.
> When this file changes, read the **Changes** section and `git diff` and apply only what changed.

---

## Changes

| Version | Date | Change |
|---|---|---|
| v2.1 | 2026-10-29 | §7 rewritten for the actual repo: hybrid theme (classic header/footer), CPTs in the mu-plugin, meta names aligned (`isbn` stays the print ISBN), publication meta added, legacy redirects. §10: architecture and language questions added. No visual changes. |
| v2.0 | 2026-10-29 | First handoff. Direction "Index": monochrome text, one pastel field per page. Replaces v1 (wine & gold), see *Rejected*. |

---

## 1. Concept

Canaparo is a cultural philosopher whose work deals with representation. His central idea, "geo-epistemology", holds that spaces and identities exist only through images, maps and texts. The site should be quiet, typographic and long-lived, and let the content speak.

Three rules:

1. **Text is ink.** Every letter is near-black on white or on a pastel field. There is no coloured text and no house colour.
2. **Colour is a field.** Each page has one pastel background field, taken from the Bruno Latour palette the client liked and made accessible by using it only behind text, never as text.
3. **Images are printed into the field.** Artwork is shown in greyscale, multiplied into the page's pastel. Book covers keep their own colours.

Images are meant to be read, not to decorate. Captions are hidden behind a "What is this?" disclosure.

### References and what was taken from each

| Reference | Taken | Not taken |
|---|---|---|
| bruno-latour.fr | Pastel palette; vertical index navigation separated by hairlines | Coloured text (fails contrast) |
| susanamonso.com | Fixed background field with content scrolling over it; colour shift between pages | The strongly coloured photography |
| Harun Farocki, Eduardo Restrepo | Text-first, archival, timeless academic restraint | — |
| Peter Lang covers / site | Nothing (see Rejected) | Wine red, gold, cover typography |

### Rejected (do not reintroduce)

- **Wine #4D2432 + gold #DBBE84 (v1).** This is Peter Lang's series design and belongs to the publisher, not the author. It was made for print and looks heavy on screen, and it would clash with future covers from other publishers.
- **Cream paper + classic Garamond.** Generic.
- **Latour red #D01F51, Sedona #CC6600 as text colours.** They fail or barely pass contrast and are too loud.

---

## 2. Colour

### Core

| Token (theme.json slug) | Hex | Use | Contrast |
|---|---|---|---|
| `ink` | `#1E1E1C` | All text, links, rules, outline buttons | 16.7:1 on white, ≥ 11.4:1 on every field |
| `ink-quiet` | `#55544E` | Metadata, captions, secondary text | 7.6:1 on white, ≥ 5.18:1 on every field |
| `base` | `#FFFFFF` | Page ground. Plain white, not cream. | — |
| `hairline` | `#CFCDC6` | Row dividers. Decorative only, never the sole indicator of anything. | 1.6:1 (decorative) |
| `focus` | `#026576` | Focus ring and link hover **only**. Never body text. | 6.7:1 on white, ≥ 4.59:1 on fields (needs ≥ 3:1 as a non-text indicator) |

### Fields (backgrounds only, never text)

| Token | Hex | Derived from | Ink on it |
|---|---|---|---|
| `field-sage` | `#DEDFC8` | Latour backdrop | 12.3:1 |
| `field-stone` | `#DAD5C8` | Latour beige | 11.4:1 |
| `field-lagoon` | `#CFDFDD` | Latour teal | 12.1:1 |
| `field-rose` | `#EED6DB` | Latour magenta | 12.2:1 |
| `field-apricot` | `#F1DCC4` | Sedona tawny | 12.5:1 |

Rules:

- **One field per page.** The mockups use sage on Home, stone on the Books index and rose on a single Book.
- The editor chooses the field per page (see section 7).
- Text on a field is always `ink` or `ink-quiet`. Nothing else.
- Open question: the client may reduce five fields to two or three.

---

## 3. Typography

| Role | Family | Settings |
|---|---|---|
| Display: name, page titles, index nav, quotations | **Archivo** (variable, `wdth` 62–125, `wght` 100–900) | `font-stretch: 62%` at display sizes, `75%` for nav and titles in lists; weight 300 for display, 400 for lists, 600 for the current nav item |
| Reading text | **Spectral** | 300 / 400 / 500, italic 400 |
| Labels, metadata, buttons | Archivo, normal width | 13 px caps, `letter-spacing: .1em`, weight 500 |

Both families are SIL OFL. Self-host subset woff2 (Latin + Latin Extended, for Spanish and French) through `theme.json` `fontFace`. Preload Spectral 400 only.

Archivo is unrelated to the cover typography on purpose. Spectral is a screen-first serif, quieter than Garamond.

### Scale (desktop → mobile)

| Use | Size / line height | theme.json suggestion |
|---|---|---|
| Name on Home | 168 px / 0.86, `letter-spacing: -.015em` → 84 px on mobile, 72 px tablet | `clamp(4.5rem, 11vw, 10.5rem)` |
| Name on inner pages (header) | 64 px / 0.9 | `4rem` |
| Archive title (e.g. "Books") | 112 px / 0.85 | `clamp(4rem, 8vw, 7rem)` |
| Single title (book) | 96 px / 0.88 → 64 px | `clamp(4rem, 7vw, 6rem)` |
| Pull quotation | 54 px / 1.05, Archivo 70% wdth 300 → 34 px | `clamp(2.125rem, 4vw, 3.375rem)` |
| Subtitle (italic Spectral) | 24 px / 1.3 | `1.5rem` |
| Intro paragraph | 24 px / 1.5 → 20 px | `clamp(1.25rem, 1.8vw, 1.5rem)` |
| Body | 20 px / 1.6, measure ≈ 34 em | `1.25rem` |
| List title (books index) | 28 px / 1.1, Archivo 75% | `1.75rem` |
| Index nav | 22 px, Archivo 75% → 24 px on mobile | `1.375rem` |
| Meta / small UI | 15 px | `0.9375rem` |
| Labels | 13 px caps, tracked .1em | `0.8125rem` |

### Links

- `ink`, underlined, `text-underline-offset: .2em`, 1 px thick.
- Hover: colour `focus`, 2 px underline.
- Focus visible: `outline: 3px solid #026576; outline-offset: 3px`.
- Never remove underlines from links in running text. Navigation and title links may drop the underline.

---

## 4. Layout

- **Max width** 1280 px. **Page gutter** 40 px desktop, 16 px mobile: `clamp(1rem, 4vw, 2.5rem)`.
- **Grid** 12 columns, 24 px column gap.
  - **Index column**: columns 1–3, holding the vertical nav.
  - **Main**: columns 5–12 (column 4 empty).
  - Below about 820 px everything stacks to one column.
- **Reading measure** about 34 em (`contentSize: 42rem` works).
- **Vertical rhythm**: spacing steps 8 / 16 / 24 / 40 / 64 / 104 px. Section gaps are 104–120 px on desktop and 56–64 px on mobile.
- **Rules**:
  - A 1 px `ink` rule opens a list or section.
  - 1 px `hairline` rules separate rows.
- **Shapes**: square corners everywhere. No shadows, gradients, rounded cards or pills.

---

## 5. Components

### Header
- **Home:** search top right; the name very large below it; under the name, "Professor / Université de Bordeaux" in `ink-quiet` at the top of the index column.
- **Inner pages:** name at 64 px, top left, linking home; search at right.
- **Search:** an input with a bottom border only, with an icon button inside (44 × 44 px, `aria-label="Search"`).
- **Mobile:** a top bar with an "Index" text button on the left and a search icon on the right, separated by an `ink` rule, then the name at 84 px. The full index is repeated at the bottom of the page.

### Index navigation
- Vertical list in the index column. `ink` rule on top, `hairline` between items.
- Archivo 75% width, 22 px, no underline, 9 px vertical padding.
- Current page: weight 600 plus `aria-current="page"`.
- Items: Books · Articles · Chapters · Edited volumes · Media · Notes · About. Confirm with the client; "Reviews" or "Translations" may replace one.
- On single pages, add "← All books" below the list.

### Field block
- A full-width or main-column block with a pastel background; text on it is `ink`.
- Used for: the Home image, the Home quotation band (full bleed, image on the right 46%), the Books archive title block, and the Book hero (cover + title).

### Printed image
```css
.is-style-printed img {
  filter: grayscale(1) contrast(1.1);
  mix-blend-mode: multiply;
  object-fit: cover;
}
```
- The parent must have the field background.
- Works for any photograph or painting: it becomes a duotone of the page's pastel, which unifies images that differ widely.
- **Not** applied to book covers.

### "What is this?" caption
- A `<details>` under the image. The summary text is "What is this?" (on a book page: "About the cover").
- Content: `[Title], [Artist], [Year], [medium]`, plus an optional sentence. Archivo 15 px summary, 44 px minimum touch target.

### Recently list (Home)
- An `<ol>` of rows on a three-column grid: year (label), type (label), title link plus source in `ink-quiet`. Mixes books, articles, media and notes, newest first.
- Mobile: two columns, year | type over title.

### Books index row
- Grid columns: 72 px cover | title + italic subtitle | publisher (150 px) | year (70 px).
- Missing cover: a 72 × 108 px block in the page's field colour. No fallback artwork or typography.
- Sorting links "Newest first · Oldest first · A–Z" and view links "List · Covers". The covers grid view is not yet designed.

### Book metadata
- A `<dl>` in the left column of the content area (3 of 8 columns): Publisher, Year, Series, Pages, ISBN (print / eBook), DOI.
- Labels as tracked caps, values 18 px Spectral.

### Disclosures (Book)
- `<details>` for Contents, Cite and Reviews. Summary in Archivo 75% at 24 px, `ink` rule above the first one, `hairline` between them.
- **Cite** contains a Chicago-style citation, a "Copy" button, and BibTeX and RIS links.

### Buttons
- Outline only: 1 px `ink` border, 48 px minimum height, 20–22 px horizontal padding, Archivo 15 px.
- External links get " ↗".
- No filled buttons.

### Footer
- `ink` rule on top.
- Left: "© [year] Claudio Canaparo · artwork © the artists" in `ink-quiet`.
- Right: Contact · Imprint · Privacy policy · Sitemap.

---

## 6. Motion

All motion goes inside `@media (prefers-reduced-motion: no-preference)` and `@supports` checks, and uses no JavaScript. Without support the page is simply static.

1. **Fixed field.** On the Home image and quotation band, the field and image are `position: sticky` while the text column scrolls over them. The image drifts slightly slower using `animation-timeline: view()`.
2. **Page-to-page colour shift.** `@view-transition { navigation: auto; }`. The field element gets `view-transition-name: field`, so its pastel cross-fades to the next page's. Supported in Chrome, Edge and Safari 18+; elsewhere pages just load normally.
3. **No other motion:** no hover animations, parallax on text, or loaders.

---

## 7. WordPress implementation map

Repository: `openmindculture/cc`. Child theme `themes/twentytwentyfive-child` (parent **Twenty Twenty-Five**). Custom post types live in `mu-plugins/cc-register-post-types.php`, not in the theme, so content survives a theme switch.

### Architecture: hybrid theme (current repo) — decision pending

The repo is a **hybrid** theme:
- `theme.json` drives tokens and editor settings.
- `header.php`, `footer.php` and `index.php` render the frame.
- Block templates and template parts are switched off.
- Editors only get a curated list of content blocks. Navigation, Template Part, Cover and Quote are disallowed.

This design works with either architecture:

- **A. Keep hybrid (recommended for now).** PHP templates render the frame and the archive and book layouts. Editors edit content blocks, the menu and per-page settings, not layout. This is the least risky route for a client who should add content, not redesign pages.
- **B. Switch to full site editing.** Re-enable block templates and move the frame into `templates/*.html` and `parts/*.html`. This gives more editor freedom and requires allowing the Navigation and Template Part blocks.

The table assumes **A**. The FSE equivalent is noted where it differs.

| Design element | Implementation (A, hybrid) | If B (FSE) |
|---|---|---|
| Colours, fonts, sizes, spacing, widths | `theme.json`: replace the old palette with the slugs in §2, `fontFamilies` + `fontFace` (Archivo, Spectral; remove unused fonts), fluid `fontSizes`, spacing 8–104, `layout.contentSize: 42rem`, `wideSize: 80rem`. Update `_preview-variables-twentytwentyfive-child.css` to match. | same |
| `style.css` | Rewrite: the old template rules (Manrope, 992 px, coloured header) go. Keep only what theme.json can't express (§6, printed image, `font-stretch`, header and index layout). | same, smaller |
| Header (home + inner) | `header.php`: `is_front_page()` switches between the large name and the 64 px name. Search is a plain GET form. Remove the logo image and the coloured header row. | `parts/header*.html` |
| Index navigation | `wp_nav_menu( [ 'theme_location' => 'primary' ] )`. The location is already registered; editors manage it in Appearance → Menus. Replaces `wp_list_pages`. | Navigation block |
| Footer | `footer.php`: copyright + Contact · Imprint · Privacy policy · Sitemap. Decide whether the share links stay; they aren't in the design. | `parts/footer.html` |
| Front page | `front-page.php`: intro (page content), field image, quotation band, Recently list (`WP_Query` across `book`, `publication`, `post`). | `templates/front-page.html` + Query Loop |
| Books index | `archive-book.php`, with the sort links as `?orderby=` handled in `pre_get_posts` | `templates/archive-book.html` |
| Single book | `single-book.php`: meta `<dl>` from post meta, abstract = excerpt + content, Contents and Reviews as Details blocks in the content, Cite generated in PHP | `templates/single-book.html` + Block Bindings |
| Publications | `archive-publication.php` + `taxonomy-publication_type.php`, grouped by year | block templates |
| Field per page | Post meta `cc_field` (enum of the 5 slugs) on page, post, book and publication, with a small editor panel. The `body_class` filter (already in `customize-frontend.php`) adds `has-field-{slug}`, and CSS sets `--field`. | same |
| Field block | Block style "Field" on Group (`background: var(--field)`) | same |
| Printed image | Block style "Printed" on Image | also on Cover, if allowed |
| "What is this?" | Pattern: Image + Details. Details is already allowed. The existing "Weniger" close-button filter must be made language-aware or removed. | same |
| Books (data) | CPT `book` (mu-plugin). Meta: `subtitle`, `year`, `publisher`, `series`, `series_volume`, `language`, `pages`, `isbn` (print), `isbn_ebook`, `doi`, `publisher_url`, `cover_credit`. Cover = featured image. Abstract = excerpt. schema.org `Book` JSON-LD in `wp_head`. | Block Bindings for meta in templates |
| Publications (data) | CPT `publication` + taxonomy `publication_type` (Articles, Chapters, Edited volumes, Reviews) in the mu-plugin. Meta: `year`, `container_title` (journal or book), `volume`, `issue`, `pages_range`, `publisher`, `language`, `doi`, `url`. | same |
| Media, Notes | Regular posts with categories | same |
| Legacy URLs | `inc/functions/redirect-historic-urls.php`: map the old `canaparo.org/Publications/*.shtml` and `/CC/*.shtml` pages to the new archives | same |

### theme.json starting point

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "color": {
      "defaultPalette": false, "defaultGradients": false, "custom": false,
      "palette": [
        { "slug": "ink", "name": "Ink", "color": "#1E1E1C" },
        { "slug": "ink-quiet", "name": "Ink, quiet", "color": "#55544E" },
        { "slug": "base", "name": "White", "color": "#FFFFFF" },
        { "slug": "hairline", "name": "Hairline", "color": "#CFCDC6" },
        { "slug": "focus", "name": "Focus", "color": "#026576" },
        { "slug": "field-sage", "name": "Field: sage", "color": "#DEDFC8" },
        { "slug": "field-stone", "name": "Field: stone", "color": "#DAD5C8" },
        { "slug": "field-lagoon", "name": "Field: lagoon", "color": "#CFDFDD" },
        { "slug": "field-rose", "name": "Field: rose", "color": "#EED6DB" },
        { "slug": "field-apricot", "name": "Field: apricot", "color": "#F1DCC4" }
      ]
    },
    "layout": { "contentSize": "42rem", "wideSize": "80rem" },
    "spacing": {
      "spacingSizes": [
        { "slug": "20", "name": "8",   "size": "0.5rem" },
        { "slug": "30", "name": "16",  "size": "1rem" },
        { "slug": "40", "name": "24",  "size": "1.5rem" },
        { "slug": "50", "name": "40",  "size": "2.5rem" },
        { "slug": "60", "name": "64",  "size": "4rem" },
        { "slug": "70", "name": "104", "size": "6.5rem" }
      ]
    },
    "typography": {
      "fontFamilies": [
        { "slug": "display", "name": "Archivo", "fontFamily": "Archivo, system-ui, sans-serif" },
        { "slug": "body", "name": "Spectral", "fontFamily": "Spectral, Georgia, serif" }
      ]
    }
  },
  "styles": {
    "color": { "background": "var(--wp--preset--color--base)", "text": "var(--wp--preset--color--ink)" },
    "typography": { "fontFamily": "var(--wp--preset--font-family--body)", "fontSize": "1.25rem", "lineHeight": "1.6" },
    "elements": {
      "link": {
        "color": { "text": "var(--wp--preset--color--ink)" },
        ":hover": { "color": { "text": "var(--wp--preset--color--focus)" } }
      },
      "heading": { "typography": { "fontFamily": "var(--wp--preset--font-family--display)", "fontWeight": "300" } }
    }
  }
}
```

`fontFace` entries, font sizes, `font-stretch` per element and block styles are still to be added. `custom: false` keeps the client from picking off-palette colours.

---

## 8. Accessibility (WCAG 2.2 AA)

- Contrast as listed. No text in `hairline` or field colours.
- A skip link to `#main`.
- One `h1` per page. On Home it is visually hidden: "Claudio Canaparo — books, essays and notes".
- Touch targets at least 44 × 44 px.
- `aria-current="page"` in the nav.
- Icon-only buttons carry `aria-label`.
- Mark language changes with `lang="es"`, `lang="fr"` on the element (titles, abstracts, quotations).
- Alt text: describe the artwork. Covers: "Cover of [Title]: [short description]". Decorative duplicates get `alt=""`.
- All motion respects `prefers-reduced-motion`.

---

## 9. Content and data

- **Placeholders** in square brackets in the mockups (`[Year]`, `[Journal]`) are unknown facts. Never invent values.
- **Known books (all Peter Lang):**
  - *Bios politikos: Viviente y no-viviente en la especulación*
  - *El pensamiento basura: Transitoriedad, materia, viaje y mundo periférico*
  - *El mundo Ingaramo*
  - *El imaginario Patagonia: Ensayo acerca de la evolución conceptual del espacio*
- **Unverified:** the year 2022 for *El pensamiento basura* (from a secondary German assessment). Confirm with the client.
- **Back catalogue:** 30–45 books expected. Seed from `seed/books.csv` (`data/` is gitignored in this repo) with an idempotent WP-CLI script (update by ISBN or slug).
- **Artwork:**
  - The images in the mockups are stand-ins cut from cover art: the portrait from *Bios politikos*, the train from *El imaginario Patagonia*.
  - The professor's own visual works (conceptual art, visual poetry) are the preferred source, if he can supply them.
  - Every artwork needs a credit and permission.
- **Tone:** dry and precise, with room for understated irony in small copy such as "What is this?" and the 404 page. No jokes in headings.

---

## 10. Open questions

1. Can the professor supply his own visual works?
2. Five fields, or reduce to two or three?
3. The index menu items: confirm the four publication categories.
4. Keep the hybrid architecture (A) or switch to full site editing (B)? See §7.
5. Languages: Spanish only, or Spanish and English (and French)? This affects whether a multilingual plugin is needed.
6. Site language and legal pages: the starter footer is German (Impressum, Datenschutz, "Weniger"). Which language is the interface, and which are the legal pages?
7. Not yet designed: the covers grid view, Publications archive v2, the Media and Notes single templates, About, search results and 404.
