<?php
/**
 * @author      Wployalty (Ilaiyaraja, Sabhari, Roshan Britto)
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.wployalty.net
 * */

namespace Wlopt\App\Controller\Site;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Wlopt\App\Controller\Site\Blocks\Integration\Message;
use Wlopt\App\Helper\Input;
use Wlopt\App\Helper\Woocommerce;
use Wlopt\App\Model\Users;

defined( "ABSPATH" ) or die();

class Main {
	/**
	 * Customer email variable.
	 *
	 * @var
	 */
	public static $email;

    /**
     * Checks status and prevent earning.
     *
     * @param $user_email
     * @return void
     */
	public static function preventWPLoyaltyMembership($user_email = '') {
		if ( self::checkStatus($user_email) ) {
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
		//hide earn message in block cart & checkout
		add_filter( 'wlr_points_rewards_earn_points_message', function ( $message, $short_code_list ) {
			return '';
		}, 10, 2 );
		//hide redeem message in block cart & checkout
		add_filter( 'wlr_point_redeem_points_message', function ( $message ) {
			return '';
		}, 10, 1 );
		//hide reward page
		add_filter( 'wlr_my_account_point_and_reward_page', function ( $my_account_content, $main_page_params ) {
			return '';
		}, 10, 2 );

        add_filter('wlr_before_process_order_earning', '__return_false', 1);
        add_filter( 'wlr_earn_point_advocate_referral', '__return_false', 1);
        add_filter( 'wlr_earn_coupon_advocate_referral', '__return_false', 1);
        add_filter( 'wlr_earn_point_friend_referral', '__return_false', 1);
        add_filter( 'wlr_earn_coupon_friend_referral', '__return_false', 1);

        add_filter( 'wlr_achievement_check_status', '__return_true', 1);

        add_filter( 'wlr_is_referral_eligible_for_earning', function ($action_type, $extra) {
            return self::checkStatus($extra['user_email']);
        }, 10, 2);
	}

    /**
     * Check status of earning.
     *
     * @param $user_email
     * @return bool|mixed|string|null
     */
	public static function checkStatus($user_email = '') {
		$user_email = !empty($user_email) ? $user_email : self::getEmail();
		if ( empty( $user_email ) ) {
			return apply_filters( 'wlopt_work_on_guest_user', false );
		}
        $optin_status = Users::getUserOptinStatus($user_email);

        if ( $optin_status == 'no_data') {
            $user_data = get_user_by( 'email', $user_email );
            $loyalty_user_data = Woocommerce::getLoyaltyUserData( $user_email );
            $data = array(
                'user_email' => $user_email,
                'wp_user_id' => $user_data->ID,
                'wlr_user_id' => $loyalty_user_data->id ?? null,
                'optin_status' => !empty( $loyalty_user_data ) ? 1 : 0,
            );
            Users::save($data);
        }

		return $optin_status;
	}

    /**
     * Handle order status change.
     *
     * @param $order_id
     * @param $from_status
     * @param $to_status
     * @param $order_obj
     * @return void
     */
    public static function handleOrderStatusChange($order_id, $from_status, $to_status, $order_obj) {
        if ( is_object( $order_obj ) ) {
            $user_email = Woocommerce::getOrderEmail( $order_obj );

            if (self::checkStatus($user_email) ) {
                add_filter('wlr_before_process_order_earning', '__return_true', 1);
            }
        }
    }

	/**
	 * Get logged user email.
	 *
	 * @return mixed
	 */
	public static function getEmail() {
		if ( ! empty( self::$email ) ) {
			return self::$email;
		}
		$woo_helper = Woocommerce::getInstance();

		return self::$email = $woo_helper->get_login_user_email();
	}

	/**
	 * Site assets.
	 *
	 * @return void
	 */
	public static function siteAssets() {
		$suffix = '.min';
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			$suffix = SCRIPT_DEBUG ? '' : '.min';
		}
		wp_enqueue_script(
			WLOPT_PLUGIN_SLUG . '-main',
			WLOPT_PLUGIN_URL . 'Assets/Site/Js/main' . $suffix . '.js',
			[ 'jquery' ],
			WLOPT_PLUGIN_VERSION . '&t=' . time()
		);
		$localize = [
			'ajax_url'                    => admin_url( 'admin-ajax.php' ),
			'update_wployalty_membership' => wp_create_nonce( 'update_wployalty_membership_nonce' )
		];
		wp_localize_script(
			WLOPT_PLUGIN_SLUG . '-main',
			'wlopt_localize_data',
			$localize
		);
	}

