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
    </div>l
</div>
