<?php
add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
    $version = time();

    wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

    // wp_enqueue_script('main-scripts-js', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), $version, true);

    // Load Thirt-party
	// wp_enqueue_style('vanilla-celendar-css', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.css', array(), $version, 'all');
	// wp_enqueue_script('vanilla-scripts-js', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.js', array('jquery'), $version, true);
}

add_filter( 'woocommerce_my_account_my_orders_actions', 'remove_pay_action', 10, 2 );

function remove_pay_action( $actions, $order ) {
    unset( $actions['pay'] );
    return $actions;
}


add_filter( 'woocommerce_email_recipient_customer_completed_order', 'override_email_recipient_for_testing', 10, 2 );

function override_email_recipient_for_testing( $recipient, $order ) {
    if ( is_a( $order, 'WC_Order' ) ) {
        // Kiểm tra nếu đang ở chế độ test
        // if ( defined('WC_EMAIL_TEST_MODE') && WC_EMAIL_TEST_MODE ) {
            $recipient = 'tai.phan@floatingcube.com'; // Thay bằng địa chỉ email test của bạn
        // }
    }
    return $recipient;
}


add_filter( 'woocommerce_email_enabled_customer_completed_order', 'disable_completed_email_for_non_monthly_orders', 10, 2 );

function disable_completed_email_for_non_monthly_orders( $enabled, $order ) {
    if ( is_a( $order, 'WC_Order' ) ) {
        $is_monthly = $order->get_meta('is_monthly_payment_order');

        if ( ! $is_monthly ) {
            return false;
        }
    }

    return $enabled;
}
