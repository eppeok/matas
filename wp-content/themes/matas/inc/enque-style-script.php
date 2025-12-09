<?php
function creative813_register_scripts(){
  
  //Custom CSS file Enqueue
  wp_enqueue_style('main_style_creative_css', get_template_directory_uri().'/style.css');
  wp_enqueue_style('theme_inner_style_creative_css', get_template_directory_uri().'/assets/css/theme-inner.css');
  if ( class_exists( 'WooCommerce' ) ){ 
    wp_enqueue_style('woo_style_creative_css', get_template_directory_uri().'/assets/css/woo-style.css');
  }

  if( get_theme_mod('enqueue_style_url') != ''){
    $style_list = explode("|",get_theme_mod('enqueue_style_url'));
    $st = 1;
    foreach( $style_list as $style_list_val ){
      wp_enqueue_style(str_replace(".","_",$style_list_val).'_creative_css'.$st, get_template_directory_uri().'/assets/css/'.$style_list_val);
    $st++; }
  }
  


  
  //Custom JS file Enqueue
 
  if( get_theme_mod('enqueue_style_url') != ''){
    $script_list = explode("|",get_theme_mod('enqueue_script_url'));
    $sy = 1;
    foreach( $script_list as $script_list_val ){
      wp_enqueue_script( str_replace(".","_",$script_list_val).'_creative_script'.$sy, get_template_directory_uri() . '/assets/js/'.$script_list_val, array('jquery'), '27012021', true );
      $sy++; }
    }
  if ( class_exists( 'WooCommerce' ) ){ 
    wp_enqueue_script( 'creative_woo_theme_js', get_template_directory_uri() . '/assets/js/woo-theme.js', array('jquery'), '27012021', true );
  }

}


add_action( 'wp_enqueue_scripts', 'creative813_register_scripts' );