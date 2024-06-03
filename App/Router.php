<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App;

defined( "ABSPATH" ) or die();

class Router {

	static function init() {

		if ( is_admin() ) {
			register_activation_hook( WLOPT_PLUGIN_FILE, 'Wlopt\App\Controller\Admin\Main::activatePlugin' );
			add_action( 'admin_menu', 'Wlopt\App\Controller\Admin\Main::adminMenu' );
			add_action( 'admin_footer', 'Wlopt\App\Controller\Admin\Main::menuHide' );
			add_action( 'admin_enqueue_scripts', 'Wlopt\App\Controller\Admin\Main::adminAssets' );
		} else {
			add_action( 'wp_enqueue_scripts', 'Wlopt\App\Controller\Site\Main::siteAssets' );
			add_action( 'woocommerce_init', 'Wlopt\App\Controller\Site\Main::preventWPLoyaltyMembership' );
			add_shortcode( 'wlopt_decline_loyalty_membership', 'Wlopt\App\Controller\Site\Main::declineMembership' );
			add_shortcode( 'wlopt_accept_loyalty_membership', 'Wlopt\App\Controller\Site\Main::acceptMembership' );


		}

		add_action( 'wp_ajax_decline_wployalty_membership', 'Wlopt\App\Controller\Site\Main::updateOptIn' );
		add_action( 'wp_ajax_accept_wployalty_membership', 'Wlopt\App\Controller\Site\Main::updateAcceptance' );

		//register page
		add_action( 'woocommerce_register_form', 'Wlopt\App\Controller\Site\Main::addRegistrationCheckbox' );
		add_action( 'woocommerce_register_post', 'Wlopt\App\Controller\Site\Main::validateInRegisterForm', 10, 3 );
		add_action( 'woocommerce_created_customer', 'Wlopt\App\Controller\Site\Main::saveRegisterCheckbox', 10, 3 );
		add_action( 'user_register', 'Wlopt\App\Controller\Site\Main::addUserRegistration', 10, 1 );
		//before register status check
		add_filter( 'wlr_before_add_to_loyalty_customer', 'Wlopt\App\Controller\Site\Main::getStatusForRegisterUser', 10, 2 );

		//checkbox in checkout page
		add_action( 'woocommerce_after_checkout_billing_form', 'Wlopt\App\Controller\Site\Main::addCheckoutCheckbox' );
		add_action( 'woocommerce_after_checkout_validation', 'Wlopt\App\Controller\Site\Main::validateCheckoutForm', 10, 2 );
		add_action( 'woocommerce_checkout_create_order', 'Wlopt\App\Controller\Site\Main::saveCheckoutFormData', 10, 2 );
		//before earn via order
		add_filter( 'wlr_not_eligible_to_earn_via_order', 'Wlopt\App\Controller\Site\Main::notEligibleToEarn', 10, 3 );

		//sending email check
		add_filter( 'wlr_before_send_email', 'Wlopt\App\Controller\Site\Main::beforeSendEmail', 10, 2 );

	}

}