<?php
/**
 * Plugin Name: reCAPTCHA Enterprise Integration
 * Plugin URI: https://github.com/CMFR/recaptcha-enterprise-integration
 * Description: Easily integrate Google reCAPTCHA Enterprise with WordPress.
 * Version: 1.0.0
 * Author: Clearinghouse for Military Family Readiness
 * Author URI: https://militaryfamilies.psu.edu
 * Author: Jaemie Gyurik
 * Author URI: https://github.com/jaemie
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: recaptcha-enterprise-integration
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'RECAPTCHA_ENTERPRISE_PATH', plugin_dir_path( __FILE__ ) );
define( 'RECAPTCHA_ENTERPRISE_URL', plugin_dir_url( __FILE__ ) );

// Include components.
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/settings-page.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/script-loader.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/rest-endpoints.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/token-verification.php';