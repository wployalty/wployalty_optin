<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App;

use Wlopt\App\Controller\Site\Main;
use Wlopt\App\Model\Users;

defined( "ABSPATH" ) or die();

class Router {
	/**
	 * Hooks for all actions related.
	 *
	 * @return void
	 */
	public static function init() {
		if ( is_admin() ) {
			add_action( 'admin_menu', 'Wlopt\App\Controller\Admin\Main::adminMenu' );
			add_action( 'admin_footer', 'Wlopt\App\Controller\Admin\Main::menuHide' );
			add_action( 'admin_enqueue_scripts', 'Wlopt\App\Controller\Admin\Main::adminAssets' );
			if ( wp_doing_ajax() ) {
                add_action( 'wp_ajax_wlopt_get_customer_details', 'Wlopt\App\Controller\Admin\Main::showCustomerDetails' );
			}
		}
		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_update_wployalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembershipPreference' );
		}
        add_filter( 'wlr_before_add_to_loyalty_customer', function ($status, $user_id, $user_email) {
            if (!empty($user_email)) {
                return Main::checkStatus($user_email);
            }
            return $status;
        }, 10, 3 );
        add_action( 'wp_enqueue_scripts', 'Wlopt\App\Controller\Site\Main::siteAssets' );
        add_action( 'init', 'Wlopt\App\Controller\Site\Main::preventWPLoyaltyMembership', 1 );
        add_action( 'template_redirect', 'Wlopt\App\Controller\Site\Main::handleExistingUserPreference' );
        add_action( 'woocommerce_order_status_changed', 'Wlopt\App\Controller\Site\Main::handleOrderStatusChange', 1, 4 );
        add_shortcode( 'wlopt_update_loyalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembership' );
        //register & login case
		add_action( 'woocommerce_register_form', 'Wlopt\App\Controller\Site\Main::addRegistrationCheckbox' );
		add_action( 'woocommerce_register_post', 'Wlopt\App\Controller\Site\Main::validateInRegisterForm', 10, 3 );
		add_action( 'user_register', 'Wlopt\App\Controller\Site\Main::registerUserHandler', 1, 1 );
		add_action( 'wp_login', 'Wlopt\App\Controller\Site\Main::loginUserHandler', 9, 2 );

        //Classic Checkout
        add_action( 'woocommerce_after_checkout_billing_form', 'Wlopt\App\Controller\Site\Main::addCheckoutCheckbox' );
        add_action( 'woocommerce_after_checkout_validation', 'Wlopt\App\Controller\Site\Main::validateCheckoutForm', 10, 2 );
        add_action( 'woocommerce_checkout_create_order', 'Wlopt\App\Controller\Site\Main::saveCheckoutFormData', PHP_INT_MAX, 2 );

        /* Block checkout */
        add_action( 'plugins_loaded', 'Wlopt\App\Controller\Site\Main::initBlocks' , 20);
        add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'Wlopt\App\Controller\Site\Main::updateOrderFromRequest', 10, 2 );
        add_action( 'woocommerce_store_api_checkout_update_customer_from_request',
            'Wlopt\App\Controller\Site\Main::checkBlockCheckoutEarning', 7, 2 );

        add_filter( 'wlr_delete_customer', [ __CLASS__, 'deleteOptInData' ], 10, 2 );
        add_action( 'wp_set_comment_status', 'Wlopt\App\Controller\Site\Main::handleReviewApproval', 1, 2  );
	}

    /**
     * Delete opt-in data if the customer deleted in WPLoyalty
     *
     * @param $status
     * @param $condition
     * @return mixed
     */
    public static function deleteOptInData($status, $condition) {
        if (!$status || empty($condition) || !is_array($condition)) {
            return $status;
        }
        Users::delete($condition, '%s');

        return $status;
    }

}