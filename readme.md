# reCAPTCHA Enterprise Integration

**Contributors:** Jaemie Gyurik, Clearinghouse for Military Readiness at Penn State  
**Tags:** reCAPTCHA, enterprise, security, spam protection, WordPress  
**Requires at least:** 6.0  
**Tested up to:** 6.8  
**Requires PHP:** 7.4  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Easily integrate Google reCAPTCHA Enterprise with your WordPress site for enhanced security and spam protection.

---

## Description

The **reCAPTCHA Enterprise Integration** plugin allows you to add Google reCAPTCHA Enterprise to your WordPress site for advanced bot protection. It provides a straightforward way to integrate reCAPTCHA verification into your forms, ensuring a secure user experience.

### Features

- Supports Google reCAPTCHA Enterprise
- Secure token verification via REST API
- Admin settings page for easy configuration
- Customizable action labels
- Error logging for better troubleshooting (with optional debug mode)
- Simple JavaScript hooks for testing

---

## Installation

1. Upload the `recaptcha-enterprise-integration` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings** → **reCAPTCHA Enterprise** to configure the plugin.

---

## Configuration

To use this plugin, you'll need the following:

- **Project ID**: Your Google Cloud project ID.
- **API Key**: A valid API key for the reCAPTCHA Enterprise API.
- **Site Key**: The site key associated with your project.

Refer to the [Google reCAPTCHA Enterprise Documentation](https://cloud.google.com/recaptcha-enterprise/docs) for detailed setup instructions.

---

## Changelog

### 1.0.0

- Initial release
- Added REST API support for token verification
- Implemented admin settings page
- Integrated debug mode for detailed error logging

---

## Frequently Asked Questions

### How do I get my Project ID, API Key, and Site Key?

#### **Project ID:**

1. Go to the **Google Cloud Console**: [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing project.
3. The **Project ID** is listed at the top of the project dashboard or in the **Project Settings**.

#### **API Key:**

1. In the **Google Cloud Console**, navigate to **APIs & Services** → **Credentials**.
2. Click **+ CREATE CREDENTIALS** and select **API Key**.
3. Copy the generated API Key.
4. (Recommended) Click **Restrict Key** to secure your API key:
   - Set **Application restrictions** to **None** (for now).
   - Under **API restrictions**, select **reCAPTCHA Enterprise API**.
   - Save the changes.

#### **Site Key:**

1. In the **Google Cloud Console**, navigate to **reCAPTCHA Enterprise**.
2. Click **+ CREATE KEY**.
3. Choose the appropriate reCAPTCHA type (e.g., reCAPTCHA v3).
4. Complete the setup and copy the generated Site Key.

### Can I use this plugin with reCAPTCHA v2 or v3?

No, this plugin is specifically designed for Google reCAPTCHA Enterprise.

### Is my API Key secure?

Yes, the API Key is securely stored in the WordPress database, but you should still follow best practices for securing your WordPress installation.

---

## License

This plugin is licensed under the GNU General Public License v2.0 or later.

---

## Upgrade Notice

### 1.0.0

Initial release with core functionality.

---

## Support

For support and feedback, please open an issue on the [GitHub repository](https://github.com/CMFR/recaptcha-enterprise-integration).
