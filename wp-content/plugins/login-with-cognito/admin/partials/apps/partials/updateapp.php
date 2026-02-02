<?php
/**
 * MINIORANGE Admin Apps
 *
 * @package    Admin\Apps
 */

/**
 * Update App Page
 *
 * @param AppName $appname for app name to be updated.
 */
function mocognito_update_app_page( $appname ) {

	$appslist       = get_option( 'mo_oauth_apps_list' );
	$currentappname = $appname;
	$currentapp     = null;
	foreach ( $appslist as $key => $app ) {
		if ( $appname === $key ) {
			$currentapp = $app;
			break;
		}
	}
	if ( ! $currentapp ) {
		return;
	}
	$is_other_app = true;

	?>
		<div id="toggle2" class="mo_panel_toggle">
			<h3>Configure OAuth Provider</h3>
		</div>
		<div id="mo_oauth_update_app">
		<form id="form-common" name="form-common" method="post" action="admin.php?page=mo_oauth_settings">
	<?php wp_nonce_field( 'mo_oauth_add_app_form', 'mo_oauth_add_app_form_field' ); ?>

				<input class="mo_table_textbox" required="" type="hidden" name="mo_oauth_app_name" value="<?php echo isset( $currentapp['appId'] ) ? esc_attr( $currentapp['appId'] ) : 'other'; ?>">
				<input type="hidden" name="option" value="mo_oauth_add_app" />
				<input type="hidden" id="mo_oauth_app_nameid" value="<?php echo esc_attr( $currentappname ); ?>">
				<input type="hidden" name="mo_oauth_app_type" value="<?php echo esc_attr( $currentapp['apptype'] ); ?>">
				<input class="mo_table_textbox" required="" type="hidden" name="mo_oauth_custom_app_name" value="<?php echo esc_attr( $currentappname ); ?>">

		<table class="mo_settings_table">
			<tr id="mo_oauth_display_app_name_div">
				<td><strong>Display App Name:</strong><br>&emsp;<font color="#FF0000"><small><a href="admin.php?page=mo_oauth_settings&tab=licensing" target="_blank" rel="noopener noreferrer">[STANDARD]</a></small></font></td>
				<td><input disabled class="mo_table_textbox" type="text" value="Login with <?php echo esc_attr( $currentappname ); ?>"></td>
			</tr>
			<tr><td><strong>Redirect / Callback URL</strong><br>&emsp;<font color="#FF0000"><small><a href="admin.php?page=mo_oauth_settings&tab=licensing" target="_blank" rel="noopener noreferrer">[STANDARD]</a></small></font></td>
			<td><input class="mo_table_textbox" id="callbackurl" type="text" readonly="true" value='<?php echo esc_attr( $currentapp['redirecturi'] ); ?>'>
			&nbsp;&nbsp;
			<div class="tooltip" style="display: inline;"><span class="tooltiptext" id="moTooltip">Copy to clipboard</span><i class="fa fa-clipboard fa-border" style="font-size:20px; align-items: center;vertical-align: middle;" aria-hidden="true" onclick="copyUrl()" onmouseout="mocognito_outFunc()"></i></div>
			</td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client ID:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" name="mo_oauth_client_id" value="<?php echo esc_attr( $currentapp['clientid'] ); ?>"></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client Secret:</strong></td>
				<td><input class="mo_table_textbox" required="" type="password" name="mo_oauth_client_secret" id="mo_oauth_client_secret" value="<?php echo esc_attr( $currentapp['clientsecret'] ); ?>">
				<i class="fa fa-eye" onclick="mocognito_showClientSecret()" id="show_button" style="margin-left:-30px; cursor:pointer;"></i></td>
			</tr>
			<tr>
				<td><strong>Scope:</strong></td>
				<td><input class="mo_table_textbox" type="text" name="mo_oauth_scope" value="<?php echo esc_attr( $currentapp['scope'] ); ?>"></td>
			</tr>
		<?php if ( $is_other_app ) { ?>
				<tr  id="mo_oauth_authorizeurl_div">
					<td><strong><font color="#FF0000">*</font>Authorize Endpoint:</strong></td>
					<td><input class="mo_table_textbox" required="" type="text" id="mo_oauth_authorizeurl" name="mo_oauth_authorizeurl" value="<?php echo esc_attr( $currentapp['authorizeurl'] ); ?>"></td>
				</tr>
				<tr id="mo_oauth_accesstokenurl_div">
					<td><strong><font color="#FF0000">*</font>Access Token Endpoint:</strong></td>
					<td><input class="mo_table_textbox" required="" type="text" id="mo_oauth_accesstokenurl" name="mo_oauth_accesstokenurl" value="<?php echo esc_attr( $currentapp['accesstokenurl'] ); ?>"></td>
				</tr>
				<?php
				if ( isset( $currentapp['apptype'] ) && 'openidconnect' !== $currentapp['apptype'] ) {
						$oidc = false;
				} else {
					$oidc = true;
				}
				?>
				<tr id="mo_oauth_resourceownerdetailsurl_div">
					<td><strong>
					<?php
					if ( false === $oidc ) {
						echo '<font color="#FF0000">*</font>'; }
					?>
					Get User Info Endpoint:</strong></td>
					<td><input class="mo_table_textbox" type="text" id="mo_oauth_resourceownerdetailsurl" name="mo_oauth_resourceownerdetailsurl" 
					<?php
					if ( false === $oidc ) {
						echo 'required';}
					?>
					value="<?php //@phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen, Squiz.PHP.EmbeddedPhp.ContentAfterOpen, Squiz.WhiteSpace.SuperfluousWhitespace.EndLine -- Ignoring as spaces affecting input value
					if ( isset( $currentapp['resourceownerdetailsurl'] ) ) {
						echo esc_attr( $currentapp['resourceownerdetailsurl'] ); }
					//@phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd -- Ignoring as spaces affecting input values
					?>"></td>
				</tr>		
				<tr>
					<td></td>
					<td><div style="padding:5px;"></div>
					<input type="checkbox" class="mo_table_textbox" name="mo_oauth_authorization_header" 
					<?php checked( '1' === $currentapp['send_headers'] || 1 === $currentapp['send_headers'] ); ?>
					value="1">Set client credentials in Header<span style="padding:0px 0px 0px 8px;"></span>
					<input type="checkbox" class="mo_table_textbox" name="mo_oauth_body"
					<?php checked( '1' === $currentapp['send_body'] || 1 === $currentapp['send_body'] ); ?>
					value="1">Set client credentials in Body<div style="padding:5px;"></div></td>
				</tr>

			<tr>
				<td><strong>Group User Info Endpoint:</strong><br>&emsp;<font color="#FF0000"><small><a href="admin.php?page=mo_oauth_settings&tab=licensing" target="_blank" rel="noopener noreferrer">[PREMIUM]</a></small></font></td>
				<td><input class="mo_table_textbox" type="text" value="" disabled></td>
			</tr>
			<tr>
				<td><strong>JWKS URL:</strong><br>&emsp;<font color="#FF0000"><small><a href="admin.php?page=mo_oauth_settings&tab=licensing" target="_blank" rel="noopener noreferrer">[PREMIUM]</a></small></font></td>
				<td><input class="mo_table_textbox" type="text" value="" disabled></td>
			</tr>
			<tr>
				<td><strong>Login Button:</strong></td>
				<td><div style="padding:5px;"></div><input type="checkbox" name="mo_oauth_show_on_login_page" value ="1" 
				<?php
				if ( isset( $currentapp['show_on_login_page'] ) ) {
					if ( 1 === $currentapp['show_on_login_page'] ) {
						echo 'checked';
					}
				};
				?>
				/>Show on login page [WordPress Dashboard]</td>
			</tr>
			<tr>
				<td><br></td>
				<td><br></td>
			</tr>

			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" value="Save settings" class="button button-primary button-large" />
					<!-- 
					<?php
					if ( $is_other_app ) {
						?>
						-->
						<input id="mo_oauth_test_configuration" type="button" name="button" value="Test Configuration" class="button button-primary button-large" onclick="mocognito_testConfiguration()" />
					<!-- <?php } ?> -->
				</td>
			</tr>
			<tr>
				<td><strong>Note:</strong></td>
				<td colspan="2">
					<b>Please configure <a href='admin.php?page=mo_oauth_settings&tab=attributemapping'>Attribute Mapping</a> before trying Single Sign-On.</b>
				</td>
			</tr>
		</table>
		</form>
		</div>
		</div>
		<?php if ( $is_other_app ) { ?>
		<script>
		function mocognito_testConfiguration(){
			var mo_oauth_app_name = jQuery("#mo_oauth_app_nameid").val();
			var myWindow = window.open('<?php echo esc_url( site_url() ); ?>' + '/?option=testattrmappingconfig&app='+mo_oauth_app_name, "Test Attribute Configuration", "width=600, height=600");
			try{
				while(1) {
					if(myWindow.closed()) {
						$(document).trigger("config_tested");
						break;
					} else {continue;}
				}
			} catch(err) {
				console.error(err);
			}
		}

		function mocognito_showClientSecret(){
			var field = document.getElementById("mo_oauth_client_secret");
			var show_button = document.getElementById("show_button");
			if(field.type == "password"){
				field.type = "text";
				show_button.className = "fa fa-eye-slash";
			}
			else{
				field.type = "password";
				show_button.className = "fa fa-eye";
			}
		}
		</script>
			<?php
		}
		mocognito_grant_type_settings();
}
