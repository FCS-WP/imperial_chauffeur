<?php

function process_form_submission()
{
    if (isset($_GET['submit_car_booking'])) {
        $product_name = sanitize_text_field($_GET['car-booked']);
        $service_type = sanitize_text_field($_GET['servicetype']);
        $product_id = sanitize_text_field($_GET['id-product']);
        $key_member = sanitize_text_field($_GET['key-member']);
        $pickupdate = sanitize_text_field($_GET['pickupdate']);
        $pickuptime = sanitize_text_field($_GET['pickuptime']);
        $flight_details = sanitize_text_field($_GET['flight-details']);
        $pickuplocation = sanitize_text_field($_GET['pickuplocation']);
        $dropofflocation = sanitize_text_field($_GET['dropofflocation']);
        $salutation = sanitize_text_field($_GET['salutation']);
        $firstname = sanitize_text_field($_GET['firstname']);
        $lastname = sanitize_text_field($_GET['lastname']);
        $tel = sanitize_text_field($_GET['tel']);
        $email = sanitize_text_field($_GET['email']);
        $numpassengers = sanitize_text_field($_GET['numpassengers']);
        $numbaggage = sanitize_text_field($_GET['numbaggage']);
        $requests = sanitize_text_field($_GET['requests']);
        $user_id = get_current_user_id();

        $order = wc_create_order();

        if (is_a($order, 'WC_Order')) {
            $order->add_product(wc_get_product($product_id), 1);

            $order->update_meta_data('_car_booking_product_name', $product_name);
            $order->update_meta_data('_car_booking_service_type', $service_type);
            $order->update_meta_data('_car_booking_product_id', $product_id);
            $order->update_meta_data('_car_booking_key_member', $key_member);
            $order->update_meta_data('_car_booking_pickupdate', $pickupdate);
            $order->update_meta_data('_car_booking_pickuptime', $pickuptime);
            $order->update_meta_data('_car_booking_flight_details', $flight_details);
            $order->update_meta_data('_car_booking_pickuplocation', $pickuplocation);
            $order->update_meta_data('_car_booking_dropofflocation', $dropofflocation);
            $order->update_meta_data('_car_booking_salutation', $salutation);
            $order->update_meta_data('_car_booking_firstname', $firstname);
            $order->update_meta_data('_car_booking_lastname', $lastname);
            $order->update_meta_data('_car_booking_tel', $tel);
            $order->update_meta_data('_car_booking_email', $email);
            $order->update_meta_data('_car_booking_numpassengers', $numpassengers);
            $order->update_meta_data('_car_booking_numbaggage', $numbaggage);
            $order->update_meta_data('_car_booking_requests', $requests);

            $order->set_billing_phone($tel);
            $order->set_billing_email($email);
            if (isset($user_id)) {
                $order->set_customer_id($user_id);
            }
            $order->save();

            wc_add_notice('Your booking enquiry has been submitted successfully!', 'success');
        } else {
            wc_add_notice('There was an error creating your order. Please try again.', 'error');
        }
    }
}
add_action('init', 'process_form_submission');

function book_car_page()
{
    global $product;
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => '',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
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
                        <?php echo get_the_post_thumbnail(get_the_ID(), 'full'); ?>

                    </div>
                    <div class="product-item-col">
                        <h2 class="product-title"> <?php echo get_the_title(); ?></h2>
                        <div class="product-full-description"><?php echo $full_description; ?></div>
                    </div>
                    <div class="product-item-col center-product-col">
                        <button><a href="<?php echo get_the_permalink(); ?>">Enquire Now</a></button>
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
// add_shortcode('booking-car-list', 'book_car_page');
