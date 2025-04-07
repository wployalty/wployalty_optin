<?php
/**
 * @author      Wployalty (Ilaiyaraja, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

use Wlopt\App\Helper\Woocommerce;

defined( "ABSPATH" ) or die();

$customers_details = $customers_details ?? [];
?>
<div id="wlopt-customer-page">
    <div class="wlopt-customer-page-holder">
        <div class="wlopt-customers-header">
            <div class="wlopt-customers-heading"><p><?php esc_html_e( 'CUSTOMERS', 'wp-loyalty-optin' ) ?></p></div>
            <div class="wlopt-button-block">
                <div class="wlopt-back-to-apps">
                    <a class="button" target="_self"
                       href="<?php echo isset( $app_url ) ? esc_url( $app_url ) : '#'; ?>">
                        <img src="<?php echo ( isset( $back ) && ! empty( $back ) ) ? esc_url( $back ) : ''; ?>"
                             alt="<?php esc_html_e( "Back", "wp-loyalty-optin" ); ?>">
                        <?php esc_html_e( 'Back to WPLoyalty', 'wp-loyalty-optin' ); ?></a>
                </div>
            </div>
        </div>
        <div class="wlopt-customer-type-details">
            <label for="wlopt-customer-type" class="wlopt-customer-type-label"><?php echo esc_html__('Customers type', 'wp-loyalty-optin'); ?></label>
            <select id="wlopt-customer-type">
                <option value="opt-in"><?php echo esc_html__('Opt-in Customers', 'wp-loyalty-optin'); ?></option>
                <option value="opt-out"><?php echo esc_html__('Opt-out Customers', 'wp-loyalty-optin'); ?></option>
            </select>
        </div>
        <div id="wlopt-customer-details">
            <?php Woocommerce::renderTemplate(
                WLOPT_VIEW_PATH . '/Admin/Components/CustomerTable.php', [
                    'customers_details' => $customers_details,
                ]
            ); ?>
        </div>
    </div>
</div>
