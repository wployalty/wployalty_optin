<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Site\Blocks\Integration;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;
use Wlopt\App\Controller\Site\Main;

class Message implements IntegrationInterface {

	/**
	 * The name of the integration.
	 *
	 * @inheritDoc
	 */
	public function get_name() {
		return WLOPT_TEXT_DOMAIN . '-message';
	}

	/**
	 * When invoked returns handler name.
	 *
	 * @param string $handler handler name.
	 *
	 * @return string
	 */
	protected function getHandlerName( $handler = '' ) {
		return trim( WLOPT_TEXT_DOMAIN . '-message-' . $handler, '-' );
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 * @inheritDoc
	 */
	public function initialize() {
		$script_asset_path = WLOPT_PLUGIN_PATH . 'blocks/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path ) ? require $script_asset_path : [
			'dependencies' => [],
			'version'      => WLOPT_PLUGIN_VERSION,
		];
		wp_register_script( $this->getHandlerName(),
			WLOPT_PLUGIN_URL . 'blocks/build/index.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true );
		$extend = StoreApi::container()->get( ExtendSchema::class );
		$extend->register_endpoint_data(
			[
				'endpoint'        => CheckoutSchema::IDENTIFIER,
				'namespace'       => 'wlopt_checkout_block',
				'scheme_callback' => [ $this, 'getOptinSchema' ],
				'schema_type'     => ARRAY_A
			]
		);
	}

	public function getOptinSchema() {
		$validate_callback = function ( $value ) {
			if ( ! is_bool( $value ) && null !== $value ) {
				return new \WP_Error(
					'api-error',
					esc_html__( 'Please enter a valid opt-in value (true or false).', 'wp-loyalty-optin' )
				);
			}

			return true;
		};
		$sanitize_callback = function ( $value ) {
			return is_bool( $value ) ? $value : false;
		};

		return [
			'wlr_optin' => [
				'description' => __( 'Checkout opt-in checkbox', 'wp-loyalty-optin' ),
				'type'        => [ 'boolean', 'null' ],
				'context'     => [],
				'arg_options' => [
					'validate_callback' => $validate_callback,
					'sanitize_callback' => $sanitize_callback,
				],
			]
		];
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @inheritDoc
	 */
	public function get_script_handles() {
		return [ $this->getHandlerName() ];
	}

	/**
	 * @inheritDoc
	 */
	public function get_editor_script_handles() {
		return [];
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @inheritDoc
	 */
	public function get_script_data() {
		return [
			'user_optin'            => Main::checkStatus(),
			'is_enable_optin_field' => apply_filters( 'wlopt_enable_optin_field', true ),
			'optin_parent_block'    => [ 'woocommerce/checkout-contact-information-block' ],
            'user_option_label'     => apply_filters('wlr_opt_user_option_label', __('Check this to become member of WPLoyalty', 'wp-loyalty-optin'))
		];
	}
}