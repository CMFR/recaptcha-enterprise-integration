<?php

/**
 * Plugin Name: reCAPTCHA Enterprise Integration
 * Plugin URI: https://github.com/CMFR/recaptcha-enterprise-integration
 * Description: Easily integrate Google reCAPTCHA Enterprise with WordPress
 * Version: 1.1.4
 * Author: Clearinghouse for Military Family Readiness
 * Author URI: https://militaryfamilies.psu.edu
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: recaptcha-enterprise-integration
 */

// Stop if someone tries to load this directly
if (! defined('ABSPATH')) {
	exit;
}

// =============================
// Plugin setup
// =============================

// Paths
define('RECAPTCHA_ENTERPRISE_PATH', plugin_dir_path(__FILE__));
define('RECAPTCHA_ENTERPRISE_URL', plugin_dir_url(__FILE__));

// Core includes
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/settings-page.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/script-loader.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/rest-endpoints.php';
require_once RECAPTCHA_ENTERPRISE_PATH . 'inc/token-verification.php';

// =============================
// GitHub updater
// =============================

// bring in the updater library first
require_once RECAPTCHA_ENTERPRISE_PATH . 'plugin-update-checker/plugin-update-checker.php';

// use the namespaced factory (v5)
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// set up the updater using the main plugin file
$cmfr_recaptcha_updater = PucFactory::buildUpdateChecker(
	'https://github.com/CMFR/recaptcha-enterprise-integration',
	__FILE__,
	'recaptcha-enterprise-integration'
);

// keep it on main branch
$cmfr_recaptcha_updater->setBranch('main');

// use release zips when available
$api = $cmfr_recaptcha_updater->getVcsApi();
if ($api) {
	$api->enableReleaseAssets();
}

// =============================
// Admin view cleanup
// =============================

// remove the "check for updates" link in the plugin list
add_filter('puc_manual_check_link-recaptcha-enterprise-integration', '__return_empty_string');
