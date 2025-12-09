<?php
/*
Plugin Name: SMW Webinar — Events & Ticketing
Description: SMW Webinar is a plugin for ticketing system.
Version: 2.0
Author: Mimo Dev
Author URI: https://evolv109.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: ticket
*/

define( 'SMW_VERSION', '1.6' );
define( 'SMW_PLUGIN', __FILE__ );
define( 'SMW_PLUGIN_BASENAME', plugin_basename( SMW_PLUGIN ) );
define( 'SMW_PLUGIN_DIRPATH', plugin_dir_path( SMW_PLUGIN ) );

// register_activation_hook
function smw_plugin_create_winner_page() {
    $winner_page = get_page_by_path( 'winner' );
    if ( ! $winner_page ) {
        $winner_page_id = wp_insert_post(
            array(
                'post_title'   => 'Winner',
                'post_content' => '[smw_shortcode]',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => 'winner'
            )
        );
    }

}

register_activation_hook( __FILE__, 'smw_plugin_create_winner_page' );

function custom_add_to_cart_redirect($url) {
    if (!empty($_REQUEST['add-to-cart'])) {
        return wc_get_cart_url(); // Redirect to the cart page
    }
    return $url;
}
add_filter('woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect');

// Disable AJAX add to cart buttons on archives
function custom_disable_ajax_add_to_cart($link, $product) {
    if (is_post_type_archive('product')) {
        return str_replace('ajax_add_to_cart', 'add_to_cart_button', $link);
    }
    return $link;
}
add_filter('woocommerce_loop_add_to_cart_link', 'custom_disable_ajax_add_to_cart', 10, 2);

function delete_custom_page_on_deactivation() {
    $page_slug = 'winner';
	$page = get_page_by_path($page_slug);
	$WinnerPageId = $page->ID;
    
    if ( $WinnerPageId ) {
        wp_delete_post( $WinnerPageId, true );
    }
}
register_deactivation_hook( __FILE__, 'delete_custom_page_on_deactivation' );
#Twilio
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
function wp_twilio_sms($to_mobile, $msg){
	$settings = get_option( 'smw_woo_settings' );
	$sid = $settings['twilio_sid'];
	$token = $settings['twilio_token'];
	if($sid==""){
	    return;
	}
	if($token==""){
	    return;
	}
	if($to_mobile==""){
		return;
	}
	if($msg==""){
		return;
	}
	
	$to_mobile = str_replace(array(' ', '-',')','('),'', $to_mobile);
	$to_mobile = str_replace('+1','', $to_mobile);
	$to_mobile = str_replace('+','', $to_mobile);
	$to_mobile = '+1'.$to_mobile;
				
	$twilio = new Client($sid, $token);
	try{
		$response = $twilio->messages->create($to_mobile, // to
							   array("body" => $msg, "from" => $settings['twilio_from'])); //12132635238		
		smw_twilio_log($to_mobile, $msg, $response, 1);
		return $response;
	}
	catch(Exception $ex){
		smw_twilio_log($to_mobile, $msg, $ex, 0);
		return;
	}
}
function smw_twilio_log($phone_number, $message, $response, $type){
	global $wpdb;
	$response = serialize($response);
	$table = $wpdb->prefix.'twilio_log';
	$data = array('phone_number' => $phone_number, 'message' => $message, 'response' => $response, 'type' => $type, 'date_added' => @gmdate('Y-m-d H:i:s'));
	$format = array('%s', '%s', '%s', '%d');
	$wpdb->insert($table,$data,$format);
	return $wpdb->insert_id;
}
#Twilio

/*CREATE ADMIN MENU*/

/*
 * Add our Custom Fields to variable products
 */
function smw_woo_add_custom_variation_fields( $loop, $variation_data, $variation ) {
		echo '<div class="options_group form-row form-row-full">';

	 	// Text Field
		woocommerce_wp_text_input(
			array(
				'id'          => '_variable_text_field[' . $variation->ID . ']',
				'label'       => __( 'Maximum Seats', 'woocommerce' ),
				'type'		  => 'number',
				'placeholder' => 'Maximum Seats',
				'desc_tip'    => true,
				'description' => __( "Maximum number of seats for the variation", "woocommerce" ),
				'value' => get_post_meta( $variation->ID, '_variable_text_field', true )
			)
	 	);

		// Add extra custom fields here as necessary...

		echo '</div>';
}
// Variations table
add_action( 'woocommerce_variation_options_pricing', 'smw_woo_add_custom_variation_fields', 10, 3 ); // After Price fields

/*
 * Save our variable product fields
 */
function smw_woo_add_custom_variation_fields_save( $post_id ){

 	// Text Field
 	$woocommerce_text_field = $_POST['_variable_text_field'][ $post_id ];
	update_post_meta( $post_id, '_variable_text_field', esc_attr( $woocommerce_text_field ) );

}
add_action( 'woocommerce_save_product_variation', 'smw_woo_add_custom_variation_fields_save', 10, 2 );

add_action('woocommerce_before_add_to_cart_button', 'custom_product_meta_end');
function custom_product_meta_end(){
	require plugin_dir_path( __FILE__ ) . 'smw-ticket-layout.php';
}

add_action( 'woocommerce_single_product_summary', 'custom_single_product_summary', 45 );
function custom_single_product_summary() {
	global $product;
	$product_id = $product->get_id();
	if(smw_woo_product_seat($product_id)){
		$winner_id = get_post_meta( $product_id, 'smw_woo_winner_id', true );
		if($winner_id>0){
			
		}
		else{
			if( current_user_can('editor') || current_user_can('admstrator') ) {
				$page_slug = 'winner';
				$page = get_page_by_path($page_slug);
				$Winnerpermalink = get_permalink($page->ID);
				?>
				<p class="mt-4 mb-0"><a target="_blank" href="<?php echo $Winnerpermalink;?>?pid=<?php echo $product_id;?>" class="btn btn-winner">Select Attendee</a></p>
				<?php
			}
		}
	}
}

function smw_woo_scripts(){
	wp_enqueue_style( 'smw_woo', plugins_url('asset/css/style.css',__FILE__ ), array(), SMW_VERSION );
	wp_enqueue_script( 'smw_woo_js', plugins_url('asset/js/main.js',__FILE__ ), array('jquery'), SMW_VERSION, true );
	
	//passing variables to the javascript file
	wp_localize_script('smw_woo_js', 'smwfa', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce('ajax_nonce')
	));
}
add_action( 'wp_enqueue_scripts', 'smw_woo_scripts' );

function smw_woo_ajax_function(){
	check_ajax_referer('ajax_nonce', 'nonce');
}

add_action( 'wp_ajax_nopriv_smw_woo_ajax_function', 'smw_woo_ajax_function' );
add_action( 'wp_ajax_smw_woo_ajax_function', 'smw_woo_ajax_function' );


function get_available_ticket_count($product_id){
	$product = wc_get_product($product_id);
	$current_products = $product->get_children();
	//echo '<pre>';
	$ticket_left = 0;
	foreach($current_products as $item_id){
		
		$variation_price = get_variation_price_by_id($product_id, $item_id);
		$ticket_left += $variation_price->ticket_left;
	}
	return $ticket_left;
}


