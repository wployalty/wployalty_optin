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
                <div class="wlopt-customer-type-actions">
                    <button class="wlopt-customer-type wlopt-active-customer-type" data-type="opt-in">
                        <?php esc_html_e( 'Opt In', 'wp-loyalty-optin' ); ?>
                    </button>
                    <button class="wlopt-customer-type">
                        <?php esc_html_e( 'Opt Out', 'wp-loyalty-optin' ); ?>
                    </button>
                </div>
                <div class="search-container">
                    <input type="text" id="wlopt-customer-email-search" class="search-input" placeholder="Search email..." />
                    <div class="search-icon">
                        <img src="<?php echo ( isset( $search ) && ! empty( $search ) ) ? esc_url( $search ) : ''; ?>"
                             alt="<?php esc_html_e( "search", "wp-loyalty-optin" ); ?>">
                    </div>
                </div>
                <div class="wlopt-back-to-apps">
                    <a class="button" target="_self"
                       href="<?php echo isset( $app_url ) ? esc_url( $app_url ) : '#'; ?>">
                        <img src="<?php echo ( isset( $back ) && ! empty( $back ) ) ? esc_url( $back ) : ''; ?>"
                             alt="<?php esc_html_e( "Back", "wp-loyalty-optin" ); ?>">
                        <?php esc_html_e( 'Back to WPLoyalty', 'wp-loyalty-optin' ); ?></a>
                </div>
            </div>
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
