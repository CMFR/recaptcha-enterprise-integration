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
    button.textContent = shouldReveal ? 'Hide Secrets' : 'Reveal Secrets';
}

document.addEventListener('DOMContentLoaded', function () {
	const versionSelect = document.getElementById('cmfr_recaptcha_version');
	const secretRow = document.getElementById('recaptcha_v2_secret_key_row');

	function toggleSecretKeyField() {
		if (!versionSelect || !secretRow) return;

		secretRow.style.display = versionSelect.value === 'v2' ? '' : 'none';
	}

	toggleSecretKeyField(); // Run on load
	versionSelect?.addEventListener('change', toggleSecretKeyField);
});

document.addEventListener('DOMContentLoaded', function () {
	const versionSelect = document.getElementById('cmfr_recaptcha_version');
	const secretRow = document.getElementById('recaptcha_v2_secret_key_row');

	function toggleSecretKeyField() {
		if (!versionSelect || !secretRow) return;
		secretRow.style.display = versionSelect.value === 'v2' ? '' : 'none';
	}

	toggleSecretKeyField();
	versionSelect?.addEventListener('change', toggleSecretKeyField);
});

// Enterprise button click handler
function onClick(e, action) {
	e.preventDefault();

	if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
		alert('reCAPTCHA not loaded.');
		return;
	}

	grecaptcha.enterprise.ready(function () {
		grecaptcha.enterprise.execute(recaptchaData.site_key, { action: action }).then(function (token) {
			console.log('Enterprise token:', token);

			// Optional: Send to your REST endpoint here
			alert('Enterprise token generated. Check console for details.');
		});
	});
}