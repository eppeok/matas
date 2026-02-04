<?php
/**
 * Plugin Name: Remaining Quantity Slider Filter
 * Description: Adds a Remaining Quantity slider filter with UI matching WooCommerce Price Filter (no JS conflict).
 * Version: 2.0
 * Author: Mimo Dev
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue jQuery UI Slider + CSS
 */
add_action( 'wp_enqueue_scripts', function () {

    if ( ! is_shop() && ! is_product_taxonomy() ) return;

    wp_enqueue_script( 'jquery-ui-slider' );

    // Woo already enqueues jQuery UI theme; fallback if theme doesn't
    wp_enqueue_style(
        'jquery-ui-theme',
        'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css'
    );
});

class WC_Widget_Remaining_Slider extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wc_remaining_slider',
            __( 'Filter by Remaining/Count', 'woocommerce' )
        );
    }

    public function widget( $args, $instance ) {

        if ( ! is_shop() && ! is_product_taxonomy() ) return;

        global $wpdb;

        $limits = $wpdb->get_row("
            SELECT 
                MIN(CAST(meta_value AS UNSIGNED)) AS min_val,
                MAX(CAST(meta_value AS UNSIGNED)) AS max_val
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_remaining_qty'
        ");

        if ( ! $limits || $limits->max_val === null ) return;

        $min = isset($_GET['min_remaining']) ? (int) $_GET['min_remaining'] : (int) $limits->min_val;
        $max = isset($_GET['max_remaining']) ? (int) $_GET['max_remaining'] : (int) $limits->max_val;

        echo $args['before_widget'];
        ?>

        <form method="get" class="remaining-filter-form">

            <h2 class="widget-title">
                <?php echo esc_html( $instance['title'] ?? 'Count' ); ?>
            </h2>

            <div class="remaining_slider_wrapper">

                <div class="remaining_slider"
                     data-min="<?php echo esc_attr($limits->min_val); ?>"
                     data-max="<?php echo esc_attr($limits->max_val); ?>"
                     data-current-min="<?php echo esc_attr($min); ?>"
                     data-current-max="<?php echo esc_attr($max); ?>">
                </div>

                <div class="remaining_slider_amount">
                    <input type="hidden" name="min_remaining" value="<?php echo esc_attr($min); ?>">
                    <input type="hidden" name="max_remaining" value="<?php echo esc_attr($max); ?>">

                    <div class="remaining_label">
                        Remaining:
                        <span class="from"><?php echo esc_html($min); ?></span> —
                        <span class="to"><?php echo esc_html($max); ?></span>
                    </div>

                    <button type="submit" class="button">
                        <?php esc_html_e('Filter', 'woocommerce'); ?>
                    </button>

                    <div class="clear"></div>
                </div>
            </div>

            <?php
            foreach ( $_GET as $key => $value ) {
                if ( in_array( $key, ['min_remaining','max_remaining'], true ) ) continue;
                if ( is_array( $value ) ) continue;
                echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';
            }
            ?>

        </form>

        <?php
        echo $args['after_widget'];
    }
}

add_action( 'widgets_init', function () {
    register_widget( 'WC_Widget_Remaining_Slider' );
});

add_action( 'wp_footer', function () {

    if ( ! is_shop() && ! is_product_taxonomy() ) return;
    ?>
    <script>
        jQuery(function ($) {

            $('.remaining_slider').each(function () {

                var $slider = $(this);

                var min = parseInt($slider.data('min'));
                var max = parseInt($slider.data('max'));
                var curMin = parseInt($slider.data('current-min'));
                var curMax = parseInt($slider.data('current-max'));

                var $wrap = $slider.closest('.remaining_slider_wrapper');

                $slider.slider({
                    range: true,
                    min: min,
                    max: max,
                    values: [curMin, curMax],

                    create: function () {
                        $wrap.find('.from').text(curMin);
                        $wrap.find('.to').text(curMax);
                    },

                    slide: function (event, ui) {
                        $wrap.find('.from').text(ui.values[0]);
                        $wrap.find('.to').text(ui.values[1]);
                        $wrap.find('input[name="min_remaining"]').val(ui.values[0]);
                        $wrap.find('input[name="max_remaining"]').val(ui.values[1]);
                    }
                });

            });

        });
    </script>
    <?php
});

add_action( 'woocommerce_product_query', function ( $q ) {

    if ( is_admin() ) return;

    if ( ! isset($_GET['min_remaining'], $_GET['max_remaining']) ) return;

    $meta_query = (array) $q->get( 'meta_query' );

    $meta_query[] = [
        'key'     => '_remaining_qty',
        'value'   => [ (int) $_GET['min_remaining'], (int) $_GET['max_remaining'] ],
        'compare' => 'BETWEEN',
        'type'    => 'NUMERIC',
    ];

    $meta_query['relation'] = 'AND';
    $q->set( 'meta_query', $meta_query );
});

add_action( 'wp_footer', function () {
    if ( ! is_shop() && ! is_product_taxonomy() ) return;
    ?>
    <script>
        jQuery(function ($) {

            $('.remaining-filter-form').on('submit', function () {

                var $priceForm = $('.widget_price_filter');
                var $thisForm  = $(this);

                // Copy price values if present
                if ($priceForm.length) {
                    var minPrice = $priceForm.find('input[name="min_price"]').val();
                    var maxPrice = $priceForm.find('input[name="max_price"]').val();

                    if (minPrice !== undefined) {
                        $('<input>', {
                            type: 'hidden',
                            name: 'min_price',
                            value: minPrice
                        }).appendTo($thisForm);
                    }

                    if (maxPrice !== undefined) {
                        $('<input>', {
                            type: 'hidden',
                            name: 'max_price',
                            value: maxPrice
                        }).appendTo($thisForm);
                    }
                }

                return true;
            });

        });
    </script>
    <?php
});