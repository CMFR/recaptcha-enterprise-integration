// Actions must match the expected actions checked in token-verification.php
const recaptchaForms = {
    'form.wpcf7-form': 'contact_form',
    'form.register': 'register'
};

const recaptchaSelector = Object.keys(recaptchaForms).join(', ');
const recaptchaAction = (form) => recaptchaForms[Object.keys(recaptchaForms).find((selector) => form.matches(selector))];
let recaptchaResubmitting = false;

grecaptcha.enterprise.ready(() => {
    const widgets = new Map();

    document.querySelectorAll(recaptchaSelector).forEach((form) => {
        if (recaptchaFrontend.version === 'challenge') {
            // The widget adds its own g-recaptcha-response field inside the container
            const container = document.createElement('div');
            const submit = form.querySelector('[type="submit"]');
            submit ? submit.before(container) : form.append(container);
            widgets.set(form, grecaptcha.enterprise.render(container, {
                sitekey: recaptchaFrontend.site_key,
                action: recaptchaAction(form)
            }));
        } else {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'g-recaptcha-response';
            form.append(input);
        }

        if (recaptchaFrontend.disclosure) {
            // Built and escaped server-side so the message can be translated
            form.insertAdjacentHTML('afterend', '<p class="recaptcha-disclosure">' + recaptchaFrontend.disclosure + '</p>');
        }
    });

    // Tokens are single use, so reset the checkbox once the server has responded. Resetting on submit
    // clears the token too early when another plugin (e.g. Conditional Fields for CF7) delays the submit
    const resetWidget = (form) => widgets.has(form) && grecaptcha.enterprise.reset(widgets.get(form));
    document.addEventListener('wpcf7submit', (e) => resetWidget(e.target));
    if (window.jQuery) {
        jQuery(document).on('user_registration_frontend_after_ajax_complete', (e, response, type, $form) => resetWidget($form[0]));
    }

    // Capture phase runs before the CF7 and User Registration submit handlers
    document.addEventListener('submit', (e) => {
        const form = e.target;

        if (!form.matches(recaptchaSelector) || recaptchaResubmitting || widgets.has(form)) {
            return;
        }

        e.preventDefault();
        e.stopImmediatePropagation();

        grecaptcha.enterprise.execute(recaptchaFrontend.site_key, { action: recaptchaAction(form) })
            .catch(() => '')
            .then((token) => {
                form.querySelector('input[name="g-recaptcha-response"]').value = token;
                recaptchaResubmitting = true;
                form.requestSubmit(e.submitter);
                recaptchaResubmitting = false;
            });
    }, true);
});
