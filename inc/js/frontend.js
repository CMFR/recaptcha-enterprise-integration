// Actions must match the expected actions checked in token-verification.php
const recaptchaForms = {
    'form.wpcf7-form': 'contact_form',
    'form.register': 'register'
};

const recaptchaSelector = Object.keys(recaptchaForms).join(', ');
const recaptchaAction = (form) => recaptchaForms[Object.keys(recaptchaForms).find((selector) => form.matches(selector))];
let recaptchaResubmitting = false;

const recaptchaDisclosure = () => {
    const link = (href, text) => {
        const a = document.createElement('a');
        a.href = href;
        a.textContent = text;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        return a;
    };

    const message = document.createElement('p');
    message.className = 'recaptcha-disclosure';
    message.append(
        'This site is protected by reCAPTCHA and the Google ',
        link('https://policies.google.com/privacy', 'Privacy Policy'),
        ' and ',
        link('https://policies.google.com/terms', 'Terms of Service'),
        ' apply.'
    );
    return message;
};

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
            form.after(recaptchaDisclosure());
        }
    });

    // Capture phase runs before the CF7 and User Registration submit handlers, which read the token synchronously
    document.addEventListener('submit', (e) => {
        const form = e.target;

        if (!form.matches(recaptchaSelector) || recaptchaResubmitting) {
            return;
        }

        // Tokens are single use, so reset once the form's own handlers have read this one
        if (widgets.has(form)) {
            setTimeout(() => grecaptcha.enterprise.reset(widgets.get(form)));
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
