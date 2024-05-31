<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Admin;

use Wlopt\App\Controller\Base;
use Wlr\App\Helpers\Input;

class Main extends Base
{
    /**
     * Plugin activation.
     *
     * @return void
     */
    function activatePlugin(){
        $compatibility = new \Wlopt\App\Helper\Compatibility();
//        if (!$compatibility->check(true)) {
//
//        }

    }

    /**
     * Adding menu.
     *
     * @return void
     */
    function adminMenu()
    {
        if (current_user_can('manage_woocommerce')) {
            add_menu_page(__("WPLoyalty: Optin", "wp-loyalty-optin"),
                __("WPLoyalty: Optin", "wp-loyalty-otin"), "manage_woocommerce", WLOPT_PLUGIN_SLUG,
                array($this, "addMenuPage"), 'dashicons-megaphone', 59);
        }
    }

    /**
     * Menu page.
     *
     * @return void
     */
    function addMenuPage()
    {
        echo "hey dude!";
    }

    /**
     * Enqueueing styles and scripts.
     *
     * @return void
     */
    function adminAssets()
    {

    }

}