<?php
/**
 * @author      Wployalty (Ilaiyaraja)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Helper;
defined( "ABSPATH" ) or die();

class Compatibility {
	public static function check( $allow_exit = false ) {

		if ( ! self::isPHPCompatible() ) {
			$message = sprintf( __( '%s requires minimum PHP version %s', 'wp-loyalty-optin' ), WLOPT_PLUGIN_NAME, WLOPT_MINIMUM_PHP_VERSION );
			$allow_exit ? die( esc_html( $message ) ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}
		if ( ! self::isWordPressCompatible() ) {
			$message = sprintf( __( '%s requires minimum WordPress version %s', 'wp-loyalty-optin' ), WLOPT_PLUGIN_NAME, WLOPT_MINIMUM_WP_VERSION );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}

		if ( ! self::isWooCompatible() ) {
			$message = sprintf( __( '%s requires minimum Woocommerce version %s', 'wp-loyalty-optin' ), WLOPT_PLUGIN_NAME, WLOPT_MINIMUM_WC_VERSION );
			$allow_exit ? exit( $message ) : self::adminNotice( esc_html( $message ), 'error' );

			return false;
		}

		return true;
	}

	/**
	 * Add admin notice.
	 *
	 * @param string $message Message.
	 * @param string $status Status.
	 *
	 * @return void
	 */
	public static function adminNotice( $message, $status = "success" ) {
		add_action( 'admin_notices', function () use ( $message, $status ) {
			?>
            <div class="notice notice-<?php echo esc_attr( $status ); ?>">
                <p><?php echo wp_kses_post( $message ); ?></p>
            </div>
			<?php
		}, 1 );
	}

	/**
	 * Check php version is compatible.
	 *
	 * @return bool
	 */
	protected static function isPHPCompatible() {
		return (int) version_compare( PHP_VERSION, WLOPT_MINIMUM_PHP_VERSION, '>=' ) > 0;
	}

	/**
	 * Check WordPress required version.
	 *
	 * @return bool
	 */
	protected static function isWordPressCompatible() {
		return (int) version_compare( get_bloginfo( 'version' ), WLOPT_MINIMUM_WP_VERSION, '>=' ) > 0;
	}

	/**
	 * Check woocommerce is compatible.
	 *
	 * @return bool
	 */
	protected static function isWooCompatible() {
		$woo_version = self::getWooVersion();

		return (int) version_compare( $woo_version, WLOPT_MINIMUM_WC_VERSION, '>=' ) > 0;
	}

	/**
	 * Get Woocommerce version.
	 *
	 * @return string
	 */
	protected static function getWooVersion() {
		if ( defined( 'WC_VERSION' ) ) {
			return WC_VERSION;
		}
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/woocommerce' );

		return isset( $plugin_folder['woocommerce.php']['Version'] ) ? $plugin_folder['woocommerce.php']['Version'] : '1.0.0';
	}

}