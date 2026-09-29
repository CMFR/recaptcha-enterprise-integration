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
			'.grecaptcha-badge { visibility: hidden !important; }'
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

// Enqueue Front-End Form Protection
function recaptcha_enterprise_enqueue_frontend_scripts() {
	$site_key = get_option('recaptcha_enterprise_site_key', '');

	// Server-side checks pass everything until settings are complete, so skip the scripts too
	if (!$site_key || !get_option('recaptcha_enterprise_project_id', '') || !get_option('recaptcha_enterprise_api_key', '')) {
		return;
	}

	$recaptcha_version = get_option('cmfr_recaptcha_version', 'invisible');
	$render = $recaptcha_version === 'invisible' ? $site_key : 'explicit';

	wp_enqueue_script(
		'recaptcha-enterprise',
		'https://www.google.com/recaptcha/enterprise.js?render=' . rawurlencode($render),
		array(),
		null,
		true
	);

	wp_enqueue_script(
		'recaptcha-enterprise-frontend',
		RECAPTCHA_ENTERPRISE_URL . 'inc/js/frontend.js',
		array('recaptcha-enterprise'),
		filemtime(RECAPTCHA_ENTERPRISE_PATH . 'inc/js/frontend.js'),
		true
	);

	// Challenge mode shows Google branding in the widget, so the badge setting only applies to Invisible
	$disclosure = $recaptcha_version === 'invisible' ? get_option('recaptcha_enterprise_disclosure', 'form') : 'badge';

	wp_localize_script('recaptcha-enterprise-frontend', 'recaptchaFrontend', array(
		'site_key'   => $site_key,
		'version'    => $recaptcha_version,
		'disclosure' => $disclosure === 'form'
	));

	if ($disclosure === 'badge') {
		return;
	}

	// Google's FAQ specifies visibility: hidden; display: none can stop reCAPTCHA working
	// Single-class selectors so theme styles can override the message
	wp_register_style('recaptcha-enterprise-frontend', false);
	wp_enqueue_style('recaptcha-enterprise-frontend');
	wp_add_inline_style('recaptcha-enterprise-frontend', '.grecaptcha-badge { visibility: hidden !important; } .recaptcha-disclosure { margin: 0.75rem 0 0; font-size: 0.8125rem; line-height: 1.5; }');
}
add_action('wp_enqueue_scripts', 'recaptcha_enterprise_enqueue_frontend_scripts');
