<?php
/**
 * Plugin Name: WPLoyalty - Optin
 * Plugin URI: https://www.wployalty.net
 * Description: The WPLoyalty Opt-in Add-on allows you to give customers the choice to Accept/Decline your loyalty program through a simple checkbox.
 * Version: 1.0.0
 * Author: WPLoyalty
 * Slug: wp-loyalty-optin
 * Text Domain: wp-loyalty-optin
 * Domain Path: /i18n/languages/
 * Requires at least: 4.9.0
 * WC requires at least: 6.5
 * WC tested up to: 9.9
 * Contributors: Ilaiyaraja, Sabhari, Roshan Britto
 * Author URI: https://wployalty.net/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * WPLoyalty: 1.3.4
 * WPLoyalty Page Link: wp-loyalty-optin
 */

defined( 'ABSPATH' ) or die;

//HPOS Support
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if ( ! function_exists( 'isWLROPGWooCommerceActive' ) ) {
	function isWLROPGWooCommerceActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
}

if ( ! function_exists( 'isWLROPLoyaltyActive' ) ) {
	function isWLROPLoyaltyActive() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'wp-loyalty-rules/wp-loyalty-rules-lite.php', $active_plugins ) || array_key_exists( 'wp-loyalty-rules/wp-loyalty-rules-lite.php', $active_plugins )
		       || in_array( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins ) || array_key_exists( 'wp-loyalty-rules/wp-loyalty-rules.php', $active_plugins );
	}
}

if ( ! isWLROPGWooCommerceActive() || ! isWLROPLoyaltyActive() ) {
	return;
}
//Define the plugin version
defined( 'WLOPT_PLUGIN_NAME' ) or define( 'WLOPT_PLUGIN_NAME', 'WPLoyalty - Optin' );
defined( 'WLOPT_PLUGIN_VERSION' ) or define( 'WLOPT_PLUGIN_VERSION', '1.0.0' );
defined( 'WLOPT_MINIMUM_PHP_VERSION' ) or define( 'WLOPT_MINIMUM_PHP_VERSION', '7.4' );
defined( 'WLOPT_MINIMUM_WP_VERSION' ) or define( 'WLOPT_MINIMUM_WP_VERSION', '4.9' );
defined( 'WLOPT_MINIMUM_WC_VERSION' ) or define( 'WLOPT_MINIMUM_WC_VERSION', '6.0' );
defined( 'WLOPT_MINIMUM_WLR_VERSION' ) or define( 'WLOPT_MINIMUM_WLR_VERSION', '1.3.4' );
defined( 'WLOPT_PLUGIN_SLUG' ) or define( 'WLOPT_PLUGIN_SLUG', 'wp-loyalty-optin' );
defined( 'WLOPT_TEXT_DOMAIN' ) or define( 'WLOPT_TEXT_DOMAIN', 'wp-loyalty-optin' );
defined( 'WLOPT_PLUGIN_FILE' ) or define( 'WLOPT_PLUGIN_FILE', __FILE__ );
defined( 'WLOPT_PLUGIN_PATH' ) or define( 'WLOPT_PLUGIN_PATH', __DIR__ . '/' );
defined( 'WLOPT_PLUGIN_URL' ) or define( 'WLOPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'WLOPT_VIEW_PATH' ) or define( 'WLOPT_VIEW_PATH', str_replace( "\\", '/', __DIR__ ) . '/App/Views' );

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}
require __DIR__ . '/vendor/autoload.php';


$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/wployalty/wployalty_optin',
	__FILE__,
	'wp-loyalty-optin'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
if ( class_exists( \Wlopt\App\Helper\Plugin::class ) ) {
    \Wlopt\App\Setup::init();
}
add_action( 'plugins_loaded', function () {
    if ( ! class_exists( '\Wlr\App\Helpers\Input' ) ) {
		return;
	}
	if ( ! class_exists( '\Wlopt\App\Router' ) ) {
		return;
	}
	if ( \Wlopt\App\Helper\Plugin::checkDependencies() ) {
		\Wlopt\App\Router::init();
	}
} );