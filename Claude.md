# CLAUDE.md

Instructions for Claude Code in this repository. Read this file and `DESIGN.md` at the start of every session.

> Placeholders in `[square brackets]` are for the developer to fill in once. Never guess them; ask.

## Project

WordPress **block child theme** for the website of Professor Claudio Canaparo (cultural philosopher, Université de Bordeaux).

- Parent theme: `[twentytwentyfive]`
- Child theme folder: `[wp-content/themes/canaparo]`
- Local site: `[http://localhost:8080]` (Docker)
- Admin: `[http://localhost:8080/wp-admin]`. Credentials are not stored here; ask if a login is needed.

## Source of truth

`DESIGN.md` is the design specification, exported from a visual design canvas in a separate Claude session. It holds the exact values: tokens, type scale, layout, components, motion, the WordPress mapping and accessibility rules.

- Implement from `DESIGN.md`, not from screenshots or memory.
- **Never edit `DESIGN.md` from here.** It is regenerated on the design side. If something in it is impossible, contradictory or unclear, stop and say so; don't silently deviate.
- Its **Rejected** section is binding. In particular, never reintroduce wine red / gold or the Peter Lang cover styling.
- Square-bracket values in `DESIGN.md` are unknown facts (years, ISBNs, journals). Never invent them.

### When DESIGN.md changes

1. Read its **Changes** table and run `git diff HEAD~1 -- DESIGN.md`, or diff against the last commit that touched it.
2. List the affected files (theme.json, parts, templates, style.css, functions.php) and propose a plan before editing.
3. Apply only what changed. Don't refactor unrelated code in the same pass.
4. Verify in the browser (see **Verification**), then summarise what changed and what you checked.

## Commands

All WordPress commands run inside the container:

```bash
docker compose up -d                                       # start
docker compose exec [wordpress] wp [command] --allow-root  # WP-CLI; drop --allow-root if not needed
docker compose exec [wordpress] wp cache flush --allow-root
docker compose exec [wordpress] wp eval-file wp-content/themes/[canaparo]/tools/seed.php --allow-root
docker compose logs -f [wordpress]                         # PHP errors
```

`[Fill in: service name, whether a separate wp-cli container is used, any make/npm scripts.]`

## Theme structure

```
[canaparo]/
├── style.css              # header (Template: [twentytwentyfive]) + CSS that theme.json cannot express
├── theme.json             # tokens and global styles — the first place to change
├── functions.php          # loads inc/*
├── inc/                   # cpt-book.php, cpt-publication.php, meta.php, field.php, schema.php, citations.php
├── parts/                 # header.html, header-home.html, footer.html, index-nav.html
├── templates/             # front-page, archive-book, single-book, archive-publication, taxonomy-publication_type, page, single, index, search, 404
├── patterns/              # e.g. printed image + "What is this?"
├── styles/                # optional style variations
├── assets/fonts/          # Archivo + Spectral, subset woff2 (SIL OFL)
└── tools/seed.php         # idempotent demo/real data import from data/books.csv
```

## Conventions

- **theme.json first.** Colours, fonts, sizes and spacing come from presets (`var(--wp--preset--…)`). No hard-coded hex values in templates or CSS, except in `theme.json` itself.
- **Block markup in templates and parts**, written as valid serialized block comments that the Site Editor can open without "unexpected content" warnings. No PHP templates except where a block cannot do the job.
- **Templates stay editable.** The client edits content in the Site Editor after handoff, so prefer patterns, block styles and template parts over hard-coded markup.
- **No page builders, no ACF or field plugins.** Use `register_post_type`, `register_post_meta` (`show_in_rest: true`) and the Block Bindings API.
- **PHP:** prefix everything `cc_`, escape all output, sanitise all meta, WordPress coding standards. No closing `?>`.
- **CSS in `style.css`** only for what theme.json can't express: the printed-image blend, `font-stretch`, sticky field, view transitions, `prefers-reduced-motion`. Keep it small and commented.
- **No JavaScript** unless a feature truly needs it. All motion is CSS (`DESIGN.md` §6).
- **Fonts self-hosted.** Nothing loaded from Google Fonts or other CDNs (GDPR).
- **Never modify the parent theme** or WordPress core.

## Data

- Content data lives in `data/books.csv` (and later `data/publications.csv`). The seed script must be idempotent: update by ISBN or slug, never duplicate.
- Include awkward cases on purpose: long titles, missing covers, Spanish, English and French entries, no DOI, several items in one year.
- Don't commit real cover images or artwork if the repository is public.

## Verification (Claude in Chrome)

Browser checks need Claude Code started with `claude --chrome`; a project setting cannot enable it. Use a new tab, and only on the local site.

For each changed template, check:

1. **Viewport widths 390, 768, 1280 and 1440 px.**
    - Resize the window, then confirm the real width with `window.innerWidth` before judging the layout. The window is wider than the viewport.
    - No horizontal scroll at any width.
    - At 820 px and below, the index column stacks above main.
2. **Console:** no errors or warnings from the theme. Filter the console output for the theme or for "error".
3. **Against `DESIGN.md`:** compare computed values, not impressions. Use `getComputedStyle` for colours, font family and size, `font-stretch`, max width and gutters.
4. **Accessibility:**
    - Tab through the page: skip link, visible teal focus ring, logical order.
    - One `h1` per page; `aria-current` on the nav.
    - `lang` set on Spanish and French passages; alt text present.
5. **Reduced motion:** with motion reduced, nothing animates.
6. **Site Editor:** open the changed template in Appearance → Editor and confirm there are no block validation errors.

Report results as a short checklist with the widths tested. Save screenshots only when asked.

Don't click anything in wp-admin that deletes, resets or publishes content without asking first, and avoid buttons that open browser alert or confirm dialogs.

## Boundaries

- **Ask before:**
    - installing plugins or Composer/npm packages
    - changing `docker-compose.yml`
    - running database-changing WP-CLI commands other than the seed script (`wp db reset`, `search-replace`, `post delete`, …)
- **Never:**
    - read or print `.env`, keys or credentials
    - push, force-push or rewrite git history
    - change anything outside this repository
- Commit only when asked, with clear messages. Work on a branch, not `main`.