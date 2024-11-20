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
use Wlr\App\Helpers\Woocommerce;

defined( "ABSPATH" ) or die();

class Main {
	/**
	 * Customer email variable.
	 *
	 * @var
	 */
	public static $email;

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
			} else {
				return false;
			}
		}

		return false;
	}

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
		wp_enqueue_script( WLOPT_PLUGIN_SLUG . '-main',
			WLOPT_PLUGIN_URL . 'Assets/Site/Js/main' . $suffix . '.js',
			array( 'jquery' ), WLOPT_PLUGIN_VERSION . '&t=' . time() );
		$localize = array(
			'ajax_url'                     => admin_url( 'admin-ajax.php' ),
			'decline_wployalty_membership' => wp_create_nonce( 'decline_wployalty_membership_nonce' ),
			'accept_wployalty_membership'  => wp_create_nonce( 'accept_wployalty_membership_nonce' ),
		);
		wp_localize_script( WLOPT_PLUGIN_SLUG . '-main', 'wlopt_localize_data',
			$localize );

	}

	/**
	 * Shortcode for field for decline.
	 *
	 * @return string|void
	 */
	static function declineMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return;
		}
		if ( ! self::checkStatus() ) {
			return;
		}
		ob_start();
		?>
        <div class="wlopt-decline-membership">
            <input type="checkbox" name="decline_wployalty_membership" id="decline_wployalty_membership">
            <label for="decline_wployalty_membership" class="wlr-text-color"
            ><?php echo __( 'Check this to confirm don\'t want to became a member of a WPLoyalty program.',
					'wp-loyalty-optin' ) ?></label>
        </div>
		<?php
		return ob_get_clean();
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
	 * Shortcode for field for acceptance.
	 *
	 * @return string|void
	 */
	public static function acceptMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return '';
		}
		if ( self::checkStatus() ) {
			return '';
		}
		ob_start();
		?>
        <div class="wlopt-accept-membership">
            <input type="checkbox" name="accept_wployalty_membership" id="accept_wployalty_membership">
            <label for="accept_wployalty_membership" class="wlr-text-color"
            ><?php echo __( 'Check this to became a member of a WPLoyalty program.', 'wp-loyalty-optin' ) ?></label>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Update status of decline.
	 *
	 * @return void
	 */
	public static function updateOptIn() {

		$wlr_nonce = (string) Input::get( 'wlopt_nonce', '' );
		$json      = array(
			'success' => false,
			'data'    => array(
				'message' => __( 'Update data failed',
					'wp-loyalty-optin' ),
			)
		);
		if ( ! Woocommerce::verify_nonce( $wlr_nonce, 'decline_wployalty_membership_nonce' ) ) {
			$json['message'] = __( 'Invalid nonce', 'wp-loyalty-optin' );
			wp_send_json( $json );
		}
		$decline_wployalty_membership = Input::get( 'decline_wployalty_membership' ) ? "yes" : "no";
		$user_email                   = self::getEmail();
		if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'decline_wployalty_membership',
				sanitize_text_field( $decline_wployalty_membership ) );
			$update_status = $decline_wployalty_membership == "no" ? "yes" : "no";
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', sanitize_text_field( $update_status ) );
			$json['success']         = true;
			$json['data']['message'] = __( 'Updated successfully', 'wp-loyalty-optin' );
		}
		wp_send_json( $json );

	}

	/**
	 * Update data fo accept membership.
	 *
	 * @return void
	 */
	static function updateAcceptance() {
		$wlr_nonce = (string) Input::get( 'wlopt_nonce', '' );
		$json      = array(
			'success' => false,
			'data'    => array(
				'message' => __( 'Update data failed',
					'wp-loyalty-optin' ),
			)
		);
		if ( ! Woocommerce::verify_nonce( $wlr_nonce, 'accept_wployalty_membership_nonce' ) ) {
			$json['message'] = __( 'Invalid nonce', 'wp-loyalty-optin' );
			wp_send_json( $json );
		}
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership' ) ? "yes" : "no";

		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership',
				sanitize_text_field( $accept_wployalty_membership ) );
			$update_status = $accept_wployalty_membership == "no" ? "yes" : "no";
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );
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

		woocommerce_form_field( 'accept_wployalty_membership', array(
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => array( 'form-row-wide accept_wployalty_membership' ),
			'label'    => __( 'Check this to become a member of WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		) );
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

		if ( ! in_array( $accept_wployalty_membership, array( 0, 1 ) ) ) {
			$errors->add( 'accept_wployalty_membership',
				__( 'Must be valid', 'wp-loyalty-optin' ) );
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
	static function addUserRegistration( $user_id ) {

		if ( empty( $user_id ) ) {
			return;
		}
		//Updating user meta for block checkout
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership' ) ? "yes" : "no";
		if ( empty( $_POST ) && is_object( $user = get_user_by( 'id',
				$user_id ) ) && ! empty( get_transient( 'wlr_opt_in_' . $user->user_email ) ) ) {
			$accept_wployalty_membership = get_transient( 'wlr_opt_in_' . $user->user_email );
		}
		if ( empty( get_transient( 'wlr_opt_in_status' ) ) || get_transient( 'wlr_opt_in_status' ) !== $accept_wployalty_membership ) {
			self::setTransient( 'wlr_opt_in_status', $accept_wployalty_membership );
		}
		update_user_meta( $user_id, 'accept_wployalty_membership',
			sanitize_text_field( $accept_wployalty_membership ) );
		$update_status = $accept_wployalty_membership == "no" ? "yes" : "no";
		update_user_meta( $user_id, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );

	}

	/**
	 * Add field in checkout form.
	 *
	 * @return void
	 */
	static function addCheckoutCheckbox() {

		woocommerce_form_field( 'accept_wployalty_membership', array(
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => array( 'form-row-wide accept_wployalty_membership' ),
			'label'    => __( 'Check this to become a member of WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		), self::checkStatus() );

	}

	/**
	 * Validation for checkout fields.
	 *
	 * @param $fields
	 * @param $errors
	 *
	 * @return mixed
	 */
	static function validateCheckoutForm( $fields, $errors ) {
		$accept_wployalty_membership = (int) Input::get( 'accept_wployalty_membership' );
		if ( ! in_array( $accept_wployalty_membership, array( 0, 1 ) ) ) {
			$errors->add( 'accept_wployalty_membership',
				__( 'Must be valid', 'wp-loyalty-optin' ) );
		} else {
			$accept_wployalty_membership = Input::get( 'accept_wployalty_membership' ) ? "yes" : "no";
			if ( empty( get_transient( 'wlr_opt_in_status' ) ) || get_transient( 'wlr_opt_in_status' ) !== $accept_wployalty_membership ) {
				self::setTransient( 'wlr_opt_in_status', $accept_wployalty_membership );
			}
		}

		return $errors;
	}

	/**
	 * Save checkout field data.
	 *
	 * @param          $order
	 * @param   array  $data  Checkout fields data.
	 *
	 * @return void
	 */
	static function saveCheckoutFormData( $order, $data ) {
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership' ) ? "yes" : "no";
		$user_email                  = isset( $data['billing_email'] )
		                               && ! empty( $data['billing_email'] )
			? $data['billing_email'] : "";
		if ( empty( $user_email ) ) {
			return;
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', $accept_wployalty_membership );
			$update_status = $accept_wployalty_membership == "no" ? "yes" : "no";
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', $update_status );
		}

	}

	static function checkBlockCheckoutEarning( \WC_Customer $customer, \WP_REST_Request $request ) {
		if ( ! isset( $request['extensions'] )
		     || ! isset( $request['extensions']['wlopt_checkout_block'] )
		     || ! isset( $request['extensions']['wlopt_checkout_block']['wpl_optin'] )
		) {
			return;
		}
		if ( ! is_object( $customer ) ) {
			return;
		}
		$billing_email = $customer->get_billing_email();

		if ( empty( $billing_email ) || ! is_email( $billing_email ) ) {
			return;
		}
		$accept_wployalty_membership = $request['extensions']['wlopt_checkout_block']['wpl_optin'] ? "yes" : "no";
		if ( empty( get_transient( 'wlr_opt_in_status' ) ) || get_transient( 'wlr_opt_in_status' ) !== $accept_wployalty_membership ) {
			self::setTransient( 'wlr_opt_in_status', $accept_wployalty_membership );
		}
		$params = $request->get_params();
		if ( is_array( $params ) && ! empty( $params ) && $params['create_account'] ) {
			set_transient( 'wlr_opt_in_' . $billing_email, $accept_wployalty_membership, 60 * 60 );
		}
		$log = wc_get_logger();
		$log->add( 'otest', 'Request params : ' . json_encode( $params ) );
		$log->add( 'otest', 'Saved transient : ' . get_transient( 'wlr_opt_in_' . $billing_email ) );
	}

	static function preventEarning( $status, $user_id, $user_email ) {
		if ( ! empty( get_transient( 'wlr_opt_in_status' ) ) && get_transient( 'wlr_opt_in_status' ) === "no" ) {

			return false;
		}

		return $status;
	}

	static function initBlocks() {
		if ( function_exists( 'WC' ) && WC()->is_rest_api_request() ) {
			$message = new Message();
			woocommerce_store_api_register_endpoint_data(
				[
					'endpoint'        => CheckoutSchema::IDENTIFIER,
					'namespace'       => 'wlopt_checkout_block',
					'schema_callback' => [ $message, 'getOptinSchema' ],
					'schema_type'     => ARRAY_A,
				]
			);
		}
		add_action(

			'woocommerce_blocks_checkout_block_registration',
			function ( $integration_registry ) {
				$integration_registry->register( new Message() );
			}
		);
	}

	static function setTransient( $transient_name, $transient_value, $expiration = 7200 ) {
		if ( empty( $transient_name ) || ! is_string( $transient_name ) ) {
			return;
		}
		if ( empty( $transient_name ) || ! is_string( $transient_name ) || ! in_array( $transient_value,
				[ "yes", "no" ] ) ) {
			return;
		}
		if ( ! is_int( $expiration ) ) {
			return;
		}
		set_transient( $transient_name, $transient_value, $expiration );
	}

	static function clearTransient() {
		if ( get_transient( 'wlr_opt_in_status' ) ) {
			delete_transient( 'wlr_opt_in_status' );
		}
	}
}