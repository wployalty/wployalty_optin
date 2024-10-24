<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Helper;

defined( 'ABSPATH' ) or die();

class Woocommerce {
	public static $instance = null;

	public static function verify_nonce( $nonce, $action = - 1 ) {
		if ( wp_verify_nonce( $nonce, $action ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function getInstance( array $config = array() ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $config );
		}

		return self::$instance;
	}

	function get_login_user_email() {
		$user       = get_user_by( 'id', get_current_user_id() );
		$user_email = '';
		if ( ! empty( $user ) ) {
			$user_email = $user->user_email;
		}

		return $user_email;
	}
}