<?php
function booking_car_form_picktime(){
    global $product;
    if (is_product()) {
    $today = date('Y-m-d');
    $key_member = 0;
    if(is_user_logged_in()){
        $key_member = 1;
    }
    ?>
    <p class="title-form">Pick Up Information:</p>
    <form method="POST">
        <div class="row-form-custom">
            <input name="id_product" type="hidden" value="<?php echo $product->get_id();?>">
            <input name="key_member" type="hidden" value="<?php echo $key_member;?>">
        </div>
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
        <div class="row-form-custom col-2">
            <div class="col-form-custom">
                <label for="DropOffDate">DropOffDate<span style="color:red;">*</span></label>
                <input class="" id="DropOffDate" min="<?php echo $today;?>" step="1" value="" type="date" name="DropOffDate">
            </div>
            <div class="col-form-custom">
                <label for="DropOffTime">Drop Off Time<span style="color:red;">*</span></label>
                <select class="" id="DropOffTime" aria-required="true" aria-invalid="false" name="DropOffTime">
                    <option value="">Select Drop Off Time</option>
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
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
                <label for="flight">Flight Details<span style="color:red;">*</span></label>
                <input size="40" maxlength="400" class="" id="flight" aria-required="true" aria-invalid="false" placeholder="Flight Details" value="" type="text" name="flight_details">
            </div>
        </div>
        <div class="row-form-custom col-1">
            <div class="col-form-custom">
            <input class="" id="btnReserve" name="submit_car_booking_time" type="submit" value="Payment Booking">
            </div>
        </div>
    </form>
    
    <?php
    }
}
add_shortcode('booking_car_form_picktime', 'booking_car_form_picktime');


function process_booking_time(){
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
        
        var_dump($time_use);
        $cart = WC()->cart;
        $cart->empty_cart();
        $cart->add_to_cart($id_product, $time_use);
        
    }
}
add_action('init', 'process_booking_time');

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

function information_basic_booking_car(){
    $cart = WC()->cart;
    $total_quantity = $cart->get_cart_contents_count();
    $total_price = $cart->get_cart_total();

    foreach ($cart->get_cart() as $cart_item){
        $product_name = $cart_item['data']->get_name();
        
    ?>
    <div class="box-order-booking">
        <h4 class="box-header">Your Booking</h4>
        <div class="heading-booking">
            <p>Car: <span><?php echo $product_name;?></span></p>
            <p><span class="">Book: <?php echo $total_quantity; ?> Day(s) - Total Price: </span><span class="inner-h"> <?php echo $total_price; ?></span></p>
            <p><span class="">Type: </span><span class="inner-h"><?php echo $cart_item['time_booking']['servicetype']; ?></span></p>
            <p><span class="">Flight Details: </span><span class="inner-h"><?php echo $cart_item['time_booking']['flight_details']; ?></span></p>
            <p><span class="">Key Member: </span><span class="inner-h"><?php echo $cart_item['time_booking']['key_member']; ?></span></p>
        </div>
        <div class="date-time-form ">
            <div class="form-item">
                <label>Pick Up </label>
                <div class="wrap-date-time">
                    <div class="wrap-date">
                        <span class="value"><?php echo $cart_item['time_booking']['pickupdate']; ?></span>
                    </div>
                    <div class="wrap-time">
                        <span class="value"><?php echo $cart_item['time_booking']['pickuptime']; ?></span>
                    </div>
                </div>
            </div>
            <div class="line"></div>
            <div class="form-item">
                <label>Drop Off</label>
                <div class="wrap-date-time">
                    <div class="wrap-date">
                        <span class="value"><?php echo $cart_item['time_booking']['DropOffDate']; ?></span>
                    </div>
                    <div class="wrap-time">
                        <span class="value"><?php echo $cart_item['time_booking']['DropOffTime']; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Car delivery location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['time_booking']['pickuplocation']; ?></span>
            </div>
        </div>
        <div class="dropdown-form">
            <label>Car return location</label>
            <div class="wrap-form">
                <span class="value"><?php echo $cart_item['time_booking']['dropofflocation']; ?></span>
            </div>
        </div>
    </div>
    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
    <?php
    }
    
}
add_shortcode('information_basic_booking_car', 'information_basic_booking_car');