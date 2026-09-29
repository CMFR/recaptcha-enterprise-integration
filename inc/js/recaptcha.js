// Test Button Click Handler
function onClick(e, action) {
    e.preventDefault();

    // Check if recaptchaData is defined
    if (typeof recaptchaData === 'undefined') {
        showToast(wp.i18n.__("reCAPTCHA data is not loaded.", "recaptcha-enterprise-integration"), "error");
        return;
    }

    const siteKey = recaptchaData.site_key;

    if (!siteKey || !action) {
        showToast(wp.i18n.__("Missing site key or action.", "recaptcha-enterprise-integration"), "error");
        return;
    }

    grecaptcha.enterprise.ready(async () => {
        try {
            const token = await grecaptcha.enterprise.execute(siteKey, { action });
            verifyToken(token, action);
        } catch (error) {
            // translators: %s: error message
            showToast(wp.i18n.sprintf(wp.i18n.__("Error executing reCAPTCHA: %s", "recaptcha-enterprise-integration"), error.message), "error");
        }
    });
}

// Verify Token
async function verifyToken(token, action) {
    try {
        const response = await fetch(recaptchaData.rest_url + "verify-token/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": recaptchaData.nonce
            },
            body: JSON.stringify({ token, action })
        });

        const result = await response.json();

        if (result.success) {
            showToast("✅ " + wp.i18n.__("Token validated successfully!", "recaptcha-enterprise-integration"), "success");
        } else {
            // translators: %s: error message
            showToast("❌ " + wp.i18n.sprintf(wp.i18n.__("Token validation failed: %s", "recaptcha-enterprise-integration"), result.error ?? result.message), "error");
        }

    } catch (error) {
        // translators: %s: error message
        showToast(wp.i18n.sprintf(wp.i18n.__("Error verifying token: %s", "recaptcha-enterprise-integration"), error.message), "error");
    }
}

// Toast Notification with Button Reset
function showToast(message, type = "info", button = null) {
    const notice = document.createElement("div");
    notice.className = `notice notice-${type} is-dismissible`;
    const text = document.createElement("p");
    text.textContent = message;
    notice.append(text);

    // Append to the admin notice area
   const noticeArea = document.querySelector("td.recaptcha-test-message") || document.body;
    noticeArea.prepend(notice);

    // Auto-remove after 5 seconds and reset button state if provided
    setTimeout(() => {
        notice.remove();
        if (button) {
            button.disabled = false;
            button.textContent = wp.i18n.__("Test reCAPTCHA", "recaptcha-enterprise-integration");
        }
    }, 5000);
}