function get_variation_price_by_id($product_id, $variation_id){
	$currency_symbol = get_woocommerce_currency_symbol();
	$product = new WC_Product_Variable($product_id);
	$variations = $product->get_available_variations();
	$var_data = array();
	foreach ($variations as $variation) {
		if($variation['variation_id'] == $variation_id){
			
			$display_regular_price = '<span class="currency">'. $currency_symbol .'</span>'.$variation['display_regular_price'];
			$display_price = '<span class="currency">'. $currency_symbol .'</span>'.$variation['display_price'];
			$variation_name = implode(' ', $variation['attributes']);
			$ticket_left = $variation['max_qty'];
		}
	}
 
	//Check if Regular price is equal with Sale price (Display price)
	if ($display_regular_price == $display_price){
		$display_price = false;
	}
 
	$priceArray = array(
		'display_regular_price' => $display_regular_price,
		'display_price' => $display_price,
		'variation_name' => $variation_name,
		'ticket_left' => $ticket_left,
	);
	$priceObject = (object)$priceArray;
	return $priceObject;
}

/**
 * Validate our custom text input field value
 */
function smw_woo_add_to_cart_validation($passed, $product_id, $quantity, $variation_id = null) {
    if (!empty($_POST['seat'])) {
        $booked_seats = $_POST['seat'];

        // Check if the count of booked seats matches the quantity
        if (count($booked_seats) == $quantity) {
            foreach ($booked_seats as $seat) {
                $availability = smw_woo_get_availability($variation_id, $seat);

                // Check seat availability
                if ($availability) {
                    $passed = false;
                    wc_add_notice(__('Selected seat "' . $seat . '" is not available for booking.', 'woocommerce'), 'error');
                    // No need for the 'break' statement here
                }
            }
        } else {
            // Quantity and booked seats count don't match
            $passed = false;
            wc_add_notice(__('Please try again later.', 'woocommerce'), 'error');
        }
    }

    if ($passed) {
        if (!WC()->cart->is_empty()) {
            // Loop through the cart items and remove items with specific conditions
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product_id_n = $cart_item['product_id'];
                $variation_id_n = $cart_item['variation_id'];

                // Check if the product has specific conditions for removal
                if (smw_woo_product_seat($product_id_n)) {
                    if ($product_id_n == $product_id) {
                        WC()->cart->remove_cart_item($cart_item_key);
                    }
                }
            }
        }

        // Set cookies for seat selection (not sure if this is necessary for debugging)
        setcookie('seat_selected', time() + 300, time() + 300, '/');
        setcookie("seat_selected_{$variation_id}", time() + 300, time() + 300, '/');
    }

    return $passed;
}

// Hook this validation function into WooCommerce
add_filter('woocommerce_add_to_cart_validation', 'smw_woo_add_to_cart_validation', 10, 4);

function smw_woo_check_cart_timing(){
	if(is_admin()){
		return;
	}
	
	if( ! WC()->cart->is_empty() ){
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

			// get the data of the cart item
			$product_id         = $cart_item['product_id'];
			$variation_id       = $cart_item['variation_id'];
			if(smw_woo_product_seat($cart_item['product_id'])){
				if(!isset($_COOKIE['seat_selected_'.$variation_id])) {
					WC()->cart->remove_cart_item($cart_item_key);
				}
			}
		}
	}
	
}
add_action('wp', 'smw_woo_check_cart_timing');


/**
 * Add custom cart item data
 */
function smw_woo_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
 if( isset( $_POST['seat'] ) ) {
	 $booked_seats = $_POST['seat'];
	 $cart_item_data['seat'] = implode(', ', $booked_seats );
	 
	 //$booking_details = array('seats' => $booked_seats, 'booking_time' => current_time( 'mysql' ));
	 foreach($booked_seats as $seat){
		 update_post_meta( $variation_id, 'temp_booked_seat_'.$seat, current_time( 'timestamp' ) );
	 }
 }
 return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'smw_woo_add_cart_item_data', 10, 3 );

/**
 * Display custom item data in the cart
 */
function smw_woo_get_item_data( $item_data, $cart_item_data ) {
	if( isset( $cart_item_data['seat'] ) ) {
		$item_data[] = array(
			'key' => __( 'Seat(s)', 'woocommerce' ),
			'value' => wc_clean( $cart_item_data['seat'] )
		);
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'smw_woo_get_item_data', 10, 2 );

/**
 * Add custom meta to order
 */
function smw_woo_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
	if( isset( $values['seat'] ) ) {
		$item->add_meta_data(
			__( 'Seat(s)', 'woocommerce' ),
			$values['seat'],
			true
		);
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'smw_woo_checkout_create_order_line_item', 10, 4 );

function smw_woo_get_availability_v2($item_id, $ticket_no){
	#PERMANENT CHECKING
	$perma_book = get_post_meta($item_id, 'perma_booked_seat_'.$ticket_no, true);
	if($perma_book!=""){
		return array("status" => true, "type" => "perma");
	}
	#TEMP CHECKING
	$temp_booking_time = get_post_meta($item_id, 'temp_booked_seat_'.$ticket_no, true);
	if($temp_booking_time!=""){
		$current_time = current_time( 'timestamp' );
		$time_diff = $current_time - $temp_booking_time;
		if($time_diff>300){
			return array("status" => false, "type" => "");
		}
		else{
			return array("status" => true, "type" => "temp");
		}
	}
}
function smw_woo_get_availability($item_id, $ticket_no){
	#PERMANENT CHECKING
	$perma_book = get_post_meta($item_id, 'perma_booked_seat_'.$ticket_no, true);
	if($perma_book!=""){
		return true;
	}
	#TEMP CHECKING
	$temp_booking_time = get_post_meta($item_id, 'temp_booked_seat_'.$ticket_no, true);
	if($temp_booking_time!=""){
		$current_time = current_time( 'timestamp' );
		$time_diff = $current_time - $temp_booking_time;
		if($time_diff>300){
			return false;
		}
		else{
			return true;
		}
	}
}

add_filter( 'woocommerce_cart_item_quantity', 'smw_woo_cart_item_quantity', 10, 3 );
function smw_woo_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ){
    if( is_cart() ){
		if(smw_woo_product_seat($cart_item['product_id'])){
        	$product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
		}
    }
    return $product_quantity;
}

function smw_woo_product_seat($product_id){
	$woo_seat_show = get_post_meta( $product_id, 'woo_seat_show', true );
	if($woo_seat_show==1){
		return true;
	}
}
function smw_woo_login_check(){
	$options = get_option( 'smw_woo_settings' );
	if($options['login_restrict']==1){
		if ( !is_user_logged_in() ) {
			return array('status' => true, 'text' => $options['login_button_text']);
		}
	}
	return array('status' => false);
}

add_action('template_redirect', 'smw_woo_show_notice');
function smw_woo_show_notice() {
    if (!WC()->cart->is_empty()) {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $variation_id = $cart_item['variation_id'];

            if (smw_woo_product_seat($cart_item['product_id'])) {
                add_action('woocommerce_before_cart', 'smw_woo_display_notice', 5);
                add_action('woocommerce_before_checkout_form', 'smw_woo_display_notice', 5);
                break; // Add notice only once
            }
        }
    }
}

function smw_woo_display_notice() {
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $variation_id = $cart_item['variation_id'];

        if (smw_woo_product_seat($cart_item['product_id'])) {
            $pname = html_entity_decode(get_the_title($product_id));
            echo '<ul class="woocommerce-error" role="alert"><li><span class="smw_woo_cc_notice" data-id="' . $variation_id . '" data-title="' . $pname . '">Please wait...</span></li></ul>';
        }
    }
}



