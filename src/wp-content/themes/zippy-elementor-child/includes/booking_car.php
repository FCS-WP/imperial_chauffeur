<?php

function booking_car_form(){
    global $product;
    if (is_product()) {
    $product_name = $product->get_name();
    $today = date('Y-m-d');
    $user_id = get_current_user_id();
    $key_member = 0;
    if(is_user_logged_in()){
        $key_member = 1;
    }
    ?>
    <form>
        <p class="title-form">Pick Up Information:</p>
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
                <label for="servicetype">Type<span style="color:red;">*</span></label>
                <select class="" id="servicetype" name="servicetype">
                    <option value="">—Please choose an option—</option>
                    <option value="Airport Arrival Transfer">Airport Arrival Transfer</option>
                    <option value="Airport Departure Transfer">Airport Departure Transfer</option>
                    <option value="Point-to-point Transfer">Point-to-point Transfer</option>
                    <option value="Hourly/Disposal">Hourly/Disposal</option>
                </select>
            </div>
        </div>
        <div class="row-form-custom">
            <input name="car-booked" type="hidden" value="<?php echo $product_name;?>">
            <input name="id-product" type="hidden" value="<?php echo $product->get_id();?>">
            <input name="key-member" type="hidden" value="<?php echo $key_member;?>">
        </div>
        <div class="row-form-custom col-2">
            <div class="col-form-custom">
                <label for="pickupdate">Pick Up Date<span style="color:red;">*</span></label>
                <input class="" id="pickupdate" min="<?php echo $today;?>" step="1" value="" type="date" name="pickupdate">
            </div>
            <div class="col-form-custom">
                <label for="pickuptime">Pick Up Time<span style="color:red;">*</span></label>
                <select class="" id="pickuptime" aria-required="true" aria-invalid="false" name="pickuptime">
                    <option value="">Select Pick Up Time</option>
                    <?php
                    for ($hour = 0; $hour < 24; $hour++) {
                        for ($minute = 0; $minute < 60; $minute += 5) {
                            $time = sprintf('%02d:%02d', $hour, $minute);
                            echo '<option value="' . $time . '">' . $time . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
                <label for="flight">Flight Details<span style="color:red;">*</span></label>
                <input size="40" maxlength="400" class="" id="flight" aria-required="true" aria-invalid="false" placeholder="Flight Details" value="" type="text" name="flight-details">
            </div>
        </div>
        <div class="row-form-custom col-2">
            <div class="col-form-custom">
                <label for="pickuplocation">Pick Up Location<span style="color:red;">*</span></label>
                <input size="40" maxlength="60" class="" id="pickuplocation" aria-required="true" aria-invalid="false" placeholder="Pick Up Location" value="" type="text" name="pickuplocation">
            </div>
            <div class="col-form-custom">
                <label for="doaddress">Drop Off Location<span style="color:red;">*</span></label>
                <input size="40" maxlength="50" class="" id="dolocation" aria-required="true" aria-invalid="false" placeholder="Drop Off Location" value="" type="text" name="dropofflocation">
            </div>
        </div>
        <p class="title-form">Pax Information:</p>
        <div class="row-form-custom col-3">
            <div class="col-form-custom">
                <label for="salutation">Salutation<span style="color:red;">*</span></label>
                <select class="" id="salutation" aria-required="true" aria-invalid="false" name="salutation"><option value="">—Please choose an option—</option><option value="Mr">Mr</option><option value="Mrs">Mrs</option><option value="Miss">Miss</option><option value="Mdm">Mdm</option><option value="Dr">Dr</option><option value="Prof">Prof</option></select>
            </div>
            <div class="col-form-custom">
                <label for="firstname">First Name<span style="color:red;">*</span></label>
                <input size="40" maxlength="35" class="" id="firstname" aria-required="true" aria-invalid="false" placeholder="First Name" value="" type="text" name="firstname">
            </div>
            <div class="col-form-custom">
                <label for="lastname">Last Name<span style="color:red;">*</span></label>
                <input size="40" maxlength="35" class="" id="lastname" aria-required="true" aria-invalid="false" placeholder="Last Name" value="" type="text" name="lastname">
            </div>
        </div>
        <div class="row-form-custom col-2">
            <div class="col-form-custom">
                <label for="tel">Telephone No.<span style="color:red;">*</span></label>
                <input size="40" maxlength="50" class="" id="tel" aria-required="true" aria-invalid="false" placeholder="Telephone number" value="" type="tel" name="tel">
            </div>
            <div class="col-form-custom">
                <label for="email">E-mail<span style="color:red;">*</span></label>
                <input size="40" maxlength="35" class="" id="email" aria-required="true" aria-invalid="false" placeholder="E-mail address" value="" type="email" name="email">
            </div>
        </div>
        <div class="row-form-custom col-2">
            <div class="col-form-custom">
                <label for="numpassengers">No. of Passengers<span style="color:red;">*</span></label>
                <input size="10" maxlength="400" class="" id="numpassengers" aria-required="true" aria-invalid="false" placeholder="No. of Passengers" value="" type="text" name="numpassengers">
            </div>
            <div class="col-form-custom">
                <label for="numbaggage">No. of Baggage<span style="color:red;">*</span></label>
                <input size="10" maxlength="400" class="" id="numbaggage" aria-required="true" aria-invalid="false" placeholder="No. of Baggage" value="" type="text" name="numbaggage">
            </div>
        </div>
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
                <p class="special-request">Special Requests</p>
                <textarea cols="50" rows="6" maxlength="2000" class="" id="requests" aria-invalid="false" placeholder="Please state your special requests here" name="requests"></textarea>
            </div>
        </div>
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
            <input class="" id="btnReserve" name="submit_car_booking" type="submit" value="Submit Enquiry">
            </div>
        </div>
    </form>
    <?php
    }
    
}
add_shortcode('booking-car-form','booking_car_form');

function process_form_submission() {
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
            
            $order->set_billing_phone( $tel );
            $order->set_billing_email($email);
            if(isset($user_id)){
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


function display_car_booking_meta_in_admin($order) {
    $product_name = $order->get_meta('_car_booking_product_name');
    $service_type = $order->get_meta('_car_booking_service_type');
    $product_id = $order->get_meta('_car_booking_product_id');
    $pickupdate = $order->get_meta('_car_booking_pickupdate');
    $key_member = $order->get_meta('_car_booking_key_member');
    $pickuptime = $order->get_meta('_car_booking_pickuptime');
    $flight_details = $order->get_meta('_car_booking_flight_details');
    $pickuplocation = $order->get_meta('_car_booking_pickuplocation');
    $dropofflocation = $order->get_meta('_car_booking_dropofflocation');
    $salutation = $order->get_meta('_car_booking_salutation');
    $firstname = $order->get_meta('_car_booking_firstname');
    $lastname = $order->get_meta('_car_booking_lastname');
    $tel = $order->get_meta('_car_booking_tel');
    $email = $order->get_meta('_car_booking_email');
    $numpassengers = $order->get_meta('_car_booking_numpassengers');
    $numbaggage = $order->get_meta('_car_booking_numbaggage');
    $requests = $order->get_meta('_car_booking_requests');
    ?>

    <div class="car-booking-infor">
        <p style="font-size:14px"><b>Information Customer</b></p>
        <div class="name-booking"><b>Name:</b> <?php echo $salutation . ' ' . $firstname . ' ' . $lastname; ?></div>
        <div class="pickup-booking"><b>Pick Up Location:</b> <?php echo $pickuplocation; ?></div>
        <div class="drop-off-booking"><b>Drop Off Location:</b> <?php echo $dropofflocation; ?></div>
        <div class="pickuptime"><b>Time:</b> <?php echo $pickuptime; ?> - <b>Date:</b> <?php echo $pickupdate; ?></div>
        <div class="service-type-booking"><b>Service Type:</b> <?php echo $service_type; ?></div>
        <div class="flight-details"><b>Flight Details:</b> <?php echo $flight_details; ?></div>
        <div class="numpassengers"><b>No. of Passengers:</b> <?php echo $numpassengers; ?></div>
        <div class="numbaggage"><b>No. of Baggage:</b> <?php echo $numbaggage; ?></div>
        <div class="requests"><b>Special Requests:</b> <?php echo $requests; ?></div>
        <div class="keymember"><b>Key: </b> <?php echo $key_member;?></div>
    </div>

    <?php
}
add_action('woocommerce_admin_order_data_after_billing_address', 'display_car_booking_meta_in_admin');

function book_car_page_admin(){
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
add_shortcode('booking-car-list', 'book_car_page_admin');
