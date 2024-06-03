<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Site;

use Wlopt\App\Controller\Base;
use Wlr\App\Helpers\Woocommerce;
use Wlr\App\Helpers\Input;

defined( "ABSPATH" ) or die();

class Main extends Base {

	static $email;

	public static function checkStatus() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return apply_filters( 'wlopt_work_on_guest_user', false );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			$accept_wployalty_membership = get_user_meta( $user_data->ID, 'accept_wployalty_membership', true );
			if ( $accept_wployalty_membership > 0 ) {
				return false;
			}
			$decline_wployalty_membership = get_user_meta( $user_data->ID, 'decline_wployalty_membership', true );

			return $decline_wployalty_membership > 0;
		}

		return false;
	}

	public static function preventWPLoyaltyMembership() {
//		var_dump( self::checkStatus() );
		if ( ! self::checkStatus() ) {
			return;
		}
//        //order status
//		add_filter( 'wlr_not_eligible_to_earn_via_order', '__return_true');

		//earn point prevent
		add_filter( 'wlr_before_earn_point_calculation', function ( $status, $data ) {
			return false;
		}, 10, 2 );
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
		//prevent sent mail
		add_filter( 'wlr_before_send_email', function ( $status, $data ) {
			if ( $data['data']['user_email'] ) {
				$email = get_user_by_email( $data['data']['user_email'] );

			}
		} );

	}

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

	static function declineMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return;
		}
		if ( self::checkStatus() ) {
			return;
		}
		?>
        <div class="wlopt-decline-membership">
            <input type="checkbox" name="decline_wployalty_membership" id="decline_wployalty_membership">
            <label for="decline_wployalty_membership" class="wlr-text-color"
            ><?php _e( 'Check this to conform don\'t want to became a member of a WPLoyalty program.', 'wlr-loyalty-optin' ) ?></label>
        </div>
		<?php

	}


	static function getEmail() {
		if ( ! empty( self::$email ) ) {
			return self::$email;
		}
		$woo_helper = Woocommerce::getInstance();

		return self::$email = $woo_helper->get_login_user_email();
	}

	public static function acceptMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return '';
		}
		if ( ! self::checkStatus() ) {
			return '';
		}
		?>
        <div class="wlopt-accept-membership">
            <input type="checkbox" name="accept_wployalty_membership" id="accept_wployalty_membership">
            <label for="accept_wployalty_membership" class="wlr-text-color"
            ><?php _e( 'Check this to became a member of a WPLoyalty program.', 'wlr-loyalty-optin' ) ?></label>
        </div>
		<?php
	}

	/**
	 *
	 *
	 * @return void
	 */
	public static function updateOptIn() {

		$input_helper = new \Wlr\App\Helpers\Input();
		$wlr_nonce    = (string) $input_helper->post_get( 'wlopt_nonce', '' );
		$json         = array(
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
		$decline_wployalty_membership = (int) $input_helper->post_get( 'decline_wployalty_membership', 0 );
		$user_email                   = self::getEmail();
		if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', sanitize_text_field( $decline_wployalty_membership ) );
			$update_status = $decline_wployalty_membership == 0 ? 1 : 0;
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', sanitize_text_field( $update_status ) );
			$json['success']         = true;
			$json['data']['message'] = __( 'Updated successfully', 'wp-loyalty-optin' );
		}
		wp_send_json( $json );

	}

	static function updateAcceptance() {
		$input_helper = new Input();
		$wlr_nonce    = (string) $input_helper->post_get( 'wlopt_nonce', '' );
		$json         = array(
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
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );

		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', sanitize_text_field( $accept_wployalty_membership ) );
			$update_status = $accept_wployalty_membership == 0 ? 1 : 0;
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );
			$json['success']         = true;
			$json['data']['message'] = __( 'Updated successfully', 'wp-loyalty-optin' );
		}
		wp_send_json( $json );
	}

	static function addRegistrationCheckbox() {
		$user_email = self::getEmail();
		if ( ! empty( $user_email ) ) {
			return;
		}

		woocommerce_form_field( 'accept_wployalty_membership', array(
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => array( 'form-row-wide accept_wployalty_membership' ),
			'label'    => __( 'Check this to became a member of a WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		) );
	}

	static function validateInRegisterForm( $username, $user_email, $errors ) {
		$input_helper                = new Input();
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );

		if ( ! in_array( $accept_wployalty_membership, array( 0, 1 ) ) ) {
			$errors->add( 'accept_wployalty_membership',
				__( 'Must be valid', 'wp-loyalty-optin' ) );
		}

		return $errors;
	}

	/**
	 * user register.
	 *
	 * @param $customer_id
	 * @param $new_customer_data
	 * @param $password_generated
	 *
	 * @return void
	 */
	static function saveRegisterCheckbox( $customer_id, $new_customer_data, $password_generated ) {
		if ( empty( $customer_id ) ) {
			return;
		}
		$input_helper                = new Input();
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );
		$user_email                  = $input_helper->post_get( 'email', '' );

		if ( empty( $user_email ) ) {
			return;
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', sanitize_text_field( $accept_wployalty_membership ) );
			$update_status = $accept_wployalty_membership == 0 ? 1 : 0;
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );
		}
	}

	static function addUserRegistration( $user_id ) {
		if ( empty( $user_id ) ) {
			return;
		}
		$user                        = get_user_by( 'id', $user_id );
		$input_helper                = new Input();
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );

		if ( is_object( $user ) && isset( $user->ID ) ) {
			update_user_meta( $user->ID, 'accept_wployalty_membership', sanitize_text_field( $accept_wployalty_membership ) );
			$update_status = $accept_wployalty_membership == 0 ? 1 : 0;
			update_user_meta( $user->ID, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );
		}

	}


	static function getStatusForRegisterUser( $status, $user_id ) {

		if ( empty( $user_id ) ) {
			return $status;
		}
		$user = get_user_by( 'id', $user_id );
		if ( is_object( $user ) && isset( $user->ID ) ) {
			$accept_wployalty_membership = get_user_meta( $user->ID, 'accept_wployalty_membership' );
			if ( $accept_wployalty_membership == 0 ) {
				return false;
			}
		}

		return $status;
	}


	static function addCheckoutCheckbox() {

		$user_email = self::getEmail();
		if ( ! empty( $user_email ) ) {
			return;
		}

		woocommerce_form_field( 'accept_wployalty_membership', array(
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => array( 'form-row-wide accept_wployalty_membership' ),
			'label'    => __( 'Check this to became a member of a WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		) );

	}

	static function validateCheckoutForm( $fields, $errors ) {
		$input_helper                = new Input();
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );

		if ( ! in_array( $accept_wployalty_membership, array( 0, 1 ) ) ) {
			$errors->add( 'accept_wployalty_membership',
				__( 'Must be valid', 'wp-loyalty-optin' ) );
		}
	}

	static function saveCheckoutFormData() {
		$input_helper                = new Input();
		$accept_wployalty_membership = (int) $input_helper->post_get( 'accept_wployalty_membership', 0 );
		$user_email                  = $input_helper->post_get( 'email', '' );

		if ( empty( $user_email ) ) {
			return;
		}
		$user_data = get_user_by( 'email', $user_email );
		if ( is_object( $user_data ) && isset( $user_data->ID ) ) {
			update_user_meta( $user_data->ID, 'accept_wployalty_membership', sanitize_text_field( $accept_wployalty_membership ) );
			$update_status = $accept_wployalty_membership == 0 ? 1 : 0;
			update_user_meta( $user_data->ID, 'decline_wployalty_membership', sanitize_text_field( $update_status ) );
		}

	}

}