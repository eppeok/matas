<?php
#ZOOM
function smw_send_zoom_notification($product_id, $event_url, $event_time, $event_password=""){
	global $wpdb;
	$product_name = get_the_title($product_id);
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
	$done = 0;
	$scounter = 0;
	foreach($order_ids as $order_id){
		if(!smw_has_refunds_v2($order_id)){
		$sent = get_post_meta( $order_id, '_sent_zoom_link', true );
		$sent_zoom_link = get_post_meta($order_id, '_sent_zoom_link_' . $product_id, true);
		if($sent!=1 || $sent_zoom_link!=1){
			$scounter++;
			$done = 1;
			$order = wc_get_order( $order_id );
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
			
			smw_zoom_email_send($email, $first_name, $last_name, $product_name, $event_url, $event_time, $event_password);
			####
			$settings = get_option( 'smw_woo_settings' );
				$smscontent = $settings['sms_zoom_notification'];
				// Replace placeholders with actual values
				$smscontent = str_replace('%first_name%', $first_name, $smscontent);
				$smscontent = str_replace('%product_name%', $product_name, $smscontent);
				$smscontent = str_replace('%event_time%', $event_time, $smscontent);
				$smscontent = str_replace('%event_url%', $event_url, $smscontent);
				$twilio_msg = $smscontent;
			if($event_password!=""){
				$twilio_msg .= "(Passcode: {$event_password})";
			}
			wp_twilio_sms($phone, $twilio_msg);
			//update_post_meta($order_id, '_sent_zoom_link', 1);
			update_post_meta($order_id, '_sent_zoom_link_' . $product_id, 1);
			#sleep(1);
		}
	}
	}
	echo $done;
}

function smw_zoom_email_send($email, $first_name, $last_name, $product_name, $event_url, $event_time, $event_password=""){
	global $woocommerce;
	$mailer = $woocommerce->mailer();
	$settings = get_option( 'smw_woo_settings' );
	$emailsubject = $settings['ezoom_notification_sub'];
	$emailcontent = $settings['ezoom_notification'];
	// Replace placeholders with actual values
	$emailsubject = str_replace('%first_name%', $first_name, $emailsubject);
	$emailsubject = str_replace('%last_name%', $last_name, $emailsubject);
    $emailsubject = str_replace('%product_name%', $product_name, $emailsubject);
    $emailsubject = str_replace('%event_url%', $event_url, $emailsubject);
	$emailsubject = str_replace('%event_time%', $event_time, $emailsubject);
    $emailcontent = str_replace('%first_name%', $first_name, $emailcontent);
    $emailcontent = str_replace('%last_name%', $last_name, $emailcontent);
    $emailcontent = str_replace('%product_name%', $product_name, $emailcontent);
    $emailcontent = str_replace('%event_url%', $event_url, $emailcontent);
	$emailcontent = str_replace('%event_time%', $event_time, $emailcontent);
	
	$email_heading = $emailsubject;
	ob_start();
	wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	echo wpautop($emailcontent);
	if($event_password!=""){
		echo "<p><strong>Event Passcode:</strong> {$event_password}</p>";
	}
	wc_get_template( 'emails/email-footer.php' );
	#echo '<p style="text-align:center;margin-top:0;">If you would prefer not receiving our emails Please <a href="'.home_url('/?unsub=email&email='.urlencode($email)).'">unsubscribe</a></p>';
	$message = ob_get_clean();
	$subject = $email_heading;
	return $mailer->send( $email, $subject, $message);
}

add_action( 'post_submitbox_misc_actions', 'add_notfication_sender_button', 10, 2 );
function add_notfication_sender_button( $post_obj ) {
	//post or whatever custom post type you'd like to add the text

		if ( 'product' === $post_obj->post_type && 'publish' === $post_obj->post_status ) {
			$post_id = $post_obj->ID;
			$notification_sent = get_post_meta($post_id, 'smw_np_notification_sent', true);
			if($notification_sent==1){
			}
			else{
				$np_post_id = get_option( 'smw_np_notification_auto_start_post_id' );
				if($np_post_id>0){
					?>
	                <div class="misc-pub-section"><p>Notify your customer about the arrival of new webinar/event.</p>
	                <p>Please wait a process is already runing.</p></div>
	                <?php
				}
				else{
				?>
	            <div class="misc-pub-section">
				<p>Notify your customer about the arrival of new webinar/event.</p>
	            <p ><label><input type="checkbox" name="smw_np_notification_sent" value="1" />Send Notification</label></p></div>
	            <?php
				}
			}
		}

}

