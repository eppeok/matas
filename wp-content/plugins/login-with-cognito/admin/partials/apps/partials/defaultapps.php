<?php
/**
 * MINIORANGE Admin Apps
 *
 * @package    Admin\Apps
 */

/**
 * Show default apps UI
 */
function mocognito_show_default_apps() { ?>

	<hr />
	<h4>Pre-Configured Application<div class="mo-oauth-tooltip">&#x1F6C8;<div class="mo-oauth-tooltip-text mo-tt-right">By selecting pre-configured application, the configuration would already be half-done!</div> </div></h4>
	<ul id="mo_oauth_client_default_apps">
		<div id="mo_oauth_client_searchable_apps">
		<?php
			$defaultappsjson = wp_json_file_decode( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'defaultapps.json' );
			$custom_apps     = array();
		foreach ( $defaultappsjson as $app_id => $application ) {
			if ( 'other' === $app_id || 'openidconnect' === $app_id ) {
				$custom_apps[ $app_id ] = $application;
				continue;
			}
			echo '<li data-appid="' . esc_attr( $app_id ) . '"><a ' . ( 'cognito' === $app_id ? 'id=vip-default-app' : '' ) . ' href="#"><img class="mo_oauth_client_default_app_icon" src="' . esc_url( plugins_url( '../images/' . $application->image, __FILE__ ) ) . '"><br>' . esc_attr( $application->label ) . '</a></li>';
		}
		?>
		</div>
		<div id="mo_oauth_client_search_res"></div>
		<hr>
		<h4>Custom Applications</h4>
		<div id="mo_oauth_client_custom_apps">
			<?php
			foreach ( $custom_apps as $app_id => $application ) {
				echo '<li data-appid="' . esc_attr( $app_id ) . '"><a href="#"><img class="mo_oauth_client_default_app_icon" src="' . esc_url( plugins_url( '../images/' . $application->image, __FILE__ ) ) . '"><br>' . esc_attr( $application->label ) . '</a></li>';
			}
			?>
		</div>
	</ul>
	<script>

		jQuery("#mo_oauth_client_default_apps li").click(function(){
			var appId = jQuery(this).data("appid");
				window.location.href += "&appId="+appId;
		});

	</script>

<?php }

/**
 * Get App with AppId
 *
 * @param CurrentAppID $current_app_id for current app ID.
 */
function mocognito_get_app( $current_app_id ) {
	$defaultappsjson = wp_json_file_decode( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'defaultapps.json' );
	foreach ( $defaultappsjson as $app_id => $application ) {
		if ( $app_id === $current_app_id ) {
			$application->app_id = $app_id;
			return $application;
		}
	}
	return false;
}



?>
