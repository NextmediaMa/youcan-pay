<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters(
	'wc_youcanpay_sofort_settings',
	[
		'geo_target'  => [
			'description' => __( 'Customer Geography: Germany, Austria', 'woocommerce-youcan-pay' ),
			'type'        => 'title',
		],
		'guide'       => [
			'description' => __( '<a href="https://youcanpay.com/payments/payment-methods-guide#sofort" target="_blank">Payment Method Guide</a>', 'woocommerce-youcan-pay' ),
			'type'        => 'title',
		],
		'activation'  => [
			'description' => __( 'Must be activated from your YouCan Pay Dashboard Settings <a href="https://dashboard.youcanpay.com/account/payments/settings" target="_blank">here</a>', 'woocommerce-youcan-pay' ),
			'type'        => 'title',
		],
		'enabled'     => [
			'title'       => __( 'Enable/Disable', 'woocommerce-youcan-pay' ),
			'label'       => __( 'Enable YouCan Pay SOFORT', 'woocommerce-youcan-pay' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no',
		],
		'title'       => [
			'title'       => __( 'Title', 'woocommerce-youcan-pay' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-youcan-pay' ),
			'default'     => __( 'SOFORT', 'woocommerce-youcan-pay' ),
			'desc_tip'    => true,
		],
		'description' => [
			'title'       => __( 'Description', 'woocommerce-youcan-pay' ),
			'type'        => 'text',
			'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-youcan-pay' ),
			'default'     => __( 'You will be redirected to SOFORT.', 'woocommerce-youcan-pay' ),
			'desc_tip'    => true,
		],
	]
);