	/**
	 * Shortcode for field to update membership preference.
	 *
	 * @return false|string
	 */
	public static function updateMembership() {
		$user_email = self::getEmail();
		if ( empty( $user_email ) || Woocommerce::isBannedUser( $user_email ) ) {
			return '';
		}
		$checked = '';
		if ( self::checkStatus() ) {
			$checked = 'checked';
		}
		ob_start();
		?>
        <div class="wlopt-update-membership">
            <input type="checkbox" name="update_wployalty_membership"
                   id="update_wployalty_membership"<?php echo $checked ?>>
            <label for="update_wployalty_membership"
                   class="wlr-text-color"><?php echo __( 'Check this to become a member of WPLoyalty program.',
					'wp-loyalty-optin' ) ?></label>
        </div>
		<?php
		return ob_get_clean();
	}

    /**
     * Update membership preference in user meta.
     *
     * @return void
     */
	public static function updateMembershipPreference() {
		$wlr_nonce = (string) Input::get( 'wlopt_nonce', '' );
		$json      = [
			'success' => false,
			'data'    => [
				'message' => __( 'Update data failed', 'wp-loyalty-optin' )
			]
		];
		if ( ! Woocommerce::verify_nonce( $wlr_nonce, 'update_wployalty_membership_nonce' ) ) {
			$json['message'] = __( 'Invalid nonce', 'wp-loyalty-optin' );
			wp_send_json( $json );
		}
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership', 0 );
        $user_email                  = self::getEmail();
        if ( empty( $user_email ) ) {
			wp_send_json( $json );
		}
        self::updateUserOptInStatus($user_email, $accept_wployalty_membership);
        $json = [
            'success' => true,
            'message' => __( 'Updated successfully', 'wp-loyalty-optin' ),
            'reload' => true
        ];
		wp_send_json( $json );
	}

