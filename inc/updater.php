<?php
// GitHub auto-updater for reCAPTCHA Enterprise Integration plugin

if (! class_exists('Puc_v5_Factory')) {
	require_once RECAPTCHA_ENTERPRISE_PATH . 'plugin-update-checker/plugin-update-checker.php';
}

$cmfr_recaptcha_updater = Puc_v5_Factory::buildUpdateChecker(
	'https://github.com/CMFR/recaptcha-enterprise-integration/',
	__FILE__,
	'recaptcha-enterprise-integration'
);

// Public repo: no authentication needed
$cmfr_recaptcha_updater->setBranch('main');
