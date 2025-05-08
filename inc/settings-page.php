<?php

// Ensure this file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue Admin Styles
function recaptcha_enterprise_enqueue_admin_styles($hook) {
	// Only load on the settings page
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
	// Check user permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Handle form submission
	if ( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'recaptcha_enterprise_settings' );
		
		// Update the site key, project ID, and API key
		$site_key = sanitize_text_field( $_POST['recaptcha_enterprise_site_key'] );
		$project_id = sanitize_text_field( $_POST['recaptcha_enterprise_project_id'] );
		$api_key = sanitize_text_field( $_POST['recaptcha_enterprise_api_key'] );

		if ( empty( $site_key ) || empty( $project_id ) || empty( $api_key ) ) {
			add_settings_error(
				'recaptcha_enterprise_settings',
				'settings_error',
				'All fields are required. Please make sure you have entered a Site Key, Project ID, and API Key.',
				'error'
			);
		} else {
			update_option( 'recaptcha_enterprise_site_key', $site_key );
			update_option( 'recaptcha_enterprise_project_id', $project_id );
			update_option( 'recaptcha_enterprise_api_key', $api_key );

			// Add a success message
			add_settings_error(
				'recaptcha_enterprise_settings',
				'settings_updated',
				'Settings updated successfully.',
				'updated'
			);
		}
	}

	// Display settings messages
	settings_errors( 'recaptcha_enterprise_settings' );

	// Fetch current settings
	$site_key   = get_option( 'recaptcha_enterprise_site_key', '' );
	$project_id = get_option( 'recaptcha_enterprise_project_id', '' );
	$api_key    = get_option( 'recaptcha_enterprise_api_key', '' );

	?>
	<div class="wrap recaptcha-wrap">
		<h1>reCAPTCHA Enterprise Integration</h1>
		<p class="instructions">
			To use this plugin, you'll need to set up reCAPTCHA Enterprise in the Google Cloud Console. 
			Visit the <a href="https://cloud.google.com/recaptcha-enterprise/docs" target="_blank" rel="noopener noreferrer">
				Google reCAPTCHA Enterprise documentation
			</a> for instructions.
		</p>

		<form method="post">
			<?php wp_nonce_field( 'recaptcha_enterprise_settings' ); ?>
			<table class="form-table">
                <tr>
                    <th><label for="recaptcha_enterprise_project_id">Project ID</label></th>
                    <td>
                        <input type="password" name="recaptcha_enterprise_project_id" id="recaptcha_enterprise_project_id" 
                            value="<?php echo esc_attr( $project_id ); ?>" required>
                    </td>
                </tr>

                <tr>
                    <th><label for="recaptcha_enterprise_api_key">API Key</label></th>
                    <td>
                        <input type="password" name="recaptcha_enterprise_api_key" id="recaptcha_enterprise_api_key" 
                            value="<?php echo esc_attr( $api_key ); ?>" required>
                    </td>
                </tr>

                <tr>
                    <th><label for="recaptcha_enterprise_site_key">Site Key</label></th>
                    <td>
                        <input type="password" name="recaptcha_enterprise_site_key" id="recaptcha_enterprise_site_key" 
                            value="<?php echo esc_attr( $site_key ); ?>" required>
                    </td>
                </tr>


				<?php if ( $site_key && $project_id && $api_key ) : ?>
					<tr class="recaptcha-test">
						<th><label>Integration</label></th>
						<td>
							<button id="recaptcha-test-button" class="button-secondary" onclick="onClick(event, 'login')">
								Test reCAPTCHA
							</button>
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