# Changelog

All notable changes to this plugin will be documented here.

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
