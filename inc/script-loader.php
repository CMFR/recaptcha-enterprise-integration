<?php
// Enqueue Frontend Script for Admin Settings
function recaptcha_enterprise_enqueue_scripts($hook) {
	// Only enqueue scripts on the reCAPTCHA settings page
	if ($hook !== 'settings_page_recaptcha-enterprise-settings') {
		return;
	}

	// Always enqueue admin-specific scripts for settings page behavior
	wp_enqueue_script(
		'recaptcha-enterprise-admin-scripts',
		RECAPTCHA_ENTERPRISE_URL . 'inc/js/admin-scripts.js',
		array(),
		filemtime(RECAPTCHA_ENTERPRISE_PATH . 'inc/js/admin-scripts.js'),
		true
	);

	$site_key = get_option('recaptcha_enterprise_site_key', '');
	$recaptcha_version = get_option('cmfr_recaptcha_version', 'invisible');

	// Hide reCAPTCHA badge if version is invisible
	if ($recaptcha_version === 'invisible') {
		wp_add_inline_style(
			'recaptcha-enterprise-admin-styles',
			'.grecaptcha-badge { display: none !important; }'
		);
	}

	// Load reCAPTCHA and frontend integration scripts only if site key is set
	if ($site_key) {
		// Challenge keys auto-render .g-recaptcha; render= is for score (invisible) keys only
		$script_url = 'https://www.google.com/recaptcha/enterprise.js';
		if ($recaptcha_version === 'invisible') {
			$script_url .= '?render=' . rawurlencode($site_key);
		}

		wp_enqueue_script(
			'recaptcha-enterprise',
			$script_url,
			array(),
			null,
			true
		);

		wp_enqueue_script(
			'recaptcha-frontend',
			RECAPTCHA_ENTERPRISE_URL . 'inc/js/recaptcha.js',
			array('recaptcha-enterprise'),
			filemtime(RECAPTCHA_ENTERPRISE_PATH . 'inc/js/recaptcha.js'),
			true
		);

		wp_localize_script('recaptcha-frontend', 'recaptchaData', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'rest_url' => rest_url('recaptcha-enterprise/v1/'),
			'nonce'    => wp_create_nonce('wp_rest'),
			'site_key' => $site_key
		));
	}
}
add_action('admin_enqueue_scripts', 'recaptcha_enterprise_enqueue_scripts');