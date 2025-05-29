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
			'error'   => 'Missing project ID, site key, or API key',
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
		'https://recaptchaenterprise.googleapis.com/v1/projects/' . esc_attr( $project_id ) . '/assessments?key=' . esc_attr( $api_key ),
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
			'error'   => 'Error verifying reCAPTCHA token',
		], 500 );
	}

	$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $response_body['tokenProperties']['valid'] ) && $response_body['tokenProperties']['valid'] === true ) {
		return new WP_REST_Response( [
			'success' => true,
			'message' => 'Token validated successfully',
			'score'   => $response_body['riskAnalysis']['score'] ?? null,
			'reasons' => $response_body['riskAnalysis']['reasons'] ?? [],
		], 200 );
	}

	return new WP_REST_Response( [
		'success' => false,
		'error'   => $response_body['error']['message'] ?? 'Token validation failed',
		'details' => $response_body,
	], 400 );
}