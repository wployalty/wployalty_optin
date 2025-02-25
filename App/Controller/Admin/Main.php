<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Admin;

use Wlopt\App\Helper\Input;
use Wlopt\App\Helper\Woocommerce;

defined( "ABSPATH" ) or die();

class Main {

	/**
	 * Adding menu.
	 *
	 * @return void
	 */
	public static function adminMenu() {
		if ( Woocommerce::hasAdminPrivilege() ) {
			add_menu_page(
				__( 'WPLoyalty: Optin', 'wp-loyalty-optin' ),
				__( 'WPLoyalty: Optin', 'wp-loyalty-optin' ),
				"manage_woocommerce",
				WLOPT_PLUGIN_SLUG,
				[ self::class, 'addMenuPage' ],
				'dashicons-megaphone',
				59 );
		}
	}

	/**
	 * Menu page.
	 *
	 * @return void
	 */
	public static function addMenuPage() {
		if ( ! Woocommerce::hasAdminPrivilege() ) {
			return;
		}
		$params    = [];
		$file_path = get_theme_file_path( 'wp-loyalty-optin/Admin/main.php' );
		if ( ! file_exists( $file_path ) ) {
			$file_path = WLOPT_VIEW_PATH . '/Admin/main.php';
		}
		Woocommerce::renderTemplate( $file_path, $params );
	}

	/**
	 * Enqueueing styles and scripts.
	 *
	 * @return void
	 */
	static function adminAssets() {
		if ( Input::get( 'page' ) != WLOPT_PLUGIN_SLUG ) {
			return;
		}
		remove_all_actions( "admin_notices" );
		wp_enqueue_style(
			WLOPT_PLUGIN_SLUG . "-main-style",
			WLOPT_PLUGIN_URL . "Assets/Admin/Css/style.css",
			[],
			WLOPT_PLUGIN_VERSION . "&t=" . time()
		);
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