	/**
	 * Add field in register page.
	 *
	 * @return void
	 */
	public static function addRegistrationCheckbox() {
		$user_email = self::getEmail();
		if ( ! empty( $user_email ) ) {
			return;
		}

		woocommerce_form_field( 'accept_wployalty_membership', [
			'type'     => 'checkbox',
			'id'       => 'accept_wployalty_membership',
			'class'    => [ 'form-row-wide accept_wployalty_membership' ],
			'label'    => __( 'Check this to become a member of WPLoyalty program.', 'woocommerce' ),
			'required' => false,
		] );
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
	public static function validateInRegisterForm( $username, $user_email, $errors ) {
		$accept_wployalty_membership = (int) Input::get( 'accept_wployalty_membership', 0 );

		if ( ! in_array( $accept_wployalty_membership, [ 0, 1 ] ) ) {
			$errors->add( 'accept_wployalty_membership', __( 'Must be valid', 'wp-loyalty-optin' ) );
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
	public static function registerUserHandler( $user_id ) {
		if ( empty( $user_id ) ) {
			return;
		}
		$accept_wployalty_membership = Input::get( 'accept_wployalty_membership', 0 );
        if ( empty($accept_wployalty_membership) ) {
            add_filter( 'wlr_before_add_to_loyalty_customer', '__return_false', 10, 1 );
        }
        $user_email = Input::get( 'email', '' );
        self::updateUserOptInStatus($user_email, $accept_wployalty_membership);
	}

	/**
	 * Handle user login to prevent adding customer to loyalty by default.
	 *
	 * This method checks the user's loyalty membership status upon login.
	 * If the user has not accepted the loyalty membership, it updates the user meta
	 * to reflect the non-acceptance and prevents adding the user to the loyalty program.
	 * For existing users, it updates the user meta to as per store owner choice in onboarding.
	 *
	 * @param string $user_name The username of the user logging in.
	 * @param \WP_User $user The WP_User object of the user logging in.
	 *
	 * @return void
	 */
	public static function loginUserHandler( $user_name, $user ) {
		if ( empty( $user ) || ! is_object( $user ) ) {
			return;
		}
        $accept_wployalty_membership = Users::getUserOptinStatus($user->user_email);
        if ( isset( $user->ID ) ) {
            $optin_user_data = Users::getOptinData($user->user_email);
            $optin_status = $optin_user_data['optin_status'] ?? 0;
            $loyalty_user_data = Woocommerce::getLoyaltyUserData( $user->user_email );
            $data = array(
                'id' => $optin_user_data['id'] ?? 0,
                'user_email'    => $user->user_email,
                'wp_user_id'    => $user->ID ?? null,
                'wlr_user_id'   => $loyalty_user_data->ID ?? null,
                'optin_status'  => $optin_status,
            );

            if ($accept_wployalty_membership == 'no_data') {
                $options      = get_option( 'wlopt_settings', [] );
                $optin_status =  $options['existing_user_wlr_preference'] ?? 0;
                $data['optin_status'] = $optin_status;
            }
            Users::save($data);
		}
		if ( !Users::getUserOptinStatus($user->user_email) ) {
			add_filter( 'wlr_before_add_to_loyalty_customer', '__return_false', 10, 1 );
		}
	}

    /**
     * Add field in checkout form.
     *
     * @return void
     */
    public static function addCheckoutCheckbox() {
        $user_email = self::getEmail();
        if ( !empty( $user_email ) && Woocommerce::isBannedUser( $user_email ) ) {
            return;
        }

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
    public static function validateCheckoutForm( $fields, $errors ) {
        $accept_wployalty_membership = (int) Input::get( 'accept_wployalty_membership','','post' );
        if ( ! in_array( $accept_wployalty_membership, array( 0, 1 ) ) ) {
            $errors->add( 'accept_wployalty_membership', __( 'Must be valid', 'wp-loyalty-optin' ) );
        }

        return $errors;
    }

    /**
     * Save checkout field data.
     *
     * @param $order
     * @param array $data Checkout fields data.
     *
     * @return void
     */
    public static function saveCheckoutFormData( $order, $data ) {
        $accept_wployalty_membership = Input::get( 'accept_wployalty_membership' );
        $user_email = ! empty( $data['billing_email'] )
            ? $data['billing_email'] : "";
        if ( empty( $user_email ) ) {
            return;
        }
        self::updateUserOptInStatus($user_email, $accept_wployalty_membership);
        self::preventWPLoyaltyMembership($user_email);
    }

	public static function handleExistingUserPreference() {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			if ( $user_id == 0 ) {
				return;
			}
            self::checkStatus();
		}
	}

    /**
     * To initialize checkout block assets.
     *
     * @return void
     */
    public static function initBlocks() {
        $user_email = self::getEmail();
        if ( !empty( $user_email ) && Woocommerce::isBannedUser( $user_email ) ) {
            return;
        }

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

    /**
     * To register option status in checkout block data.
     *
     * @param \WC_Customer $customer
     * @param \WP_REST_Request $request
     * @return void
     */
    public static function checkBlockCheckoutEarning( \WC_Customer $customer, \WP_REST_Request $request ) {
        if ( ! isset( $request['extensions'] )
            || ! isset( $request['extensions']['wlopt_checkout_block'] )
            || ! isset( $request['extensions']['wlopt_checkout_block']['wlr_optin'] )
        ) {
            return;
        }
        if ( ! is_object( $customer ) ) {
            return;
        }
        $user_email = $customer->get_billing_email();
        if ( empty( $user_email )  || ! is_email( $user_email ) ) {
            return;
        }

        $accept_wployalty_membership = $request['extensions']['wlopt_checkout_block']['wlr_optin'] ? 1 : 0;
        self::updateUserOptInStatus($user_email, $accept_wployalty_membership);
    }

    /**
     * Update user optin status
     *
     * @param $user_email
     * @param $optin_status
     * @return void
     */
    public static function updateUserOptInStatus($user_email, $optin_status): void
    {
        $user_data = get_user_by('email', $user_email);
        $optin_data = Users::getOptinData($user_email);
        $loyalty_user_data = Woocommerce::getLoyaltyUserData($user_email);
        $data = array(
            'user_email' => $user_email,
            'wp_user_id' => $user_data->ID ?? null,
            'wlr_user_id' => $loyalty_user_data->id ?? null,
            'optin_status' => $optin_status,
        );
        if (!empty($optin_data)) {
            $data['id'] = $optin_data['id'] ?? 0;
        }
        Users::save($data);
    }
}