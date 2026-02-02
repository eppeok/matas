<?php
/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Creative Theme
 */

/**
 *  Creative Theme only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
  require get_template_directory() . '/inc/back-compat.php';
  return;
}

if ( ! function_exists( 'cretive_theme_setup' ) ) :
  /**
   * Sets up theme defaults and registers support for various WordPress features.
   *
   * Note that this function is hooked into the after_setup_theme hook, which
   * runs before the init hook. The init hook is too late for some features, such
   * as indicating support for post thumbnails.
   */
  function cretive_theme_setup() {



    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1568, 9999 );

    // This theme uses wp_nav_menu() in two locations.
    register_nav_menus(
      array(
        'menu-1' => __( 'Primary', 'creative_atsotaro' ),
        'footer' => __( 'Footer Menu', 'creative_atsotaro' ),
        'social' => __( 'Social Links Menu', 'creative_atsotaro' ),
      )
    );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support(
      'html5',
      array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
        'navigation-widgets',
      )
    );

  }
endif;

// Woo Support Hook
  if ( class_exists( 'WooCommerce' ) ){
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
  }




add_action( 'after_setup_theme', 'cretive_theme_setup' );

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

require get_template_directory() . '/inc/creative-customizer.php';
require get_template_directory() . '/inc/enque-style-script.php';
require get_template_directory() . '/inc/add_meta_hook.php';
require get_template_directory() . '/inc/woo-mail-function.php';







function creative_theme_widgets_init() {

  register_sidebar(
    array(
      'name'          => __( 'Footer', 'creative_atsotaro' ),
      'id'            => 'sidebar-1',
      'description'   => __( 'Add widgets here to appear in your footer.', 'creative_atsotaro' ),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

  register_sidebar(
    array(
      'name'          => __( 'Shop Sidebar', 'creative_atsotaro' ),
      'id'            => 'sidebar-shop-archiv',
      'description'   => __( 'Add widgets here to appear in your footer.', 'creative_atsotaro' ),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );

}
add_action( 'widgets_init', 'creative_theme_widgets_init' );




add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
  function jk_related_products_args( $args ) {
  $args['posts_per_page'] = 8; // 4 related products
  $args['columns'] = 4; // arranged in 2 columns
  return $args;
}


function pippin_get_image_alt($image_url) {
  global $wpdb;
  $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));  
  $alt = get_post_meta ( $attachment[0], '_wp_attachment_image_alt', true );
   return $alt;
}


// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );



// 1. Remove default add-to-cart button on product archive pages
add_action('init','mytheme_remove_loop_add_to_cart');
function mytheme_remove_loop_add_to_cart() {
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}

// 2. Add a custom “Purchase Seat” button linking to the single product page
add_action('woocommerce_after_shop_loop_item', 'mytheme_add_purchase_seat_button', 15);
function mytheme_add_purchase_seat_button() {
    global $product;
    if ( ! $product ) return;
    // Get the product page URL
    $url = $product->get_permalink();
    if ( is_product_category( 'matas-exclusives' ) ) {
        echo '<a href="' . esc_url($url) . '" class="button purchase-seat-button">Purchase</a>';
    } else if( is_product_category( 'webinars' ) ) {
        echo '<a href="' . esc_url($url) . '" class="button purchase-seat-button">Purchase Seat</a>';
    } else {
        echo '<a href="' . esc_url($url) . '" class="button purchase-seat-button">Purchase</a>';
    }
}

function custom_current_year () {
  $year = date_i18n ('Y');
  return $year;
}
add_shortcode ('copyright_year', 'custom_current_year');

// Shortcode Support on Mail message box

add_filter( 'wpcf7_mail_components', function( $components ){
  $components['body'] = do_shortcode( $components['body'] );
  return $components;
} );

// Reorder single product summary
function smw_reorder_single_product_summary() {

    // Remove price from its default position (priority 10)
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

    // Add price back AFTER the short description (which is priority 20)
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
}
add_action( 'woocommerce_before_single_product', 'smw_reorder_single_product_summary' );

// Remove product meta (SKU, categories, tags) on single product page
function smw_remove_single_product_meta() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
}
add_action( 'woocommerce_before_single_product', 'smw_remove_single_product_meta' );

