<?php
function book_car_page(){
    global $product;
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => '',        
        'orderby'        => 'date',    
        'order'          => 'DESC',   
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ?>
        <div class="products-grid">

        <?php while ($query->have_posts()) { 
            $query->the_post();
            global $product;
            $full_description = $product->get_description();
        ?>    
        <div class="products-row">
            <div class="product-item-col">
                <?php echo get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                
            </div>
            <div class="product-item-col">
                <h2 class="product-title"> <?php echo get_the_title(); ?></h2>
                <div class="product-full-description"><?php echo $full_description; ?></div>
            </div>
            <div class="product-item-col center-product-col">
                <button><a href="<?php echo get_the_permalink(); ?>">Enquire Now</a></button>
                <form>
                    <input type="hidden" name="productID" value="<?php echo $product->get_id(); ?>">
                </form>
            </div>
        </div>
        <?php } ?>

        </div>
        <?php } else { ?>
        <p>No products found.</p>
    <?php
    }

    wp_reset_postdata();


}
add_shortcode('booking-car-list', 'book_car_page');




add_filter('woocommerce_checkout_fields', 'remove_billing_details');

function remove_billing_details($fields) {
    // Remove specific billing fields
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);

    
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'add_multiple_custom_checkout_fields');

function add_multiple_custom_checkout_fields($fields) {
    $cart = WC()->cart;
    foreach ($cart->get_cart() as $cart_item){
    
    $fields['billing']['no_of_passengers'] = array(
        'type'        => 'text',
        'label'       => __('No. of Passengers', 'woocommerce'),
        'placeholder' => __('Enter no. of Passengers', 'woocommerce'),
        'required'    => true, 
        'class'       => array('form-row-wide'),
        'clear'       => true,
    );
    
    $fields['billing']['no_of_baggage'] = array(
        'type'        => 'text',
        'label'       => __('No. of Baggage', 'woocommerce'),
        'placeholder' => __('Enter no. of Baggage', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'clear'       => true,
    );

    $fields['billing']['servicetype'] = array(
        'type'        => 'hidden', 
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['servicetype']
    );

    $fields['billing']['flight_details'] = array(
        'type'        => 'hidden', 
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['flight_details']
    );

    $fields['billing']['key_member'] = array(
        'type'        => 'hidden', 
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['key_member']
    );

    $fields['billing']['pickupdate'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['pickupdate']
    );

    $fields['billing']['pickuptime'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['pickuptime']
    );

    $fields['billing']['DropOffDate'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['DropOffDate']
    );

    $fields['billing']['DropOffTime'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['DropOffTime']
    );

    $fields['billing']['pickuplocation'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['pickuplocation']
    );

    $fields['billing']['dropofflocation'] = array(
        'type'        => 'hidden',
        'required'    => true,
        'class'       => array('form-row-wide hidden-field'),
        'clear'       => true,
        'default'     => $cart_item['time_booking']['dropofflocation']
    );
    }
    return $fields;
}
add_action('woocommerce_checkout_update_order_meta', 'save_multiple_custom_checkout_fields');

function save_multiple_custom_checkout_fields($order_id) {

    if (!empty($_POST['no_of_passengers'])) {
        update_post_meta($order_id, 'no_of_passengers', sanitize_text_field($_POST['no_of_passengers']));
    }

    if (!empty($_POST['no_of_baggage'])) {
        update_post_meta($order_id, 'no_of_baggage', sanitize_text_field($_POST['no_of_baggage']));
    }

    if (!empty($_POST['servicetype'])) {
        update_post_meta($order_id, 'servicetype', sanitize_text_field($_POST['servicetype']));
    }
    if (!empty($_POST['flight_details'])) {
        update_post_meta($order_id, 'flight_details', sanitize_text_field($_POST['flight_details']));
    }
    if (!empty($_POST['key_member'])) {
        update_post_meta($order_id, 'key_member', sanitize_text_field($_POST['key_member']));
    }
    if (!empty($_POST['pickupdate'])) {
        update_post_meta($order_id, 'pickupdate', sanitize_text_field($_POST['pickupdate']));
    }
    if (!empty($_POST['pickuptime'])) {
        update_post_meta($order_id, 'pickuptime', sanitize_text_field($_POST['pickuptime']));
    }
    if (!empty($_POST['DropOffDate'])) {
        update_post_meta($order_id, 'DropOffDate', sanitize_text_field($_POST['DropOffDate']));
    }
    if (!empty($_POST['DropOffTime'])) {
        update_post_meta($order_id, 'DropOffTime', sanitize_text_field($_POST['DropOffTime']));
    }
    if (!empty($_POST['pickuplocation'])) {
        update_post_meta($order_id, 'pickuplocation', sanitize_text_field($_POST['pickuplocation']));
    }
    if (!empty($_POST['dropofflocation'])) {
        update_post_meta($order_id, 'dropofflocation', sanitize_text_field($_POST['dropofflocation']));
    }
    

}
add_action('woocommerce_admin_order_data_after_billing_address', 'display_multiple_custom_checkout_fields_in_admin', 10, 1);

function display_multiple_custom_checkout_fields_in_admin($order) {

    $no_of_passengers = get_post_meta($order->get_id(), 'no_of_passengers', true);
    if ($no_of_passengers) {
        echo '<p><strong>' . __('No Of Passengers: ', 'woocommerce') . ':</strong> ' . esc_html($no_of_passengers) . '</p>';
    }

    $no_of_baggage = get_post_meta($order->get_id(), 'no_of_baggage', true);
    if ($no_of_baggage) {
        echo '<p><strong>' . __('No Of Baggage: ', 'woocommerce') . ':</strong> ' . esc_html($no_of_baggage) . '</p>';
    }

    $servicetype = get_post_meta($order->get_id(), 'servicetype', true);
    if ($servicetype) {
        echo '<p><strong>' . __('Service Type: ', 'woocommerce') . ':</strong> ' . esc_html($servicetype) . '</p>';
    }

    $flight_details = get_post_meta($order->get_id(), 'flight_details', true);
    if ($flight_details) {
        echo '<p><strong>' . __('Flight Details: ', 'woocommerce') . ':</strong> ' . esc_html($flight_details) . '</p>';
    }

    $key_member = get_post_meta($order->get_id(), 'key_member', true);
    if ($key_member) {
        echo '<p><strong>' . __('Key Member: ', 'woocommerce') . ':</strong> ' . esc_html($key_member) . '</p>';
    }

    $pickupdate = get_post_meta($order->get_id(), 'pickupdate', true);
    if ($pickupdate) {
        echo '<p><strong>' . __('Pick Up Date: ', 'woocommerce') . ':</strong> ' . esc_html($pickupdate) . '</p>';
    }

    $pickuptime = get_post_meta($order->get_id(), 'pickuptime', true);
    if ($pickuptime) {
        echo '<p><strong>' . __('Pick Up Time: ', 'woocommerce') . ':</strong> ' . esc_html($pickuptime) . '</p>';
    }

    $DropOffDate = get_post_meta($order->get_id(), 'DropOffDate', true);
    if ($DropOffDate) {
        echo '<p><strong>' . __('Drop Off Date: ', 'woocommerce') . ':</strong> ' . esc_html($DropOffDate) . '</p>';
    }

    $DropOffTime = get_post_meta($order->get_id(), 'DropOffTime', true);
    if ($DropOffTime) {
        echo '<p><strong>' . __('Drop Off Time: ', 'woocommerce') . ':</strong> ' . esc_html($DropOffTime) . '</p>';
    }

    $pickuplocation = get_post_meta($order->get_id(), 'pickuplocation', true);
    if ($pickuplocation) {
        echo '<p><strong>' . __('Pick Up Location: ', 'woocommerce') . ':</strong> ' . esc_html($pickuplocation) . '</p>';
    }

    $dropofflocation = get_post_meta($order->get_id(), 'dropofflocation', true);
    if ($dropofflocation) {
        echo '<p><strong>' . __('Drop Off Location: ', 'woocommerce') . ':</strong> ' . esc_html($dropofflocation) . '</p>';
    }

}

add_filter('woocommerce_available_payment_gateways', 'restrict_payment_methods_for_logged_in_users');

function restrict_payment_methods_for_logged_in_users($available_gateways) {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Loop through the available gateways
        foreach ($available_gateways as $gateway_id => $gateway) {
            // Allow only 'cod' (Cash on Delivery) for logged-in users
            if ($gateway_id !== 'cheque') {
                unset($available_gateways[$gateway_id]);
            }
        }
    }else{
        foreach ($available_gateways as $gateway_id => $gateway) {
            if ($gateway_id === 'cheque') {
                unset($available_gateways[$gateway_id]);
            }
        }
    }

    return $available_gateways;
}
