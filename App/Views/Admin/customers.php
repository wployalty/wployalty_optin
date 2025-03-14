<?php

defined( 'ABSPATH' ) or die;
$customers = $customers ?? [];
?>
<div id="wlopt-customer-page">
    <table id="wlpot-customer-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $customer) : ?>
            <tr>
                <td>
                    <?php echo esc_html($customer->display_name); ?>
                </td>
                <td>
                    <?php echo esc_html($customer->user_email); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
