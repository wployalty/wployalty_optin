<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App;

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
				add_action( 'wp_ajax_wlopt_save_settings', 'Wlopt\App\Controller\Admin\Main::saveSettings' );
                add_action( 'wp_ajax_wlopt_get_customer_details', 'Wlopt\App\Controller\Admin\Main::showCustomerDetails' );
			}
		}
		if ( ! self::isOptinEnabled() ) {
			return;
		}
		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_update_wployalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembershipPreference' );
		}
		add_shortcode( 'wlopt_update_loyalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembership' );
		add_action( 'template_redirect', 'Wlopt\App\Controller\Site\Main::handleExistingUserPreference' );
		add_action( 'wp_enqueue_scripts', 'Wlopt\App\Controller\Site\Main::siteAssets' );
		add_action( 'woocommerce_init', 'Wlopt\App\Controller\Site\Main::preventWPLoyaltyMembership' );
		//register & login case
		add_action( 'woocommerce_register_form', 'Wlopt\App\Controller\Site\Main::addRegistrationCheckbox' );
		add_action( 'woocommerce_register_post', 'Wlopt\App\Controller\Site\Main::validateInRegisterForm', 10, 3 );
		add_action( 'user_register', 'Wlopt\App\Controller\Site\Main::registerUserHandler', 9, 1 );
		add_action( 'wp_login', 'Wlopt\App\Controller\Site\Main::loginUserHandler', 9, 2 );

        //Classic Checkout
        add_action( 'woocommerce_after_checkout_billing_form', 'Wlopt\App\Controller\Site\Main::addCheckoutCheckbox' );
        add_action( 'woocommerce_after_checkout_validation', 'Wlopt\App\Controller\Site\Main::validateCheckoutForm', 10, 2 );
        add_action( 'woocommerce_checkout_create_order', 'Wlopt\App\Controller\Site\Main::saveCheckoutFormData', PHP_INT_MAX, 2 );

        /* Block checkout */
        add_action( 'plugins_loaded', 'Wlopt\App\Controller\Site\Main::initBlocks' , 20);
        add_action( 'woocommerce_store_api_checkout_update_customer_from_request',
            'Wlopt\App\Controller\Site\Main::checkBlockCheckoutEarning', 7, 2 );
	}

    /**
     * Check opt-in is enabled.
     *
     * @return bool
     */
	private static function isOptinEnabled() {
		$options = get_option( 'wlopt_settings', [] );

		return isset( $options['enable_optin'] ) && $options['enable_optin'] === 'yes';
	}

}