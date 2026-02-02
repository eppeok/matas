<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin\CognitoIntegrator
 */

/**
 * Admin Cognito Integration
 */
class MoCognito_Admin_Cognito_Integration {


	/**
	 * Page CSS
	 */
	public static function page_css() {         ?>
		<style>
			.mo_cog_int_page_header{
				font-weight: 70px;
				font-size: 20px;
				margin:10px 10px 20px 0px;
			}
			.mo_cognito_login_cog_int_page_layout{
				/* background-color:red; */
			}
			.mo_cognito_login_cog_int_page_layout{
				padding: 30px;
				padding-top: 15px;
				background-color: #f5f5f5;
				margin:10px 10px 10px 0px;
				font-weight:700;
			}
			.mo_cognito_login_integrator_cards_container{
				display: flex;
				flex-wrap: wrap;
				margin-top:20px;
			}
			.mo_cognito_login_cg_int_cards{
				flex: 0 46%;
				box-sizing:border-box; 
				background-color:white;
				margin:5px; 
				border-radius:5px;
				padding:20px 20px 5px 20px;
				/* box-shadow:2px 2px 4px #000000; */
				box-shadow: inset 2px 2px 2px 0 rgb(255 255 255 / 50%), 7px 7px 20px 0 rgb(0 0 0 / 10%), 4px 4px 5px 0 rgb(0 0 0 / 10%);
			}
			.mo_cog_int_card_heading{
				font-weight:strong;
				font-size:17px;
				line-height:normal;
			}
			.mo_cog_int_card_discription{
				font-weight:normal;
				font-size:15px;
				line-height:25px;
			}
			.mo_cog_int_page_footer{
				margin:20px 
			}
			.mo_cognito_login_integrator_container{
				display:flex;
				flex-wrap:wrap;
			}
			.mo_cog_int_page_footer{
				background-color:white;
				padding:24px;
				margin:5px 47px 20px 6px;
				border-radius:5px;
				box-shadow: inset 2px 2px 2px 0 rgb(255 255 255 / 50%), 7px 7px 20px 0 rgb(0 0 0 / 10%), 4px 4px 5px 0 rgb(0 0 0 / 10%);
			}
			.mo_cog_int_card_footer_container{
				/* display:flex;
				flex-wrap:wrap; */
				margin-top:20px;
				margin-bottom:5px;
				/* flex-direction:row; */
			}
			.mo_cog_int_card_learn_more{
				flex: 0 46%;
			}
			.mo_cog_int_card_footer_image{
				flex: 0 46%;
			}
			.mo_cog_int_card_1_footer_image img:first-child{
				width:25%;
				height:auto;
					/* margin:0px 0px 0px 121px; */
				display:inline;
				float:right;
			}
			.mo_cog_int_card_2_footer_image img:first-child{
				width:16%;
				height:auto;
				margin:36px 7px 0px 117px;
				display:inline;
				float:right;
			}
			.mo_cog_int_card_3_footer_image img:first-child{
				width:19%;
				height:auto;
				margin:35px 16px 0px 121px;
				display:inline;
				float:right;
			}
			.mo_cog_int_card_4_footer_image img:first-child{
				width:25%;
				height:auto;
				margin:5px 0px 0px 121px;
				display:inline;
				float:right;
			}
			.mo_cog_int_card_1_learn_more{
				margin:27px 0px 0px 3px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_card_2_learn_more{
				margin:52px 0px 0px 4px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_card_3_learn_more{
				margin:62px 0px 0px 3px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_card_4_learn_more{
				margin:37px 0px 0px 3px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_card_5_learn_more{
				margin:37px 0px 0px 3px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_card_6_learn_more{
				margin:56px 0px 0px 3px;
				color:red;
				font-size:1.2rem;
				font-weight:600;
				opacity:.8;
				display:inline;
				float:left;
			}
			.mo_cog_int_page_footer{
				font-size:16px;
			}
		</style>
		<?php

	}

	/**
	 * Cognito Integrator Page
	 */
	public static function show_cognito_integrator_page() {
		self::page_css();
		?>
		<!-- html code -->
		<div class="mo_cognito_login_cog_int_page_layout">
			<div>
				<div>
					<div class="mo_cognito_login_cog_int_page_box">
						<div class="mo_cog_int_page_header">
							Checkout all usecases of our WordPress Cognito Integration Plugin
						</div>
						<div>
						<hr style="height:5px; background-color: #1f3668;margin-top: 9px;border-radius: 30px;">
						</div>
						<div class="mo_cognito_login_integrator_container">
							<div class="mo_cognito_login_integrator_cards_container">
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
										Register User in Amazon Cognito pools from the WordPress website
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
										Want to automatically register users into Cognito Pools from WordPress plugin forms and skip all the tedious process? This can be seamlessly achieved by using our WP Cognito Integration plugin.
									</div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_1_learn_more">
											<a href="https://plugins.miniorange.com/wordpress-cognito-user-management-with-cognito-integrator" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_1_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/cognito.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
										Login users into WordPress without redirecting them to Cognito
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
										Want to use custom login forms? Also don't want to redirect users to Cognito at the SSO event. This can easily be integrated by using our WP Cognito Integration plugin.                                    </div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_2_learn_more">
											<a href="https://plugins.miniorange.com/wordpress-login-with-cognito-using-wordpress-custom-forms" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_2_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/user.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
										Sync User Memberships informations and status to AWS Cognito Pool with WordPress Cognito Integration
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
									On WordPress when users purchase any membership, the status of memberships like upgrade, downgrade, renewal, and expiration can be synced to the user profile in AWS Cognito.
									Also based on Memberships the user can be added to different Cognito User Pools. (Like Users with GOLD membership can be added to Pool A, members with SILVER membership to Pool B while free members to Pool C.)									</div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_5_learn_more">
											<a href="https://plugins.miniorange.com/wordpress-user-membership-sync-with-cognito-pool" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_4_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/membership.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
									Authenticate users with Cognito using WordPress Custom Login Code
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
									There may be situations where the custom forms plugin has limitations and you need your own code to design the login and registration forms, handle the form submit event, and manage the backend flow to authenticate users via Cognito credentials. 
									In these cases, having the ability to write custom code can be useful. By creating your own code, you have full control over the form design and authentication functionality.

									</div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_6_learn_more">
											<a href="https://plugins.miniorange.com/cognito-login-registration-with-wordpress-custom-login-code" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_4_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/programming.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
										Have multiple WordPress sites, choose Cognito to maintain users
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
										Have multiple WordPress websites and don't want to have a headache maintaining separate login credentials. 
										Cognito integrator is a one-stop solution allowing you to login into multiple sites using your Cognito Credentials. 
										This will need customization based on your registration form and how many sites you are using Cognito login for.
									</div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_3_learn_more">
											<a href="https://plugins.miniorange.com/aws-cognito-wordpress-single-sign-on-integration" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_3_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/internet.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
								<div class="mo_cognito_login_cg_int_cards">
									<div class="mo_cog_int_card_heading">
										User verification in Cognito when a user creates an account from the woocommerce checkout page.
										<hr style="height:2px; background-color: #f0f0f1;margin-top: 9px;border-radius: 30px;">
									</div>
									<div class="mo_cog_int_card_discription">
										Using the Cognito integrator you can automatically create users in Cognito when they try to create an account on the Woocommerce checkout page. 
										This feature also includes verifying the users via OTP. The user would need to enter the OTP in the pop-up window which comes up when the user clicks on the Place Order button.
									</div>
									<div class="mo_cog_int_card_footer_container">
										<div class="mo_cog_int_card_4_learn_more">
											<a href="https://plugins.miniorange.com/cognito-otp-authentication-in-woocommerce-checkout" target="_blank">Learn more</a>
										</div>
										<div class="mo_cog_int_card_4_footer_image">
											<img src=<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/woo-3.png' ); ?> alt="Cognito image">
										</div>
									</div>
								</div>
							</div>
							<div class="mo_cog_int_page_footer">
								If you want custom features in the plugin, just drop an email at <a href = "mailto: oauthsupport@xecurify.com ">oauthsupport@xecurify.com </a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
