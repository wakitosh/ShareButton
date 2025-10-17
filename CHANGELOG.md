# Changelog

All notable changes to this project will be documented in this file.

This project follows semantic versioning. Dates use ISO-8601 (YYYY-MM-DD).

## [1.0.0] - 2025-10-18

Initial public release of ShareButton.

- Derived from the Omeka S "Sharing" module (no upstream changelog available)
- View helper: provide `shareButton` with backward-compatible alias `sharing`
- Block layouts (site and resource page): register only `sharebutton` in invokables and add alias `sharing` to avoid duplicate entries in the UI
- Controllers and routes: embed endpoints for items, media, and pages; oEmbed endpoint
- Assets: CSS and JS for share buttons
- i18n: translator configuration for gettext `.mo` files
- Module metadata: GPLv3 license, author (Toshihito Waki), repository links, tags
- Code style: cleaned and linted `config/module.config.php` (docblock, indentation, uppercase NULL)
- Compatibility: Omeka S ^4.0.0

