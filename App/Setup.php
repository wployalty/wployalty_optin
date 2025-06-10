<?php

namespace Wlopt\App;

use Wlopt\App\Model\Users;
use Exception;

defined( 'ABSPATH' ) || exit();

class Setup {
	public static function init() {
        register_activation_hook( WLOPT_PLUGIN_FILE, [ __CLASS__, 'activate' ] );
		register_deactivation_hook( WLOPT_PLUGIN_FILE, [ __CLASS__, 'deactivate' ] );
		register_uninstall_hook( WLOPT_PLUGIN_FILE, [ __CLASS__, 'uninstall' ] );
		add_filter( 'plugin_row_meta', [ __CLASS__, 'getPluginRowMeta' ], 10, 2 );

        add_action('plugins_loaded', [__CLASS__, 'runDataBaseMigration']);
	}

	/**
	 * Method to run plugin activation scripts.
	 */
	public static function activate() {
        self::runDataBaseMigration();

        $wlopt_settings = get_option('wlopt_settings', []);
        if (empty($wlopt_settings)) {
            update_option( 'wlopt_settings', [
                'enable_optin'           => 'no',
                'is_onboarding_complete' => 'no',
            ] );
        }
	}

    /**
     * Run database migration.
     *
     * @return void
     */
    public static function runDataBaseMigration() {
        if (!is_admin()) {
            return;
        }

        $models = array (
            new Users(),
        );

        foreach ($models as $model) {
            try {
                if (!$model::isTableExist()) {
                    $model->create();
                }
            } catch ( Exception $e ) {
                exit( esc_html( WLR_PLUGIN_NAME . __( 'Plugin required table creation failed.', 'wp-loyalty-optin' ) ) );
            }
        }
    }

	/**
	 * Method to run plugin deactivation scripts.
	 */
	public static function deactivate() {
		// silence is golden
	}

	/**
	 * Method to run plugin uninstall scripts.
	 */
	public static function uninstall() {
		// silence is golden
	}

	/**
	 * Retrieves the plugin row meta to be displayed on the Woocommerce appointments plugin page.
	 *
	 * @param array $links The existing plugin row meta links.
	 * @param string $file The path to the plugin file.
	 *
	 * @return array
	 */
	public static function getPluginRowMeta( $links, $file ) {
		if ( $file != plugin_basename( WLOPT_PLUGIN_FILE ) ) {
			return $links;
		}
		$row_meta = [
			'support' => '<a href="' . esc_url( 'https://wployalty.net/support/' ) . '" aria-label="' . esc_attr__( 'Support', 'wp-loyalty-optin' ) . '">' . esc_html__( 'Support', 'wp-loyalty-optin' ) . '</a>',
		];

		return array_merge( $links, $row_meta );
	}
}