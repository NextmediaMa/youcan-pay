<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Controls whether we're on the settings page and enqueues the JS code.
 */
class WC_YouCanPay_Settings_Controller
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    /**
     * Load admin scripts.
     */
    public function admin_scripts($hook_suffix)
    {
        if ('woocommerce_page_wc-settings' !== $hook_suffix) {
            return;
        }

        if (!WC_YouCanPay_Helper::should_enqueue_in_current_tab_section('checkout', 'youcanpay')) {
            return;
        }

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        wp_register_script(
            'woocommerce_youcanpay_admin',
            plugins_url('assets/js/youcanpay-admin' . $suffix . '.js', WC_YOUCAN_PAY_MAIN_FILE),
            [],
            WC_YOUCAN_PAY_VERSION,
            true
        );

        wp_localize_script('woocommerce_youcanpay_admin', 'wc_youcanpay_settings_params', []);

        wp_enqueue_script('woocommerce_youcanpay_admin');
        wp_enqueue_style('woocommerce_youcanpay_admin');
    }
}
