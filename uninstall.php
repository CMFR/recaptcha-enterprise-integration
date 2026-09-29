<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

foreach ( array(
	'recaptcha_enterprise_site_key',
	'recaptcha_enterprise_project_id',
	'recaptcha_enterprise_api_key',
	'cmfr_recaptcha_version',
	'recaptcha_enterprise_score_threshold',
	'recaptcha_enterprise_disclosure',
	// Update checker bookkeeping; the library doesn't remove it on uninstall
	'external_updates-recaptcha-enterprise-integration',
) as $option ) {
	delete_option( $option );
}
