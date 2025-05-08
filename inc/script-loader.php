<?php
// Enqueue Frontend Script for Admin Settings
function recaptcha_enterprise_enqueue_scripts($hook) {
    // Only enqueue on the reCAPTCHA settings page
    if ($hook !== 'settings_page_recaptcha-enterprise-settings') {
        return;
    }

    $site_key = get_option('recaptcha_enterprise_site_key', '');

    if ($site_key) {
        // Enqueue the Google reCAPTCHA Enterprise script
        wp_enqueue_script(
            'recaptcha-enterprise',
            'https://www.google.com/recaptcha/enterprise.js?render=' . esc_attr($site_key),
            array(),
            null,
            true
        );

        // Enqueue frontend scripts
        wp_enqueue_script(
            'recaptcha-enterprise-admin-scripts',
            RECAPTCHA_ENTERPRISE_URL . 'inc/js/admin-scripts.js',
            array(),
            null,
            true
        );
        wp_enqueue_script(
            'recaptcha-frontend',
            RECAPTCHA_ENTERPRISE_URL . 'inc/js/recaptcha.js',
            array('recaptcha-enterprise'),
            null,
            true
        );

        // Localize script with dynamic data
        wp_localize_script('recaptcha-frontend', 'recaptchaData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('recaptcha-enterprise/v1/'),
            'site_key' => $site_key
        ));
    }
}
add_action('admin_enqueue_scripts', 'recaptcha_enterprise_enqueue_scripts');