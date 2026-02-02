<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin\Partials
 */

/**
 * Include
 */
require 'class-mocognito-admin-utils.php';
require 'account/class-mocognito-admin-account.php';
require 'apps/class-mocognito-admin-apps.php';
require 'licensing/class-mocognito-admin-admin-licensing.php';
require 'guides/class-mocognito-admin-admin-guides.php';
require 'support/class-mocognito-admin-support.php';
require 'guides/class-mocognito-admin-attribute-mapping.php';
require 'demo/class-mocognito-admin-admin-rfd.php';
require 'faq/class-mocognito-admin-faq.php';
require 'addons/class-mocognito-admin-addons.php';
require 'cognitoIntegrator/class-mocognito-admin-cognito-integration.php';

/**
 * Plugin settings style
 *
 * @param Hook $hook for hook.
 */
function mocognito_plugin_settings_style( $hook ) {
	if ( 'toplevel_page_mo_oauth_settings' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'mo_oauth_admin_style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/admin.min.css', array(), '1.5.3' );
	wp_enqueue_style( 'mo_oauth_admin_settings_style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/style_settings.min.css', array(), '1.5.3' );
	wp_enqueue_style( 'mo_oauth_admin_settings_font_awesome', plugin_dir_url( dirname( __FILE__ ) ) . 'css/font-awesome.min.css', array(), '1.5.3' );
	wp_enqueue_style( 'mo_oauth_admin_settings_phone_style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/phone.min.css', array(), '1.5.3' );
	wp_enqueue_style( 'mo_oauth_admin_settings_datatable_style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/jquery.dataTables.min.css', array(), '1.5.3' );
	wp_enqueue_style( 'mo_oauth_admin_settings_inteltelinput_style', plugin_dir_url( dirname( __FILE__ ) ) . 'css/intlTelInput.min.css', array(), '17.0.19' );
}

/**
 * Plugin settings script
 *
 * @param Hook $hook for hook.
 */
function mocognito_plugin_settings_script( $hook ) {
	if ( 'toplevel_page_mo_oauth_settings' !== $hook ) {
		return;
	}
	wp_enqueue_script( 'mo_oauth_admin_script', plugin_dir_url( dirname( __FILE__ ) ) . 'js/admin.min.js', array(), '1.5.3', true );
	wp_enqueue_script( 'mo_oauth_admin_settings_script', plugin_dir_url( dirname( __FILE__ ) ) . 'js/settings.min.js', array(), '1.5.3', true );
	wp_enqueue_script( 'mo_oauth_admin_settings_phone_script', plugin_dir_url( dirname( __FILE__ ) ) . 'js/phone.min.js', array(), '1.5.3', true );
	wp_enqueue_script( 'mo_oauth_admin_settings_datatable_script', plugin_dir_url( dirname( __FILE__ ) ) . 'js/jquery.dataTables.min.js', array(), '1.5.3', true );
	wp_enqueue_script( 'mo_oauth_admin_settings_inteltelinput', plugin_dir_url( dirname( __FILE__ ) ) . 'js/intlTelInput.min.js', array(), $ver = '13.0.4', false );
}

/**
 * Main Menu
 */
function mocognito_main_menu() {
	$today      = gmdate( 'Y-m-d H:i:s' );
	$date       = '2020-12-31 23:59:59';
	$currenttab = '';
	// @phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe to ignore nonce here as no data operation
	if ( isset( $_GET['tab'] ) ) {
		// @phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe to ignore nonce here as no data operation
		$currenttab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
	}

	MoCognito_Admin_Utils::curl_extension_check();
	MoCognito_Admin_Menu::show_menu( $currenttab );
	echo '<div id="mo_oauth_settings">';
		MoCognito_Admin_Menu::show_idp_link( $currenttab );
	if ( $today <= $date ) {
		MoCognito_Admin_Menu::show_bfs_note();
	}
		echo '
		<div class="miniorange_container">';

		echo '<table style="width:100%;">
			<tr>
				<td style="vertical-align:top;width:65%;" class="mo_oauth_content">';

				MoCognito_Admin_Menu::show_tab( $currenttab );

				MoCognito_Admin_Menu::show_support_sidebar( $currenttab );
				echo '</tr>
				</table>
				<div class="mo_tutorial_overlay" id="mo_tutorial_overlay" hidden></div>
		</div>';
}

