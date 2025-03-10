<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Helper;

use Wlr\App\Models\Users;

defined( 'ABSPATH' ) or die();

class Woocommerce {
	public static $instance = null;
	protected static $banned_user = array();

	public static function create_nonce( $action = - 1 ) {
		return wp_create_nonce( $action );
	}

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

	public static function isBannedUser( $user_email = "" ) {
		if ( empty( $user_email ) ) {
			$user_email = self::get_login_user_email();
			if ( empty( $user_email ) ) {
				return false;
			}
		}
		/*$user    = get_user_by( 'email', $user_email );
		$user_id = isset( $user->ID ) && ! empty( $user->ID ) ? $user->ID : 0;
		if ( ! apply_filters( 'wlr_before_add_to_loyalty_customer', true,
			$user_id, $user_email ) ) {
			return true;
		}*/
		if ( isset( static::$banned_user[ $user_email ] ) ) {
			return static::$banned_user[ $user_email ];
		}
		$user_modal = new Users();
		global $wpdb;
		$where = $wpdb->prepare( "user_email = %s AND is_banned_user = %d ", array( $user_email, 1 ) );
		$user  = $user_modal->getWhere( $where, "*", true );

		return static::$banned_user[ $user_email ] = ( ! empty( $user ) && is_object( $user ) && isset( $user->is_banned_user ) );
	}

	static function get_login_user_email() {
		$user       = get_user_by( 'id', get_current_user_id() );
		$user_email = '';
		if ( ! empty( $user ) ) {
			$user_email = $user->user_email;
		}

		return $user_email;
	}

	public static function hasAdminPrivilege() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * render template.
	 *
	 * @param string $file File path.
	 * @param array $data Template data.
	 * @param bool $display Display or not.
	 *
	 * @return string|void
	 */
	public static function renderTemplate( string $file, array $data = [], bool $display = true ) {
		$content = '';
		if ( file_exists( $file ) ) {
			ob_start();
			extract( $data );
			include $file;
			$content = ob_get_clean();
		}
		if ( $display ) {
			echo $content;
		} else {
			return $content;
		}
	}
}