<?php
/**
 * @author      Wployalty (Ilaiyaraja, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

defined( "ABSPATH" ) or die();
?>
<div id="wlopt-settings">
    <div class="wlopt-setting-page-holder">
        <form id="wlopt-settings-form" method="post">
            <div class="wlopt-settings-header">
                <div class="wlopt-settings-heading"><p><?php esc_html_e( 'SETTINGS', 'wp-loyalty-optin' ) ?></p></div>
                <div class="wlopt-button-block">
                    <div class="wlopt-back-to-apps">
                        <a class="button" target="_self"
                           href="<?php echo isset( $app_url ) ? esc_url( $app_url ) : '#'; ?>">
                            <i class="wlrop-back"></i>
							<?php esc_html_e( 'Back to WPLoyalty', 'wp-loyalty-optin' ); ?></a>
                    </div>
                </div>
            </div>
            <div class="wlopt-setting-body">
                <div class="wlopt-alert-message">
                    <i class="wlrop-info_circle"></i>
                    <div>
                            <span class="wlr-notice-header">
                                <?php esc_html_e( 'IMPORTANT', 'wp-loyalty-optin' ); ?>
                            </span>
                        <span>
                                <?php esc_html_e( 'Existing loyalty customers will be automatically added to the Opt-in customers list upon their next logging in', 'wp-loyalty-optin' ); ?>.
                            </span>
                    </div>
                </div>
                <div class="wlopt-settings-body-content">
                    <div class="wlopt-field-block">
                        <div class="wlopt-copy-field">
                            <div class="wlopt-copy-label">
                                <label><?php esc_html_e( 'Use this shortcode to let user update preference to wployalty membership:', 'wp-loyalty-optin' ); ?></label>
                            </div>
                            <div class="wlopt-copy-container">
                                <input type="text" id="wlopt-shortcode-value" value="[wlopt_update_loyalty_membership]"
                                       readonly>
                                <button type="button" class="wlopt-copy-button"
                                        onclick="wlopt_jquery.copyToClipboard('wlopt-shortcode-value')">
                                    <i class="wlr wlrop-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