add_action( 'woocommerce_after_shop_loop_item_title', 'smw_woo_show_stock_shop', 7 );
  
function smw_woo_show_stock_shop() {
    global $product;

		if ($product->is_type( 'variable' ))
	    {
			if(get_post_meta( $product->get_id(), 'woo_seat_show', true )==1){
				echo '<p class="list-ticket-avialble ">';
				$available_variations = $product->get_available_variations();
				foreach ($available_variations as $key => $value)
				{
					$variation_id = $value['variation_id'];
					$attribute_pa_colour = $value['attributes']['attribute_pa_seat'];
					$variation_obj = new WC_Product_variation($variation_id);
					$stock = $variation_obj->get_stock_quantity();
					$totalMaxseat = get_post_meta( $variation_id, '_variable_text_field', true );
					echo "" . $stock ." Seats Remaining<br>";
					if (is_shop() || is_product_category() || is_product_tag()) {
						if($stock > 0){           
							echo "<span class='register-now'>Register now</span>";
					}
				}
					

				}
				echo '</p>';
			}
		}

}

// === REMOVE DEFAULT PIECES WE WANT TO REORDER ===
add_action( 'init', function() {
    // Remove price from its default position
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

    // (Optional) remove rating if you don't want it
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
});


// === ADD CUSTOM FIELDS IN ORDER ===
// 1. Instructor
add_action( 'woocommerce_after_shop_loop_item_title', 'smw_loop_instructor', 5 );
function smw_loop_instructor() {
    global $product;

    $instructor = get_field( 'instructor_name', $product->get_id() );
    if ( ! empty( $instructor ) ) {
        echo '<p class="smw-loop-instructor">Instructor: ' . esc_html( $instructor ) . '</p>';
    }
}

// 2. Date
add_action( 'woocommerce_after_shop_loop_item_title', 'smw_loop_date', 6 );
function smw_loop_date() {
    global $product;

    $date = get_field( 'start_date', $product->get_id() );
    if ( ! empty( $date ) ) {
        echo '<p class="smw-loop-date">Date: ' . esc_html( $date ) . '</p>';
    }
}

// 4. Short Description (before price)
add_action( 'woocommerce_after_shop_loop_item_title', 'smw_loop_short_description', 8 );
function smw_loop_short_description() {
    global $post;

    if ( empty( $post ) ) {
        return;
    }

    $short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
    if ( ! $short_description ) {
        return;
    }

    echo '<p class="smw-loop-short-description">' . wp_kses_post( $short_description ) . '</p>';
}

// 5. Price (after short description)
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 9 );

#REFUND,Cancel,failed

// Hook into the order status failed action
add_action('woocommerce_order_status_failed', 'handle_order_failed', 10, 2);

function handle_order_failed($order_id) {
    $order = wc_get_order($order_id);

    // Ensure seats are removed when payment fails
    $items = $order->get_items();

    foreach ($items as $item) {
        $product_variation_id = $item->get_variation_id();
        $seats = $item->get_meta('Seat(s)', true);

        if (!empty($seats)) {
            $seats = explode(',', $seats);
            custom_delete_seats($product_variation_id, $seats);
        }
    }

    // Mark the order as seats removed
    update_post_meta($order_id, 'epp_is_seats_removed', 1);
}

// Function to delete seats
function custom_delete_seats($item_id, $seats) {
    global $wpdb;

    foreach ($seats as $seat) {
        $seat = trim($seat);
        $meta_key_perma = 'perma_booked_seat_' . $seat;
        $meta_key_temp = 'temp_booked_seat_' . $seat;

        // Remove single quotes around $item_id and use "=" for an exact match
        $sql_perma = $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
            $item_id,
            $meta_key_perma
        );

        $sql_temp = $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
            $item_id,
            $meta_key_temp
        );

        // Execute the SQL queries
        $wpdb->query($sql_perma);
        $wpdb->query($sql_temp);
    }
}

// Hook into the order refunded action
add_action('woocommerce_order_refunded', 'action_woocommerce_order_refunded', 10, 2);

function action_woocommerce_order_refunded($order_id, $refund_id) {
    if (isset($_POST['restock_refunded_items']) && $_POST['restock_refunded_items'] == 'true') {
        $order = wc_get_order($order_id);
        if (!smw_has_refunds_v2($order_id)) {
            $items = $order->get_items();
            foreach ($items as $item) {
                $product_name = $item->get_name();
                $product_id = $item->get_product_id();
                $product_variation_id = $item->get_variation_id();
                $seats = $item->get_meta('Seat(s)', true);
                $seats = explode(',', $seats);
                smw_delete_seat($product_variation_id, $seats);
                update_post_meta($order_id, 'epp_is_refunded', 1);
            }
        }
    }
}

// Hook into the order status cancelled action
add_action('woocommerce_order_status_cancelled', 'custom_handle_order_cancellation', 10, 2);

function custom_handle_order_cancellation($order_id, $order) {
    // Check if the order has been refunded
    if (!custom_has_refunds($order)) {
        $items = $order->get_items();

        foreach ($items as $item) {
            $product_variation_id = $item->get_variation_id();
            $seats = $item->get_meta('Seat(s)', true);

            if (!empty($seats)) {
                $seats = explode(',', $seats);
                custom_delete_seats($product_variation_id, $seats);
            }
        }

        // Mark the order as refunded
        update_post_meta($order_id, 'epp_is_refunded', 1);
    }
}

function custom_has_refunds($order) {
    // Check if there are any refunds associated with the order
    $refunds = $order->get_refunds();
    return count($refunds) > 0;
}

function smw_delete_seat($item_id, $seats) {
    global $wpdb;
    foreach ($seats as $seat) {
        $seat = trim($seat);
        $sql = "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s";
        $wpdb->query($wpdb->prepare($sql, $item_id, 'perma_booked_seat_' . $seat));
        $wpdb->query($wpdb->prepare($sql, $item_id, 'temp_booked_seat_' . $seat));
    }
}

function smw_update_seat($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();
    
    foreach ($items as $item) {
        $product_name = $item->get_name();
        $product_id = $item->get_product_id();
        $product_variation_id = $item->get_variation_id();
        $seats = $item->get_meta('Seat(s)', true);
        $booked_seats = explode(',', $seats);

        foreach ($booked_seats as $seat) {
            $seat = trim($seat);
            update_post_meta($product_variation_id, 'perma_booked_seat_' . $seat, current_time('timestamp'));
        }
    }
}

add_action('woocommerce_order_status_processing', 'smw_update_seat');
add_action('woocommerce_order_status_completed', 'smw_update_seat');
add_action('woocommerce_order_status_on-hold', 'smw_update_seat');

add_action( 'woocommerce_before_checkout_billing_form', 'smw_add_custom_checkout_field' );
function smw_add_custom_checkout_field( $checkout ) { 
   $current_user = wp_get_current_user();
   $saved_screen_name = $current_user->display_name;
   woocommerce_form_field( 'screen_name', array(        
      'type' => 'text',        
      'class' => array( 'form-row-wide' ),        
      'label' => 'Screen Name',        
      'placeholder' => 'Screen Name',        
      'required' => true,        
      'default' => $saved_screen_name,        
   ), $checkout->get_value( 'screen_name' ) ); 
}
add_action( 'woocommerce_checkout_process', 'smw_validate_new_checkout_field' );
  
function smw_validate_new_checkout_field() {    
   if ( ! $_POST['screen_name'] ) {
      wc_add_notice( 'Please enter your Screen Name', 'error' );
   }
}

