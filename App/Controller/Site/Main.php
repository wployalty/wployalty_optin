<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Site;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Wlopt\App\Controller\Site\Blocks\Integration\Message;
use Wlopt\App\Helper\Input;
use Wlopt\App\Helper\Woocommerce;

defined( "ABSPATH" ) or die();

class Main {
	/**
	 * Customer email variable.
	 *
	 * @var
	 */
	public static $email;

	/**
	 * Checks status and prevent earning.
	 *
	 * @return void
	 */
	public static function preventWPLoyaltyMembership() {
		if ( self::checkStatus() ) {
			return;
		}
		//display message
		add_filter( 'wlr_before_display_messages', '__return_false' );
		//Loyalty assets
		add_filter( 'wlr_before_loyalty_assets', '__return_false' );
		//Launcher assets
		add_filter( 'wll_before_launcher_display', '__return_false' );
		add_filter( 'wll_before_launcher_assets', '__return_false' );
		//loyalty menu
		add_filter( 'wlr_myaccount_loyalty_menu_label', function ( $menu_items ) {
			unset( $menu_items['loyalty_reward'] );

			return $menu_items;
		} );
		add_filter( 'wlr_before_adding_menu', '__return_false' );
		add_filter( 'wlr_before_adding_menu_endpoint', '__return_false' );
		//birthday fields
		add_filter( 'wlr_before_adding_birthday_fields', '__return_false' );
		//hide signup message
		add_filter( 'wlr_show_signup_message_for_guest_user', '__return_false' );
		//hide birthday input
		add_filter( 'wlr_show_birthday_input_for_guest_user', '__return_false' );
		//hide earn message in block cart & checkout
		add_filter( 'wlr_points_rewards_earn_points_message', function ( $message, $short_code_list ) {
			return '';
		}, 10, 2 );
		//hide redeem message in block cart & checkout
		add_filter( 'wlr_point_redeem_points_message', function ( $message ) {
			return '';
		}, 10, 1 );
		//hide reward page
		add_filter( 'wlr_my_account_point_and_reward_page', function ( $my_account_content, $main_page_params ) {
			return '';
		}, 10, 2 );
	}

