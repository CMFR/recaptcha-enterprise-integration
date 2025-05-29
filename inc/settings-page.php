<?php

// Ensure this file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue Admin Styles
function recaptcha_enterprise_enqueue_admin_styles($hook) {
	if ($hook !== 'settings_page_recaptcha-enterprise-settings') {
		return;
	}
	wp_enqueue_style(
		'recaptcha-enterprise-admin-styles',
		RECAPTCHA_ENTERPRISE_URL . 'inc/css/admin-styles.css',
		array(),
		null
	);
}
add_action('admin_enqueue_scripts', 'recaptcha_enterprise_enqueue_admin_styles');

// Register Settings Page
function recaptcha_enterprise_register_settings_page() {
	add_options_page(
		'reCAPTCHA Enterprise Integration',
		'reCAPTCHA',
		'manage_options',
		'recaptcha-enterprise-settings',
		'recaptcha_enterprise_settings_page'
	);
}
add_action( 'admin_menu', 'recaptcha_enterprise_register_settings_page' );

// Render Settings Page
function recaptcha_enterprise_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'recaptcha_enterprise_settings' );
		$site_key = sanitize_text_field( $_POST['recaptcha_enterprise_site_key'] );
		$project_id = sanitize_text_field( $_POST['recaptcha_enterprise_project_id'] );
		$api_key = sanitize_text_field( $_POST['recaptcha_enterprise_api_key'] );
		$recaptcha_version = in_array( $_POST['cmfr_recaptcha_version'], ['challenge', 'invisible'], true ) ? $_POST['cmfr_recaptcha_version'] : 'enterprise';

		if ( empty( $site_key ) || empty( $project_id ) || empty( $api_key ) ) {
			add_settings_error('recaptcha_enterprise_settings','settings_error','All fields are required.','error');
		} else {
			update_option( 'recaptcha_enterprise_site_key', $site_key );
			update_option( 'recaptcha_enterprise_project_id', $project_id );
			update_option( 'recaptcha_enterprise_api_key', $api_key );
			update_option( 'cmfr_recaptcha_version', $recaptcha_version );
			add_settings_error('recaptcha_enterprise_settings','settings_updated','Settings updated successfully.','updated');
		}
	}

	if ( isset( $_POST['submit_challenge_test'] ) && isset( $_POST['g-recaptcha-response'] ) ) {
		check_admin_referer( 'recaptcha_enterprise_settings' );
		$token = sanitize_text_field( $_POST['g-recaptcha-response'] );
		$api_key = get_option( 'recaptcha_enterprise_api_key' );
		$project_id = get_option( 'recaptcha_enterprise_project_id' );
		$site_key = get_option( 'recaptcha_enterprise_site_key' );

		$body = json_encode(array('event' => array('token' => $token, 'expectedAction' => 'login', 'siteKey' => $site_key)));
		$response = wp_remote_post(
			"https://recaptchaenterprise.googleapis.com/v1/projects/$project_id/assessments?key=$api_key",
			array('body' => $body,'headers' => array('Content-Type' => 'application/json'),'timeout' => 15)
		);
		if ( is_wp_error( $response ) ) {
			add_settings_error('recaptcha_enterprise_settings','challenge_test_error','Error connecting to reCAPTCHA API.','error');
		} else {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $response_body['tokenProperties']['valid'] ) && $response_body['tokenProperties']['valid'] === true ) {
				add_settings_error('recaptcha_enterprise_settings','challenge_test_success','reCAPTCHA verified successfully.','updated');
			} else {
				add_settings_error('recaptcha_enterprise_settings','challenge_test_fail','reCAPTCHA verification failed.','error');
			}
		}
	}

	settings_errors( 'recaptcha_enterprise_settings' );
	$site_key = get_option( 'recaptcha_enterprise_site_key', '' );
	$project_id = get_option( 'recaptcha_enterprise_project_id', '' );
	$api_key = get_option( 'recaptcha_enterprise_api_key', '' );
	$recaptcha_version = get_option( 'cmfr_recaptcha_version', 'enterprise' );
?>
	<div class="wrap recaptcha-wrap">
		<h1>reCAPTCHA Enterprise Integration</h1>
		<p class="instructions">
			To use this plugin, you'll need to set up reCAPTCHA Enterprise in the Google Cloud Console. 
			Visit the <a href="https://cloud.google.com/recaptcha-enterprise/docs" target="_blank" rel="noopener noreferrer">Google reCAPTCHA Enterprise documentation</a> for instructions.
		</p>
		<form method="post">
			<?php wp_nonce_field( 'recaptcha_enterprise_settings' ); ?>
			<table class="form-table">
                <tr>
                    <th><label for="recaptcha_enterprise_project_id">Project ID</label></th>
                    <td><input type="password" name="recaptcha_enterprise_project_id" id="recaptcha_enterprise_project_id" value="<?php echo esc_attr( $project_id ); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="recaptcha_enterprise_api_key">API Key</label></th>
                    <td><input type="password" name="recaptcha_enterprise_api_key" id="recaptcha_enterprise_api_key" value="<?php echo esc_attr( $api_key ); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="recaptcha_enterprise_site_key">Site Key</label></th>
                    <td><input type="password" name="recaptcha_enterprise_site_key" id="recaptcha_enterprise_site_key" value="<?php echo esc_attr( $site_key ); ?>" required></td>
                </tr>
                <tr>
                    <th><label>Version</label></th>
                    <td>
                        <?php $version = get_option( 'cmfr_recaptcha_version', 'invisible' ); ?>
                       <select name="cmfr_recaptcha_version" id="cmfr_recaptcha_version">
                            <option value="invisible" <?php selected( $version, 'invisible' ); ?>>Invisible</option>
                            <option value="challenge" <?php selected( $version, 'challenge' ); ?>>Challenge</option>
                        </select>
                    </td>
                </tr>
				<?php if ( $site_key && $project_id && $api_key ) : ?>
                    <tr class="recaptcha-test">
                        <th><label>Integration</label></th>
                        <td>
                            <?php if ( $recaptcha_version === 'challenge' ) : ?>
                                <form method="post">
                                    <?php wp_nonce_field( 'recaptcha_enterprise_settings' ); ?>
                                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
                                    <p><button type="submit" name="submit_challenge_test" class="button-secondary">Verify reCAPTCHA</button></p>
                                </form>
                                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                            <?php else : ?>
                                <button id="recaptcha-test-button" class="button-secondary" onclick="onClick(event, 'login')">Test reCAPTCHA</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
			</table>
			<p class="submit-button-group">
                <button type="button" onclick="toggleVisibility()" class="button-secondary">Reveal Secrets</button>
                <input type="submit" name="submit" class="button-primary" value="Save Settings">
            </p>
		</form>
	</div>
	<?php
}