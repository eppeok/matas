<?php
/**
 * Plugin Name: SMW WooCommerce Reload on Coupon
 * Description: Reloads the Cart & Checkout page after applying or removing a coupon (instead of just AJAX updating).
 * Version: 1.0.0
 * Author: Mimo dev
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SMW_WC_Reload_On_Coupon {

    public function __construct() {
        // Print our JS in the footer on frontend
        add_action( 'wp_footer', [ $this, 'output_reload_script' ], 99 );
    }

    /**
     * Outputs JS to force page reload after coupon actions.
     */
    public function output_reload_script() {

        // WooCommerce may not be active
        if ( ! function_exists( 'is_cart' ) || ! function_exists( 'is_checkout' ) ) {
            return;
        }

        // Only on Cart & Checkout pages
        if ( ! is_cart() && ! is_checkout() ) {
            return;
        }
        ?>
        <script type="text/javascript">
        (function($){
            $(function(){

                // CART page: events when coupon is applied/removed
                $(document.body).on('applied_coupon removed_coupon', function(){
                    // AJAX finished, now do full reload
                    window.location.reload();
                });

                // CHECKOUT page: events when coupon is applied/removed
                $(document.body).on('applied_coupon_in_checkout removed_coupon_in_checkout', function(){
                    // AJAX finished, now do full reload
                    window.location.reload();
                });

                // Extra safety: if remove link is clicked, reload shortly after
                $(document).on('click', '.woocommerce-remove-coupon', function(){
                    setTimeout(function(){
                        window.location.reload();
                    }, 400);
                });
            });
        })(jQuery);
        </script>
        <?php
    }
}

new SMW_WC_Reload_On_Coupon();