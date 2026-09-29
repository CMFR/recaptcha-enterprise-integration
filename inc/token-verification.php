<?php 
// Verify Token
function recaptcha_enterprise_verify_token( WP_REST_Request $request ) {
	$token  = sanitize_text_field( $request->get_param( 'token' ) );
	$action = sanitize_text_field( $request->get_param( 'action' ) );

	$project_id = get_option( 'recaptcha_enterprise_project_id', '' );
	$site_key   = get_option( 'recaptcha_enterprise_site_key', '' );
	$api_key    = get_option( 'recaptcha_enterprise_api_key', '' );

	if ( ! $project_id || ! $site_key || ! $api_key ) {
		return new WP_REST_Response( [
			'success' => false,
			'error'   => __( 'Missing project ID, site key, or API key', 'recaptcha-enterprise-integration' ),
		], 400 );
	}

	$assessment_request = json_encode( [
		'event' => [
			'token'          => $token,
			'expectedAction' => $action,
			'siteKey'        => $site_key,
		],
	] );

	$response = wp_remote_post(
		'https://recaptchaenterprise.googleapis.com/v1/projects/' . rawurlencode( $project_id ) . '/assessments?key=' . rawurlencode( $api_key ),
		[
			'body'    => $assessment_request,
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'timeout' => 15,
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_REST_Response( [
			'success' => false,
			'error'   => __( 'Error verifying reCAPTCHA token', 'recaptcha-enterprise-integration' ),
		], 500 );
	}

	$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $response_body['tokenProperties']['valid'] ) && $response_body['tokenProperties']['valid'] === true ) {
		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Token validated successfully', 'recaptcha-enterprise-integration' ),
			'score'   => $response_body['riskAnalysis']['score'] ?? null,
			'reasons' => $response_body['riskAnalysis']['reasons'] ?? [],
		], 200 );
	}

	return new WP_REST_Response( [
		'success' => false,
		'error'   => $response_body['error']['message'] ?? __( 'Token validation failed', 'recaptcha-enterprise-integration' ),
	], 400 );
}

// Front-end check shared by the CF7 and User Registration hooks
function recaptcha_enterprise_passes( $token, $action ) {
	$project_id = get_option( 'recaptcha_enterprise_project_id', '' );
	$site_key   = get_option( 'recaptcha_enterprise_site_key', '' );
	$api_key    = get_option( 'recaptcha_enterprise_api_key', '' );

	// Front-end scripts don't load until settings are complete, so there's no token to check
	if ( ! $project_id || ! $site_key || ! $api_key ) {
		return true;
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'reCAPTCHA debug: action=' . $action . ' token_length=' . strlen( $token ) );
	}

	if ( ! $token ) {
		return false;
	}

	$response = wp_remote_post(
		'https://recaptchaenterprise.googleapis.com/v1/projects/' . rawurlencode( $project_id ) . '/assessments?key=' . rawurlencode( $api_key ),
		[
			'body'    => json_encode( [
				'event' => [
					'token'          => $token,
					'expectedAction' => $action,
					'siteKey'        => $site_key,
				],
			] ),
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'timeout' => 15,
		]
	);

	// Fail open so a Google outage or bad key doesn't block every submission; the settings page test catches bad keys
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( 'reCAPTCHA Enterprise: assessment request failed, submission allowed' );
		return true;
	}

	$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'reCAPTCHA debug: ' . wp_json_encode( [
			'tokenProperties' => $response_body['tokenProperties'] ?? null,
			'riskAnalysis'    => $response_body['riskAnalysis'] ?? null,
		] ) );
	}

	if ( empty( $response_body['tokenProperties']['valid'] ) || ( $response_body['tokenProperties']['action'] ?? '' ) !== $action ) {
		return false;
	}

	// Challenge keys don't return a meaningful score; solving the checkbox is the check
	if ( get_option( 'cmfr_recaptcha_version', 'invisible' ) === 'challenge' ) {
		return true;
	}

	return ( $response_body['riskAnalysis']['score'] ?? 0 ) >= (float) get_option( 'recaptcha_enterprise_score_threshold', 0.5 );
}

// Contact Form 7
add_filter( 'wpcf7_spam', function ( $spam ) {
	if ( $spam ) {
		return $spam;
	}

	$token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

	return ! recaptcha_enterprise_passes( $token, 'contact_form' );
} );

// User Registration: its JS sends the form's g-recaptcha-response value as captchaResponse
add_action( 'user_registration_before_register_user_action', function () {
	$token = isset( $_POST['captchaResponse'] ) ? sanitize_text_field( wp_unslash( $_POST['captchaResponse'] ) ) : '';

	if ( ! recaptcha_enterprise_passes( $token, 'register' ) ) {
		wp_send_json_error( [
			'message' => __( 'reCAPTCHA verification failed. Please try again.', 'recaptcha-enterprise-integration' ),
		] );
	}
} );
