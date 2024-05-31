<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Admin;

use Wlopt\App\Controller\Base;
use Wlr\App\Helpers\Input;

defined( "ABSPATH" ) or die();

class Main extends Base {
	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	function activatePlugin() {

	}

	/**
	 * Adding menu.
	 *
	 * @return void
	 */
	static function adminMenu() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_menu_page( __( "WPLoyalty: Optin", "wp-loyalty-optin" ),
				__( "WPLoyalty: Optin", "wp-loyalty-optin" ), "manage_woocommerce", WLOPT_PLUGIN_SLUG,
				"Wlopt\App\Controller\Admin\Main::addMenuPage", 'dashicons-megaphone', 59 );
		}
	}

	/**
	 * Menu page.
	 *
	 * @return void
	 */
	static function addMenuPage() {
		echo "hey dude!";
	}

	/**
	 * Enqueueing styles and scripts.
	 *
	 * @return void
	 */
	static function adminAssets() {

	}

}