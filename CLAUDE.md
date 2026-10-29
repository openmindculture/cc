# CLAUDE.md

Instructions for Claude Code in this repository. Read this file, `README.md` and `DESIGN.md` at the start of every session.

## Project

WordPress child theme for the website of Professor Claudio Canaparo (cultural philosopher, Université de Bordeaux). It replaces the legacy static site `canaparo.org`.

- Parent theme: **Twenty Twenty-Five** (`themes/twentytwentyfive`, installed by the container, gitignored).
- Child theme: `themes/twentytwentyfive-child` (Theme Name "CC", text domain `twentytwentyfive-child`; never rename the text domain).
- Custom post types and their meta: `mu-plugins/cc-register-post-types.php`. **Never in the theme**, so content survives a theme switch (see README).
- Local site: http://localhost:8204. Admin: http://localhost:8204/wp-admin (local demo login documented in README).
- Architecture: **hybrid**. `theme.json` handles tokens; `header.php`, `footer.php` and `index.php` render the frame; block templates are switched off in `inc/functions/customize-frontend.php`; editors get a curated block list (`inc/functions/customize-backend.php`). Don't switch to full site editing unless asked. That is open decision §10.4 in DESIGN.md.

## Source of truth

`DESIGN.md` is the design specification, exported from a visual design canvas in a separate Claude session. It holds the exact values: tokens, type scale, layout, components, motion, the WordPress mapping (§7) and accessibility rules.

- Implement from `DESIGN.md`, not from screenshots or memory.
- **Never edit `DESIGN.md` from here.** It is regenerated on the design side. If something in it is impossible, contradictory or unclear, stop and say so; don't silently deviate.
- Its **Rejected** section is binding. Never reintroduce wine red / gold (`--color-darkpurple`, `--color-light` in the preview variables) or the Peter Lang cover styling.
- Square-bracket values in `DESIGN.md` are unknown facts (years, ISBNs, journals). Never invent them.

### When DESIGN.md changes

1. Read its **Changes** table and run `git log -p -1 -- DESIGN.md`.
2. List the affected files and propose a plan before editing.
3. Apply only what changed. Don't refactor unrelated code in the same pass.
4. Verify in the browser (see **Verification**), then summarise what changed and what you checked.

## Commands

```bash
npm start                 # docker compose restart
npm run stop              # docker compose stop
npm run eslint            # lint theme JS
npx stylelint "themes/twentytwentyfive-child/**/*.css"
php -l <file>             # syntax check, if PHP is installed on the host
docker compose logs -f wordpress   # PHP errors and notices (WORDPRESS_DEBUG=1)
```

**WP-CLI** runs in the separate `wp-cli` service. Its default command is the install script, so always pass the command explicitly:

```bash
docker compose run --rm wp-cli wp <command>
docker compose run --rm wp-cli wp cache flush
docker compose run --rm wp-cli wp post list --post_type=book
```

Caveat: the `wp-cli` service mounts only `./wp_data`. The theme (`./themes`) is mounted into the `wordpress` service only, and `./mu-plugins` is not mounted anywhere yet. Before relying on WP-CLI or the CPTs, check:

```bash
docker compose exec wordpress ls wp-content/mu-plugins wp-content/themes/twentytwentyfive-child
```

If they're missing, propose the `docker-compose.yml` change (mount `./themes` and `./mu-plugins` into both services) and wait for approval.

**Never run `npm run destroy`.** It runs `docker-compose down -v`, which deletes the database volume.

## Repository layout (relevant parts)

```
mu-plugins/cc-register-post-types.php   # CPT book, publication; taxonomy publication_type; post meta
themes/twentytwentyfive-child/
├── style.css            # theme header + CSS that theme.json cannot express
├── theme.json           # tokens and editor settings — first place to change
├── functions.php        # loads inc/functions/*
├── header.php, footer.php, index.php    # hybrid frame
├── (to add) front-page.php, archive-book.php, single-book.php, archive-publication.php, taxonomy-publication_type.php, search.php, 404.php
├── inc/functions/       # customize-frontend.php, customize-backend.php, redirect-historic-urls.php
├── blocks/custom-card/  # example custom block from the starter template
├── fonts/               # self-hosted woff2 (SIL OFL); remove fonts the design doesn't use
└── js/scripts.js        # menu toggle, share link
_preview-variables-twentytwentyfive-child.css   # IDE-only mirror of theme.json variables; keep in sync
seed/books.csv           # book data for the idempotent seed script (data/ is gitignored)
material/                # source images — see the copyright note below
```

