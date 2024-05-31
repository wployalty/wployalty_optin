<?php

namespace Wlopt\App;
use Wlopt\App\Controller\Admin\Main;


defined("ABSPATH") or die();
class Router
{
    private static $admin,$site;

    function init(){

        self::$admin = empty(self::$admin) ? new Main() : self::$admin;

        //1. compatibility check
//        $compatibility = new \Wlopt\App\Helper\Compatibility();
//        var_dump($compatibility);
//        if (!$compatibility->check()){
//            return;
//        }
        self::$site = empty(self::$site) ? new \Wlopt\App\Controller\Site\Main() : self::$site;
        //2. add on menu
        if (is_admin()){
            add_action('admin_menu', [self::$admin,'adminMenu']);
            add_action('admin_enqueue_scripts', [self::$admin,'adminAssets']);
        }else{

            add_action('wp_enqueue_scripts', [self::$site,'siteAssets']);

            //shortcode for checkbox render and save
            add_shortcode('wlr_optin_checkbox',[self::$site,'addCheckoutCheckbox']);
        }
        add_action('wp_ajax_decline_wployalty_membership', [self::$site,'updateOptIn']);



        //add check box in register page
        add_action( 'woocommerce_register_form', [self::$site,'addRegistrationCheckbox'] );
//        add_action('woocommerce_register_post', array(self::$main, 'validateInRegisterForm'), 10, 3);
        add_action( 'woocommerce_created_customer', [self::$site,'saveRegisterCheckbox'] ,10,3);

        //add checkbox in checkout
        add_action( 'woocommerce_after_checkout_billing_form', [self::$site,'addCheckoutCheckbox'] );
        add_action('woocommerce_checkout_create_order', [self::$site, 'saveCheckoutFormData'], 10, 2);

        //3.check eligibility
        add_filter('wlr_before_load_wployalty_actions',function($status){
//            var_dump($status);
            return true;
            return $status;
        },10,1);


    }

}