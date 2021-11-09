<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls whether we're on the settings page and enqueues the JS code.
 *
 * @since 5.4.1
 */
class WC_YouCanPay_Settings_Controller {
	/**
	 * The YouCan Pay account instance.
	 *
	 * @var WC_YouCanPay_Account
	 */
	private $account;

	/**
	 * Constructor
	 *
	 * @param WC_YouCanPay_Account $account YouCan Pay account
	 */
	public function __construct( WC_YouCanPay_Account $account ) {
		$this->account = $account;
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'wc_youcanpay_gateway_admin_options_wrapper', [ $this, 'admin_options' ] );
	}

	/**
	 * Prints the admin options for the gateway.
	 * Remove this action once we're fully migrated to UPE and move the wrapper in the `admin_options` method of the UPE gateway.
	 *
	 * @param WC_YouCanPay_Payment_Gateway $gateway the YouCan Pay gateway.
	 */
	public function admin_options( WC_YouCanPay_Payment_Gateway $gateway ) {
		global $hide_save_button;
		$hide_save_button    = true;
		$is_youcanpay_connected = woocommerce_gateway_youcanpay()->connect->is_connected();

		echo '<h2>' . esc_html( $gateway->get_method_title() );
		wc_back_link( __( 'Return to payments', 'woocommerce-gateway-youcanpay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) );
		echo '</h2>';

		echo $is_youcanpay_connected ? '<div id="wc-youcanpay-account-settings-container"></div>' : '<div id="wc-youcanpay-new-account-container"></div>';
	}

	/**
	 * Load admin scripts.
	 */
	public function admin_scripts( $hook_suffix ) {
		if ( 'woocommerce_page_wc-settings' !== $hook_suffix ) {
			return;
		}

		if ( ! WC_YouCanPay_Helper::should_enqueue_in_current_tab_section( 'checkout', 'youcanpay' ) ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( WC_YouCanPay_Feature_Flags::is_upe_settings_redesign_enabled() ) {
			// Webpack generates an assets file containing a dependencies array for our built JS file.
			$script_asset_path = WC_YOUCAN_PAY_PLUGIN_PATH . '/build/upe_settings.asset.php';
			$script_asset      = file_exists( $script_asset_path )
				? require $script_asset_path
				: [
					'dependencies' => [],
					'version'      => WC_YOUCAN_PAY_VERSION,
				];

			wp_register_script(
				'woocommerce_youcanpay_admin',
				plugins_url( 'build/upe_settings.js', WC_YOUCAN_PAY_MAIN_FILE ),
				$script_asset['dependencies'],
				$script_asset['version'],
				true
			);
			wp_register_style(
				'woocommerce_youcanpay_admin',
				plugins_url( 'build/upe_settings.css', WC_YOUCAN_PAY_MAIN_FILE ),
				[ 'wc-components' ],
				$script_asset['version']
			);
		} else {
			wp_register_script( 'woocommerce_youcanpay_admin', plugins_url( 'assets/js/youcanpay-admin' . $suffix . '.js', WC_YOUCAN_PAY_MAIN_FILE ), [], WC_YOUCAN_PAY_VERSION, true );
			wp_register_style(
				'woocommerce_youcanpay_admin',
				plugins_url( 'assets/css/youcanpay-admin-styles' . $suffix . '.css', WC_YOUCAN_PAY_MAIN_FILE ),
				[],
				WC_YOUCAN_PAY_VERSION
			);
		}

		$oauth_url = woocommerce_gateway_youcanpay()->connect->get_oauth_url();
		if ( is_wp_error( $oauth_url ) ) {
			$oauth_url = '';
		}

		$params = [
			'time'                    => time(),
			'i18n_out_of_sync'        => wp_kses(
				__( '<strong>Warning:</strong> your site\'s time does not match the time on your browser and may be incorrect. Some payment methods depend on webhook verification and verifying webhooks with a signing secret depends on your site\'s time being correct, so please check your site\'s time before setting a webhook secret. You may need to contact your site\'s hosting provider to correct the site\'s time.', 'woocommerce-gateway-youcanpay' ),
				[ 'strong' => [] ]
			),
			'is_upe_checkout_enabled' => WC_YouCanPay_Feature_Flags::is_upe_checkout_enabled(),
			'youcanpay_oauth_url'        => $oauth_url,
		];
		wp_localize_script( 'woocommerce_youcanpay_admin', 'wc_youcanpay_settings_params', $params );

		wp_enqueue_script( 'woocommerce_youcanpay_admin' );
		wp_enqueue_style( 'woocommerce_youcanpay_admin' );
	}
}
