<?php
/**
 * @author      Wployalty (Ilaiyaraja, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

defined( "ABSPATH" ) or die();
?>
<div id="wlopt-main-page">
    <div>
        <div class="wlopt-main-header">
            <h1><?php echo esc_html__( 'WPLoyalty - Optin', 'wp-loyalty-optin' ); ?> </h1>
            <div><b><?php echo esc_html( "v" . WLOPT_PLUGIN_VERSION ); ?></b></div>
        </div>
        <div class="wlopt-tabs">
            <a class="<?php echo ( isset( $current_view ) && $current_view == "optin_users" ) ? 'nav-tab-active' : ''; ?>"
               href="<?php echo esc_url( admin_url( 'admin.php?' . http_build_query( array(
					   'page' => WLOPT_PLUGIN_SLUG,
					   'view' => 'optin_users'
				   ) ) ) ); ?>"
            ><i class="wlr wlrop-customers"></i><?php esc_html_e( 'Customers', 'wp-loyalty-optin' ) ?></a>
            <a class="<?php echo ( isset( $current_view ) && $current_view == "settings" ) ? 'nav-tab-active' : ''; ?>"
               href="<?php echo esc_url( admin_url( 'admin.php?' . http_build_query( array(
					   'page' => WLOPT_PLUGIN_SLUG,
					   'view' => 'settings'
				   ) ) ) ) ?>"
            ><i class="wlr wlrop-settings"></i><?php esc_html_e( 'Settings', 'wp-loyalty-optin' ) ?></a>
        </div>
        <div>
            <?php echo apply_filters( 'wlopt_extra_content', ( isset( $extra ) ? $extra : null ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo isset( $tab_content ) ? $tab_content : null; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    </div>
</div>
