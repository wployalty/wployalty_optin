/**
 * @author      WPLoyalty (Ilaiyaraja, Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://www.wployalty.net
 * */

if (typeof (wlopt_jquery) == 'undefined') {
    wlopt_jquery = jQuery.noConflict();
}
wlopt = window.wlopt || {};

(function (wlopt) {
    wlopt_jquery(document).on('click', '#update_wployalty_membership', function () {
        let update_wployalty_membership = wlopt_jquery("#update_wployalty_membership").is(':checked') ? 1 : 0;
        wlopt_jquery.ajax({
            url: wlopt_localize_data.ajax_url,
            type: "POST",
            dataType: 'json',
            data: {
                action: "update_wployalty_membership",
                wlopt_nonce: wlopt_localize_data.update_wployalty_membership,
                accept_wployalty_membership: update_wployalty_membership
            },
            success: function (json) {
                window.location.reload();
            }
        });
    });
})(wlopt_jquery);