# Changelog

All notable changes to this plugin will be documented here.

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
