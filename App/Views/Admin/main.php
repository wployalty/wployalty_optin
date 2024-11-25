<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */
defined( "ABSPATH" ) or die();
?>
<div id="wlopt-main-page">
    <div>
        <div class="wlopt-main-header">
            <h1><?php echo WLOPT_PLUGIN_NAME; ?> </h1>
            <div><b><?php echo "v" . WLOPT_PLUGIN_VERSION; ?></b></div>
        </div>
        <div class="wlopt-admin-main">
            <div class="wlopt-admin-nav">
                <a class="active-nav"
                   href="<?php echo admin_url( "admin.php?" . http_build_query( array(
						   "page" => WLOPT_PLUGIN_SLUG,
						   "view" => 'settings'
					   ) ) ) ?>"
                ><?php _e( "Settings", "wp-loyalty-optin" ); ?></a>
            </div>
        </div>
        <div class="wlopt-parent">
            <div class="wlopt-body-content">

                <div class="wlopt-body-active-content active-content">
                    <div>
                        <p><?php _e( 'Use this shortcode to let user update preference to wployalty membership: [wlopt_update_loyalty_membership]',
								'wp-loyalty-optin' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