add_action('save_post', 'save_post_send_notfication');

function save_post_send_notfication($post_id){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
	
    if (isset($_POST['smw_np_notification_sent'])) {
		add_option('smw_np_notification_auto_start_post_id', $post_id);
        update_post_meta($post_id, 'smw_np_notification_sent', 1);
    }
}

//add_action( 'smw_cron_hook', 'smw_send_np_notification' );
function smw_send_np_notification() {
    $np_post_id = get_option('smw_np_notification_auto_start_post_id');

    if ($np_post_id > 0) {
        $product_name = get_the_title($np_post_id);
        $url = get_the_permalink($np_post_id);

        $customers = get_users(array('role' => 'customer'));

        foreach ($customers as $customer) {
            $user_id = $customer->ID;
            $email = $customer->user_email;
            $first_name = get_user_meta($user_id, 'first_name', true);
            $phone = get_user_meta($user_id, 'billing_phone', true);
            $unsubscribe_notification_email = get_user_meta($user_id, 'unsubscribe_notification_email', true);

            // Check if this user has already been notified about this product
            $notified_products = get_user_meta($user_id, 'smw_notified_products', true);
            if (!is_array($notified_products)) {
                $notified_products = array();
            }

            if (in_array($np_post_id, $notified_products)) {
                continue; // Skip this user, they’ve already been notified
            }

            if ($unsubscribe_notification_email != 1) {
                smw_npo_email_send($email, $first_name, $product_name, $url);

                // Send SMS
                $settings = get_option('smw_woo_settings');
                $smscontent = $settings['sms_new_product_notification'];
                $smscontent = str_replace('%first_name%', $first_name, $smscontent);
                $smscontent = str_replace('%product_name%', $product_name, $smscontent);
                $smscontent = str_replace('%url%', $url, $smscontent);

                wp_twilio_sms($phone, $smscontent);
                sleep(1);
            }

            // Mark this product as notified for the user
            $notified_products[] = $np_post_id;
            update_user_meta($user_id, 'smw_notified_products', $notified_products);
        }

        delete_option('smw_np_notification_auto_start_post_id');
    }
}

function smw_npo_email_send($email, $first_name, $product_name, $url){
	global $woocommerce;
	$mailer = $woocommerce->mailer();
	$settings = get_option( 'smw_woo_settings' );
	$emailsubject = $settings['new_product_notification_sub'];
	$emailcontent = $settings['new_product_notification'];
	// Replace placeholders with actual values
	$emailsubject = str_replace('%first_name%', $first_name, $emailsubject);
    $emailsubject = str_replace('%product_name%', $product_name, $emailsubject);
    $emailsubject = str_replace('%url%', $url, $emailsubject);
    $emailcontent = str_replace('%first_name%', $first_name, $emailcontent);
    $emailcontent = str_replace('%product_name%', $product_name, $emailcontent);
    $emailcontent = str_replace('%url%', $url, $emailcontent);
	
	$email_heading = $emailsubject;
	ob_start();
	wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	echo wpautop($emailcontent);
	wc_get_template( 'emails/email-footer.php' );
	echo '<p style="text-align:center;margin-top:0;">If you would prefer not receiving our emails Please <a href="'.home_url('/?unsub=email&email='.urlencode($email)).'">unsubscribe</a></p>';
	$message = ob_get_clean();
	$subject = $email_heading;
	return $mailer->send( $email, $subject, $message);
	//return true;
}

//subscribe
add_action('init', 'mt_unsubscribe');
function mt_unsubscribe(){
    if(@$_GET['unsub']=="email"){
        $email = $_GET['email'];
        $user = get_user_by( 'email', $email );
        $user_id = $user->ID;
        update_user_meta( $user_id, 'unsubscribe_notification_email', 1);
        if ( wp_redirect( home_url('/?alertmsg='.urlencode('You are successfully unsubscribed.')) ) ) {
            exit;
        }
    }
}