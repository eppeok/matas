=== Login with Cognito ===
Contributors: cyberlord92
Tags: cognito, aws, amazon, OAuth 2.0, SSO
Requires at least: 3.0.1
Tested up to: 6.8
Stable tag: 1.5.3
License: Expat
License URI: https://plugins.miniorange.com/mit-license

WordPress Login with Cognito plugin allows Login ( Single Sign-On ) to WordPress using AWS Cognito account credentials. You can Login to your WordPress site with AWS Cognito using this plugin. This plugin uses OAuth protocol to achieve Single Sign-on.

== Description ==

**[WordPress Login with Cognito plugin](https://plugins.miniorange.com/aws-cognito-wordpress-single-sign-on-integration)** allows Login ( Single Sign-On ) to WordPress using AWS Cognito account credentials. You can SSO ( Single Sign-on )/Login to your WordPress site with Cognito using this plugin. This plugin uses OAuth protocol to achieve Single Sign-on. It also covers User Authentication with OAuth protocol and allow authorized user to login into WordPress site.

= Single Sign-On ( SSO ) =

In simple term, Single Sign-On ( SSO ) means login into 1 site / application using the credentials of another app/site.
Example. If you have all your Users/Customers/Members/Employees stored on 1 site(ex. gmail, wordpress, etc.), lets say site A and you want all of them to register/login into your WordPress site say site B. In this scenario, you can register/login all your users of site A into Site B using the login credentials/account of Site A. This is called Single Sign-On or SSO.


= FEATURES =

*	WordPress Login with Cognito supports single sign-on / SSO with Cognito domain.
*	Single Sign On ( SSO ) Grant Support : Standard OAuth 2.0 Grant : Authorization Code
*	Auto Create Users : After SSO, new user automatically gets created in WordPress
*	Account Linking : After user SSO to WordPress, if user already exists in WordPress, then his profile gets updated or it will create a new WordPress User
*	Attribute Mapping : Login with Cognito supports username Attribute Mapping feature to map WordPress user profile username attribute.
*	Login Widget : Use Widgets to easily integrate the login link with your WordPress site 
*	Redirect URL after Login : OAuth Login Automatically Redirects user after successful login. 

= USE CASES =

*   Easily auto-register users into Cognito Pools from WordPress login forms with our WP Cognito Integration plugin.[More Details](https://plugins.miniorange.com/wordpress-cognito-user-management-with-cognito-integrator)
*   Use custom login forms and avoid redirecting users to Cognito during SSO with our WP Cognito Integration plugin.[More Details](https://plugins.miniorange.com/wordpress-login-with-cognito-using-wordpress-custom-forms)
*   Sync membership status updates (upgrade, downgrade, renewal, expiration) to AWS Cognito user profiles.[More Details](https://plugins.miniorange.com/wordpress-user-membership-sync-with-cognito-pool)
*   Manage backend authentication via Cognito credentials for your custom designed code.[More Details](https://plugins.miniorange.com/cognito-login-registration-with-wordpress-custom-login-code)
*   Cognito Integrator enables single-login access across multiple WordPress sites with customizable integration.[More Details](https://plugins.miniorange.com/aws-cognito-wordpress-single-sign-on-integration)
*   User verification in Cognito when a user creates an account from the woocommerce checkout page.[More Details](https://plugins.miniorange.com/cognito-otp-authentication-in-woocommerce-checkout)


= No SSL restriction =
*	Login to WordPress ( WordPress SSO ) using Cognito without having an SSL or HTTPS enabled site.


== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`
2. Search for `cognito`. Find and Install `Login with Cognito` plugin by miniOrange
3. Activate the plugin

= From WordPress.org =
1. Download WordPress Login with Cognito.
2. Unzip and upload the `Login with Cognito` directory to your `/wp-content/plugins/` directory.
3. Activate Login with Cognito from your Plugins page.

= Once Activated =
1. Go to `Settings-> Login with Cognito -> Configure OAuth`, and follow the instructions
2. Go to `Appearance->Widgets` ,in available widgets you will find `Login with Cognito` widget, drag it to chosen widget area where you want it to appear.
3. Now visit your site and you will see login with widget.

= For Viewing Corporation, Alliance, Character Name in user profile =
To view Corporation, Alliance and Character Name in edit user profile, copy the following code in the end of your theme's `Theme Functions(functions.php)`. You can find `Theme Functions(functions.php)` in `Appearance->Editor`.
<code>
add_action( 'show_user_profile', 'mo_oauth_my_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'mo_oauth_my_show_extra_profile_fields' );
</code>

== Frequently Asked Questions ==
= I need to customize the plugin or I need support and help? =
Please email us at <a href="mailto:info@xecurify.com" target="_blank">info@xecurify.com</a> or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>. You can also submit your query from plugin's configuration page.

= I need integration of this plugin with my other installed plugins like BuddyPress, etc.? =
We will help you in integrating this plugin with your other installed plugins. Please email us at <a href="mailto:info@xecurify.com" target="_blank">info@xecurify.com</a> or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>. You can also submit your query from plugin's configuration page.

= Is it possible to set a different redirect URL after login & logout =
Yes, With standard license you can set different redirect URL to redirect to after login as well as after logout.

= For any other query/problem/request =
Please email us at <a href="mailto:info@xecurify.com" target="_blank">info@xecurify.com</a> or <a href="http://miniorange.com/contact" target="_blank">Contact us</a>. You can also submit your query from plugin's configuration page.


== Screenshots ==

1. Plugin Configuration
2. Attribute Mapping
3. Login Button / Widget
4. WordPress Dashboard Login

== Changelog ==
= 1.5.2 =
* Added compatibility fixes for WP 6.8
* URI migration

= 1.5.1 =
* Added compatibility fixes for WP 6.3
* Fixed feedback form issue
* UI changes

= 1.5.0 =
* Added compatibility fixes for WP 6.2 
* Codesniffer fixes

= 1.4.9 =
* Added XSS security fixes 

= 1.4.8 =
* Added compatibility with WP 6.1

= 1.4.7 =
* Security fixes

= 1.4.6 =
* Added compatiblity with WP 6.0

= 1.4.5 =
* Added compatiblity with WP 5.9

= 1.4.4 =
* Security Fixes

= 1.4.3 =
* Added compatiblity with WP 5.8
* Minor bug fixes

= 1.4.2 =
* Readme changes
* Minor Improvements

= 1.4.1 = 
* Added compatibility with WP 5.7
* Added application update/delete options
* Added support for custom OAuth/OpenID application
* Minor bug fixes

= 1.4.0 = 
* Added compatibility with WP 5.6

= 1.3.6 =
* updated setup guide link

= 1.3.5 =
* Readme changes

= 1.3.4 =
* Added compatibility with WP 5.5

= 1.3.3 =
* Licensing plan changes

= 1.3.2 =
* Copy callback URL feature
* Add-on tab UI changes
* Bug fixes
* SEO update

= 1.3.0 =
* Bug fixes
* UI changes
* Licensing plan changes

= 1.2.3 =
* Bug fixes
* Compatibility fixes
* Licensing changes

= 1.2.2 =
* Bug fixes
* SEO update

= 1.0.0 =
* First version for Login with Cognito.
