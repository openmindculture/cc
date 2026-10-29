# Seed data

`books.csv` holds the book data for local development, and possibly the production import later. Each row is one book.

- **Empty cells are unknown facts.** Fill them from the publisher pages or the professor's CV; never guess. The year 2022 for *El pensamiento basura* comes from a secondary source and is unverified, so it's left empty.
- `language` is a BCP 47 code (`es`, `en`, `fr`).
- `cover_file` is a file name to import with `wp media import`. Keep cover files **out of this public repository**: put them in the gitignored `data/` folder and reference them by name.
- `slug` is the stable key. The seed script updates an existing book by slug (or `isbn`) and never creates duplicates.

Test rows for awkward cases (long titles, no cover, other languages) belong in a separate `books-test.csv`, so they never reach production.
