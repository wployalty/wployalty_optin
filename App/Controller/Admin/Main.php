<?php

namespace Wlopt\App\Controller\Admin;

use Wlopt\App\Controller\Base;
use Wlr\App\Helpers\Input;

class Main extends Base
{
    function adminMenu()
    {
        if (current_user_can('manage_woocommerce')) {
            add_menu_page(__("WPLoyalty: Optin", "wp-loyalty-optin"),
                __("WPLoyalty: Optin", "wp-loyalty-otin"), "manage_woocommerce", WLOPT_PLUGIN_SLUG,
                array($this, "addMenuPage"), 'dashicons-megaphone', 59);
        }
    }

    function addMenuPage()
    {
        echo "heeloo";
    }

    function adminAssets()
    {

    }

    function checkToShowOption()
    {

    }





    function saveCheckoutFormData($order, $data)
    {

    }

}