<?php
function smw_custom_endpoints() {
    add_rewrite_endpoint( 'webinars-videos', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'smw_custom_endpoints' );

// so you can use is_wc_endpoint_url( 'webinars' )
add_filter( 'woocommerce_get_query_vars', 'smw_custom_woocommerce_query_vars', 0 );

function smw_custom_woocommerce_query_vars( $vars ) {
	$vars['webinars'] = 'webinars';
	return $vars;
}

function smw_custom_flush_rewrite_rules() {
    flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'smw_custom_flush_rewrite_rules' );

function smw_custom_my_account_menu_items( $items ) {

	$new_item = array( 'webinars' => __( 'Webinar Videos', 'woocommerce' ) );
	
    // add item in 2nd place
	$items = array_slice($items, 0, 2, TRUE) + $new_item + array_slice($items, 2, NULL, TRUE);
	
	//$items['orders'] = 'Booking History';
    return $items;

}

add_filter( 'woocommerce_account_menu_items', 'smw_custom_my_account_menu_items' );

function smw_custom_endpoint_content() {
    wc_get_template( 'smw-account-webinars.php', array(), '', SMW_PLUGIN_DIRPATH);
	
}

add_action( 'woocommerce_account_webinars_endpoint', 'smw_custom_endpoint_content' );