##
add_action( 'woocommerce_checkout_update_order_meta', 'smw_save_new_checkout_field' );
  
function smw_save_new_checkout_field( $order_id ) { 
    if ( $_POST['screen_name'] ) update_post_meta( $order_id, '_screen_name', esc_attr( $_POST['screen_name'] ) );
}

add_action( 'woocommerce_checkout_update_user_meta', 'checkout_update_user_display_name', 10, 2 );
function checkout_update_user_display_name( $customer_id, $data ) {
    if ( isset($_POST['screen_name']) ) {
        $user_id = wp_update_user( array( 'ID' => $customer_id, 'display_name' => sanitize_text_field($_POST['screen_name']) ) );
    }
}
  
add_action( 'woocommerce_admin_order_data_after_billing_address', 'smw_show_new_checkout_field_order', 10, 1 );
   
function smw_show_new_checkout_field_order( $order ) {    
   $order_id = $order->get_id();
   if ( get_post_meta( $order_id, '_screen_name', true ) ) echo '<p><strong>Screen Name:</strong> ' . get_post_meta( $order_id, '_screen_name', true ) . '</p>';
}
 
add_action( 'woocommerce_email_after_order_table', 'smw_show_new_checkout_field_emails', 20, 4 );
  
function smw_show_new_checkout_field_emails( $order, $sent_to_admin, $plain_text, $email ) {
    if ( get_post_meta( $order->get_id(), '_screen_name', true ) ) echo '<p><strong>Screen Name:</strong> ' . get_post_meta( $order->get_id(), '_screen_name', true ) . '</p>';
}

add_filter( 'woocommerce_add_to_cart_redirect', 'smw_add_to_cart_redirect', 10, 1 );
function smw_add_to_cart_redirect( $url ) {

    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );

    // Only redirect the product IDs in the array to the checkout
    if ( smw_woo_product_seat($product_id) ) {
        // This is more correct to get the checkout URL
        $url = get_permalink( get_option('woocommerce_cart_page_id') );
    }
    return $url;
}
##

##SELECT WINNER##
add_action('init', 'smw_select_winner');
function smw_select_winner(){
	if(@$_GET['myaction']=="selectwinner"){
		
		$product_id = $_GET['pid'];
		$order_id = $_GET['oid'];
		$seat = @$_GET['seat'];
		$current_timestamp = current_time( 'timestamp', 0 );
		$winner_selected_at = date( 'Y-m-d H:i:s', $current_timestamp );
		//$add_2hr = $current_timestamp + (60*60*2);
		$winner_notification_at = date( 'Y-m-d H:i:s', $current_timestamp );
		update_post_meta($product_id, 'tww_winner_id', $order_id);
		update_post_meta($product_id, 'tww_winner_seat_id', $seat);
		update_post_meta($product_id, 'tww_winner_s_date', $winner_selected_at);
		update_post_meta($product_id, 'tww_winner_n_date', $winner_notification_at);
		update_post_meta($product_id, 'tww_winner_n_send', 0);
		update_post_meta($product_id, 'tww_winner_form_id', md5(mt_rand()));
		update_post_meta($product_id, 'tww_winner_form_submit', 0);
		$optionsrm = get_option( 'smw_woo_settings' );	
		if($optionsrm['winner_noto_others']==1){	
			smw_send_msg_winnerselected($product_id);	
		}
		exit;
	}
	else if(@$_GET['myaction']=="createevent"){
		$event_name = $_GET['evn'];
		$event_time = $_GET['evd'].':00';
		$timestampm = strtotime($event_time);	
		$formattedDatem = date("F j, Y, g:i A", $timestampm);
		$product_id = $_GET['pid'];
		$event_created = get_post_meta( $product_id, 'event_created', true );
		
		if($event_created!=1){
			
			$event = smw_create_event($event_name, $event_time);
			
			if($event['status']==1){
				$response = json_decode($event['response']);
				update_post_meta($product_id, 'event_data', $response);
				if($response->errors){
					update_post_meta($product_id, 'event_created', 0);
					
				}
				else{
					update_post_meta($product_id, 'event_created', 1);
					update_post_meta($product_id, '_event_name', $event_name);
					update_post_meta($product_id, '_event_ls_time', $event_time);
					#For Notification
					$current_timestamp = current_time( 'timestamp', 0 );
					$event_start_at = $event_time;
					$sub_10min = @strtotime($event_time) - (60*10);
					$event_notification_at = date( 'Y-m-d H:i:s', $sub_10min );
					$event_time_m = $formattedDatem.' ('.get_option('timezone_string').')';

					update_post_meta($product_id, 'tww_event_s_date', $event_start_at);
					update_post_meta($product_id, 'tww_event_n_date', $event_notification_at);
					update_post_meta($product_id, 'tww_event_n_send', 0);
				}
				echo $event['response'];
			}
		}
		exit;
	}
	else if(@$_GET['myaction']=="zoomnotification"){
		$event_url = $_GET['evn'];
		$event_password = $_GET['evp'];
		$event_time = $_GET['evd'].':00';
		$timestampm = strtotime($event_time);	
		$formattedDatem = date("F j, Y, g:i A", $timestampm);
		$product_id = $_GET['pid'];
		$event_created = get_post_meta( $product_id, 'event_created', true );
		
		if($event_created!=1){
			$event_time_m = $formattedDatem.' ('.get_option('timezone_string').')';
			$event = smw_send_zoom_notification($product_id, $event_url, $event_time_m, $event_password);
			update_post_meta($product_id, 'event_created', 1);
			update_post_meta($product_id, '_event_name', $event_url);
			update_post_meta($product_id, '_event_password', $event_password);
			#For Notification
			$current_timestamp = current_time( 'timestamp', 0 );
			$event_start_at = $event_time;
			$sub_10min = @strtotime($event_time) - (60*10);
			$event_notification_at = date( 'Y-m-d H:i:s', $sub_10min );
			update_post_meta($product_id, 'tww_event_s_date', $event_start_at);
			update_post_meta($product_id, 'tww_event_n_date', $event_notification_at);
			update_post_meta($product_id, 'tww_event_n_send', 0);
				
		}
		exit;
	}
	else if(@$_GET['myaction']=="sendcustomer"){
		$product_id = $_GET['pid'];
		smw_send_customer($product_id);
		exit;
	}
	else if(@$_GET['myaction']=="remove-webinar"){
		$rdata = array();
		if ( is_user_logged_in() ) {
			$product_id = $_GET['pid'];
			add_user_meta( get_current_user_id(), 'remove_webinar_video', $product_id);
			$rdata['status'] = 'success';
		}
		else{
			$rdata['status'] = 'error';
		}
		echo json_encode($rdata);
		exit;
	}
	else if(@$_GET['myaction']=="export_csv"){
		smw_get_csv($_GET['pid']);
		exit;
	}
}

/*WP Cron*/
//Schedule an action if it's not already scheduled
add_filter( 'cron_schedules', function ( $schedules ) {
   $schedules['smw_per_one_minute'] = array(
       'interval' => 600,
       'display' => __( 'Every 1 mins' )
   );
   return $schedules;
} );

add_filter('cron_schedules', function ($schedules) {
    $schedules['smw_per_twenty_minute'] = [
        'interval' => 1200, // 20 minutes
        'display' => __('Every 20 Minutes')
    ];
    return $schedules;
});

