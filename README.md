# Galeria Reverso — WordPress Site

Custom WordPress theme and plugin for [Galeria Reverso](https://galeriareverso.com/).

## Structure

```
plugin/     — Custom plugin: CPTs, shortcodes, business logic
theme/      — Divi child theme: CSS, JS, template customizations
template/   — CF7 contact form HTML template
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
