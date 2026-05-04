# Galeria Reverso — WordPress Site

Custom WordPress theme and plugin for [Galeria Reverso](https://galeriareverso.com/).

## Structure

```
plugin/
  includes/             — CPT definitions, shortcodes, REST endpoints

theme/
  functions.php         — enqueue scripts/styles, load components
  includes/
    css/                — base.css: global CSS vars, typography
    js/                 — scripts.js: global init (ResizeObserver, etc.)
  components/
    slider-intro/       — Homepage intro slider (exhibitions, shop slide)
      index.php         — shortcode + filters (post meta, atts, featured image)
      index.html        — Swiper markup template
      assets/
        css/            — Component styles and CSS vars
        js/             — init.js (Swiper setup, GSAP), shop.js (nested shop slider)

template/               — CF7 contact form HTML templates
```

## Deployment

Files are deployed via FTP directly to the server. There is no local WordPress installation.

- `plugin/` → `public_html/v2/wp-content/plugins/galeriareverso/`
- `theme/` → `public_html/v2/wp-content/themes/Divi-child/`

SFTP config (`.vscode/sftp.json`) is gitignored — credentials are not stored in this repo.

## Dependencies

- [Divi](https://www.elegantthemes.com/gallery/divi/) — parent theme
- [ACF](https://www.advancedcustomfields.com/) — custom fields
- [CF7](https://contactform7.com/) — contact forms
- [Plura plugin](https://github.com/plura/wp-plugin-plura) — shared utility plugin (symlinked, not modified here)