// Open WooCommerce Terms & Conditions link in a new tab WITH real page URL
add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', 'smw_terms_link_open_in_new_tab', 10, 1 );
function smw_terms_link_open_in_new_tab( $text ) {

    $terms_page_id = wc_get_page_id( 'terms' );

    if ( $terms_page_id && $terms_page_id > 0 ) {

        $terms_link   = get_permalink( $terms_page_id );
        $link_text    = __( 'terms and conditions', 'woocommerce' );

        // Rebuild text but keep same wording as default
        $text = sprintf(
            'I have read and agree to the website <a href="%s" class="woocommerce-terms-and-conditions-link" target="_blank" rel="noopener noreferrer">%s</a>',
            esc_url( $terms_link ),
            esc_html( $link_text )
        );
    }

    return $text;
}

// Change Related Products title on single product page
add_filter( 'woocommerce_product_related_products_heading', 'smw_change_related_products_title' );

function smw_change_related_products_title( $heading ) {
    if ( is_product() ) {
        global $product;

        if ( has_term( 'matas-exclusives', 'product_cat', $product->get_id() ) ) {
            return 'Shop Similar Items';
        } else {
            return 'Shop Similar Webinars';
        }
    }

    return $heading;
}


/**
 * Update parent product stock based on variation stock (for sorting webinars)
 */
add_action( 'woocommerce_variation_set_stock', 'update_parent_webinar_stock' );
add_action( 'woocommerce_variation_set_stock_status', 'update_parent_webinar_stock' );

function update_parent_webinar_stock( $variation ) {

    $parent_id = $variation->get_parent_id();
    if ( ! $parent_id ) return;

    $product = wc_get_product( $parent_id );

    if ( ! $product || $product->get_type() !== 'variable' ) return;

    $total_stock = 0;

    foreach ( $product->get_children() as $child_id ) {
        $child = wc_get_product( $child_id );

        if ( $child && $child->managing_stock() ) {
            $total_stock += (int) $child->get_stock_quantity();
        }
    }

    // Save total remaining seats to parent product
    update_post_meta( $parent_id, '_total_remaining_seats', $total_stock );
}
/**
 * Add Remaining Seats sorting options
 */
add_filter( 'woocommerce_catalog_orderby', 'add_stock_remaining_sorting' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'add_stock_remaining_sorting' );

function add_stock_remaining_sorting( $sortby ) {

    $sortby['stock_low_high']  = __( 'Sort by remaining: low to high', 'woocommerce' );
    $sortby['stock_high_low']  = __( 'Sort by remaining: high to low', 'woocommerce' );

    return $sortby;
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'stock_remaining_ordering_args' );

function stock_remaining_ordering_args( $args ) {

    if ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], ['stock_low_high','stock_high_low'] ) ) {

        global $wpdb;

        $order = $_GET['orderby'] === 'stock_low_high' ? 'ASC' : 'DESC';

        // Custom ORDER BY that falls back to _stock if _total_remaining_seats doesn't exist
        add_filter( 'posts_clauses', function( $clauses ) use ( $order, $wpdb ) {

            $clauses['join'] .= " 
                LEFT JOIN {$wpdb->postmeta} AS total_seats_meta 
                    ON ({$wpdb->posts}.ID = total_seats_meta.post_id 
                    AND total_seats_meta.meta_key = '_total_remaining_seats')

                LEFT JOIN {$wpdb->postmeta} AS stock_meta 
                    ON ({$wpdb->posts}.ID = stock_meta.post_id 
                    AND stock_meta.meta_key = '_stock')
            ";

            $clauses['orderby'] = " 
                CAST(
                    COALESCE(total_seats_meta.meta_value, stock_meta.meta_value, 0)
                AS UNSIGNED) {$order}
            ";

            return $clauses;
        });

    }

    return $args;
}


add_action( 'woocommerce_after_shop_loop_item_title', 'matas_category_stock_display', 6 );

function matas_category_stock_display() {
    if ( ! is_product_category( 'matas-exclusives' ) ) {
        return;
    }

    global $product;

    if ( ! $product ) {
        return;
    }

    echo '<p class="list-ticket-avialble">';

    if ( $product->managing_stock() ) {
        $qty = $product->get_stock_quantity();

        if ( $qty > 0 ) {
            echo 'Available: ' . esc_html( $qty );
        } else {
            echo 'Sold Out';
        }

    } elseif ( $product->is_in_stock() ) {
        echo 'In Stock';
    } else {
        echo 'Sold Out';
    }

    echo '</p>';
}