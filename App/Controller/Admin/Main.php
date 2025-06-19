<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Admin;

use Wlopt\App\Helper\Input;
use Wlopt\App\Helper\Woocommerce;
use Wlopt\App\Model\Users;

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
				59
            );
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
		if ( Input::get( 'page' ) == WLOPT_PLUGIN_SLUG ) {
			$view             = (string) Input::get( 'view', 'settings' );
			$main_page_params = [
				'current_view' => $view,
				'tab_content'  => null,
			];
			$main_page_params = apply_filters( 'wlopt_manage_pages_data', $main_page_params );
			switch ( $view ) {
				case 'optin_users':
                    $customers_details = self::getCustomersDetails();
                    $main_page_params['tab_content'] = Woocommerce::renderTemplate(
                    WLOPT_VIEW_PATH . '/Admin/Customers.php',
                        [
                            'customers_details' => $customers_details,
                            'app_url'           => admin_url('admin.php?' . http_build_query(array('page' => WLR_PLUGIN_SLUG))) . '#/apps',
                            'customers_table'    => Woocommerce::renderTemplate(
                                    WLOPT_VIEW_PATH . '/Admin/Components/CustomerTable.php', [
                                    'customers_details' => $customers_details,
                                    'page_no' => 1,
                                ], false )
                        ],
                        false
                    );
					break;
				case 'settings':
					$main_page_params['tab_content'] = Woocommerce::renderTemplate(
                    WLOPT_VIEW_PATH . '/Admin/Settings.php',
                        [
                            'app_url'                  => admin_url('admin.php?' . http_build_query(array('page' => WLR_PLUGIN_SLUG))) . '#/apps',
                            'wlopt_save_setting_nonce' => Woocommerce::create_nonce( 'wlopt_setting_nonce' ),
                        ],
                        false
                    );
					break;
				default:
					break;
			}
			if ( in_array( $view, array( 'settings', 'optin_users' ) ) ) {
				$path = WLOPT_PLUGIN_PATH . 'App/Views/Admin/Main.php';
				Woocommerce::renderTemplate( $path, $main_page_params );
			}
		} else {
			wp_die( esc_html( __( 'Page query params missing...', 'wp-loyalty-rules' ) ) );
		}
		/*$params    = [];
		$file_path = get_theme_file_path( 'wp-loyalty-optin/Admin/main.php' );
		if ( ! file_exists( $file_path ) ) {
			$file_path = WLOPT_VIEW_PATH . '/Admin/main.php';
		}
		Woocommerce::renderTemplate( $file_path, $params );*/
	}

	/**
	 * Enqueueing styles and scripts.
	 *
	 * @return void
	 */
	public static function adminAssets() {
		if ( Input::get( 'page' ) != WLOPT_PLUGIN_SLUG ) {
			return;
		}
		remove_all_actions( "admin_notices" );
		// Enqueueing styles
		wp_register_style(
			WLOPT_PLUGIN_SLUG . '-wlrop-font',
			WLOPT_PLUGIN_URL . 'Assets/Admin/Css/wlrop-fonts.css',
			[],
			WLOPT_PLUGIN_VERSION . '&t=' . time() );
		wp_enqueue_style( WLOPT_PLUGIN_SLUG . '-wlrop-font' );
		wp_enqueue_style(
			WLOPT_PLUGIN_SLUG . "-main-style",
			WLOPT_PLUGIN_URL . "Assets/Admin/Css/style.css",
			[],
			WLOPT_PLUGIN_VERSION . "&t=" . time()
		);
		wp_register_script(
			WLOPT_PLUGIN_SLUG . '-main-script',
			WLOPT_PLUGIN_URL . 'Assets/Admin/Js/wlopt-main.js',
			[ 'jquery' ],
			WLOPT_PLUGIN_VERSION . '&t=' . time(),
            true
		);
		wp_enqueue_script( WLOPT_PLUGIN_SLUG . '-main-script' );
        wp_enqueue_style( WLR_PLUGIN_SLUG . '-alertify', WLR_PLUGIN_URL . 'Assets/Admin/Css/alertify.min.css', array(), WLR_PLUGIN_VERSION);
        wp_enqueue_script( WLR_PLUGIN_SLUG . '-alertify', WLR_PLUGIN_URL . 'Assets/Admin/Js/alertify.min.js', array(), WLR_PLUGIN_VERSION . '&t=' . time(), true);
        wp_enqueue_style( WLR_PLUGIN_SLUG . '-wlr-font', WLR_PLUGIN_URL . 'Assets/Site/Css/wlrop-fonts.css', array(), WLR_PLUGIN_VERSION);

		//localize script
		$localize = [
			'home_url'              => get_home_url(),
			'admin_url'             => admin_url(),
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			'save_nonce'            => wp_create_nonce( 'wlopt_save_setting_nonce' ),
            'get_customer_details'  => wp_create_nonce( 'wlopt_customer_details_nonce' ),
			'copy_clipboard'        => __( 'Copied to clipboard', 'wp-loyalty-optin' ),
            'copy_error_message'    => __( 'Could not copy text.', 'wp-loyalty-optin' ),
			'onboarding_save_nonce' => wp_create_nonce( 'wlopt_onboarding_save_nonce' ),
		];
		wp_localize_script( WLOPT_PLUGIN_SLUG . '-main-script', 'wlopt_localize_data', $localize );
	}

	/**
	 * Hide menu.
	 *
	 * @return void
	 */
    public static function menuHide() {
		?>
        <style>
            #toplevel_page_wp-loyalty-optin {
                display: none !important;
            }
        </style>
		<?php
	}

    /**
     * To update customer details based on customer type, page and display list.
     *
     * @return void
     */
    public static function showCustomerDetails()
    {
        $wlrop_nonce = Input::get( 'wlopt_nonce' );
        if ( ! Woocommerce::verify_nonce( $wlrop_nonce, 'wlopt_customer_details_nonce' ) ) {
            wp_send_json_error( [ 'message' => __( 'Security verification failed', 'wp-loyalty-optin' ) ] );
        }
        try {
            $customer_type = Input::get( 'customer_type', '' );
            $list_no = Input::get( 'list_no', 5 );
            $page_no = Input::get( 'page_no', 1 );
            $search_email = Input::get( 'search_email', '' );
            $customers_details = self::getCustomersDetails( $customer_type, $list_no, $page_no, $search_email );
            $html = Woocommerce::renderTemplate(
                WLOPT_VIEW_PATH . '/Admin/Components/CustomerTable.php', [
                    'customers_details' => $customers_details,
                    'page_no' => 1,
                ],
                false
            );
            wp_send_json_success( [ 'html' => $html] );
        } catch ( \Exception $e ) {
            wp_send_json_error( [ 'message' => $e->getMessage() ] );
        }
    }

    /**
     * Get customers details
     *
     * @param string $customer_type
     * @param int $list_no
     * @param int $page_no
     * @param string $search_email
     * @return array
     */
    public static function getCustomersDetails ($customer_type = 'opt-in', $list_no = 5, $page_no = 1, $search_email = '')
    {
        $customer_type = ($customer_type == 'opt-in') ? 1 : 0;
        $total_users = Users::totalUsersCount( $customer_type );
        $customer_loyalty_data = Users::getUsersDetails( $customer_type, $page_no, $list_no, $search_email );

        return array(
            'total_users' => $total_users,
            'customers' => $customer_loyalty_data,
            'list_no' => $list_no,
            'page_no' => $page_no,
        );
    }
}