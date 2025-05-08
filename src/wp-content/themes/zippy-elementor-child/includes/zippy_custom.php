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

add_filter( 'woocommerce_account_menu_items', 'remove_my_account_downloads_tab', 99 );
function remove_my_account_downloads_tab( $items ) {
    $links = [
        "downloads",
        "edit-address",
        "edit-account",
    ];

    foreach ($links as $link) {
        unset( $items[$link] );        
    }

    return $items;
}

add_filter('woocommerce_order_again_button', 'custom_hide_order_again_button_detail', 10, 1);

function custom_hide_order_again_button_detail($button_html) {
    global $order;
    if (is_account_page() && is_wc_endpoint_url('view-order')) {
        if ($order && is_a($order, 'WC_Order') && $order->has_status('completed')) {
            return '';
        }
    }

    return $button_html;
}
function get_tax_percent()
{
  $all_tax_rates = [];
  $tax_classes = WC_Tax::get_tax_classes();
  if (!in_array('', $tax_classes)) {
    array_unshift($tax_classes, '');
  }

  foreach ($tax_classes as $tax_class) {
    $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
    $all_tax_rates = array_merge($all_tax_rates, $taxes);
  }

  if (empty($all_tax_rates)) return;
  return $all_tax_rates[0];
}


add_filter('woocommerce_my_account_my_orders_query', function($args) {
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date_created';
    $order   = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

    switch ($orderby) {
        case 'id':
            $args['orderby'] = 'ID';
            break;
        case 'date_created':
            $args['orderby'] = 'date';
            break;
        default:
            $args['orderby'] = 'date';
    }

    $args['order'] = $order;

    return $args;
});


function build_sort_link($label, $orderby_field, $current_orderby, $current_order) {
    $base_url = wc_get_endpoint_url('orders');
    $params = $_GET;

    $is_active = ($orderby_field === $current_orderby);
    $new_order = ($is_active && $current_order === 'asc') ? 'desc' : 'asc';
    $arrow = '';

    if ($is_active) {
        $arrow = $current_order === 'asc' ? ' ▲' : ' ▼'; // Arrow
    } else {
        $arrow = ' ⇅';
    }

    $params['orderby'] = $orderby_field;
    $params['order'] = $new_order;

    $url = esc_url(add_query_arg($params, $base_url));

    return "<a href='{$url}'>{$label}<span>{$arrow}</span></a>";
}

function add_history_menu_item( $items ) {
    $items['history'] = 'Edit History';
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'add_history_menu_item' );



// history endpoints
function add_history_endpoint() {
    add_rewrite_endpoint( 'history', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'order-history', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_history_endpoint' );


// history content
function display_history_content() {
    $customer_orders = wc_get_orders( array(
        'customer' => get_current_user_id(),
    ) );

    echo '<h2>Edit History</h2>';

    if ( ! empty( $customer_orders ) ) {
        echo '<table class="shop_table shop_table_responsive my_account_orders">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="order-number">Order Number</th>';
        echo '<th class="order-actions">Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ( $customer_orders as $order ) {
            echo '<tr class="order">';
            echo '<td class="order-number" data-title="Order Number">';
            echo '<a href="' . esc_url( wc_get_endpoint_url( 'view-order', $order->get_id() ) ) . '">#' . $order->get_order_number() . '</a>';
            echo '</td>';
            echo '<td class="order-actions" data-title="Action">';
            echo '<a href="' . esc_url( wc_get_endpoint_url( 'order-history', $order->get_id() ) ) . '" class="woocommerce-button button view">View</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No order found!</p>';
    }
}
add_action( 'woocommerce_account_history_endpoint', 'display_history_content' );



// detail history
function display_order_history_content() {
    global $wp_query;

    if ( ! isset( $wp_query->query_vars['order-history'] ) ) {
        echo '<p>No order found!</p>';
        return;
    }

    $order_id = $wp_query->query_vars['order-history'];
    $order = wc_get_order( $order_id );

    if ( ! $order || $order->get_customer_id() != get_current_user_id() ) {
        echo '<p>No order found!</p>';
        return;
    }

    $order_notes = wc_get_order_notes( array(
        'order_id' => $order->get_id(),
        'order_by' => 'date_created',
        'order'    => 'DESC',
    ) );

    echo '<h2>Order #' . $order->get_order_number() . '</h2>';

    if ( ! empty( $order_notes ) ) {
        echo '<table class="shop_table shop_table_responsive order_notes_table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="note-action">Action</th>';
        echo '<th class="note-time">Time</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ( $order_notes as $note ) {
            echo '<tr>';
            echo '<td class="note-action">' . esc_html( $note->content ) . '</td>';
            echo '<td class="note-time">' . esc_html( $note->date_created->date( 'Y-m-d H:i:s' ) ) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>This order have no history yet.</p>';
    }
}
add_action( 'woocommerce_account_order-history_endpoint', 'display_order_history_content' );