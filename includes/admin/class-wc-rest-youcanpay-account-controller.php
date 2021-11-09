<?php
/**
 * Class WC_REST_YouCanPay_Account_Controller
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST controller for retrieving YouCanPay's account data.
 *
 * @since 5.6.0
 */
class WC_REST_YouCanPay_Account_Controller extends WC_YouCanPay_REST_Base_Controller {
	/**
	 * Endpoint path.
	 *
	 * @var string
	 */
	protected $rest_base = 'wc_youcanpay/account';

	/**
	 * The account data utility.
	 *
	 * @var WC_YouCanPay_Account
	 */
	private $account;

	public function __construct( WC_YouCanPay_Account $account ) {
		$this->account = $account;
	}

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_account' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/webhook-status-message',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_webhook_status_message' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/refresh',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'refresh_account' ],
				'permission_callback' => [ $this, 'check_permission' ],
			]
		);
	}

	/**
	 * Retrieve the YouCanPay account information.
	 *
	 * @return WP_REST_Response
	 */
	public function get_account() {
		return new WP_REST_Response(
			[
				'account'                => $this->account->get_cached_account_data(),
				'webhook_status_message' => WC_YouCanPay_Webhook_State::get_webhook_status_message(),
				'webhook_url'            => WC_YouCanPay_Helper::get_webhook_url(),
			]
		);
	}

	/**
	 * Retrieve the webhook status information.
	 *
	 * @return WP_REST_Response
	 */
	public function get_webhook_status_message() {
		return new WP_REST_Response( WC_YouCanPay_Webhook_State::get_webhook_status_message() );
	}

	/**
	 * Clears the cached account data and returns the updated one.
	 *
	 * @return WP_REST_Response
	 */
	public function refresh_account() {
		$this->account->clear_cache();

		// calling the same "get" method, so that the data format is the same.
		return $this->get_account();
	}
}
