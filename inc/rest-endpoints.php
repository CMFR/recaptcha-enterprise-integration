<?php
// Ensure the REST API is enabled
function recaptcha_enterprise_check_rest_api() {
    // Check if REST API is available
    if ( ! function_exists( 'rest_url' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>reCAPTCHA Enterprise:</strong> The REST API is not enabled on this site. Please enable the WordPress REST API for this plugin to work.</p></div>';
        });
        return false;
    }

    return true;
}

// Check for REST API support
if ( ! recaptcha_enterprise_check_rest_api() ) {
    return;
}

// Register the REST route
function recaptcha_enterprise_register_rest_routes() {
	// Enterprise verification route
	register_rest_route( 'recaptcha-enterprise/v1', '/verify-token/', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'recaptcha_enterprise_verify_token',
		'permission_callback' => '__return_true',
		'args'                => array(
			'token' => array(
				'required'          => true,
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( $param );
				}
			),
			'action' => array(
				'required'          => true,
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( $param );
				}
			)
		)
	) );

	// v2 verification route
	register_rest_route( 'recaptcha-enterprise/v1', '/verify-v2/', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'recaptcha_verify_token_v2',
		'permission_callback' => '__return_true',
		'args'                => array(
			'token' => array(
				'required'          => true,
				'validate_callback' => function( $param ) {
					return is_string( $param ) && ! empty( $param );
				}
			)
		)
	) );
}
add_action( 'rest_api_init', 'recaptcha_enterprise_register_rest_routes' );