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
    $(document).on('change', '#wlopt-customer-type', function () {
        wlopt_jquery.getCustomerData();
    });
    $(document).on('change', '#wlpot-customer-list-count', function () {
        wlopt_jquery.getCustomerData();
    });
    $(document).on('click', '#wlopt-prev-page', function () {
        let page_no = $(document).find('.wlopt-page-no').html();
        $(document).find('.wlopt-page-no').html(parseInt(page_no) - 1);
        wlopt_jquery.getCustomerData();
    });
    $(document).on('click', '#wlopt-next-page', function () {
        let page_no = $(document).find('.wlopt-page-no').html();
        $(document).find('.wlopt-page-no').html(parseInt(page_no) + 1);
        wlopt_jquery.getCustomerData();
    });
    wlopt_jquery.getCustomerData = function () {
        let customer_type = $(document).find('#wlopt-customer-type').val();
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        let page_no = $(document).find('.wlopt-page-no').html();
        let data = {
            'action' : 'wlopt_get_customer_details',
            'customer_type' : customer_type,
            'list_no' : list_no,
            'page_no': page_no,
            'wlopt_nonce' : wlopt_localize_data.get_customer_details
        }

        $.ajax({
            url: wlopt_localize_data.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success: function (response) {
                if (response.data.html) {
                    $(document).find('#wlopt-customer-details').html(response.data.html);
                }
            },
            error: function (xhr, status, error) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error(error);
            }
        });
    }
})(wlopt_jquery);