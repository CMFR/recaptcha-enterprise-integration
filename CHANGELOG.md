# Changelog

All notable changes to this plugin will be documented here.

## [1.2.0] – 2026-09-29
- Renamed to Integration for reCAPTCHA Enterprise (slug and folder unchanged)
- Added front-end protection for Contact Form 7 (spam filter) and User Registration (before the user is created), with server-side token verification
- Invisible mode gets a fresh token on every submit; Challenge mode adds the checkbox above the submit button
- Added Score Threshold setting for Invisible mode (default 0.5)
- Added Disclosure setting: hide the badge and add a message below forms (default), show the badge, or hide it and add your own message
- Added Settings and Documentation links on the Plugins screen
- Added `readme.txt`, `Requires at least` and `Requires PHP` headers, and screenshots
- Made all user-facing strings translatable
- Added `uninstall.php` to delete plugin options
- Settings page accessibility: field descriptions linked for screen readers, test results announced, notices stay until dismissed
- `$_POST` input is unslashed and `isset()`-checked; settings notices escaped; test messages use `textContent`

## [1.1.5] – 2026-09-28
- Fixed critical error when checking for updates: update checker `vendor/` files (Parsedown) were missing from releases
- Restricted the token verification REST endpoint to admins and stopped returning Google's full response on failure
- Added nonce check to Delete Settings
- Fixed Challenge mode test: loads the Enterprise script and adds a submit button
- Fixed unclosed API Key input on the settings page
- Removed duplicate `onClick()` and unused variables; test failures now show the REST error message
- URL-encoded project ID and API key in assessment requests
- Versioned plugin CSS/JS with `filemtime()` for cache busting

## [1.1.4] – 2025-10-31
- Removed old `inc/updater.php` file left over from initial setup  
- No functional changes beyond cleanup and housekeeping

## [1.1.3] – 2025-10-31
- Test release for confirming GitHub-based auto-updates

## [1.1.2] – 2025-10-31

- Added GitHub-based auto-updater
- Configured public repo integration
- Enabled release asset support for easier distribution  
- Cleaned up plugin admin UI and removed manual update link  
- Verified local updater integration

## [1.1.1] - 2025-06-04

### Fixed

- Unexpected output errors during plugin activation due to stray whitespace

## [1.1.0] – 2025-06-03

### Added

- Support for challenge-based reCAPTCHA token verification (reCAPTCHA Enterprise)

### Fixed

- Improved error messages when reCAPTCHA is not loaded
- Cleanly hides badge for invisible reCAPTCHA
- Allows saving settings even with empty fields
- Prevents scripts from loading if settings are incomplete

## [1.0.0] – 2025-05-08

- Initial release
