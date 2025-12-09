<?php
global $product;
$product_id = $product->get_id();
if ( $product->is_purchasable() ) { 
	if(smw_woo_product_seat($product_id)){
?>   
		<?php 
		$login_check = smw_woo_login_check();
		if($login_check['status']){
			?>
            <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="button alt"><?php echo $login_check['text'];?></a>
            <?php
		}
		else{
		?>
		<div class="smw-ticket-wrapper">
			<button id="smw-open-seat-modal" class="button alt">Purchase Seat</button>
		</div>	

		<div id="smw-seat-modal" class="smw-seat-modal" aria-hidden="true" style="display:none;">
    <div class="overlay" style="position:absolute;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.6)"></div>

    <div class="dialog smw-seat-dialog" role="dialog" aria-modal="true" aria-labelledby="smw-seat-title">
        <!-- TOP BAR -->
        <div class="smw-dialog-header">
            <div class="smw-dialog-quote">
                It's better to have a gun and not need it than to need a gun and not have it
            </div>
            <div class="smw-dialog-actions">
                <button type="button" class="smw-header-btn smw-back-btn">
                    &larr; Back
                </button>
                <a href="<?php echo wc_get_cart_url(); ?>" class="smw-header-btn smw-go-cart-btn">
                    Go To Cart
                </a>
            </div>

            <!-- X CLOSE BUTTON (keeps old id for JS) -->
            <button class="smw-close-x" id="smw-close-modal" aria-label="Close">
                &times;
            </button>
        </div>

        <!-- BODY -->
        <div class="smw-dialog-body">
            <!-- LEFT COLUMN: description -->
            <div class="smw-col smw-col-left">
                <div class="smw-left-inner">
                    <?php
                    $product = wc_get_product($product_id);
                    $image_id = $product->get_image_id();
                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                    ?>
                    <div class="smw-left-image">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">
                    </div>

                    <div class="smw-left-tabs">
                        <button class="active">Description</button>
                    </div>

                    <div class="smw-left-desc">
                        <p><?php echo wp_kses_post($product->get_short_description()); ?></p>
                        <p><strong>Instructor:</strong> <?php the_field('instructor_name'); ?></p>
                        <p><strong>Date:</strong> <?php the_field('start_date'); ?></p>
                    </div>
                </div>
            </div>

            <!-- MIDDLE COLUMN: seats (your existing PHP) -->
            <div class="smw-col smw-col-middle">
                <h5 id="smw-seat-title" class="smw-seat-title">SCREEN / INSTRUCTOR</h5>

                <div class="smw-seat-content">
                    <div class="ticket-dashboard">
                        <div class="ticket-wrapper">
                            <?php
                            $product      = wc_get_product($product_id);
                            $current_products = $product->get_children();

                            foreach ( $current_products as $item_id ) {

                                $variation_price = get_variation_price_by_id($product_id, $item_id);
                                $totalMaxseat    = get_post_meta( $item_id, '_variable_text_field', true );
                                $options         = get_option( 'smw_woo_settings' );

                                echo '<div class="rseat-container">';
                                echo '<div class="clear"></div>';
                                echo '<ul class="my-tickets ticket-id-' . esc_attr( $item_id ) . '">';

                                $max_ticket = get_post_meta( $item_id, '_variable_text_field', true );
                                for ( $i = 1; $i <= $max_ticket; $i++ ) {
                                    $availability        = smw_woo_get_availability_v2( $item_id, $i );
                                    $availability_status = isset( $availability['status'] ) ? $availability['status'] : '';
                                    $availability_type   = isset( $availability['type'] ) ? $availability['type'] : '';
                                    ?>
                                    <li>
                                        <label>
                                            <input
                                                <?php if ( $availability_status ) { echo 'disabled="disabled"'; } ?>
                                                data-value="<?php echo esc_attr( $variation_price->variation_name ); ?>"
                                                data-vid="<?php echo esc_attr( $item_id ); ?>"
                                                class="ticket-box rbtn-tt-<?php echo esc_attr( $availability_type ); ?>"
                                                type="checkbox"
                                                name="seat[]"
                                                value="<?php echo esc_attr( $i ); ?>"
                                            >
                                            <span><?php echo esc_html( $i ); ?></span>
                                        </label>
                                    </li>
                                    <?php
                                }
                                echo '</ul></div>';
                                echo '<div class="clear"></div>';
                            }
                            ?>
                            <div class="availabilitycheck-msg"></div>
                        </div>

                        <div class="proceed-cart-info">
                            <ul>
								<li><span class="tbtn tbtn-available"></span> Available</li>
								<li><span class="tbtn tbtn-selected"></span> Selected</li>
                                <li><span class="tbtn tbtn-sold"></span> Unavailable</li>
                            </ul>
                        </div>

                        <div class="proceed-cart">
                            <div class="prc-type"></div>
                            <div class="prc-qty"></div>
                            <div class="prc-btn">
                                <button type="button" class="tbtn-button button alt">Purchase</button>
                            </div>
                        </div>
                    </div>

                    <?php
                    if ( isset( $options['randomGenerator_restrict'] ) && $options['randomGenerator_restrict'] == 1 ) {
                        echo '<div class="randomGenerator" id="randomGenerator">';
                        echo '<button id="generate-random">Random Seat Generator</button></div>';
                        ?>
                        <style>
                            .prc-qty { display:none; }
                        </style>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- RIGHT COLUMN: summary -->
            <div class="smw-col smw-col-right">
                <div class="smw-right-inner">
                    <h3 class="smw-right-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                    <p class="smw-right-sub">
                        <?php echo wp_kses_post($product->get_short_description()); ?>
                    </p>

                    <div class="smw-right-box">
                        <div class="smw-right-seats"><?php echo esc_html( $variation_price->ticket_left ); ?> seats left</div>
                        <div class="smw-right-price">
                            <?php echo wp_kses_post( $product->get_price_html() ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function($){
    // Open modal
    $('#smw-open-seat-modal').on('click', function(e){
        e.preventDefault();
        $('#smw-seat-modal').fadeIn(150).attr('aria-hidden','false');
        $('body').addClass('smw-modal-open');
    });

    // Close modal (X, Back, overlay)
    $('#smw-close-modal, .smw-back-btn, #smw-seat-modal .overlay').on('click', function(e){
        e.preventDefault();
        $('#smw-seat-modal').fadeOut(120).attr('aria-hidden','true');
        $('body').removeClass('smw-modal-open');
    });

    // ESC to close
    $(document).on('keyup', function(e){
        if (e.key === 'Escape' && $('#smw-seat-modal').is(':visible')) {
            $('#smw-close-modal').trigger('click');
        }
    });
})(jQuery);
// REAL-TIME SEAT REDUCTION ON CLICK
// LIVE UPDATE SEAT COUNT (both places)
jQuery(document).on("change", "input.ticket-box", function () {

    // Right side box
    let rightBox = jQuery(".smw-right-seats");

    // Other location (Seats Remaining)
    let listBox = jQuery(".list-ticket-avialble");

    // Extract current number
    let current = parseInt(rightBox.text());

    // Decrease or increase
    if (jQuery(this).is(":checked")) {
        current = current - 1;
        if (current < 0) current = 0;
    } else {
        current = current + 1;
    }

    // Update right panel text
    rightBox.text(current + " seats left");

    // Update other location text
    listBox.text(current + " Seats Remaining");
});

</script>

        <?php } ?>
        <?php 
		$winner_id = get_post_meta( $product_id, 'tww_winner_id', true );
		if($winner_id>0){
		}
		else{
		if( current_user_can('editor') || current_user_can('administrator') ) {  ?>
        	
            <?php /*?><p class="mt-4 mb-0"><a target="_blank" href="<?php echo get_permalink(2613);?>?pid=<?php echo $product_id;?>" class="btn ">Select Winner</a></p><?php */?>
        <?php }
		}?>
<?php 
	}
}