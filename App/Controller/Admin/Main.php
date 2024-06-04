<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Admin;

use Wlr\App\Helpers\Input;
use Wlr\App\Helpers\Woocommerce;

defined( "ABSPATH" ) or die();

class Main {
	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	function activatePlugin() {
		if ( ! \Wlopt\App\Helper\Compatibility::check() ) {
			return;
		}
	}

	/**
	 * Adding menu.
	 *
	 * @return void
	 */
	public static function adminMenu() {
		if ( Woocommerce::hasAdminPrivilege() ) {
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
	public static function addMenuPage() {
		$params = array();
		echo wc_get_template_html(
			'main.php',
			$params,
			'',
			WLOPT_PLUGIN_PATH . 'App/Views/Admin/'
		);
	}

	/**
	 * Enqueueing styles and scripts.
	 *
	 * @return void
	 */
	static function adminAssets() {
		$input_helper = new \Wlr\App\Helpers\Input();
		if ( $input_helper->get( "page" ) != WLOPT_PLUGIN_SLUG ) {
			return;
		}

		remove_all_actions( "admin_notices" );
		wp_enqueue_style( WLOPT_PLUGIN_SLUG . "-main-style", WLOPT_PLUGIN_URL . "Assets/Admin/Css/style.css",
			array(), WLOPT_PLUGIN_VERSION . "&t=" . time() );
	}

	/**
	 * Hide menu.
	 *
	 * @return void
	 */
	static function menuHide() {
		?>
        <style>
            #toplevel_page_wp-loyalty-optin {
                display: none !important;
            }
        </style>
		<?php
	}
}