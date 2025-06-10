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
                    <div class="wlopt-save-changes">
                        <button type="submit" id="wlopt-setting-submit-button">
                            <img src="<?php echo ( isset( $save ) && ! empty( $save ) ) ? esc_url( $save ) : ''; //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>">
                            <span><?php esc_html_e( 'Save Changes', 'wp-loyalty-optin' ) ?></span>
                        </button>
                    </div>
                    <div class="wlopt-back-to-apps">
                        <a class="button" target="_self"
                           href="<?php echo isset( $app_url ) ? esc_url( $app_url ) : '#'; ?>">
                            <img src="<?php echo ( isset( $back ) && ! empty( $back ) ) ? esc_url( $back ) : ''; //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>"
                                 alt="<?php esc_html_e( "Back", "wp-loyalty-optin" ); ?>">
							<?php esc_html_e( 'Back to WPLoyalty', 'wp-loyalty-optin' ); ?></a>
                    </div>
                </div>
            </div>
            <div class="wlopt-setting-body">
                <div class="wlopt-alert-message">
                    <img src="<?php echo ( isset( $info ) && ! empty( $info ) ) ? esc_url( $info ) : ''; //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>">
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
                        <div>
							<?php $enable_optin = isset( $options['enable_optin'] ) && ! empty( $options['enable_optin'] ) && ( $options['enable_optin'] === 'yes' ) ?
								$options['enable_optin'] : 'no'; ?>
                            <input type="checkbox" id="wlopt_enable_optin" name="enable_optin"
                                   value="<?php echo esc_attr( $enable_optin ) ?>"
                                   onclick="wlopt_jquery.enableOptin('wlopt_enable_optin');"
								<?php echo $enable_optin === 'yes' ? 'checked' : ''; ?>>
                            <label class="wlopt-enable-optin-label"
                                   for="wlopt_enable_optin"><?php esc_html_e( 'Enable Opt-in feature ?', 'wp-loyalty-optin' ); ?></label>
                        </div>
                    </div>
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
