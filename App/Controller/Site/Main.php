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

	public static function checkStatus() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) ) {
			return apply_filters( 'wlopt_work_on_guest_user', true );
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
		if ( ! self::checkStatus() ) {
			return;
		}
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
		$woo_helper = Woocommerce::getInstance();

		return $woo_helper->get_login_user_email();
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

	function addRegistrationCheckbox() {
		woocommerce_form_field( 'wlr_not_become_a_member', array(
			'type'     => 'checkbox',
			'id'       => 'wlr_not_become_a_member',
			'class'    => array( 'form-row-wide wlr_not_become_a_member' ),
			'label'    => __( 'Check this to conform don\'t want to became a member of a WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		) );

	}

	function validateInRegisterForm( $username, $user_email, $errors ) {
		if ( empty( $username ) || empty( $user_email ) ) {
			return $errors;
		}
		$input_helper        = new \Wlr\App\Helpers\Input();
		$not_become_a_member = $input_helper->post_get( 'wlr_not_become_a_member', '' );
	}

	function saveRegisterCheckbox( $customer_id, $new_customer_data, $password_generated ) {
		if ( empty( $customer_id ) ) {
			return;
		}
		$input_helper            = new \Wlr\App\Helpers\Input();
		$wlr_not_become_a_member = $input_helper->post_get( 'wlr_not_become_a_member', 0 );

		update_user_meta( $customer_id, 'wlr_not_become_a_member', sanitize_text_field( $wlr_not_become_a_member ) );

	}

}