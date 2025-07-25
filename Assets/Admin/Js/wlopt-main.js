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
        (success) ? alertify.success(wlopt_localize_data.copy_clipboard) : alertify.error(wlopt_localize_data.copy_error_message);
    };
    $(document).on('click', '.wlopt-customer-type', function () {
        $(document).find('.wlopt-customer-type').removeClass('wlopt-active-customer-type');
        $(this).addClass('wlopt-active-customer-type');
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        wlopt_jquery.getCustomerData(list_no, 1);
    });
    $(document).on('change', '#wlpot-customer-list-count', function () {
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        let page_no = $(document).find('.wlopt-page-no').html();
        wlopt_jquery.getCustomerData(list_no, 1);
    });
    $(document).on('click', '#wlopt-prev-page', function () {
        let page_no_html = $(document).find('.wlopt-page-no').html();
        if (page_no_html >= 1) {
            $(document).find('.wlopt-page-no').html(parseInt(page_no_html) - 1);
            let list_no = $(document).find('#wlpot-customer-list-count').val();
            let page_no = $(document).find('.wlopt-page-no').html();
            wlopt_jquery.getCustomerData(list_no, page_no);
        }
    });
    $(document).on('click', '#wlopt-next-page', function () {
        let page_no_html = $(document).find('.wlopt-page-no').html();
        $(document).find('.wlopt-page-no').html(parseInt(page_no_html) + 1);
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        let page_no = $(document).find('.wlopt-page-no').html();
        wlopt_jquery.getCustomerData(list_no, page_no);
    });
    $(document).on('click', '.search-container .search-icon', function (event) {
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        wlopt_jquery.getCustomerData(list_no, 1);
    });
    $(document).on('keydown', '#wlopt-customer-email-search', function (event) {
        if (event.key != 'Enter') {
            return
        }
        let list_no = $(document).find('#wlpot-customer-list-count').val();
        wlopt_jquery.getCustomerData(list_no, 1);
    });
    wlopt_jquery.getCustomerData = function (list_no, page_no) {
        let customer_type = $(document).find('.wlopt-active-customer-type').data('type');
        let email_search = $(document).find('#wlopt-customer-email-search').val();
        let data = {
            'action' : 'wlopt_get_customer_details',
            'customer_type' : customer_type,
            'list_no' : list_no,
            'page_no': page_no,
            'search_email' : email_search,
            'wlopt_nonce' : wlopt_localize_data.get_customer_details
        }

        $.ajax({
            url: wlopt_localize_data.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: function () {
                $(document).find("#wlopt-customer-details").addClass('spinner');
            },
            success: function (response) {
                if (response.data.html) {
                    $(document).find('#wlopt-customer-details').html(response.data.html);
                }
            },
            error: function (xhr, status, error) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.error(error);
            },
            complete: function () {
                $(document).find("#wlopt-customer-details").removeClass('spinner');
            }
        });
    }
})(wlopt_jquery);