<?php
// 1. Thêm menu "Bookings" vào WooCommerce
function add_bookings_menu_item() {
    add_submenu_page(
        'woocommerce',
        'Bookings', // Tên trang
        'Bookings', // Tên menu
        'manage_woocommerce', // Quyền
        'view-bookings', // Slug của trang
        'view_bookings_page' // Callback function
    );
}
add_action('admin_menu', 'add_bookings_menu_item');

// 2. Hiển thị danh sách đơn hàng nhóm theo Customer ID
function view_bookings_page() {
    // Lấy danh sách tất cả đơn hàng
    $args = array(
        'limit' => -1, // Lấy tất cả đơn hàng
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $orders = wc_get_orders($args);

    // Nhóm đơn hàng theo Customer ID
    $grouped_orders = array();
    foreach ($orders as $order) {
        $customer_id = $order->get_customer_id() ?: 'guest'; // Nếu không có ID, dùng "guest"
        if (!isset($grouped_orders[$customer_id])) {
            $grouped_orders[$customer_id] = array(
                'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                'orders' => array(),
            );
        }
        $grouped_orders[$customer_id]['orders'][] = $order;
    }

    // Bắt đầu bảng HTML
    echo '<div class="wrap"><h1>Bookings</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Customer ID</th><th>Customer Name</th><th>Number of Orders</th><th>Action</th></tr></thead>';
    echo '<tbody>';

    // Hiển thị từng nhóm khách hàng
    if (!empty($grouped_orders)) {
        foreach ($grouped_orders as $customer_id => $data) {
            $customer_name = $data['customer_name'];
            $order_count = count($data['orders']);

            echo '<tr>';
            echo '<td>' . esc_html($customer_id === 'guest' ? 'Guest' : $customer_id) . '</td>';
            echo '<td>' . esc_html($customer_name) . '</td>';
            echo '<td>' . esc_html($order_count) . '</td>';
            echo '<td><a href="' . esc_url(admin_url('admin.php?page=view-bookings&action=view&customer_id=' . $customer_id)) . '">View</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">No bookings found.</td></tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

// 3. Xử lý chi tiết nhóm đơn hàng của một khách hàng
function handle_view_booking() {
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['customer_id'])) {
        $customer_id = sanitize_text_field($_GET['customer_id']);
        $args = array(
            'limit' => -1,
            'customer_id' => $customer_id === 'guest' ? 0 : $customer_id,
        );
        $orders = wc_get_orders($args);

        if (!empty($orders)) {
            echo '<div class="wrap"><h1>Order Details for Customer: ' . esc_html($customer_id) . '</h1>';

            foreach ($orders as $order) {
                echo '<h2>Order #' . esc_html($order->get_id()) . '</h2>';
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<tr><th>Order ID</th><td>' . esc_html($order->get_id()) . '</td></tr>';
                echo '<tr><th>Date</th><td>' . esc_html($order->get_date_created()->date('Y-m-d H:i:s')) . '</td></tr>';
                echo '<tr><th>Customer</th><td>' . esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()) . '</td></tr>';
                echo '<tr><th>Email</th><td>' . esc_html($order->get_billing_email()) . '</td></tr>';
                echo '<tr><th>Phone</th><td>' . esc_html($order->get_billing_phone()) . '</td></tr>';
                echo '<tr><th>Total</th><td>' . esc_html(wc_price($order->get_total(), array('currency' => $order->get_currency()))) . '</td></tr>';
                echo '<tr><th>Status</th><td>' . esc_html(wc_get_order_status_name($order->get_status())) . '</td></tr>';
                echo '</table>';

                echo '<h3>Products</h3>';
                echo '<table class="wp-list-table widefat fixed striped">';
                echo '<thead><tr><th>Product</th><th>Quantity</th><th>Price</th></tr></thead>';
                echo '<tbody>';

                foreach ($order->get_items() as $item) {
                    echo '<tr>';
                    echo '<td>' . esc_html($item->get_name()) . '</td>';
                    echo '<td>' . esc_html($item->get_quantity()) . '</td>';
                    echo '<td>' . esc_html(wc_price($item->get_total())) . '</td>';
                    echo '</tr>';
                }

                echo '</tbody></table>';
            }

            echo '</div>';
        } else {
            echo '<div class="wrap"><h1>No Orders Found for Customer ID: ' . esc_html($customer_id) . '</h1></div>';
        }
    }
}
add_action('admin_init', 'handle_view_booking');
?>
