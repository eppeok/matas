<?php
/**
 * MINIORANGE Admin Apps
 *
 * @package    Admin\Apps
 */

/**
 * Include default apps
 */
require_once 'defaultapps.php';

/**
 * App list page
 */
function mocognito_applist_page() {
	?>
	<style>
		.tableborder {
			border-collapse: collapse;
			width: 100%;
			border-color:#eee;
		}

		.tableborder th, .tableborder td {
			text-align: left;
			padding: 8px;
			border-color:#eee;
		}

		.tableborder tr:nth-child(even){background-color: #f2f2f2}
	</style>
	<div id="mo_oauth_app_list" class="mo_table_layout">
	<?php

	if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] ) {
		if ( isset( $_GET['app'] ) && check_admin_referer( 'delete_' . sanitize_text_field( wp_unslash( $_GET['app'] ) ) ) ) {
			mocognito_delete_app( sanitize_text_field( wp_unslash( $_GET['app'] ) ) );
			if ( 'CognitoApp' === $_GET['app'] ) {
				?>
					<script>
						let url = window.location.href;
						url = url.split("&action=delete&app=CognitoApp")[0];
						window.location.replace(url);
					</script>
				<?php
				exit();
			}
		}
	} elseif ( isset( $_GET['action'] ) && 'instructions' === $_GET['action'] ) {
		if ( isset( $_GET['appId'] ) ) {
			MoCognito_Admin_Admin_Guides::instructions( sanitize_text_field( wp_unslash( $_GET['appId'] ) ) );
		}
	}

	if ( isset( $_GET['action'] ) && 'add' === $_GET['action'] ) {
		MoCognito_Admin_Apps::add_app();
	} elseif ( isset( $_GET['action'] ) && 'update' === $_GET['action'] ) {
		if ( isset( $_GET['app'] ) ) {
			MoCognito_Admin_Apps::update_app( sanitize_text_field( wp_unslash( $_GET['app'] ) ) );
		}
	} elseif ( get_option( 'mo_oauth_apps_list' ) ) {
		$appslist = get_option( 'mo_oauth_apps_list' );
		if ( count( $appslist ) > 0 ) {
			echo "<br><a href='#'><button disabled style='float:right'>Add Application</button></a>";
		} else {
			echo "<br><a href='admin.php?page=mo_oauth_settings&action=add'><button style='float:right'>Add Application</button></a>";
		}
		echo '<h3>Applications List</h3>';
		if ( is_array( $appslist ) && count( $appslist ) > 0 ) {
			echo "<p style='color:#a94442;background-color:#f2dede;border-color:#ebccd1;border-radius:5px;padding:12px'>You can only add 1 application with free version. Upgrade to <a href='admin.php?page=mo_oauth_settings&tab=licensing'><b>enterprise</b></a> to add more.</p>";
		}
		echo "<table class='tableborder'>";
		echo '<tr><th><b>Name</b></th><th>Action</th></tr>';
		foreach ( $appslist as $key => $app ) {
			$currentapp     = $app;
			$delete_app_url = wp_nonce_url( 'admin.php?page=mo_oauth_settings&tab=config&action=delete&app=' . esc_attr( $key ), 'delete_' . esc_attr( $key ) );
			echo '<tr><td>' . esc_attr( $key ), ' (', esc_attr( $currentapp['apptype'] ), ') ' . "</td><td><a href='admin.php?page=mo_oauth_settings&tab=config&action=update&app=" . esc_attr( $key ) . "'>Edit Application</a> | <a href='admin.php?page=mo_oauth_settings&tab=attributemapping&app=" . esc_attr( $key ) . "#attribute-mapping'>Attribute Mapping</a> | <a href='admin.php?page=mo_oauth_settings&tab=attributemapping&app=" . esc_attr( $key ) . "#role-mapping'>Role Mapping</a> | <a onclick='return confirm(\"Are you sure you want to delete this item?\")' href='" . esc_url( $delete_app_url ) . "'>Delete</a> | ";
			if ( isset( $_GET['action'] ) ) {
				if ( 'instructions' === $_GET['action'] ) {
					echo "<a href='admin.php?page=mo_oauth_settings&tab=config'>Hide Instructions</a></td></tr>";
				}
			} else {
				$instructions_app_url = wp_nonce_url( 'admin.php?page=mo_oauth_settings&tab=config&action=instructions&appId=' . ( isset( $app['appId'] ) ? esc_attr( $app['appId'] ) : '' ), 'instructions_' . ( isset( $app['appId'] ) ? esc_attr( $app['appId'] ) : '' ) );
				echo "<a href='" . esc_url( $instructions_app_url ) . "'>How to Configure?</a></td></tr>";
			}
		}
		echo '</table>';
		echo '<br><br>';

	} else {
		MoCognito_Admin_Apps::add_app();
	}
	?>
		</div>
	<?php
}

/**
 * Delete app
 *
 * @param AppName $appname application name.
 */
function mocognito_delete_app( $appname ) {
	$appslist = get_option( 'mo_oauth_apps_list' );
	if ( ! is_array( $appslist ) || empty( $appslist ) ) {
		return;
	}
	foreach ( $appslist as $key => $app ) {
		if ( $appname === $key ) {
			if ( 'wso2' === $appslist[ $appname ]['appId'] ) {
				delete_option( 'mo_oauth_client_custom_token_endpoint_no_csecret' );
			}
			unset( $appslist[ $key ] );
			delete_option( 'mo_oauth_client_disable_authorization_header' );
			delete_option( 'mo_oauth_attr_name_list' );
			delete_option( 'mo_oauth_apps_list' );
			$notices = get_option( 'mo_oauth_client_notice_messages' );
			if ( isset( $notices['attr_mapp_notice'] ) ) {
				unset( $notices['attr_mapp_notice'] );
				update_option( 'mo_oauth_client_notice_messages', $notices );
			}
		}
	}
	update_option( 'mo_oauth_apps_list', $appslist );
	?>
		<script>
			window.location.href = "<?php echo esc_url( admin_url( 'admin.php?page=mo_oauth_settings&tab=config' ) ); ?>";
		</script>
		<?php
}