if ( ! wp_next_scheduled( 'smw_cron_hook' ) ) {
    wp_schedule_event( time(), 'smw_per_one_minute', 'smw_cron_hook' );
	//Every five minutes
}
///Hook into that action that'll fire every day
add_action( 'smw_cron_hook', 'smw_send_msg_winner' );

if (!wp_next_scheduled('smw_cron_hook1')) {
    wp_schedule_event(time(), 'smw_per_twenty_minute', 'smw_cron_hook1');
}

add_action('smw_cron_hook1', 'smw_send_np_notification');

add_action( 'init', 'tww_mycall' );
function tww_mycall(){
	if(@$_GET['m']==1){
		smw_send_msg_winner();
		smw_send_np_notification();
	}
	
}

//create your function, that runs on cron
function smw_send_msg_winner() {
	global $wpdb;
	$current_timestamp = current_time( 'timestamp', 0 );
	$results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_key ='tww_winner_n_send' AND meta_value = '0'" );
	if(!empty($results)){
		foreach($results as $result){
			$product_id = $result->post_id;
            $product_name = get_the_title($product_id);
			$winner_id = get_post_meta( $product_id, 'tww_winner_id', true );
			$winner_form_id = get_post_meta( $product_id, 'tww_winner_form_id', true );
			$winner_notification_at = get_post_meta( $product_id, 'tww_winner_n_date', true );
			$winner_notification_at = strtotime($winner_notification_at);
						
			$time_diff = $winner_notification_at - $current_timestamp;
			
			if($time_diff<=0){
				###
				$order_id = $winner_id;
				$order = wc_get_order( $order_id );
				$seats = "";
				//$product_name = "";
				foreach ( $order->get_items() as $item_id => $item ) {
					//$allmeta = $item->get_meta_data();
					//$product_name = $item['name'];
					foreach ( $item->get_meta_data() as $itemvariation ) {
						if ( ! is_array( ( $itemvariation->value ) ) ) {
							$key = wc_attribute_label( $itemvariation->key );
							if($key == "Seat(s)"){
								$seats = '' . wc_attribute_label( $itemvariation->value ) . '';
							}
						}
					}
				}
				
				$screen_name = get_post_meta( $order_id, '_screen_name', true );
				if($screen_name!=""){
					
				}
				else{
					$cuser = $order->get_user();
					if(!empty($cuser->display_name)){
						$screen_name = $cuser->display_name;
					}
					else{
						$screen_name = $order->get_billing_first_name();
					}
				}
				$first_name = $order->get_billing_first_name();
				$last_name = $order->get_billing_last_name();
				$email = $order->get_billing_email();
				$phone = $order->get_billing_phone();
				$user_id = $order->get_user_id();
				$form_link = get_permalink(91).'?formid='.$winner_form_id.'&pid='.$product_id.'&uid='.$user_id;
				//$event_name = get_the_title($order_id);
				smw_email_send($email, $product_name, $first_name, $last_name, $screen_name, $phone, $form_link);
				
				$settings = get_option( 'smw_woo_settings' );
				$smscontent = $settings['sms_wwinner_notification'];
				// Replace placeholders with actual values
				$smscontent = str_replace('%first_name%', $first_name, $smscontent);
				$smscontent = str_replace('%product_name%', $product_name, $smscontent);
				$smscontent = str_replace('%screen_name%', $screen_name, $smscontent);
				$twilio_msg = $smscontent;
				wp_twilio_sms($phone, $twilio_msg);
				update_post_meta($product_id, 'tww_winner_n_send', 1);
			}
			###
		}
	}
	
}

#SEND WINNER SELECTED MESSAGE TO ALL
function smw_send_msg_winnerselected($product_id) {
	global $wpdb;
	###
	#GET WINNER USER
	$winner_id = get_post_meta( $product_id, 'tww_winner_id', true );
	$worder = wc_get_order( $winner_id );
	$winneruser = $worder->get_user();
	$order_id = $winner_id;
	$screen_name = get_post_meta( $order_id, '_screen_name', true );
	if($screen_name!=""){
		
	}
	else{
		if(!empty($winneruser->display_name)){
			$screen_name = $winneruser->display_name;
		}
		else{
			$screen_name = $worder->get_billing_first_name();
		}
	}
	#GET ALL CUSTOMER
	$statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
	//print_r($statuses);
	$order_ids = $wpdb->get_col("
	   SELECT p.ID, pm.meta_value FROM {$wpdb->posts} AS p
	   INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
	   INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
	   INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
	   WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
	   AND pm.meta_key IN ( '_billing_email' )
	   AND im.meta_key IN ( '_product_id', '_variation_id' )
	   AND im.meta_value = $product_id
	");
	 
	// Print array on screen
	if(!empty($order_ids)){
		//$event_name = get_the_title($product_id);
		$product_name = get_the_title($product_id);
		foreach($order_ids as $order_id){
			if(!smw_has_refunds_v2($order_id)){
				$order = wc_get_order( $order_id );
				$first_name = $order->get_billing_first_name();
				$last_name = $order->get_billing_last_name();
				$email = $order->get_billing_email();
				$phone = $order->get_billing_phone();
				$user = $order->get_user();
				// Skip sending email to any order belonging to the winner user
				if (!$user || $user->ID != $winneruser->ID) {
					smw_email_winner_send($email, $product_name, $first_name, $last_name, $screen_name, $phone);
				}
				$settings = get_option( 'smw_woo_settings' );
				$smscontent = $settings['sms_winner_noti_others'];
				// Replace placeholders with actual values
				$smscontent = str_replace('%first_name%', $first_name, $smscontent);
				$smscontent = str_replace('%product_name%', $product_name, $smscontent);
				$smscontent = str_replace('%screen_name%', $screen_name, $smscontent);
				$twilio_msg = $smscontent;
        
				wp_twilio_sms($phone, $twilio_msg);
			}
		}
	}
}


function smw_email_send($email, $product_name, $first_name, $last_name, $screen_name, $phone, $form_link=""){
	global $woocommerce;
	$mailer = $woocommerce->mailer();
	$settings = get_option( 'smw_woo_settings' );
	$emailsubject = $settings['wwinner_notification_sub'];
	$emailcontent = $settings['wwinner_notification'];
	
	$emailsubject = str_replace('%first_name%', $first_name, $emailsubject);
    $emailsubject = str_replace('%product_name%', $product_name, $emailsubject);
    $emailsubject = str_replace('%last_name%', $last_name, $emailsubject);
	$emailsubject = str_replace('%screen_name%', $screen_name, $emailsubject);
    $emailsubject = str_replace('%phone%', $phone, $emailsubject);
	$emailcontent = str_replace('%first_name%', $first_name, $emailcontent);
    $emailcontent = str_replace('%product_name%', $product_name, $emailcontent);
    $emailcontent = str_replace('%last_name%', $last_name, $emailcontent);
	$emailcontent = str_replace('%screen_name%', $screen_name, $emailcontent);
    $emailcontent = str_replace('%phone%', $phone, $emailcontent);
	$email_heading = $emailsubject;
	ob_start();
	wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	echo wpautop($emailcontent);
	wc_get_template( 'emails/email-footer.php' );
	$message = ob_get_clean();
	$subject = $email_heading;
	$admin_foot_notes = "<p><small>Originally sent to {$first_name}</small></p>";
	return $mailer->send( $email, $subject, $message);
	//return true;
}

function smw_email_winner_send($email, $product_name, $first_name, $last_name, $screen_name, $phone, $form_link=""){
	global $woocommerce;
	$mailer = $woocommerce->mailer();
	$settings = get_option( 'smw_woo_settings' );
	$emailsubject = $settings['winner_noti_others_sub'];
	$emailcontent = $settings['winner_noti_others'];
	
	$emailsubject = str_replace('%first_name%', $first_name, $emailsubject);
    $emailsubject = str_replace('%product_name%', $product_name, $emailsubject);
    $emailsubject = str_replace('%last_name%', $last_name, $emailsubject);
	$emailsubject = str_replace('%screen_name%', $screen_name, $emailsubject);
    $emailsubject = str_replace('%phone%', $phone, $emailsubject);
	$emailcontent = str_replace('%first_name%', $first_name, $emailcontent);
    $emailcontent = str_replace('%product_name%', $product_name, $emailcontent);
    $emailcontent = str_replace('%last_name%', $last_name, $emailcontent);
	$emailcontent = str_replace('%screen_name%', $screen_name, $emailcontent);
    $emailcontent = str_replace('%phone%', $phone, $emailcontent);
	
	$email_heading = $emailsubject;
	ob_start();
	wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	echo wpautop($emailcontent);
	wc_get_template( 'emails/email-footer.php' );
	$message = ob_get_clean();
	$subject = $email_heading;
	$admin_foot_notes = "<p><small>Originally sent to {$first_name}</small></p>";
	return $mailer->send( $email, $subject, $message);
	//return true;
}

function smw_has_refunds( $order ) {
    return sizeof( $order->get_refunds() ) > 0 ? true : false;
}
function smw_has_refunds_v2($order_id){
	if(get_post_meta( $order_id, 'epp_is_refunded', true)==1){
		return true;
	}
	else{
		return false;
	}
}
function smw_extra_register_fields(){
	?>
<p class="form-row form-row-wide">
<label><input type="checkbox" name="smw_opt_notification" value="1" checked="checked"><span>Sign up to receive notifications when a new webinar is available.</span></label>
</p>
	<?php
}
add_action( 'woocommerce_register_form', 'smw_extra_register_fields' );
//
function smw_modify_user_table( $column ) {
    $column['optin'] = 'OPT-in';
    return $column;
}
add_filter( 'manage_users_columns', 'smw_modify_user_table' );

function smw_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'optin' :
            return get_the_author_meta( 'smw_opt_notification', $user_id )==1?"Yes":"No";
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'smw_modify_user_table_row', 10, 3 );
  
