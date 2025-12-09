      jQuery(document).ready(function () {

  var home_url = jQuery('#wp-admin-bar-site-name').find('a.ab-item').attr('href');
  console.log();
  jQuery('#wp-admin-bar-wp-logo').html('');
  var html_var = "<a href="+home_url+" tergat='_blank'><img src='"+home_url+"wp-content/uploads/2021/07/logo.png'></a>";
  jQuery('#wp-admin-bar-wp-logo').html(html_var);
  jQuery("#wp-admin-bar-wp-logo img").css("width", "30px");
  jQuery("#wp-admin-bar-wp-logo img").css("margin-top", "5px");

  var login_home_url = jQuery('#login h1').find('a').attr('href');

  var copy_right =  '<p class="copy_txt_login">© Copyright 2021 Safety Net , All Rights Reserved. | Designed by <a href="https://eppeok.com/" target="_blank" rel="noopener"> <img src="'+login_home_url+'wp-content/uploads/2021/07/eppeok-logo.png" alt="creative813 logo" height="32" data-src="'+login_home_url+'wp-content/uploads/2021/07/eppeok-logo.png" /><noscript><img src="'+login_home_url+'wp-content/uploads/2021/07/eppeok-logo.png" alt="creative813 logo" height="32"> </noscript></a></p>';
        jQuery('#login').after(copy_right);
        //console.log(copy_right);

});