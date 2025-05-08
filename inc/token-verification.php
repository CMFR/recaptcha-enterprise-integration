<?php 
// Verify Token
function recaptcha_enterprise_verify_token(WP_REST_Request $request) {
    $token = sanitize_text_field($request->get_param('token'));
    $action = sanitize_text_field($request->get_param('action'));

    // Get plugin settings
    $project_id = get_option('recaptcha_enterprise_project_id', '');
    $site_key = get_option('recaptcha_enterprise_site_key', '');
    $api_key = get_option('recaptcha_enterprise_api_key', '');

    // Check for missing settings
    if (!$project_id || !$site_key || !$api_key) {
        return new WP_REST_Response(array(
            'success' => false,
            'error' => 'Missing project ID, site key, or API key'
        ), 400);
    }

    // Create the assessment request
    $assessment_request = json_encode(array(
        'event' => array(
            'token' => $token,
            'expectedAction' => $action,
            'siteKey' => $site_key
        )
    ));

    // Send the assessment request
    $response = wp_remote_post(
        'https://recaptchaenterprise.googleapis.com/v1/projects/' . esc_attr($project_id) . '/assessments?key=' . esc_attr($api_key), 
        array(
            'body' => $assessment_request,
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'timeout' => 15
        )
    );

    // Check for errors in the HTTP request
    if (is_wp_error($response)) {
        return new WP_REST_Response(array(
            'success' => false,
            'error' => 'Error verifying reCAPTCHA token'
        ), 500);
    }

    // Parse the response
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Check for a valid response
    if (isset($response_body['tokenProperties']['valid']) && $response_body['tokenProperties']['valid'] === true) {
        return new WP_REST_Response(array(
            'success' => true,
            'message' => 'Token validated successfully',
            'score' => $response_body['riskAnalysis']['score'] ?? null,
            'reasons' => $response_body['riskAnalysis']['reasons'] ?? []
        ), 200);
    }

    // Return a more detailed error message
    return new WP_REST_Response(array(
        'success' => false,
        'error' => $response_body['error']['message'] ?? 'Token validation failed',
        'details' => $response_body
    ), 400);
}