add_action( 'woocommerce_register_form_start', 'wooticket_add_name_woo_account_registration' );
  
function wooticket_add_name_woo_account_registration() {
    ?> 
    <p class="form-row form-row-wide">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    <p class="form-row form-row-wide">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
	<p class="form-row form-row-wide">
	<label for="reg_screen_name"><?php _e( 'Screen name', 'woocommerce' ); ?><span class="required">*</span></label>
	<input type="text" class="input-text" name="screen_name" id="reg_screen_name" value="<?php if ( ! empty( $_POST['screen_name'] ) ) esc_attr_e( $_POST['screen_name'] ); ?>" />
	</p>
	<p class="form-row form-row-wide">
        <label for="reg_phone_number"><?php _e( 'Phone number', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="phone_number" id="reg_phone_number" value="<?php if ( ! empty( $_POST['phone_number'] ) ) esc_attr_e( $_POST['phone_number'] ); ?>" />
    </p>
    <div class="clear"></div>
  
    <?php
}
  
add_filter( 'woocommerce_registration_errors', 'wooticket_validate_name_fields', 10, 3 );
  
function wooticket_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
	if ( isset( $_POST['screen_name'] ) && empty( $_POST['screen_name'] ) ) {
		   $errors->add( 'screen_name_error', __( '<strong>Error</strong>: Screen name is required!', 'woocommerce' ) );
	}
	if ( isset( $_POST['phone_number'] ) && empty( $_POST['phone_number'] ) ) {
        $errors->add( 'phone_number_error', __( '<strong>Error</strong>: Phone number is required!', 'woocommerce' ) );
    }
    return $errors;
}
  
// 3. SAVE FIELDS
  
add_action( 'woocommerce_created_customer', 'wooticket_save_name_fields' );
  
function wooticket_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
	if ( isset( $_POST['screen_name'] ) ) {
        update_user_meta( $customer_id, 'screen_name', sanitize_text_field( $_POST['screen_name'] ) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }
	if ( isset( $_POST['phone_number'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['phone_number'] ) );
    }
	if ( isset( $_POST['smw_opt_notification'] ) ) {
            update_user_meta( $customer_id, 'smw_opt_notification',$_POST['smw_opt_notification']);
      }
  
}
// Add custom checkbox field to WooCommerce checkout for logged-out customers
add_action('woocommerce_after_order_notes', 'add_custom_checkbox_to_checkout');
function add_custom_checkbox_to_checkout($checkout) {
    if (is_user_logged_in()) {
        return; // Do not display for logged-in customers
    }
    
    echo '<div id="custom_checkbox_field" class="form-row form-row-wide">';
    
    woocommerce_form_field('smw_opt_notification', array(
        'type'          => 'checkbox',
        'class'         => array('input-checkbox'),
        'label_class'   => array('woocommerce-form__label', 'woocommerce-form__label-for-checkbox', 'checkbox'),
        'input_class'   => array('woocommerce-form__input', 'woocommerce-form__input-checkbox'),
        'label'         => __('Sign up to receive notifications when a new webinar is available.', 'woocommerce'),
        'default'       => 1, // Checked by default
    ), $checkout->get_value('smw_opt_notification'));
    
    echo '</div>';
}

// Save custom checkbox field value to order meta for logged-out customers
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkbox_field_value');
function save_custom_checkbox_field_value($order_id) {
    if (is_user_logged_in()) {
        return; // Do not save for logged-in customers
    }
    
    if ($_POST['smw_opt_notification']) {
        update_post_meta($order_id, 'Notification Checkbox', __('1', 'woocommerce'));
    }
}

