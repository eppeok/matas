jQuery(document).ready(function () {

  var home_url       = jQuery('.ceative_home_url').val();
  var mail_site_logo = jQuery('.ceative_site_logo').val();

  if( home_url === undefined ){
    home_url = jQuery('#wp-admin-bar-view-site').find('a.ab-item').attr('href');
  }

  jQuery('#wp-admin-bar-wp-logo').html('');

  var html_var = "<a href="+home_url+" tergat='_blank'><img src="+mail_site_logo+"></a>";

  jQuery('#wp-admin-bar-wp-logo').html(html_var);
  jQuery("#wp-admin-bar-wp-logo img").css("width", "30px");
  jQuery("#wp-admin-bar-wp-logo img").css("margin-top", "5px");


  var copy_right_text = jQuery('.ceative_copyright').val();
  var ceative_bglogo  = jQuery('.ceative_bglogo').val();
  var ceative_dev     = jQuery('.ceative_dev').val();

  jQuery('#login').wrap("<div class='creative-login'></div>");

    var copy_right =  '<p class="copy_txt_login">'+copy_right_text+' | '+ceative_dev+' <a href="https://evolv109.com/" target="_blank" rel="noopener"> <img src="'+ceative_bglogo+'" alt="logo" height="32" data-src="'+ceative_bglogo+'" /><noscript><img src="'+ceative_bglogo+'" alt="logo" height="32"> </noscript></a></p>';
    jQuery('#login').after(copy_right);

});