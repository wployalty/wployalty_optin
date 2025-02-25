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
			add_action( 'admin_menu', 'Wlopt\App\Controller\Admin\Main::adminMenu' );
			add_action( 'admin_footer', 'Wlopt\App\Controller\Admin\Main::menuHide' );
			add_action( 'admin_enqueue_scripts', 'Wlopt\App\Controller\Admin\Main::adminAssets' );
		} else {
			add_action( 'wp_enqueue_scripts', 'Wlopt\App\Controller\Site\Main::siteAssets' );
			add_action( 'woocommerce_init', 'Wlopt\App\Controller\Site\Main::preventWPLoyaltyMembership' );
			add_shortcode( 'wlopt_update_loyalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembership' );
		}
		if ( wp_doing_ajax() ) {
			//ajax action for shortcode update
			add_action( 'wp_ajax_update_wployalty_membership', 'Wlopt\App\Controller\Site\Main::updateMembershipPreference' );
		}
		//register & login case
		add_action( 'woocommerce_register_form', 'Wlopt\App\Controller\Site\Main::addRegistrationCheckbox' );
		add_action( 'woocommerce_register_post', 'Wlopt\App\Controller\Site\Main::validateInRegisterForm', 10, 3 );
		add_action( 'user_register', 'Wlopt\App\Controller\Site\Main::registerUserHandler', 9, 1 );
		add_action( 'wp_login', 'Wlopt\App\Controller\Site\Main::loginUserHandler', 9, 2 );

	}

}