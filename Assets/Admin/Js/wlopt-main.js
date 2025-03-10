if (typeof (wlopt_jquery) == 'undefined') {
    wlopt_jquery = jQuery.noConflict();
}
wlopt = window.wlopt || {};
(function ($) {
    wlopt_jquery.enableOptin = function (id) {
        const checkbox = wlopt_jquery('#wlopt-main-page #wlopt-settings #' + id);
        const checked = checkbox.is(':checked');
        checkbox.val(checked ? 'yes' : 'no');
    };
    $(document).on('submit', '#wlopt-settings-form', function (event) {
        event.preventDefault();
        let formData = $(this).serialize();
        if (!$('#wlopt_enable_optin').is(':checked')) {
            if (formData.length > 0) {
                formData += '&';
            }
            formData += 'enable_optin=no';
        }
        let submitButton = $(this).find('button[id="wlopt-setting-submit-button"]');
        submitButton.prop('disabled', true);
        $.ajax({
            url: wlopt_localize_data.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: formData + '&action=wlopt_save_settings&wlopt_nonce=' + wlopt_localize_data.save_nonce,
            success: function (response) {
                alertify.set('notifier', 'position', 'top-right');
                if (response.success) {
                    alertify.success(response.data.message);
                } else {
                    alertify.error(response.data.message);
                }
            },
            complete: function () {
                submitButton.prop('disabled', false);
            },
            error: function (xhr, status, error) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error(error);
            }
        });
    });
    wlopt_jquery.copyToClipboard = function (elementId) {
        const copyText = document.getElementById(elementId);

        copyText.select();

        let success = false;
        if (navigator.clipboard && window.isSecureContext) {
            try {
                navigator.clipboard.writeText(copyText.value);
                success = true;
            } catch (err) {
                console.error('Clipboard write failed', err);
            }
        }
        if (!success) {
            try {
                success = document.execCommand('copy');
            } catch (err) {
                console.error('Copy failed', err);
            }
        }
        alertify.set('notifier', 'position', 'top-right');
        (success) ? alertify.success('Copied to clipboard') : alertify.error('Could not copy text.');
    };

    $(document).on('click', '#wlopt-onboard-submit', function (event) {
        event.preventDefault();

        let formData = {
            action: 'wlopt_process_onboarding',
            wlopt_nonce: wlopt_localize_data.onboarding_save_nonce,
            update_preference: $('#update-preference').val()
        };

        $(this).prop('disabled', true);
        $.ajax({
            url: wlopt_localize_data.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            success: function (response) {
                alertify.set('notifier', 'position', 'top-right');
                if (response.success) {
                    alertify.success(response.data.message);
                    setTimeout(function () {
                        $('#wlopt-popup').remove();
                        $('#wlopt-overlay-section').removeClass('active');
                        window.location.reload();
                    }, 1500);
                } else {
                    alertify.error(response.data.message);
                }
            },
            complete: function () {
                $('#wlopt-onboard-submit').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error(error);
                $('#wlopt-onboard-submit').prop('disabled', false);
            }
        });
    });
})(wlopt_jquery);