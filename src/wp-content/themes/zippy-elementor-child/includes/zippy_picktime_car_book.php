<?php
//shortcode vanilla_booking_car_custom create form booking on single product page
function vanilla_booking_car_custom(){
    global $product;
    if (is_product()){
    $today = date('d-m-Y');
    $key_member = 0;
    if(is_user_logged_in()){
        $key_member = 1;
    }
    ?>
    <form method="POST">
        <div class="box-pickup-information">
            <div class="calendar-box-custom">
                <div class="calendar-box"><div id="calendar"></div></div>
            </div>
            <div class="summary-information">
                <div class="col-summary-information">
                    <label>From</label><input class="" id="pickupdate" value="<?php echo $today;?>" type="text" name="pickupdate">
                </div>
                <div class="col-summary-information">
                    <label>To</label><input class="" id="DropOffDate" value="<?php echo $today;?>" type="text" name="DropOffDate">
                </div>
                <div class="col-summary-information">
                    <label>Pick Up</label><input type="time" id="pickuptime" name="pickuptime" min="00:00" max="24:00" value="<?php echo date("H:i"); ?>" required>
                </div>
                <div class="col-summary-information">
                    <label>Drop Off</label><input type="time" id="DropOffTime" name="DropOffTime" min="00:00" max="24:00" value="<?php echo date("H:i"); ?>" required>
                </div>
            </div>
            
            <div class="input-text-pickup-information">
                <div class="row-form-custom">
                    <input name="id_product" type="hidden" value="<?php echo $product->get_id();?>">
                    <input name="key_member" type="hidden" value="<?php echo $key_member;?>">
                </div>
                <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <label for="servicetype">Type Services</label>
                        <select class="" id="servicetype" name="servicetype">
                            <option value="">Please choose an option</option>
                            <option value="Airport Arrival Transfer">Airport Arrival Transfer</option>
                            <option value="Airport Departure Transfer">Airport Departure Transfer</option>
                            <option value="Point-to-point Transfer">Point-to-point Transfer</option>
                            <option value="Hourly/Disposal">Hourly/Disposal</option>
                        </select>
                    </div>
                </div>
                <div class="row-form-custom col-2">
                    <div class="col-form-custom">
                        <label for="pickuplocation">Pick Up</label>
                        <input size="40" maxlength="60" class="" id="pickuplocation" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="pickuplocation">
                    </div>
                    <div class="col-form-custom">
                        <label for="doaddress">Drop Off</label>
                        <input size="40" maxlength="50" class="" id="dolocation" aria-required="true" aria-invalid="false" placeholder="Enter location" value="" type="text" name="dropofflocation">
                    </div>
                </div>
                <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <label for="flight">Flight Details<span style="color:red;">*</span></label>
                        <input size="40" maxlength="400" class="" id="flight" aria-required="true" aria-invalid="false" placeholder="Enter your flight details" value="" type="text" name="flight_details">
                    </div>
                </div>
                
            </div>
            <div class="col-total-price-information">
                <label>Total Price: </label><span > $<span id="price-total"><?php echo $current_price = $product->get_price();?></span><span id="default-price" style="display:none"><?php echo $current_price = $product->get_price();?></span></span>
            </div>
            <div class="row-form-custom col-1">
                    <div class="col-form-custom">
                        <input class="" id="btnReserve" name="submit_car_booking_time" type="submit" value="Payment Booking">
                    </div>
                </div>
        </div>
    </form>
    <?php
    }
}
add_shortcode('vanilla_booking_car_custom', 'vanilla_booking_car_custom');

//function process submit pick up information
function process_booking_time(){
    $status_redirect = false;
    if (isset($_POST['submit_car_booking_time'])) {
        $id_product = sanitize_text_field($_POST['id_product']);
        $pickupdate = sanitize_text_field($_POST['pickupdate']);
        $pickuptime = sanitize_text_field($_POST['pickuptime']);
        $DropOffDate = sanitize_text_field($_POST['DropOffDate']);
        $DropOffTime = sanitize_text_field($_POST['DropOffTime']);
        
        $time_use = 0;

        if (!empty($pickupdate) && !empty($DropOffDate)) {
            try {
                $pickup_date = new DateTime($pickupdate);
                $dropoff_date = new DateTime($DropOffDate);
        
                // Calculate the difference
                $interval = $pickup_date->diff($dropoff_date);
                $time_use = $interval->days; // Total days difference
            } catch (Exception $e) {
                // Handle any potential errors
                error_log('Date calculation error: ' . $e->getMessage());
            }
        }
        
        $cart = WC()->cart;
        $cart->empty_cart();
        $cart->add_to_cart($id_product, $time_use);
        $status_redirect = true;
        
    }
    if($status_redirect == true){
        wp_redirect(wc_get_checkout_url());
        exit;
    }
}
add_action('init', 'process_booking_time');

//function add pick up information to cart
add_filter('woocommerce_add_cart_item_data', 'add_custom_cart_item_data_time');
function add_custom_cart_item_data_time($cart_item_data)
{
    if (isset($_POST['submit_car_booking_time'])) {
        $key_member = sanitize_text_field($_POST['key_member']);
        $pickupdate = sanitize_text_field($_POST['pickupdate']);
        $pickuptime = sanitize_text_field($_POST['pickuptime']);
        $DropOffDate = sanitize_text_field($_POST['DropOffDate']);
        $DropOffTime = sanitize_text_field($_POST['DropOffTime']);
        $pickuplocation = sanitize_text_field($_POST['pickuplocation']);
        $dropofflocation = sanitize_text_field($_POST['dropofflocation']);
        $servicetype = sanitize_text_field($_POST['servicetype']);
        $flight_details = sanitize_text_field($_POST['flight_details']);

        $cart_item_data['time_booking'] = array(
            'pickupdate' => $pickupdate,
            'pickuptime' => $pickuptime,
            'DropOffDate' => $DropOffDate,
            'DropOffTime' => $DropOffTime,
            'pickuplocation' => $pickuplocation,
            'dropofflocation' => $dropofflocation,
            'servicetype' => $servicetype,
            'flight_details' => $flight_details,
            'key_member' => $key_member,
        );
    }

    return $cart_item_data;
}