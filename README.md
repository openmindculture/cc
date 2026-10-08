# CC

CC is yet another WordPress project implementing the block theme child template.

Note that custom post types like `books` or `films` must be defined in a (mu-) plugin, not in the theme to ensure the data stays persistently and does not get deleted when switching themes!

## The Theme's Technological Basis

The current template defines a Twenty Twenty-Five child theme, based on the official WordPress twentytwentyfive default theme. We might want to change this to a Twenty Twenty-Six child theme in 2026 or 2027. A previous template version was based on Twenty Twenty-Three, following a similar concept.

### Theme Styles in CSS and theme.json

We use both `styles.css` and `theme.json` to define typography and a global color palette redundantly, so that content editors will only see the expected style options in the block editor.

### How to (re-)enable Full Site Editing (FSE)

You can delete the line `add_theme_support('block-templates', false);` in `inc/functions/customize-frontend.php` (required by `functions.php`) to reenable full site editing and use `parts/` and `patterns/` files instead of the classic `/header.php` and `footer.php` includes.

### License and Disclaimer

This code is free and open-source software without any warranty whatsoever. This template is subject to change or deletion at any time. You can fork, copy, use and modify its code at your own risk.

The theme depends on the officiel Twenty Twenty-Five WordPress theme, WordPress core blocks and functions. Any of those might also change in the feature or stop being supported. Use at your own risk!

## WordPress Block Theme Child Template

[wp_block_theme_child](https://github.com/openmindculture/wp_block_theme_child) is a local host WordPress setup using docker compose, and wp-cli
to install themes and plugins, including a simple template skeleton for a Twenty-something child
theme using the template engine (`theme.json`), block editor and full-site editing.

### Initial Setup

- `npm install`
- `npm start` (or `docker compose up --build`)
- `npm run open` and navigate to `/wp-admin` 
- or open http://localhost:8204/wp-admin (replace `8204` with the configured port) in your browser.
- Log in with the default demo credentials (user: `admin`, password: `secret`).

### Deployment

- zip and ship theme folder

see [doc/setup.md](doc/setup.md)

### Preview/Staging and going live

- preview system always `noindex`
- preview system always password protected (see `/demoserver`)
- _unless_ while restoring a backup to production
- wipe obsolete preview servers, return 410 gone status (see `/410_gone`)

### Development Requirements
- node 22+ => `nvm install 22 && nvm use 22`

### Production Server Requirements
- PHP 8.4+
- MySQL or MariaDB
- 1+ domain name
- ssh or (s)ftp or one-click WordPress installer