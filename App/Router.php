<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari)
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
			register_activation_hook( WLOPT_PLUGIN_FILE, 'Wlopt\App\Controller\Admin\Main::activatePlugin' );
			add_action( 'admin_menu', 'Wlopt\App\Controller\Admin\Main::adminMenu' );
			add_action( 'admin_footer', 'Wlopt\App\Controller\Admin\Main::menuHide' );
			add_action( 'admin_enqueue_scripts', 'Wlopt\App\Controller\Admin\Main::adminAssets' );
		} else {
			add_action( 'wp_enqueue_scripts', 'Wlopt\App\Controller\Site\Main::siteAssets' );
			add_action( 'woocommerce_init', 'Wlopt\App\Controller\Site\Main::preventWPLoyaltyMembership' );
			add_shortcode( 'wlopt_update_loyalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembership' );
		}
		//ajax action for shortcode update
		add_action( 'wp_ajax_update_wployalty_membership',
			'Wlopt\App\Controller\Site\Main::updateMembershipPreference' );
		//register page
		add_action( 'woocommerce_register_form', 'Wlopt\App\Controller\Site\Main::addRegistrationCheckbox' );
		add_action( 'woocommerce_register_post', 'Wlopt\App\Controller\Site\Main::validateInRegisterForm', 10, 3 );
		add_action( 'user_register', 'Wlopt\App\Controller\Site\Main::addUserRegistration', 10, 1 );
		add_action( 'wp_login', 'Wlopt\App\Controller\Site\Main::preventAddCustomerToLoyalty', 9, 2 );
		//Classic Checkout
		add_action( 'woocommerce_after_checkout_billing_form', 'Wlopt\App\Controller\Site\Main::addCheckoutCheckbox' );
		add_action( 'woocommerce_after_checkout_validation', 'Wlopt\App\Controller\Site\Main::validateCheckoutForm', 10,
			2 );
		add_action( 'woocommerce_checkout_create_order', 'Wlopt\App\Controller\Site\Main::saveCheckoutFormData',
			PHP_INT_MAX,
			2 );
		/* Block checkout */
		add_action( 'plugins_loaded', 'Wlopt\App\Controller\Site\Main::initBlocks' );

		add_action( 'woocommerce_store_api_checkout_update_customer_from_request',
			'Wlopt\App\Controller\Site\Main::checkBlockCheckoutEarning', 7, 2 );

		add_filter( 'wlr_before_add_to_loyalty_customer', 'Wlopt\App\Controller\Site\Main::preventEarning', 10, 3 );
		/* Clear transient */
		add_action( 'wp_logout', 'Wlopt\App\Controller\Site\Main::clearTransient', 10 );
		add_action( 'register_deactivation_hook', 'Wlopt\App\Controller\Site\Main::clearTransient', 10 );

	}

}