// Add phone number field to account edit form
add_action( 'woocommerce_edit_account_form', 'add_phone_number_to_edit_account_form');
function add_phone_number_to_edit_account_form() {
    $user_id = get_current_user_id();
	$opt_notification = get_user_meta( $user_id, 'smw_opt_notification', true );
    ?>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_screen_name"><?php _e( 'Screen Name', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_screen_name" id="account_screen_name" value="<?php echo esc_attr( get_user_meta( $user_id, 'screen_name', true ) ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_phone_number"><?php _e( 'Phone Number', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_phone_number" id="account_phone_number" value="<?php echo esc_attr( get_user_meta( $user_id, 'billing_phone', true ) ); ?>" />
        </p>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_smw_opt_notification">
                <input type="checkbox" name="account_smw_opt_notification" id="account_smw_opt_notification" value="1" <?php checked( $opt_notification, '1' ); ?>/>
                <?php _e( 'Sign up to receive notifications when a new webinar is available.', 'woocommerce' ); ?>
            </label>
        </p>
    <?php
}

// Save phone number field value to user meta
add_action( 'woocommerce_save_account_details', 'save_phone_number_field_value_on_account_page' );
function save_phone_number_field_value_on_account_page( $user_id ) {
    if ( isset( $_POST['account_phone_number'] ) ) {
        update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['account_phone_number'] ) );
    }
	if ( isset( $_POST['account_screen_name'] ) ) {
        update_user_meta( $user_id, 'screen_name', sanitize_text_field( $_POST['account_screen_name'] ) );
    }
	// opt-in checkbox: if present store '1', otherwise store '0' (so user can opt-out)
    if ( isset( $_POST['account_smw_opt_notification'] ) && $_POST['account_smw_opt_notification'] === '1' ) {
        update_user_meta( $user_id, 'smw_opt_notification', '1' );
    } else {
        // ensure we explicitly save 0 when unchecked
        update_user_meta( $user_id, 'smw_opt_notification', '0' );
    }
}
// Add Screen name field to admin profile page
add_action( 'show_user_profile', 'add_job_title_field' );
add_action( 'edit_user_profile', 'add_job_title_field' );
function add_job_title_field( $user ) {
    ?>
    <h3><?php _e( 'Screen Name', 'woocommerce' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="screen_name"><?php _e( 'Screen Name', 'woocommerce' ); ?></label></th>
            <td>
                <input type="text" name="screen_name" id="screen_name" value="<?php echo esc_attr( get_the_author_meta( 'screen_name', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description"><?php _e( 'Please enter your Screen Name.', 'woocommerce' ); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

// Save field data
add_action( 'personal_options_update', 'save_job_title_field' );
add_action( 'edit_user_profile_update', 'save_job_title_field' );
function save_job_title_field( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta( $user_id, 'screen_name', sanitize_text_field( $_POST['screen_name'] ) );
}

/** 
 * === Admin: allow editing user OPT-IN (smw_opt_notification) ===
 */

// Add the checkbox to the profile edit form
add_action('show_user_profile', 'smw_add_optin_field_admin');
add_action('edit_user_profile', 'smw_add_optin_field_admin');
function smw_add_optin_field_admin( $user ) {
    // Only admins or editors can change it
    if ( ! current_user_can('edit_users') ) {
        return;
    }

    $optin = get_user_meta( $user->ID, 'smw_opt_notification', true );
    ?>
    <h3><?php _e( 'Webinar Notifications', 'woocommerce' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="smw_opt_notification"><?php _e( 'Receive Webinar Notifications', 'woocommerce' ); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" name="smw_opt_notification" id="smw_opt_notification" value="1" <?php checked( $optin, '1' ); ?> />
                    <?php _e( 'Sign up to receive notifications when a new webinar is available.', 'woocommerce' ); ?>
                </label>
                <br><span class="description">Admins can manually opt in/out users here.</span>
            </td>
        </tr>
    </table>
    <?php
}

// Save the field when profile is updated
add_action('personal_options_update', 'smw_save_optin_field_admin');
add_action('edit_user_profile_update', 'smw_save_optin_field_admin');
function smw_save_optin_field_admin( $user_id ) {
    if ( ! current_user_can('edit_user', $user_id) ) {
        return false;
    }

    // If checked → 1, else 0
    if ( isset($_POST['smw_opt_notification']) && $_POST['smw_opt_notification'] == '1' ) {
        update_user_meta( $user_id, 'smw_opt_notification', '1' );
    } else {
        update_user_meta( $user_id, 'smw_opt_notification', '0' );
    }
}

//require file
require_once('smw-admin-settings.php');
require_once(plugin_dir_path(__FILE__) . 'smw-product-notification.php');
require_once(plugin_dir_path(__FILE__) . 'smw-order-csv.php');
require_once(plugin_dir_path(__FILE__) . 'smw-myaccount-videos.php');
require_once(plugin_dir_path(__FILE__) . 'smw-admin-metabox.php');


// Add new tab
$taboptions = get_option( 'smw_woo_settings' );	
if($taboptions['producttab']==1){
add_filter( 'woocommerce_product_tabs', 'smw_woo_new_product_tab' );
function smw_woo_new_product_tab( $tabs ) { 
global $post;
if(get_post_meta( $post->ID, 'woo_seat_show', true )==1){	
  $tabs['new_tab'] = array(
    'title' 	=> __( 'Participants', 'woocommerce' ),
    'priority' 	=> 50,
    'callback' 	=> 'smw_woo_new_product_tab_content'
  );

  	return $tabs;
} else{
	return $tabs;
}
}


function smw_woo_new_product_tab_content() {
	global $post;
	if(get_post_meta( $post->ID, 'woo_seat_show', true )==1){
		echo '<section class="tab-participant" data-id="'.$post->ID.'"></section>';
	}
}


add_action('init', 'smw_get_participant');
function smw_get_participant(){
	if(isset($_GET['participant'])){
		require plugin_dir_path( __FILE__ ) . 'smw-ticket-participant.php';
		exit;
	}
}
}
add_action('init', 'smw_get_availability_check');
function smw_get_availability_check(){
	if(isset($_GET['availabilitycheck'])){
		$variation_id = $_GET['variationid'];
		$seat = $_GET['seat'];
		$availability = smw_woo_get_availability($variation_id, $seat);
		if($availability){
			 echo 'Selected seat "'.$seat.'" is not available for booking.';
		}
		else{
			echo 1;
		}
		exit;
	}
}

function smw_customer_purchased($customer_id, $product_id){
    global $wpdb;

    $statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
    //print_r($statuses);
    $order_ids = $wpdb->get_col("
       SELECT p.ID, pm.meta_value FROM {$wpdb->posts} AS p
       INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
       INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
       INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
       WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
       AND pm.meta_key IN ( '_billing_email' )
       AND im.meta_key IN ( '_product_id', '_variation_id' )
       AND im.meta_value = $product_id
    ");
    if(!empty($order_ids)){
    	foreach($order_ids as $order_id){
    		if(!smw_has_refunds_v2($order_id)){
    			$order = wc_get_order( $order_id );
    			if($order->get_user_id()==$customer_id){
    			    return true;
    			    break;
    			}
    		}
    	}
    }
}

//shortcode
function winner_page_shortcode( $atts ) {
    ob_start();
	$woocommerce;
    $product_id = isset( $_GET['pid'] ) ? intval( $_GET['pid'] ) : 0;
    $product = wc_get_product( $product_id );
    $variations = $product ? $product->get_available_variations() : array();

    if ( ! empty( $product_id ) ) {
        $winner_id = get_post_meta( $product_id, 'tww_winner_id', true );
        $winner_seat_id = get_post_meta( $product_id, 'tww_winner_seat_id', true );
        $statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
        $order_ids = $GLOBALS['wpdb']->get_col( "
            SELECT p.ID, pm.meta_value FROM {$GLOBALS['wpdb']->posts} AS p
            INNER JOIN {$GLOBALS['wpdb']->postmeta} AS pm ON p.ID = pm.post_id
            INNER JOIN {$GLOBALS['wpdb']->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
            INNER JOIN {$GLOBALS['wpdb']->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
            WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
            AND pm.meta_key IN ( '_billing_email' )
            AND im.meta_key IN ( '_product_id', '_variation_id' )
            AND im.meta_value = $product_id
        " );
    ?>
        <div class="enterChance p-90">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="headingFnt winnerheading text-center">
                            <h3><?php echo get_the_title( $product_id ); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container pt-4 pb-5">
            <?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) { ?>
            <div class="row winnerwrap_row">
                <div class="col-lg-6 attendeewiner">
                    <h4 class="text-center mb-2">Attendee </h4>
                    <form method="post" id="tw_mywinnerform">
                        <table class="table table-bordered table-striped table-hover" id="mytable">
                            <thead>
                                <tr>
                                    <?php if ( empty( $variations ) ) { ?>
                                        <th width="56">&nbsp;</th>
                                    <?php } else { ?>
                                        <th class="sr-only">&nbsp;</th>
                                    <?php } ?>
                                    <th width="150">Seat Number</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cnn = 0;
                                foreach ( $order_ids as $order_id ) {
                                    $order = wc_get_order( $order_id );
                                    if ( ! smw_has_refunds_v2( $order_id ) ) {
                                        $screen_name = get_post_meta( $order_id, '_screen_name', true );
										$billing_first_name = $order->get_billing_first_name();
										$billing_last_name = $order->get_billing_last_name();
										$full_name = $billing_first_name . ' ' . $billing_last_name;
                                        if ( $screen_name == "" ) {
                                            $cuser = $order->get_user();
                                            if ( ! empty( $cuser->display_name ) ) {
												}
						else{
							$screen_name = $order->get_billing_first_name();
						}
					}
					
					 $seats = "";
					 foreach ( $order->get_items() as $item_id => $item ) {
						 //$allmeta = $item->get_meta_data();
						 if( $item->get_product_id() == $product_id){
							 foreach ( $item->get_meta_data() as $itemvariation ) {
								if ( ! is_array( ( $itemvariation->value ) ) ) {
									$key = wc_attribute_label( $itemvariation->key );
									if($key == "Seat(s)"){
										$seats = '' . wc_attribute_label( $itemvariation->value ) . '';
									}
								}
							}
						 }
					 }
					 $seat_array = explode(', ', $seats);
					 
					 foreach($seat_array as $seat){
						 $cnn++;
						 ?>
                         <tr>
                         <?php if(empty($variations)){ ?>
                         <td class="text-center">
                         
                         <?php if($winner_id>0){ if($winner_seat_id==$seat){ echo '<i class="dashicons dashicons-awards"></i>';}}else{?><input type="radio" name="rbtnseats" value="<?php echo $seat;?>" data-id="<?php echo $order_id;?>" class="tw_rbtn" <?php if($cnn==1){echo 'required="required"';}?> /><?php } ?></td>
                         <?php }else{echo '<td class="sr-only"></td>';} ?>
                         <td class="pts"><?php echo $seat;?></td>
                         <td id="wsnm_<?php echo $seat;?>"><?php echo $full_name;?><?php //echo $screen_name;?></td>
                         </tr>
                         <?php
					 }
					}
				}
				?>
                </tbody>
                </table>
                <?php if(empty($variations)){?>
                <div class="text-center">
                <?php if($winner_id>0){ }else{ ?>
                <input type="hidden" name="orderid" id="tw_orderid" value="0" />
                <input type="hidden" name="pid" id="tw_pid" value="<?php echo $product_id;?>" />
                <button type="submit" class="btn btn-winner" id="btn_select_winnerf"><i class="dashicons dashicons-awards"></i> <span>Select a Winner</span></button>
                <?php } ?>
                </div>
                <?php }else{ ?>
                <div class="alert alert-danger text-center"><p>Seats are still available in this webinar.</p> <p><a href="<?php echo get_permalink($product_id);?>" class="btn btn-danger">Go back</a></p></div>
				<?php }?>
                </form>
                </div>
                <div class="col-lg-6 generatornum">
                <?php if(empty($variations)){?>
                <h4 class="text-left mb-2">Generate Number</h4>
                <iframe src="https://www.random.org/widgets/integers/iframe.php?title=True+Random+Number+Generator&amp;buttontxt=Generate&amp;width=100%&amp;height=250&amp;border=on&amp;bgcolor=%23FFFFFF&amp;txtcolor=%23777777&amp;altbgcolor=%23e42c2c&amp;alttxtcolor=%23FFFFFF&amp;defaultmin=1&amp;defaultmax=&amp;fixed=off" frameborder="0" width="100%" height="250" style="min-height:250px;" scrolling="no" longdesc="https://www.random.org/integers/">
The numbers generated by this widget come from RANDOM.ORGs true random number generator.
</iframe>
<?php } ?>
</div>
</div>
</div>
<?php
			}
		}
$output = ob_get_clean();
return $output;			
 } 
add_shortcode( 'smw_shortcode', 'winner_page_shortcode' );

function my_custom_footer_message() {
$options = get_option( 'smw_woo_settings' );
if($options['bootstrap_show']==1){ 
if ( is_page( 'winner' ) ) {
wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' );
wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array( 'jquery' ), '', true );
}
 } 
if ( is_page( 'winner' ) ) {	
?>
<!-- Modal -->
<div class="modal" id="sbWinner" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sbWinnerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h3>Congratulations to <em id="wsnm"></em></h3>
        <p>You have been selected for the door prize!<?php /*?><?php echo get_the_title(@$_GET['pid']);?><?php $_GET['seat'];*/?></p>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<script>
function sortTable(){
  var rows = jQuery('#mytable tbody  tr').get();

  rows.sort(function(a, b) {

  var A = jQuery(a).children('td').eq(1).text().toUpperCase();
  var B = jQuery(b).children('td').eq(1).text().toUpperCase();
  A = parseInt(A);
  B = parseInt(B);
  if(A < B) {
    return -1;
  }

  if(A > B) {
    return 1;
  }

  return 0;

  });

  jQuery.each(rows, function(index, row) {
    jQuery('#mytable').children('tbody').append(row);
  });
}
jQuery(document).ready(function(e) {
    sortTable();
});
</script>
<?php
if (is_account_page()) {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('label[for="account_display_name"]').parent('p').addClass('custom-displayname');
            });
        </script>
<style>
.custom-displayname	{
	display: none;
}
</style>
  <?php
}

}
add_action( 'wp_footer', 'my_custom_footer_message' );

// Hook to modify the order actions for failed orders
add_filter( 'woocommerce_my_account_my_orders_actions', 'custom_my_account_failed_order_actions', 10, 2 );

function custom_my_account_failed_order_actions( $actions, $order ) {
    // Check if the order status is 'failed'
    if ( 'failed' === $order->get_status() ) {
        // Remove all actions except the "View" button
        foreach ( $actions as $key => $action ) {
            if ( 'view' !== $key ) {
                unset( $actions[$key] );
            }
        }
    }

    return $actions;
}
// Register the dashboard widget
add_action('wp_dashboard_setup', 'smw_register_notification_widget');
function smw_register_notification_widget() {
    wp_add_dashboard_widget(
        'smw_notification_status_widget',
        '📣 Product Notification Status',
        'smw_display_notification_widget'
    );
}

// Display the widget content
function smw_display_notification_widget() {
    $post_id = get_option('smw_np_notification_auto_start_post_id');

    if (!$post_id) {
        echo '<p style="color:green;">✅ No active notification process.</p>';
        return;
    }

    $post = get_post($post_id);
    if (!$post) {
        echo '<p style="color:orange;">⚠️ Notification post not found. Clean up may be needed.</p>';
        return;
    }

    // Get all customer users
    $customers = get_users(array('role' => 'customer'));
    $notified_count = 0;

    foreach ($customers as $customer) {
        $notified_products = get_user_meta($customer->ID, 'smw_notified_products', true);
        if (is_array($notified_products) && in_array($post_id, $notified_products)) {
            $notified_count++;
        }
    }

    echo '<ul>';
    echo '<li><strong>🔗 Product:</strong> <a href="' . esc_url(get_edit_post_link($post_id)) . '">' . esc_html($post->post_title) . '</a></li>';
    echo '<li><strong>👥 Users Notified:</strong> ' . number_format_i18n($notified_count) . '</li>';
    echo '</ul>';
}

