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
            add_action('woocommerce_init',[self::$site,'preventWPLoyaltyMembership']);
            add_shortcode('wlopt_decline_loyalty_membership',[self::$site,'declineMembership']);
            add_shortcode('wlopt_accept_loyalty_membership',[self::$site,'acceptMembership']);
        }
        add_action('wp_ajax_decline_wployalty_membership', [self::$site,'updateOptIn']);
        add_action('wp_ajax_accept_wployalty_membership', [self::$site,'updateAcceptance']);

        //add check box in register page
//        add_action( 'woocommerce_register_form', [self::$site,'addRegistrationCheckbox'] );
//        add_action('woocommerce_register_post', array(self::$main, 'validateInRegisterForm'), 10, 3);
//        add_action( 'woocommerce_created_customer', [self::$site,'saveRegisterCheckbox'] ,10,3);

        //add checkbox in checkout
//        add_action( 'woocommerce_after_checkout_billing_form', [self::$site,'addCheckoutCheckbox'] );
//        add_action('woocommerce_checkout_create_order', [self::$site, 'saveCheckoutFormData'], 10, 2);

    }

}