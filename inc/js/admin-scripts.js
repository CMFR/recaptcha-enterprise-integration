document.addEventListener('DOMContentLoaded', function () {
	const notice = document.querySelector('.notice.updated, .notice-success');

	if (notice) {
		setTimeout(() => {
			notice.style.transition = 'opacity 0.5s ease-out';
			notice.style.opacity = '0';

			setTimeout(() => {
				notice.remove();
			}, 500);
		}, 4000); // 4 second delay before fade
	}

	// Score threshold and badge settings don't apply to Challenge keys
	const version = document.getElementById('cmfr_recaptcha_version');
	version.addEventListener('change', () => {
		document.querySelectorAll('.recaptcha-invisible-only').forEach((row) => {
			row.hidden = version.value === 'challenge';
		});
	});
});

function toggleVisibility() {
    const fieldIds = ['recaptcha_enterprise_project_id', 'recaptcha_enterprise_api_key', 'recaptcha_enterprise_site_key'];
    let shouldReveal = false;

    // Check the current state of the first field to determine if we should reveal or hide
    const firstField = document.getElementById(fieldIds[0]);
    shouldReveal = firstField.type === 'password';

    // Toggle all fields
    fieldIds.forEach(id => {
        const field = document.getElementById(id);
        field.type = shouldReveal ? 'text' : 'password';
    });

    // Update button text
    const button = document.querySelector('button[onclick="toggleVisibility()"]');
    button.textContent = shouldReveal ? wp.i18n.__('Hide Secrets', 'recaptcha-enterprise-integration') : wp.i18n.__('Reveal Secrets', 'recaptcha-enterprise-integration');
}