<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class that handles various admin tasks.
 *
 * @since 5.6.0
 */
class WC_YouCanPay_Payment_Gateways_Controller {
	/**
	 * Constructor
	 *
	 * @since 5.6.0
	 */
	public function __construct() {
		// If UPE is enabled and there are enabled payment methods, we need to load the disable YouCan Pay confirmation modal.
		$youcanpay_settings              = get_option( 'woocommerce_youcanpay_settings', [] );
		$enabled_upe_payment_methods  = isset( $youcanpay_settings['upe_checkout_experience_accepted_payments'] ) ? $youcanpay_settings['upe_checkout_experience_accepted_payments'] : [];

		if ( count( $enabled_upe_payment_methods ) > 0) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_payments_scripts' ] );
			add_action( 'woocommerce_admin_field_payment_gateways', [ $this, 'wc_youcanpay_gateway_container' ] );
		}
	}

	public function register_payments_scripts() {
		$payment_gateways_script_asset_path = WC_YOUCAN_PAY_PLUGIN_PATH . '/build/payment_gateways.asset.php';
		$payment_gateways_script_asset      = file_exists( $payment_gateways_script_asset_path )
			? require_once $payment_gateways_script_asset_path
			: [
				'dependencies' => [],
				'version'      => WC_YOUCAN_PAY_VERSION,
			];

		wp_register_script(
			'woocommerce_youcanpay_payment_gateways_page',
			plugins_url( 'build/payment_gateways.js', WC_YOUCAN_PAY_MAIN_FILE ),
			$payment_gateways_script_asset['dependencies'],
			$payment_gateways_script_asset['version'],
			true
		);
		wp_register_style(
			'woocommerce_youcanpay_payment_gateways_page',
			plugins_url( 'build/payment_gateways.css', WC_YOUCAN_PAY_MAIN_FILE ),
			[ 'wc-components' ],
			$payment_gateways_script_asset['version']
		);
	}

	public function enqueue_payments_scripts() {
		global $current_tab, $current_section;

		$this->register_payments_scripts();

		$is_payment_methods_page = (
			is_admin() &&
			$current_tab && ! $current_section
			&& 'checkout' === $current_tab
		);

		if ( $is_payment_methods_page ) {
			wp_enqueue_script( 'woocommerce_youcanpay_payment_gateways_page' );
			wp_enqueue_style( 'woocommerce_youcanpay_payment_gateways_page' );
		}
	}

	/**
	 * Adds a container to the "payment gateways" page.
	 * This is where the "Are you sure you want to disable YouCan Pay?" confirmation dialog is rendered.
	 */
	public function wc_youcanpay_gateway_container() {
		?><div id="wc-youcanpay-payment-gateways-container" />
		<?php
	}

}
