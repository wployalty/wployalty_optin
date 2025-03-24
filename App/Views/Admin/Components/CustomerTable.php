<?php
/**
 * @author      Wployalty (Ilaiyaraja, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

defined( "ABSPATH" ) or die();

$customers_details = $customers_details ?? [];
$page_no = $customers_details['page_no'] ?? 1;
$list_no = $customers_details['list_no'] ?? 5;
$index = 0;
$total_pages = ceil($customers_details['total_users'] / $list_no);

if (!empty($customers_details['customers'])) { ?>
    <table id="wlpot-customer-table">
        <thead>
        <tr>
            <th><?php echo esc_html__('S NO', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Email', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Point balance', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Total Earned', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__(' Redeemed', 'wp-loyalty-optin'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($customers_details['customers'] as $key => $customer) : ?>
            <tr>
                <td><?php echo esc_html($key + 1); ?></td>
                <td>
                    <?php echo esc_html($customer->email); ?>
                </td>
                <td>
                    <?php echo esc_html($customer->points); ?>
                </td>
                <td>
                    <?php echo esc_html($customer->total_points); ?>
                </td>
                <td>
                    <?php echo esc_html($customer->redeemed); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="wlopt-customer-footer">
        <div class="wlopt-customer-list-actions">
            <select id="wlpot-customer-list-count">
                <option value="5" <?php if ($list_no == '5') echo "selected"; ?>><?php echo esc_html__('5', 'wp-loyalty-optin'); ?></option>
                <option value="10" <?php if ($list_no == '10') echo "selected"; ?>><?php echo esc_html__('10', 'wp-loyalty-optin'); ?></option>
                <option value="15" <?php if ($list_no == '15') echo "selected"; ?>><?php echo esc_html__('15', 'wp-loyalty-optin'); ?></option>
                <option value="20" <?php if ($list_no == '20') echo "selected"; ?>><?php echo esc_html__('20', 'wp-loyalty-optin'); ?></option>
            </select>
            <div class="wlopt-page-actions">
                <button id="wlopt-prev-page" class="wlopt-page-action" style="<?php if ($page_no <= 1) echo 'cursor: not-allowed; opacity: 0.5;'; ?>" <?php if ($page_no <= 1) echo 'disabled'; ?>>
                    <?php echo esc_html__('Prev', 'wp-loyalty-optin'); ?>
                </button>
                <div class="wlopt-page-no"><?php echo esc_html($page_no); ?></div>
                <button id="wlopt-next-page" class="wlopt-page-action" style="<?php if ($page_no >= $total_pages) echo 'cursor: not-allowed; opacity: 0.5;'; ?>" <?php if ($page_no >= $total_pages) echo 'disabled'; ?>>
                    <?php echo esc_html__('Next', 'wp-loyalty-optin'); ?>
                </button>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="wlpot-no-customer-message"><?php echo esc_html__('No Customers', 'wp-loyalty-optin'); ?>.</div>
<?php } ?>
