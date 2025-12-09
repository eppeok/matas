<?php
function hook_css() {
    ?>
      <meta property="og:title" content="<?php echo get_theme_mod('social_og_title');?>">
      <meta property="og:type" content="<?php echo get_theme_mod('social_og_type');?>">
      <meta property="og:url" content="<?php echo get_theme_mod('social_og_url');?>">
      <meta property="og:image" content="<?php echo get_theme_mod('social_og_logo');?>">

      <?php if( get_theme_mod('site_favicon_logo') != ''){?>
        <link href="<?php echo get_theme_mod('site_favicon_logo');?>" rel="shortcut icon" type="image/png" />
      <?php } ?>

      <input type="hidden" class="ceative_home_url" value="<?php echo esc_url(home_url('/'));?>">
      <input type="hidden" class="ceative_site_logo" value="<?php echo get_theme_mod('site_main_logo');?>">
      <input type="hidden" class="ceative_copyright" value="<?php echo strip_tags(apply_filters( 'the_content', get_theme_mod('site_copyright_section'))); ?>">
    <?php
}
add_action('wp_head', 'hook_css');



 function my_login_logo() { ?>
 
    <style type="text/css">
      #login h1 a, .login h1 a {
        background-image: url(<?php echo get_theme_mod('site_main_logo');?>);
      }
      .creative-login {
        background-image: url(<?php echo get_theme_mod('dashboard_wplogin_bg');?>);
        background-position: 50% 0;
      }
      <?php require get_template_directory() . '/assets/css/wp_login_page.css';?>
    </style>
  
  <input type="hidden" class="ceative_home_url" value="<?php echo esc_url(home_url('/'));?>">
  <input type="hidden" class="ceative_site_logo" value="<?php echo get_theme_mod('site_main_logo');?>">
  <input type="hidden" class="ceative_copyright" value="<?php echo strip_tags(apply_filters( 'the_content', get_theme_mod('site_copyright_section'))); ?>">
  <input type="hidden" class="ceative_dev" value="<?php echo get_theme_mod('site_development_section');?>">
  <input type="hidden" class="ceative_bglogo" value="<?php echo get_theme_mod('dashboard_creative_logo');?>">
 <?php }
  wp_enqueue_script( 'custom-admin-creative-js', get_template_directory_uri() . '/assets/js/custom-admin-js.js', array('jquery'), '27012021', true );
  add_action( 'login_enqueue_scripts', 'my_login_logo' );

add_action('admin_head', 'my_custom_logo');

function my_custom_logo() {
  echo '<input type="hidden" class="ceative_site_logo" value="'.get_theme_mod('site_main_logo').'">';
}


add_filter( 'login_headerurl', 'custom_loginlogo_url' );

function custom_loginlogo_url($url) {

     return esc_url(home_url('/'));

}