/**
 * Xyake
 */
function mocognito_hbca_xyake() {
	if ( get_option( 'mo_oauth_admin_customer_key' ) > 138200 ) {
		return true;
	} else {
		return false;
	}}

/**
 * Admin Menu
 */
class MoCognito_Admin_Menu {

	/**
	 * Show menu
	 *
	 * @param CurrentTab $currenttab for current tab.
	 */
	public static function show_menu( $currenttab ) {
		?> <div class="wrap">
			<div><img style="float:left;" src="<?php echo esc_url( dirname( plugin_dir_url( __FILE__ ) ) ); ?>/images/logo.png"></div>
		</div>
				<div class="wrap">
			<h1>

				miniOrange <?php echo esc_attr( MO_OAUTH_PLUGIN_NAME ); ?>&nbsp
				<a id="license_upgrade" class="add-new-h2 add-new-hover" style="background-color: orange !important; border-color: orange; font-size: 16px; color: #000;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'licensing' ), isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) ); ?>">Premium plans</a>
				<a id="faq_button_id" class="add-new-h2" href="https://faq.miniorange.com/kb/oauth-openid-connect/" target="_blank">Troubleshooting</a>
				<a id="form_button_id" class="add-new-h2" href="https://forum.miniorange.com/" target="_blank">Ask questions on our forum</a>
				<a id="features_button_id" class="add-new-h2" href="https://developers.miniorange.com/docs/oauth/wordpress/client" target="_blank">Feature Details</a>

			</h1>
		</div>
		<style>
			.add-new-hover:hover{
				color: white !important;
			}

		</style>
		<div id="tab">
		<h2 class="nav-tab-wrapper">
			<a id="tab-config" class="nav-tab 
			<?php
			if ( 'config' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=config">Configure OAuth</a>
			<a id="tab-attrmapping" class="nav-tab 
			<?php
			if ( 'attributemapping' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=attributemapping">Attribute/Role Mapping</a>
			<a id="tab-signinsettings" class="nav-tab 
			<?php
			if ( 'signinsettings' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=signinsettings">Login Settings</a>
			<a id="tab-customization" class="nav-tab 
			<?php
			if ( 'customization' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=customization">Login Button Customization</a>
			<a id="tab-cognito-integrator" class="nav-tab 
			<?php
			if ( 'cognitointegrator' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=cognitointegrator">Cognito Integrator</a>
			<a id="tab-requestdemo" class="nav-tab 
			<?php
			if ( 'requestfordemo' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=requestfordemo">Request For Demo</a>
			<a id="tab-acc-setup" class="nav-tab 
			<?php
			if ( 'account' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=account">Account Setup</a>
			<a id="tab-addons" class="nav-tab 
			<?php
			if ( 'addons' === $currenttab ) {
				echo 'nav-tab-active';}
			?>
			" href="admin.php?page=mo_oauth_settings&tab=addons">Add-ons</a>
		</h2>
		</div>
		<?php

	}

	/**
	 * Show BFS note
	 */
	public static function show_bfs_note() {
		?>
			<form name="f" method="post" action="" id="mo_oauth_client_bfs_note_form">
				<?php wp_nonce_field( 'mo_oauth_client_bfs_note_form', 'mo_oauth_client_bfs_note_form_field' ); ?>
				<input type="hidden" name="option" value="mo_oauth_client_bfs_note_message"/>	
				<div class="notice notice-info"style="padding-right: 38px;position: relative;border-color:red; background-color:black"><h4><center><i class="fa fa-gift" style="font-size:50px;color:red;"></i>&nbsp;&nbsp;
				<big><font style="color:white; font-size:30px;"><b>END OF YEAR SALE: </b><b style="color:yellow;">UPTO 50% OFF!</b></font> <br><br></big><font style="color:white; font-size:20px;">Contact us @ oauthsupport@xecurify.com for more details.</font></center></h4>
				<p style="text-align: center; font-size: 60px; margin-top: 0px; color:white;" id="demo"></p>
				</div>
			</form>
		<script>
		var countDownDate = <?php echo esc_attr( strtotime( 'Dec 31, 2020 23:59:59' ) ); ?> * 1000;
		var now = <?php echo esc_attr( time() ); ?> * 1000;
		var x = setInterval(function() {
			now = now + 1000;
			var distance = countDownDate - now;
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			document.getElementById("demo").innerHTML = days + "d " + hours + "h " +
				minutes + "m " + seconds + "s ";
			if (distance < 0) {
				clearInterval(x);
				document.getElementById("demo").innerHTML = "EXPIRED";
			}
		}, 1000);
		</script>
		<?php
	}

	/**
	 * Show IdP link
	 *
	 * @param CurrentTab $currenttab for current tab.
	 */
	public static function show_idp_link( $currenttab ) {
		if ( ( ! get_option( 'mo_oauth_client_show_mo_server_message' ) ) ) {
			?>
			<form name="f" method="post" action="" id="mo_oauth_client_mo_server_form">
				<?php wp_nonce_field( 'mo_oauth_mo_server_message_form', 'mo_oauth_mo_server_message_form_field' ); ?>
				<input type="hidden" name="option" value="mo_oauth_client_mo_server_message"/>
				<div class="notice notice-info" style="padding-right: 38px;position: relative;">
					<h4 style="font-size: 13px;margin: 1.33em 0px;font-weight: 600;">Looking for a User Storage/OAuth Server? We have a B2C Service(Cloud IDP) which can scale to hundreds of millions of consumer identities. You can <a href="https://idp.miniorange.com/b2c-pricing" target="_blank">click here</a> to find more about it.</h4>
					<button type="button" class="notice-dismiss" id="mo_oauth_client_mo_server"><span class="screen-reader-text">Dismiss this notice.</span>
					</button>
				</div>
			</form>
			<script>
				jQuery("#mo_oauth_client_mo_server").click(function () {
					jQuery("#mo_oauth_client_mo_server_form").submit();
				});
			</script>
			<?php
		}
	}

	/**
	 * Show tab
	 *
	 * @param CurrentTab $currenttab for current tab.
	 */
	public static function show_tab( $currenttab ) {
		if ( 'account' === $currenttab ) {
			if ( 'true' === get_option( 'verify_customer' ) ) {
				MoCognito_Admin_Account::verify_password();
			} elseif ( '' !== trim( get_option( 'mo_oauth_admin_email' ) ) && '' === trim( get_option( 'mo_oauth_admin_api_key' ) ) && 'true' !== get_option( 'new_registration' ) ) {
				MoCognito_Admin_Account::verify_password();
			} else {
				MoCognito_Admin_Account::register();
			}
		} elseif ( 'customization' === $currenttab ) {
				MoCognito_Admin_Apps::customization();
		} elseif ( 'signinsettings' === $currenttab ) {
				MoCognito_Admin_Apps::sign_in_settings();
		} elseif ( 'licensing' === $currenttab ) {
				MoCognito_Admin_Admin_Licensing::show_licensing_page();
		} elseif ( 'cognitointegrator' === $currenttab ) {
				MoCognito_Admin_Cognito_Integration::show_cognito_integrator_page();
		} elseif ( 'requestfordemo' === $currenttab ) {
				MoCognito_Admin_Admin_RFD::requestfordemo();
		} elseif ( 'faq' === $currenttab ) {
				MoCognito_Admin_Faq::faq();
		} elseif ( 'addons' === $currenttab ) {
				MoCognito_Admin_Addons::addons();
		} elseif ( 'attributemapping' === $currenttab ) {
				MoCognito_Admin_Apps::attribute_role_mapping();
		} elseif ( '' === $currenttab ) {
			?>
						<a id="goregister" style="display:none;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'config' ), isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) ); ?>">

						<script>
							location.href = jQuery('#goregister').attr('href');
						</script>
					<?php
		} else {
			MoCognito_Admin_Apps::applist();
		}
		// }
	}

	/**
	 * Show support sidebar
	 *
	 * @param CurrentTab $currenttab for current tab.
	 */
	public static function show_support_sidebar( $currenttab ) {
		if ( 'licensing' !== $currenttab ) {
			echo '<td style="vertical-align:top;padding-left:1%;" class="mo_oauth_sidebar">';
			if ( 'attributemapping' === $currenttab ) {
				echo esc_html( MoCognito_Admin_Attribute_Mapping::emit_attribute_table() );
			}
			echo esc_html( MoCognito_Admin_Support::support() );
			echo '</td>';
		}
	}

}

?>