## Conventions

- **theme.json first.** Colours, fonts, sizes and spacing come from presets (`var(--wp--preset--…)`). No hard-coded hex values in PHP templates or CSS, except in `theme.json` and the preview-variables file.
- **PHP templates** escape all output (`esc_html`, `esc_url`, `esc_attr`). Output post content with `the_content()` so editors' blocks render. Prefix new functions `cc_`. Follow WordPress coding standards; no closing `?>` in pure-PHP files.
- **Editors edit content, not layout.** New editor-facing features go through block styles, patterns, post meta panels or the menu, not free-form layout blocks. If a design element needs a block that is on the disallowed list (Cover, Quote, Navigation, …), say so rather than quietly re-enabling it.
- **No page builders, no ACF or field plugins.** Use `register_post_type`, `register_post_meta` (`show_in_rest: true`) in the mu-plugin.
- **CSS in `style.css`** only for what theme.json can't express: the printed-image blend, `font-stretch`, sticky field, view transitions, `prefers-reduced-motion`, header and index layout. Keep it small and commented. The old starter rules (Manrope, 992 px, coloured header) are to be replaced, not extended.
- **No JavaScript** unless a feature truly needs it. All motion is CSS (`DESIGN.md` §6).
- **Fonts self-hosted.** Nothing loaded from Google Fonts or other CDNs (GDPR).
- **Never modify the parent theme** or WordPress core.

## Data

- `seed/books.csv` holds book data. The seed script must be idempotent: update by `isbn` or slug, never duplicate.
- Include awkward cases on purpose, clearly marked as test rows: long titles, missing covers, Spanish, English and French entries, no DOI, several items in one year.
- **This repository is public.** Don't commit cover images, artwork or unpublished client information. `material/` already contains cover scans; flag this to the developer rather than adding more.

## Verification (Claude in Chrome)

Browser checks need Claude Code started with `claude --chrome`; a project setting cannot enable it. Use a new tab, and only on http://localhost:8204.

For each changed template, check:

1. **Viewport widths 390, 768, 1280 and 1440 px.**
   - Resize the window, then confirm the real width with `window.innerWidth` before judging the layout. The window is wider than the viewport.
   - No horizontal scroll at any width.
   - At 820 px and below, the index column stacks above main.
2. **Console and PHP:**
   - No console errors from the theme.
   - No PHP notices in `docker compose logs wordpress`, since `WORDPRESS_DEBUG` is on.
3. **Against `DESIGN.md`:** compare computed values, not impressions. Use `getComputedStyle` for colours, font family and size, `font-stretch`, max width and gutters.
4. **Accessibility:**
   - Tab through the page: skip link, visible teal focus ring, logical order.
   - One `h1` per page; `aria-current` on the nav.
   - `lang` set on Spanish and French passages; alt text present.
5. **Reduced motion:** with motion reduced, nothing animates.
6. **Editor:** open an affected post in the block editor. The editor styles (`add_editor_style('style.css')`) should match the front end closely enough, with no block validation errors.

Report results as a short checklist with the widths tested. Save screenshots only when asked.

Don't click anything in wp-admin that deletes, resets or publishes content without asking first, and avoid buttons that open browser alert or confirm dialogs.

## Boundaries

- **Ask before:**
  - installing plugins or npm packages
  - changing `docker-compose.yml`
  - running database-changing WP-CLI commands other than the seed script (`wp db …`, `search-replace`, `post delete`, …)
- **Never:**
  - run `npm run destroy` or `docker compose down -v`
  - read or print `.env`, keys or credentials
  - push, force-push or rewrite git history
  - change anything outside this repository
- Commit only when asked, with clear messages. Work on a branch, not `main`.
