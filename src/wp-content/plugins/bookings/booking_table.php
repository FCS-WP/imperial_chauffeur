<?php
// 1. Thêm tab "Bookings" vào menu WooCommerce trong admin
function add_bookings_menu_item() {
    add_submenu_page(
        'woocommerce',
        'Bookings',   
        'Bookings',   
        'manage_options',
        'view-bookings', 
        'view_bookings_page'
    );
}
add_action('admin_menu', 'add_bookings_menu_item');


function view_bookings_page() {
    global $wpdb;

    // Truy vấn tất cả các đơn hàng từ bảng wp_posts và wp_postmeta
    $results = $wpdb->get_results("
        SELECT p.ID AS order_id, p.post_date AS order_date, p.post_status AS order_status, 
               pm1.meta_value AS customer_first_name, pm2.meta_value AS customer_last_name, 
               pm3.meta_value AS customer_id
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}postmeta AS pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_billing_first_name'
        LEFT JOIN {$wpdb->prefix}postmeta AS pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_billing_last_name'
        LEFT JOIN {$wpdb->prefix}postmeta AS pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_customer_user'
        WHERE p.post_type = 'shop_order_placehold'
    ");
    
    // Bắt đầu bảng HTML
    echo '<div class="wrap"><h1>Bookings</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Order ID</th><th>Customer Name</th><th>Customer ID</th><th>Status</th><th>Action</th></tr></thead>';
    echo '<tbody>';

    // Hiển thị từng booking
    if ($results) {
        foreach ($results as $order) {
            // Kết hợp first name và last name để tạo tên khách hàng đầy đủ
            $customer_name = $order->customer_first_name . ' ' . $order->customer_last_name;

            echo '<tr>';
            echo '<td>' . $order->order_id . '</td>';
            echo '<td>' . $customer_name . '</td>';
            echo '<td>' . $order->customer_id . '</td>';
            echo '<td>' . $order->order_status . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=view-bookings&action=view&order_id=' . $order->order_id) . '">View</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">No bookings found.</td></tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}



function handle_view_booking() {
    if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];

        $order = wc_get_order($order_id);

        if ($order) {
  
            echo '<div class="wrap"><h1>Order Details</h1>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<tr><th>Order ID</th><td>' . $order->get_id() . '</td></tr>';
            echo '<tr><th>Customer</th><td>' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</td></tr>';
            echo '<tr><th>Email</th><td>' . $order->get_billing_email() . '</td></tr>';
            echo '<tr><th>Phone</th><td>' . $order->get_billing_phone() . '</td></tr>';
            echo '<tr><th>Total</th><td>' . $order->get_total() . ' ' . $order->get_currency() . '</td></tr>';
            echo '<tr><th>Status</th><td>' . $order->get_status() . '</td></tr>';
            echo '</table>';

            echo '<h2>Products</h2><table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Product</th><th>Quantity</th><th>Price</th></tr></thead>';
            echo '<tbody>';

            foreach ($order->get_items() as $item_id => $item) {
                echo '<tr>';
                echo '<td>' . $item->get_name() . '</td>';
                echo '<td>' . $item->get_quantity() . '</td>';
                echo '<td>' . wc_price($item->get_total()) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<div class="wrap"><h1>Order Not Found</h1></div>';
        }
    }
}
add_action('admin_init', 'handle_view_booking');

?>
