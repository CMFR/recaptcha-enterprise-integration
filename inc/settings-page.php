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
		filemtime(RECAPTCHA_ENTERPRISE_PATH . 'inc/css/admin-styles.css')
	);
}
add_action('admin_enqueue_scripts', 'recaptcha_enterprise_enqueue_admin_styles');

// Register Settings Page
function recaptcha_enterprise_register_settings_page() {
	add_options_page(
		__( 'Integration for reCAPTCHA Enterprise', 'recaptcha-enterprise-integration' ),
		__( 'reCAPTCHA', 'recaptcha-enterprise-integration' ),
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
		$site_key = isset( $_POST['recaptcha_enterprise_site_key'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_enterprise_site_key'] ) ) : '';
		$project_id = isset( $_POST['recaptcha_enterprise_project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_enterprise_project_id'] ) ) : '';
		$api_key = isset( $_POST['recaptcha_enterprise_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_enterprise_api_key'] ) ) : '';
		$recaptcha_version = isset( $_POST['cmfr_recaptcha_version'] ) ? sanitize_key( wp_unslash( $_POST['cmfr_recaptcha_version'] ) ) : '';
		$recaptcha_version = in_array( $recaptcha_version, ['challenge', 'invisible'], true ) ? $recaptcha_version : 'invisible';
		$score_threshold = isset( $_POST['recaptcha_enterprise_score_threshold'] ) ? min( 1, max( 0, (float) wp_unslash( $_POST['recaptcha_enterprise_score_threshold'] ) ) ) : 0.5;
		$disclosure = isset( $_POST['recaptcha_enterprise_disclosure'] ) ? sanitize_key( wp_unslash( $_POST['recaptcha_enterprise_disclosure'] ) ) : '';
		$disclosure = in_array( $disclosure, ['badge', 'form', 'custom'], true ) ? $disclosure : 'form';

        update_option( 'recaptcha_enterprise_site_key', $site_key );
        update_option( 'recaptcha_enterprise_project_id', $project_id );
        update_option( 'recaptcha_enterprise_api_key', $api_key );
        update_option( 'cmfr_recaptcha_version', $recaptcha_version );
        update_option( 'recaptcha_enterprise_score_threshold', $score_threshold );
        update_option( 'recaptcha_enterprise_disclosure', $disclosure );
        add_settings_error('recaptcha_enterprise_settings','settings_updated',__( 'Settings updated successfully.', 'recaptcha-enterprise-integration' ),'updated');
	}

    if ( isset( $_POST['delete'] ) ) {
        check_admin_referer( 'recaptcha_enterprise_settings' );
        delete_option( 'recaptcha_enterprise_site_key' );
        delete_option( 'recaptcha_enterprise_project_id' );
        delete_option( 'recaptcha_enterprise_api_key' );
        delete_option( 'cmfr_recaptcha_version' );
        delete_option( 'recaptcha_enterprise_score_threshold' );
        delete_option( 'recaptcha_enterprise_disclosure' );
        add_settings_error( 'recaptcha_enterprise_settings', 'settings_deleted', __( 'Settings have been deleted.', 'recaptcha-enterprise-integration' ), 'updated' );

        // Clear variables for display
        $site_key = '';
        $project_id = '';
        $api_key = '';
        $recaptcha_version = 'invisible';
    }

    if ( isset( $_POST['submit_challenge_test'] ) && isset( $_POST['g-recaptcha-response'] ) ) {
		check_admin_referer( 'recaptcha_enterprise_settings' );
		$token = sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) );
		$api_key = get_option( 'recaptcha_enterprise_api_key' );
		$project_id = get_option( 'recaptcha_enterprise_project_id' );
		$site_key = get_option( 'recaptcha_enterprise_site_key' );

		$body = json_encode(array('event' => array('token' => $token, 'expectedAction' => 'login', 'siteKey' => $site_key)));
		$response = wp_remote_post(
			'https://recaptchaenterprise.googleapis.com/v1/projects/' . rawurlencode( $project_id ) . '/assessments?key=' . rawurlencode( $api_key ),
			array('body' => $body,'headers' => array('Content-Type' => 'application/json'),'timeout' => 15)
		);
		if ( is_wp_error( $response ) ) {
			add_settings_error('recaptcha_enterprise_settings','challenge_test_error',__( 'Error connecting to reCAPTCHA API.', 'recaptcha-enterprise-integration' ),'error');
		} else {
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $response_body['tokenProperties']['valid'] ) && $response_body['tokenProperties']['valid'] === true ) {
				add_settings_error('recaptcha_enterprise_settings','challenge_test_success',__( 'reCAPTCHA verified successfully.', 'recaptcha-enterprise-integration' ),'updated');
			} else {
				add_settings_error('recaptcha_enterprise_settings','challenge_test_fail',__( 'reCAPTCHA verification failed.', 'recaptcha-enterprise-integration' ),'error');
			}
		}
	}

	// Load saved settings
	$site_key = get_option( 'recaptcha_enterprise_site_key', '' );
	$project_id = get_option( 'recaptcha_enterprise_project_id', '' );
	$api_key = get_option( 'recaptcha_enterprise_api_key', '' );
	$recaptcha_version = get_option( 'cmfr_recaptcha_version', 'invisible' );
	$score_threshold = get_option( 'recaptcha_enterprise_score_threshold', 0.5 );
	$disclosure = get_option( 'recaptcha_enterprise_disclosure', 'form' );

	?>
	<div class="wrap recaptcha-wrap">
		<h1><?php esc_html_e( 'Integration for reCAPTCHA Enterprise', 'recaptcha-enterprise-integration' ); ?></h1>

        <?php settings_errors( 'recaptcha_enterprise_settings' ); ?>

		<p class="instructions">
			<?php
			printf(
				/* translators: %s: link to the Google reCAPTCHA Enterprise documentation */
				esc_html__( "To use this plugin, you'll need to set up reCAPTCHA Enterprise in the Google Cloud Console. Visit the %s for instructions.", 'recaptcha-enterprise-integration' ),
				'<a href="https://cloud.google.com/recaptcha-enterprise/docs" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Google reCAPTCHA Enterprise documentation', 'recaptcha-enterprise-integration' ) . '</a>'
			);
			?>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'recaptcha_enterprise_settings' ); ?>
			<table class="form-table">
				<tr>
					<th><label for="recaptcha_enterprise_project_id"><?php esc_html_e( 'Project ID', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td><input type="password" name="recaptcha_enterprise_project_id" id="recaptcha_enterprise_project_id" value="<?php echo esc_attr( $project_id ); ?>"></td>
				</tr>
				<tr>
					<th><label for="recaptcha_enterprise_api_key"><?php esc_html_e( 'API Key', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td><input type="password" name="recaptcha_enterprise_api_key" id="recaptcha_enterprise_api_key" value="<?php echo esc_attr( $api_key ); ?>"></td>
				</tr>
				<tr>
					<th><label for="recaptcha_enterprise_site_key"><?php esc_html_e( 'Site Key', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td><input type="password" name="recaptcha_enterprise_site_key" id="recaptcha_enterprise_site_key" value="<?php echo esc_attr( $site_key ); ?>"></td>
				</tr>
				<tr>
					<th><label for="cmfr_recaptcha_version"><?php esc_html_e( 'Version', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td>
						<select name="cmfr_recaptcha_version" id="cmfr_recaptcha_version">
							<option value="invisible" <?php selected( $recaptcha_version, 'invisible' ); ?>><?php esc_html_e( 'Invisible', 'recaptcha-enterprise-integration' ); ?></option>
							<option value="challenge" <?php selected( $recaptcha_version, 'challenge' ); ?>><?php esc_html_e( 'Challenge', 'recaptcha-enterprise-integration' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="recaptcha-invisible-only" <?php echo $recaptcha_version === 'challenge' ? 'hidden' : ''; ?>>
					<th><label for="recaptcha_enterprise_score_threshold"><?php esc_html_e( 'Score Threshold', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td>
						<input type="number" name="recaptcha_enterprise_score_threshold" id="recaptcha_enterprise_score_threshold" value="<?php echo esc_attr( $score_threshold ); ?>" min="0" max="1" step="0.1">
						<p class="description"><?php esc_html_e( 'Submissions scoring below this are blocked (0.0 is likely a bot, 1.0 is likely a person). Default is 0.5.', 'recaptcha-enterprise-integration' ); ?></p>
					</td>
				</tr>
				<tr class="recaptcha-invisible-only" <?php echo $recaptcha_version === 'challenge' ? 'hidden' : ''; ?>>
					<th><label for="recaptcha_enterprise_disclosure"><?php esc_html_e( 'Disclosure', 'recaptcha-enterprise-integration' ); ?></label></th>
					<td>
						<select name="recaptcha_enterprise_disclosure" id="recaptcha_enterprise_disclosure">
							<option value="form" <?php selected( $disclosure, 'form' ); ?>><?php esc_html_e( 'Hide badge, add message below forms', 'recaptcha-enterprise-integration' ); ?></option>
							<option value="badge" <?php selected( $disclosure, 'badge' ); ?>><?php esc_html_e( 'Show Google badge', 'recaptcha-enterprise-integration' ); ?></option>
							<option value="custom" <?php selected( $disclosure, 'custom' ); ?>><?php esc_html_e( "Hide badge, I'll add my own message", 'recaptcha-enterprise-integration' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'Google allows hiding the badge only if the site says it\'s protected by reCAPTCHA. With your own message, include "This site is protected by reCAPTCHA and the Google Privacy Policy and Terms of Service apply." near your forms.', 'recaptcha-enterprise-integration' ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit-button-group">
                <button type="button" onclick="toggleVisibility()" class="button-secondary"><?php esc_html_e( 'Reveal Secrets', 'recaptcha-enterprise-integration' ); ?></button>
                <input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'recaptcha-enterprise-integration' ); ?>">
                <input type="submit" name="delete" class="button-primary button-danger" value="<?php esc_attr_e( 'Delete Settings', 'recaptcha-enterprise-integration' ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete all saved reCAPTCHA settings?', 'recaptcha-enterprise-integration' ) ); ?>');">
            </p>
		</form>

        <h2><?php esc_html_e( 'Test reCAPTCHA Integration', 'recaptcha-enterprise-integration' ); ?></h2>
        <table class="form-table">
            <tr class="recaptcha-test">
                <th><label><?php esc_html_e( 'Integration', 'recaptcha-enterprise-integration' ); ?></label></th>
                <td>
                    <?php if ( $site_key && $project_id && $api_key ) : ?>
                        <?php if ( $recaptcha_version === 'challenge' ) : ?>
                            <form method="post">
                                <?php wp_nonce_field( 'recaptcha_enterprise_settings' ); ?>
                                <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $site_key ); ?>"></div>
                                <p><input type="submit" name="submit_challenge_test" class="button-secondary" value="<?php esc_attr_e( 'Test reCAPTCHA', 'recaptcha-enterprise-integration' ); ?>"></p>
                            </form>
                        <?php elseif ( $recaptcha_version === 'invisible' ) : ?>
                            <button id="recaptcha-test-button" class="button-secondary" onclick="onClick(event, 'login')"><?php esc_html_e( 'Test reCAPTCHA', 'recaptcha-enterprise-integration' ); ?></button>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="description"><?php esc_html_e( 'Please save a Project ID, API Key, and Site Key to enable testing.', 'recaptcha-enterprise-integration' ); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th></th>
                <td class="recaptcha-test-message"></td>
            </tr>
        </table>
	</div>
<?php
}
