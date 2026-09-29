=== Integration for reCAPTCHA Enterprise ===
Contributors: jaemiegyurik
Tags: recaptcha, captcha, spam, contact form 7, user registration
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Protect Contact Form 7 and User Registration forms with Google reCAPTCHA Enterprise, in Invisible (score) or Challenge (checkbox) mode.

== Description ==

Integration for reCAPTCHA Enterprise connects your WordPress site to Google reCAPTCHA Enterprise and checks every protected form submission with Google before it is accepted.

= Features =

* Invisible (score-based) and Challenge (checkbox) modes
* Protects Contact Form 7 and User Registration forms with no changes to the forms
* Server-side token verification with an adjustable score threshold
* Badge options: show the Google badge, hide it and add a message below each form, or hide it and add your own message
* Built-in test for each mode on the settings page

= External Services =

This plugin connects to Google reCAPTCHA Enterprise to tell people from bots.

* Once the plugin is configured, every front-end page loads `https://www.google.com/recaptcha/enterprise.js`, which sends Google information about the visitor and their interaction with the page.
* When a protected form is submitted, the site sends the reCAPTCHA token, the expected action and your site key to the reCAPTCHA Enterprise API (`https://recaptchaenterprise.googleapis.com`) to get an assessment.

Google's [Terms of Service](https://policies.google.com/terms) and [Privacy Policy](https://policies.google.com/privacy) apply.

== Installation ==

1. Upload the `recaptcha-enterprise-integration` folder to `/wp-content/plugins/`, or install the plugin from the Plugins screen.
2. Activate the plugin.
3. Go to Settings → reCAPTCHA and enter your Project ID, API Key and Site Key.
4. Pick the Version that matches your key: Invisible for a score-based key, Challenge for a checkbox key.
5. Use the Test reCAPTCHA button to confirm the settings work.

== Frequently Asked Questions ==

= How do I get my Project ID, API Key and Site Key? =

* Project ID: In the Google Cloud Console, create or select a project. The Project ID is shown on the project dashboard.
* API Key: Go to APIs & Services → Credentials, click Create Credentials → API Key, then restrict the key to the reCAPTCHA Enterprise API.
* Site Key: Go to reCAPTCHA Enterprise, click Create Key, and choose a score-based key for Invisible mode or a checkbox key for Challenge mode. Add your site's domain to the key.

= Which forms are protected? =

Contact Form 7 and User Registration forms. Other forms are not protected.

= Can I use reCAPTCHA v2 or v3 keys? =

No. This plugin only works with reCAPTCHA Enterprise keys.

= Real visitors are being blocked in Invisible mode. What should I do? =

Lower the Score Threshold on the settings page. New keys and low-traffic sites can get low scores until Google has seen enough traffic.

= Can I hide the reCAPTCHA badge? =

Yes. Google allows hiding the badge as long as the site says it is protected by reCAPTCHA. Choose "Hide badge, add message below forms" to have the plugin add that text, or "Hide badge, I'll add my own message" and add it yourself near your forms.

= How do I style the message below forms? =

The message is a `<p class="recaptcha-disclosure">` placed right after each protected form. Target `.recaptcha-disclosure` for the text and `.recaptcha-disclosure a` for the links. The plugin only sets a small top margin, font size and line height, so any theme rule on that class overrides it.

== Screenshots ==

1. Settings page with Invisible mode, score threshold and disclosure options.
2. The built-in test confirming the settings work.
3. The built-in test showing Google's error when a key is wrong.
4. A Contact Form 7 form with the reCAPTCHA message below it.

== Changelog ==

= 1.1.5 =
* Fixed critical error when checking for updates: update checker `vendor/` files (Parsedown) were missing from releases
* Restricted the token verification REST endpoint to admins and stopped returning Google's full response on failure
* Added nonce check to Delete Settings
* Fixed Challenge mode test: loads the Enterprise script and adds a submit button
* Fixed unclosed API Key input on the settings page
* Removed duplicate `onClick()` and unused variables; test failures now show the REST error message
* URL-encoded project ID and API key in assessment requests
* Versioned plugin CSS/JS with `filemtime()` for cache busting

= 1.1.4 =
* Removed old `inc/updater.php` file left over from initial setup

= 1.1.3 =
* Test release for confirming GitHub-based auto-updates

= 1.1.2 =
* Added GitHub-based auto-updater
* Cleaned up plugin admin UI and removed manual update link

= 1.1.1 =
* Fixed unexpected output errors during plugin activation due to stray whitespace

= 1.1.0 =
* Added support for challenge-based reCAPTCHA token verification
* Improved error messages when reCAPTCHA is not loaded
* Allowed saving settings even with empty fields
* Prevented scripts from loading if settings are incomplete

= 1.0.0 =
* Initial release
