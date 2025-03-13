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
		if ( Input::get( 'page' ) == WLOPT_PLUGIN_SLUG ) {
			$view             = (string) Input::get( 'view', 'settings' );
			$main_page_params = [
				'current_view' => $view,
				'tab_content'  => null,
			];
			$main_page_params = apply_filters( 'wlopt_manage_pages_data', $main_page_params );
			switch ( $view ) {
				case 'optin_users':
                    $customers = get_users(array(
                        'meta_query' => array(
                            array(
                                'key'   => 'accept_wployalty_membership',
                                'value' => 'yes',
                                'compare' => '='
                            )
                        )
                    ));
                    $main_page_params['tab_content'] = Woocommerce::renderTemplate( WLOPT_VIEW_PATH . '/Admin/customers.php', ['customers' => $customers], false );
					break;
				case 'settings':
					$options = get_option( 'wlopt_settings', [] );
					if ( ! is_array( $options ) ) {
						$options = [];
					}
					$page_details                    = [
						'options'                  => $options,
						'app_url'                  => admin_url( 'admin.php?' . http_build_query( [ 'page' => WLOPT_PLUGIN_SLUG ] ) ) . '#/apps',
						'wlopt_save_setting_nonce' => Woocommerce::create_nonce( 'wlopt_setting_nonce' ),
						'save'                     => WLOPT_PLUGIN_URL . 'Assets/svg/save.svg',
						'back'                     => WLOPT_PLUGIN_URL . 'Assets/svg/back.svg',
					];
					$main_page_params['tab_content'] = Woocommerce::renderTemplate( WLOPT_VIEW_PATH . '/Admin/settings.php', $page_details, false );
					break;
				default:
					break;
			}
			if ( in_array( $view, array( 'settings', 'optin_users' ) ) ) {
				$path = WLOPT_PLUGIN_PATH . 'App/Views/Admin/main.php';
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
	static function adminAssets() {
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
		wp_enqueue_style(
			WLOPT_PLUGIN_SLUG . '-alertify',
			WLOPT_PLUGIN_URL . 'Assets/Admin/Css/alertify.css',
			[],
			WLOPT_PLUGIN_VERSION
		);
		// Enqueueing scripts
		wp_enqueue_script(
			WLOPT_PLUGIN_SLUG . '-alertify',
			WLOPT_PLUGIN_URL . 'Assets/Admin/Js/alertify.js',
			[],
			WLOPT_PLUGIN_VERSION . '&t=' . time()
		);
		wp_register_script(
			WLOPT_PLUGIN_SLUG . '-main-script',
			WLOPT_PLUGIN_URL . 'Assets/Admin/Js/wlopt-main.js',
			[ 'jquery' ],
			WLOPT_PLUGIN_VERSION . '&t=' . time()
		);
		wp_enqueue_script( WLOPT_PLUGIN_SLUG . '-main-script' );

		//localize script
		$localize = [
			'home_url'              => get_home_url(),
			'admin_url'             => admin_url(),
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			'save_nonce'            => wp_create_nonce( 'wlopt_save_setting_nonce' ),
			'saving_button_label'   => __( 'Saving...', 'wp-loyalty-optin' ),
			'saved_button_label'    => __( 'Save Changes', 'wp-loyalty-optin' ),
			'onboarding_save_nonce' => wp_create_nonce( 'wlopt_onboarding_save_nonce' ),
		];
		wp_localize_script( WLOPT_PLUGIN_SLUG . '-main-script', 'wlopt_localize_data', $localize );
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

	public static function saveSettings() {
		$wlrop_nonce = Input::get( 'wlopt_nonce' );
		if ( ! Woocommerce::verify_nonce( $wlrop_nonce, 'wlopt_save_setting_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Security verification failed', 'wp-loyalty-optin' ) ] );
		}
		try {
			$value = sanitize_text_field( Input::get( 'enable_optin' ) );
			if ( ! in_array( $value, [ 'yes', 'no' ] ) ) {
				wp_send_json_error( [ 'message' => __( 'Invalid value', 'wp-loyalty-optin' ) ] );
			}
			$options = get_option( 'wlopt_settings', [] );
			if ( ! is_array( $options ) ) {
				$options = [];
			}
			$options['enable_optin'] = $value;
			update_option( 'wlopt_settings', $options );
			wp_send_json_success( [ 'message' => __( 'Settings saved successfully', 'wp-loyalty-optin' ) ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}
}