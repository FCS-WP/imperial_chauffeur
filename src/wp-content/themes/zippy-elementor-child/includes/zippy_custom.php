<?php
add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
    $version = time();

    wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

    wp_enqueue_script('main-scripts-js', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), $version, true);

    // Load Thirt-party
    // wp_enqueue_style('vanilla-celendar-css', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.css', array(), $version, 'all');
    // wp_enqueue_script('vanilla-scripts-js', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.js', array('jquery'), $version, true);
    if (is_checkout()) {
        wp_enqueue_script('flatpickr-js', THEME_URL . '-child' . '/assets/lib/flatpickr/flatpickr.min.js', array('jquery'), null, true);
        wp_enqueue_style('flatpickr-css', THEME_URL . '-child' . '/assets/lib/flatpickr/flatpickr.min.css');
    }
}

add_filter('woocommerce_my_account_my_orders_actions', 'remove_pay_action', 10, 2);

function remove_pay_action($actions, $order)
{
    unset($actions['pay']);
    return $actions;
}

add_filter('woocommerce_email_enabled_customer_completed_order', 'disable_completed_email_for_non_monthly_orders', 10, 2);

function disable_completed_email_for_non_monthly_orders($enabled, $order)
{
    if (is_a($order, 'WC_Order')) {
        $is_monthly = $order->get_meta('is_monthly_payment_order');

        if (!$is_monthly) {
            return false;
        }
    }

    return $enabled;
}

add_filter('woocommerce_account_menu_items', 'remove_my_account_downloads_tab', 99);
function remove_my_account_downloads_tab($items)
{
    $links = [
        "downloads",
        "edit-address",
        "edit-account",
    ];

    foreach ($links as $link) {
        unset($items[$link]);
    }

    return $items;
}

add_filter('woocommerce_order_again_button', 'custom_hide_order_again_button_detail', 10, 1);

function custom_hide_order_again_button_detail($button_html)
{
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


add_filter('woocommerce_my_account_my_orders_query', function ($args) {
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


function build_sort_link($label, $orderby_field, $current_orderby, $current_order)
{
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

function add_history_menu_item($items)
{
    $items = array();

    $items['dashboard'] = 'Dashboard';
    $items['orders'] = 'Orders';
    $items['history'] = 'Edit History';
    $items['customer-logout'] = 'Log out';

    return $items;
}
add_filter('woocommerce_account_menu_items', 'add_history_menu_item');



// history endpoints
function add_history_endpoint()
{
    add_rewrite_endpoint('history', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('order-history', EP_ROOT | EP_PAGES);
}
add_action('init', 'add_history_endpoint');


// history content
function display_history_content()
{
    $orders = wc_get_orders(array(
        'customer' => get_current_user_id(),
    ));

    $filtered_orders = array();
    foreach ($orders as $order) {
        $order_notes = wc_get_order_notes(array(
            'order_id' => $order->get_id(),
            'type'     => 'customer',
            'orderby'  => 'date_created',
            'order'    => 'DESC',
            'limit'    => 1
        ));
        if (!empty($order_notes)) {
            $last_note = $order_notes[0];
            $filtered_orders[] = array(
                'order' => $order,
                'note_time' => $last_note->date_created,
            );
        }
    }
    echo '<h2>Order History</h2>';
    if (!empty($filtered_orders)) {
        echo '<table class="shop_table shop_table_responsive my_account_orders woocommerce-orders-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="order-number">Order Number</th>';
        echo '<th class="order-date">Last updated</th>';
        echo '<th class="order-actions"></th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($filtered_orders as $entry) {
            $order = $entry['order'];
            $note_time = $entry['note_time'];
            $time_format = $note_time->date('Y-m-d H:i:s');
            echo '<tr class="order">';
            echo '<td class="order-number" data-title="Order Number">';
            echo '<a href="' . esc_url(wc_get_endpoint_url('view-order', $order->get_id())) . '">#' . $order->get_order_number() . '</a>';
            echo '</td>';
            echo '<td class="order-note-time" data-title="Last Note">' .  esc_html($time_format) . '</td>';
            echo '<td class="order-actions" data-title="Action">';
            echo '<a href="' . esc_url(wc_get_endpoint_url('order-history', $order->get_id())) . '" class="woocommerce-button button view">View</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No order found!</p>';
    }
}
add_action('woocommerce_account_history_endpoint', 'display_history_content');



// detail history
function display_order_history_content()
{
    global $wp_query;

    if (!isset($wp_query->query_vars['order-history'])) {
        echo '<p>No order found!</p>';
        return;
    }

    $order_id = $wp_query->query_vars['order-history'];
    $order = wc_get_order($order_id);

    if (!$order || $order->get_customer_id() != get_current_user_id()) {
        echo '<p>No order found!</p>';
        return;
    }

    $order_notes = wc_get_order_notes(array(
        'order_id' => $order->get_id(),
        'type'     => 'customer',
        'order_by' => 'date_created',
        'order'    => 'DESC',
    ));

    echo '<h2>History for Order #' . $order->get_order_number() . '</h2>';

    if (!empty($order_notes)) {
        echo '<table class="shop_table shop_table_responsive my_account_orders order_notes_table woocommerce-orders-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="note-action">Action</th>';
        echo '<th class="note-time">Last updated</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($order_notes as $note) {
            echo '<tr>';
            echo '<td class="note-action">' . esc_html($note->content) . '</td>';
            echo '<td class="note-time">' . esc_html($note->date_created->date('Y-m-d H:i:s')) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>This order have no history yet.</p>';
    }
}
add_action('woocommerce_account_order-history_endpoint', 'display_order_history_content');


add_filter('wp_mail_from_name', 'my_mail_from_name');
function my_mail_from_name($name)
{
    return "Imperial Chauffeur Services";
}


add_filter('woocommerce_my_account_my_orders_query', 'filter_my_account_orders_by_status');

function filter_my_account_orders_by_status($args)
{
    $args['status'] = array('pending', 'processing', 'on-hold', 'confirmed');
    return $args;
}

add_action('wp_head', 'hide_product_des');

function hide_product_des()
{
    if (is_product() && !is_admin() && is_user_logged_in()) {
        echo "<style>#product-description-section{display:none;}</style>";
    }
}
