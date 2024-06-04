<?php
/**
 * Plugin Name: WPLoyalty - Optin
 * Plugin URI: https://www.wployalty.net
 * Description: Accept/Decline the WPLoyalty option to user.
 * Version: 1.0.0
 * Author: WPLoyalty
 * Slug: wp-loyalty-optin
 * Text Domain: wp-loyalty-optin
 * Domain Path: /i18n/languages/
 * Requires at least: 4.9.0
 * WC requires at least: 6.5
 * WC tested up to: 8.0
 * Contributors: Ilaiyaraja
 * Author URI: https://wployalty.net/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WPLoyalty: 1.2.9
 * WPLoyalty Page Link: wp-loyalty-optin
 */

defined( 'ABSPATH' ) or die;
if ( ! function_exists( 'isWoocommerceAndWployaltyActiveOrNot' ) ) {
	function isWoocommerceAndWployaltyActiveOrNot() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return ( ( in_array( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins ) || ( in_array( 'wployalty/wp-loyalty-rules-lite.php', $active_plugins ) ) &&
		                                                                                   ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) ) ) );
	}
}
if ( ! isWoocommerceAndWployaltyActiveOrNot() ) {
	return;
}
if ( ! class_exists( '\Wlr\App\Helpers\Input' ) ) {
	if ( file_exists( WP_PLUGIN_DIR . '/wp-loyalty-rules/vendor/autoload.php' ) ) {
		require_once WP_PLUGIN_DIR . '/wp-loyalty-rules/vendor/autoload.php';
	} elseif ( file_exists( WP_PLUGIN_DIR . '/wployalty/vendor/autoload.php' ) ) {
		require_once WP_PLUGIN_DIR . '/wployalty/vendor/autoload.php';
	}
}
if ( ! class_exists( '\Wlr\App\Helpers\Input' ) ) {
	return;
}

//Define the plugin version
defined( 'WLOPT_PLUGIN_NAME' ) or define( 'WLOPT_PLUGIN_NAME', 'WPLoyalty - Optin' );
defined( 'WLOPT_PLUGIN_VERSION' ) or define( 'WLOPT_PLUGIN_VERSION', '1.0.0' );
defined( 'WLOPT_MINIMUM_PHP_VERSION' ) or define( 'WLOPT_MINIMUM_PHP_VERSION', '5.6.0' );
defined( 'WLOPT_MINIMUM_WP_VERSION' ) or define( 'WLOPT_MINIMUM_WP_VERSION', '4.9' );
defined( 'WLOPT_MINIMUM_WC_VERSION' ) or define( 'WLOPT_MINIMUM_WC_VERSION', '6.0' );
defined( 'WLOPT_MINIMUM_WLR_VERSION' ) or define( 'WLOPT_MINIMUM_WLR_VERSION', '1.2.9' );
defined( 'WLOPT_PLUGIN_SLUG' ) or define( 'WLOPT_PLUGIN_SLUG', 'wp-loyalty-optin' );
defined( 'WLOPT_PLUGIN_FILE' ) or define( 'WLOPT_PLUGIN_FILE', __FILE__ );
defined( 'WLOPT_PLUGIN_PATH' ) or define( 'WLOPT_PLUGIN_PATH', __DIR__ . '/' );
defined( 'WLOPT_PLUGIN_URL' ) or define( 'WLOPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}
require __DIR__ . '/vendor/autoload.php';

if ( ! class_exists( \Wlopt\App\Router::class ) || ! class_exists( \Wlopt\App\Helper\Compatibility::class ) ) {
	return;
}

if ( ! \Wlopt\App\Helper\Compatibility::check() ) {
	return;
}

$router = new \Wlopt\App\Router();
if ( ! method_exists( \Wlopt\App\Router::class, 'init' ) ) {
	return;
}

\Wlopt\App\Router::init();