<?php
// Matas Customizer


function mytheme_customizer_options( $wp_customize ) {



  $wp_customize->add_panel( 'comm_theme_panel', array(

    'priority'       => 30,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          =>'Matas Fields Theme',
    'description'    => 'This Page Is for Matas Theme'
  ));

      //Theme Front-End Setting

      $wp_customize->add_section('front_common_section_id', array(
          'title' => 'Front-End Common Option',
          'panel' => 'comm_theme_panel'
      ));

      //Theme Front-End Header Logo Part

      $wp_customize->add_setting("site_main_logo", array(
          'default'    => ''
      
      ));


      $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"sitelogo_control_id",array(
        'label'       => "Upload Header Logo:",
        'settings'    => "site_main_logo",
        'section'     => 'front_common_section_id',
        'description' => 'The Image is used for your site Main Header Logo. (Key:- site_main_logo)'      
        )
      ));

      //Theme Front-End Footer Logo Part

      $wp_customize->add_setting("site_footer_logo", array(
          'default'    => ''
      
      ));

      $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"footerlogo_control_id",array(
        'label'       => "Upload Footer Logo:",
        'settings'    => "site_footer_logo",
        'section'     => 'front_common_section_id',
        'description' => 'The Image is used for your site Main Footer Logo. (Key:- site_footer_logo)'      
        )
      ));






    //FavIcon part Png

    $wp_customize->add_setting("site_favicon_logo", array(
        'default'    => ''
    
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"faviconpng_control_id",array(
      'label'       => "Upload FavIcon Logo:",
      'settings'    => "site_favicon_logo",
      'section'     => 'front_common_section_id',
      'description' => '(The Image is used for your site Favicon)'      
      )
    ));

      //Theme Front-End CopyRight Text

      $wp_customize->add_setting("site_copyright_section", array(
          'default'    => ''
      
      ));

      $wp_customize->add_control("site_copyright_control", array(
        'label'   => "CopyRight Text Box:",
        'settings'=> "site_copyright_section",
        'section' => 'front_common_section_id',
        'type'    => 'textarea',
        'description' => 'Global CopyRight Text. (Key:- site_copyright_section)' 
          
      ));

      // Theme Developed By Text 


      $wp_customize->add_setting("site_development_section", array(
          'default'    => ''
      
      ));

      $wp_customize->add_control("site_power_control", array(
        'label'   => "Power By Text Box:",
        'settings'=> "site_development_section",
        'section' => 'front_common_section_id',
        'type'    => 'text',
        'description' => 'Global Power By Text. (Key:- site_development_section)'
          
      ));

      // Matas Logo


      $wp_customize->add_setting("creative_footer_logo", array(
          'default'    => ''
      
      ));

      $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"creative_footer_control_id",array(
        'label'       => "Upload Matas Footer Logo:",
        'settings'    => "creative_footer_logo",
        'section'     => 'front_common_section_id',
        'description' => 'The Image is used for your site Matas Logo.(Key:- creative_footer_logo)'      
        )
      ));



      


    //Theme Front-End Additional Information Setting

    $wp_customize->add_section('adi_section_id', array(
        'title' => 'Theme Additional Information',
        'panel' => 'comm_theme_panel'
    ));

    $wp_customize->add_setting("creative_email_id", array(
        'default'    => ''
    ));

    $wp_customize->add_control("site_email_control", array(
        'label'   => "Email ID:",
        'settings'=> "creative_email_id",
        'section' => 'adi_section_id',
        'type'    => 'email',
        'description' => 'Global Email ID. (Key:- creative_email_id)'
    ));

    $wp_customize->add_setting("creative_email_id_label", array(
        'default'    => 'Mail Us'
    ));

    $wp_customize->add_control("site_email_control_label", array(
        'label'   => "Email ID Label:",
        'settings'=> "creative_email_id_label",
        'section' => 'adi_section_id',
        'type'    => 'email',
        'description' => 'Global Email ID Label. (Key:- creative_email_id_label)'
    ));


    $wp_customize->add_setting("creative_phone_no", array(
        'default'    => ''
    ));

    $wp_customize->add_control("site_phone_no_control", array(
        'label'   => "Phone Number:",
        'settings'=> "creative_phone_no",
        'section' => 'adi_section_id',
        'type'    => 'tel',
        'description' => 'Global Phone Number. (Key:- creative_phone_no)'
    ));

    $wp_customize->add_setting("creative_phone_no_label", array(
        'default'    => 'CALL US'
    ));

    $wp_customize->add_control("site_phone_no_control_label", array(
        'label'   => "Phone Number Label:",
        'settings'=> "creative_phone_no_label",
        'section' => 'adi_section_id',
        'type'    => 'tel',
        'description' => 'Global Phone Number Label. (Key:- creative_phone_no_label)'
    ));


    $wp_customize->add_setting("creative_address", array(
      'default'    => ''
    ));

    $wp_customize->add_control("site_address_control", array(
        'label'   => "Site Address:",
        'settings'=> "creative_address",
        'section' => 'adi_section_id',
        'type'    => 'textarea',
        'description' => 'Site Address. (Key:- creative_address)'
    ));


    $wp_customize->add_setting("creative_address_label", array(
        'default'    => 'VISIT US ON'
      ));
  
      $wp_customize->add_control("site_address_control_label", array(
          'label'   => "Site Address Label:",
          'settings'=> "creative_address_label",
          'section' => 'adi_section_id',
          'type'    => 'text',
          'description' => 'Site Address Label. (Key:- creative_address_label)'
      ));



      $wp_customize->add_setting("creative_map", array(
        'default'    => ''
      ));
  
      $wp_customize->add_control("site_map_control", array(
          'label'   => "Site Map iframe:",
          'settings'=> "creative_map",
          'section' => 'adi_section_id',
          'type'    => 'textarea',
          'description' => ' (Key:- creative_map)'
      ));

      





      $wp_customize->add_setting("contact_form_shortcode", array(
        'default'    => ''
      ));
  
      $wp_customize->add_control("contact_form_shortcode_control", array(
          'label'   => "Contact Form Shortcode:",
          'settings'=> "contact_form_shortcode",
          'section' => 'adi_section_id',
          'type'    => 'textarea',
          'description' => 'This shortcode for Default Contact Us Page form'
      ));


    // Back-End Setting Option

    $wp_customize->add_section('dash_section_id', array(
        'title' => 'Theme Backend Option',
        'panel' => 'comm_theme_panel'
    ));

    $wp_customize->add_setting("dashboard_wplogin_bg", array(
        'default'    => ''
    ));


    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"wplogin_bg_control",array(
      'label'       => "Upload Wp-Login Background Image:",
      'settings'    => "dashboard_wplogin_bg",
      'section'     => 'dash_section_id',
      'description' => '(The Image is used for your site WP-Login Background Size:- 1600X810)'
      )
    ));


    $wp_customize->add_setting("dashboard_creative_logo", array(
        'default'    => ''
    ));


    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"wplogin_creative_logo_control",array(
      'label'       => "Upload Wp-Login Matas Logo:",
      'settings'    => "dashboard_creative_logo",
      'section'     => 'dash_section_id',
      'description' => '(The Matas Logo is used for your site WP-Login.)'
      )
    ));

    // Social Share Setting Option

    $wp_customize->add_section('socal_section_id', array(
        'title' => 'Theme Social Share Option',
        'panel' => 'comm_theme_panel'
    ));

    $wp_customize->add_setting("social_og_title", array(
        'default'    => ''
    ));

    $wp_customize->add_control("social_ogtitle_control", array(
        'label'   => "OG Title:",
        'settings'=> "social_og_title",
        'section' => 'socal_section_id',
        'type'    => 'text'
    ));

    $wp_customize->add_setting("social_og_type", array(
        'default'    => ''
    ));

    $wp_customize->add_control("social_ogtype_control", array(
        'label'   => "OG Type:",
        'settings'=> "social_og_type",
        'section' => 'socal_section_id',
        'type'    => 'tel'
    ));


    $wp_customize->add_setting("social_og_url", array(
        'default'    => esc_url(home_url('/')),
    ));

    $wp_customize->add_control("social_ogurl_control", array(
        'label'   => "OG URL:",
        'settings'=> "social_og_url",
        'section' => 'socal_section_id',
        'type'    => 'url'
    ));

    $wp_customize->add_setting("social_og_logo", array(
        'default'    => ''
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize,"social_oglogo_control",array(
      'label'       => "Upload Social Share Logo:",
      'settings'    => "social_og_logo",
      'section'     => 'socal_section_id',
      'description' => '(The Image is used for your site Socail Share Size: 200X200)'      
      )
    ));






    //Enqueue Script and Style
    
    $wp_customize->add_section('enqueue_section_id', array(
        'title' => 'Theme Enqueue Script and Style',
        'panel' => 'comm_theme_panel'
    ));



    $wp_customize->add_setting("enqueue_style_url", array(
        'default'    => '',
    ));

    $wp_customize->add_control("enqueue_style_url_control", array(
        'label'   => "Style Css file name:",
        'settings'=> "enqueue_style_url",
        'section' => 'enqueue_section_id',
        'type'    => 'textarea',
        'description' => '("|" separated list with full file name of enqueue style. <strong>Like:- style.css|theme.css|other.css</strong>)'
    ));


    $wp_customize->add_setting("enqueue_script_url", array(
      'default'    => '',
    ));

    $wp_customize->add_control("enqueue_script_url_control", array(
        'label'   => "Style Script file name:",
        'settings'=> "enqueue_script_url",
        'section' => 'enqueue_section_id',
        'type'    => 'textarea',
        'description' => '("|" separated list with full file name of enqueue Script. <strong>Like:- bootstrap.min.js|slick.js|custom.js</strong>)'
    ));



    //Woo-Commerce Setting Option
    
        $wp_customize->add_section('woo_section_id', array(
            'title' => 'Matas theme support for Woocommerce ',
            'panel' => 'comm_theme_panel'
        ));
    
    
    
        $wp_customize->add_setting("woo_remove_cart_active", array(
            'default'    => '',
        ));


        $wp_customize->add_control("woo_remove_cart_active_control", array(
            'label'   => "Woo Cart Remove Mail Active",
            'settings'=> "woo_remove_cart_active",
            'section' => 'woo_section_id',
            'type'    => 'checkbox', 
            'description' => '(When user removed product from cart. Looged in user get a mail.)'
              
          ));


}
  
add_action('customize_register', 'mytheme_customizer_options');