	/**
	 * Check status of earning.
	 *
	 * @return bool Return true if opted, false if not opted.
	 */
	public static function checkStatus() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return apply_filters( 'wlopt_work_on_guest_user', false );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			$accept_wployalty_membership = get_user_meta( $user_data->ID, 'accept_wployalty_membership', true );
			if ( $accept_wployalty_membership == "yes" ) {
				return true;
			} else if (Woocommerce::isLoyaltyUser( $user_email )) {
                update_user_meta( $user_data->ID, 'accept_wployalty_membership', 'yes');
                return true;
			}
		}

		return false;
	}

	/**
	 * Get logged user email.
	 *
	 * @return mixed
	 */
	static function getEmail() {
		if ( ! empty( self::$email ) ) {
			return self::$email;
		}
		$woo_helper = Woocommerce::getInstance();

		return self::$email = $woo_helper->get_login_user_email();
	}

	/**
	 * Site assets.
	 *
	 * @return void
	 */
	static function siteAssets() {
		$suffix = '.min';
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			$suffix = SCRIPT_DEBUG ? '' : '.min';
		}
		wp_enqueue_script(
			WLOPT_PLUGIN_SLUG . '-main',
			WLOPT_PLUGIN_URL . 'Assets/Site/Js/main' . $suffix . '.js',
			[ 'jquery' ],
			WLOPT_PLUGIN_VERSION . '&t=' . time()
		);
		$localize = [
			'ajax_url'                    => admin_url( 'admin-ajax.php' ),
			'update_wployalty_membership' => wp_create_nonce( 'update_wployalty_membership_nonce' )
		];
		wp_localize_script(
			WLOPT_PLUGIN_SLUG . '-main',
			'wlopt_localize_data',
			$localize
		);
	}

	/**
	 * Shortcode for field to update membership preference.
	 *
	 * @return false|string
	 */
	public static function updateMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) || Woocommerce::isBannedUser( $user_email ) ) {
			return '';
		}
		$checked = '';
		if ( self::checkStatus() ) {
			$checked = 'checked';
		}
		ob_start();
		?>
        <div class="wlopt-update-membership">
            <input type="checkbox" name="update_wployalty_membership"
                   id="update_wployalty_membership"<?php echo $checked ?>>
            <label for="update_wployalty_membership"
                   class="wlr-text-color"><?php echo __( 'Check this to become a member of WPLoyalty program.',
					'wp-loyalty-optin' ) ?></label>
        </div>
		<?php
		return ob_get_clean();
	}

	public static function updateMembershipPreference() {
		$wlr_nonce = (string) Input::get( 'wlopt_nonce', '' );
		$json      = [
			'success' => false,
			'data'    => [
				'message' => __( 'Update data failed', 'wp-loyalty-optin' )
			]
		];
		if ( ! Woocommerce::verify_nonce( $wlr_nonce, 'update_wployalty_membership_nonce' ) ) {
			$json['message'] = __( 'Invalid nonce', 'wp-loyalty-optin' );
			wp_send_json( $json );
		}
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership', 0 ) ? "yes" : "no";
		$user_email                  = self::getEmail();
		if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership',
				sanitize_text_field( $accept_wployalty_membership ) );
			$json['success']         = true;
			$json['data']['message'] = __( 'Updated successfully', 'wp-loyalty-optin' );
		}
		wp_send_json( $json );
	}

	/**
	 * Add field in register page.
	 *
	 * @return void
	 */
	static function addRegistrationCheckbox() {
		$user_email = self::getEmail();
		if ( ! empty( $user_email ) ) {
			return;
		}

		woocommerce_form_field( 'accept_wployalty_membership', [
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => [ 'form-row-wide accept_wployalty_membership' ],
			'label'    => __( 'Check this to become a member of WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		] );
	}

	/**
	 * Validate registration page.
	 *
	 * @param $username
	 * @param $user_email
	 * @param $errors
	 *
	 * @return mixed
	 */
	static function validateInRegisterForm( $username, $user_email, $errors ) {
		$accept_wployalty_membership = (int) Input::get( 'accept_wployalty_membership', 0 );

		if ( ! in_array( $accept_wployalty_membership, [ 0, 1 ] ) ) {
			$errors->add( 'accept_wployalty_membership', __( 'Must be valid', 'wp-loyalty-optin' ) );
		}

		return $errors;
	}

	/**
	 * Update data after user registration.
	 *
	 * @param $user_id
	 *
	 * @return void
	 */
	static function registerUserHandler( $user_id ) {
		if ( empty( $user_id ) ) {
			return;
		}
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership', 0 ) ? 'yes' : 'no';

		if ( $accept_wployalty_membership == 'no' ) {
			add_filter( 'wlr_before_add_to_loyalty_customer', '__return_false', 10, 1 );
		}
		update_user_meta( $user_id, 'accept_wployalty_membership', sanitize_text_field( $accept_wployalty_membership ) );
	}

	/**
	 * Handle user login to prevent adding customer to loyalty by default.
	 *
	 * This method checks the user's loyalty membership status upon login.
	 * If the user has not accepted the loyalty membership, it updates the user meta
	 * to reflect the non-acceptance and prevents adding the user to the loyalty program.
	 * For existing users, it updates the user meta to as per store owner choince in onboarding.
	 *
	 * @param string $user_name The username of the user logging in.
	 * @param \WP_User $user The WP_User object of the user logging in.
	 *
	 * @return void
	 */
	static function loginUserHandler( $user_name, $user ) {
		if ( empty( $user ) || ! is_object( $user ) ) {
			return;
		}
		$accept_wployalty_membership = get_user_meta( $user->ID, 'accept_wployalty_membership', true );
		if ( empty( $accept_wployalty_membership ) && isset( $user->ID ) ) {
			$options                = get_option( 'wlopt_settings', [] );
			$store_admin_preference = $options['existing_user_wlr_preference'] ?? 'no';
			update_user_meta( $user->ID, 'accept_wployalty_membership', $store_admin_preference );
		}
		if ( get_user_meta( $user->ID, 'accept_wployalty_membership', true ) !== 'yes' ) {
			add_filter( 'wlr_before_add_to_loyalty_customer', '__return_false', 10, 1 );
		}
	}

	public static function handleExistingUserPreference() {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			if ( $user_id == 0 ) {
				return;
			}
			$accept_wployalty_membership = get_user_meta( $user_id, 'accept_wployalty_membership', true );
			if ( empty( $accept_wployalty_membership ) ) {
                $user_email = get_user_by( 'email', $user_id );
                $status = Woocommerce::isLoyaltyUser( $user_email ) ? 'yes' : 'no';
				update_user_meta( $user_id, 'accept_wployalty_membership', $status );
			}
		}
	}

}