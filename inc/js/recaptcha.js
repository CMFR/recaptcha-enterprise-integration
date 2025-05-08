// Test Button Click Handler
function onClick(e, action) {
    e.preventDefault();

    // Check if recaptchaData is defined
    if (typeof recaptchaData === 'undefined') {
        showToast("reCAPTCHA data is not loaded.", "error");
        return;
    }

    const siteKey = recaptchaData.site_key;

    if (!siteKey || !action) {
        showToast("Missing site key or action.", "error");
        return;
    }

    grecaptcha.enterprise.ready(async () => {
        try {
            const token = await grecaptcha.enterprise.execute(siteKey, { action });
            verifyToken(token, action);
        } catch (error) {
            showToast("Error executing reCAPTCHA: " + error.message, "error");
        }
    });
}

// Verify Token
async function verifyToken(token, action) {
    try {
        const response = await fetch(recaptchaData.rest_url + "verify-token/", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ token, action })
        });

        const result = await response.json();

        if (result.success) {
            showToast("✅ Token validated successfully!", "success");
        } else {
            showToast("❌ Token validation failed: " + result.error, "error");
        }

    } catch (error) {
        showToast("Error verifying token: " + error.message, "error");
    }
}

// Toast Notification with Button Reset
function showToast(message, type = "info", button = null) {
    const notice = document.createElement("div");
    notice.className = `notice notice-${type} is-dismissible`;
    notice.innerHTML = `<p>${message}</p>`;

    // Append to the admin notice area
    const noticeArea = document.querySelector(".wrap") || document.body;
    noticeArea.prepend(notice);

    // Auto-remove after 5 seconds and reset button state if provided
    setTimeout(() => {
        notice.remove();
        if (button) {
            button.disabled = false;
            button.textContent = "Test reCAPTCHA";
        }
    }, 5000);
}