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
    button.textContent = shouldReveal ? 'Hide Secrets' : 'Reveal Secrets';
}

function onClick(e, action) {
	e.preventDefault();

	if (typeof grecaptcha === 'undefined') {
		alert('Google reCAPTCHA script may not have fully loaded. Please wait a moment, then reload the page and try again.');
		return;
	}

	if (!grecaptcha.enterprise) {
		alert('reCAPTCHA Enterprise is not available yet. The script may still be loading. Please wait a moment, then reload the page and try again.');
		return;
	}

	grecaptcha.enterprise.ready(function () {
		grecaptcha.enterprise.execute(recaptchaData.site_key, { action: action }).then(function (token) {
			console.log('Enterprise token:', token);
			alert('Enterprise token generated. Check console for details.');
		});
	});
}