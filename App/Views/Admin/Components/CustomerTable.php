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
$index = ($page_no == 1) ? 1 : ($page_no * $list_no) - ($list_no - 1);
$total_pages = ceil($customers_details['total_users'] / $list_no);

if (!empty($customers_details['customers'])) { ?>
    <table id="wlpot-customer-table">
        <thead>
        <tr>
            <th><?php echo esc_html__('S NO', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Email', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Point balance', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Total Earned', 'wp-loyalty-optin'); ?></th>
            <th><?php echo esc_html__('Points Redeemed', 'wp-loyalty-optin'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($customers_details['customers'] as $key => $customer) : ?>
            <?php if (!empty($customer['user_email']) && is_email($customer['user_email'])) : ?>
            <tr>
                <td><?php echo esc_html($key + $index); ?></td>
                <td>
                    <?php echo esc_html($customer['user_email']); ?>
                </td>
                <td>
                    <?php echo !empty($customer['points']) ? esc_html($customer['points']) : '0'; ?>
                </td>
                <td>
                    <?php echo !empty($customer['earn_total_point']) ? esc_html($customer['earn_total_point']) : '0'; ?>
                </td>
                <td>
                    <?php echo !empty($customer['used_total_points']) ? esc_html($customer['used_total_points']) : '0'; ?>
                </td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="wlopt-customer-footer">
        <div class="wlopt-customer-list-actions">
            <div class="wlopt-customer-list-select-wrapper">
                <select id="wlpot-customer-list-count">
                    <option value="5" <?php if ($list_no == '5') echo "selected"; ?>><?php echo esc_html('5'); ?></option>
                    <option value="10" <?php if ($list_no == '10') echo "selected"; ?>><?php echo esc_html('10'); ?></option>
                    <option value="15" <?php if ($list_no == '15') echo "selected"; ?>><?php echo esc_html('15'); ?></option>
                    <option value="20" <?php if ($list_no == '20') echo "selected"; ?>><?php echo esc_html('20'); ?></option>
                    <option value="50" <?php if ($list_no == '50') echo "selected"; ?>><?php echo esc_html('50'); ?></option>
                    <option value="100" <?php if ($list_no == '100') echo "selected"; ?>><?php echo esc_html('100'); ?></option>
                </select>
            </div>
            <div class="wlopt-page-actions">
                <button id="wlopt-prev-page" class="wlopt-page-action" style="<?php if ($page_no <= 1) echo 'opacity: 0.5; pointer-events: none;'; ?>" <?php if ($page_no <= 1) echo 'disabled'; ?>>
                    <?php echo esc_html__('Prev', 'wp-loyalty-optin'); ?>
                </button>
                <div class="wlopt-page-no"><?php echo esc_html($page_no); ?></div>
                <button id="wlopt-next-page" class="wlopt-page-action" style="<?php if ($page_no >= $total_pages) echo 'pointer-events: none; opacity: 0.5;'; ?>" <?php if ($page_no >= $total_pages) echo 'disabled'; ?>>
                    <?php echo esc_html__('Next', 'wp-loyalty-optin'); ?>
                </button>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="wlpot-no-customer-message">
        <?php echo esc_html__('No Customers to display', 'wp-loyalty-optin'); ?>.
        <p><?php echo esc_html__('No customers found for your search. Please try different filters', 'wp-loyalty-optin'); ?></p>
    </div>
<?php } ?>
