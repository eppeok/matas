<?php
// SMW Webinar - Admin Settings

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// fallback version if not defined elsewhere
if ( ! defined( 'SMW_VERSION' ) ) {
	define( 'SMW_VERSION', '1.0' );
}

// Set up settings defaults
function smw_woo_set_options (){
	$defaults = array(
		'login_restrict' => 0,
		'randomGenerator_restrict' => 0,
		'producttab' => 1,
		'winner_noto_others' => 1,
		'login_button_text' => 'Login',
		'enable_notification' => 1,
		'video_show' => 1,
		'bootstrap_show' => 1,
		'winner' => 0,
		'id' => '',
		'twilio_sid' => '',
		'twilio_token' => '',
		'twilio_from' => '',
		'new_product_notification_sub' => '',
		'new_product_notification' => '',
		'wwinner_notification_sub' => '',
		'wwinner_notification' => '',
		'winner_noti_others_sub' => '',
		'winner_noti_others' => '',
		'ezoom_notification_sub' => '',
		'ezoom_notification' => '',
		'sms_new_product_notification' => '',
		'sms_wwinner_notification' => '',
		'sms_winner_noti_others' => '',
		'sms_zoom_notification' => ''
	);
	add_option('smw_woo_settings', $defaults);
}
// Clean up on uninstall (optional)
// function smw_woo_deactivate(){
// 	delete_option('smw_woo_settings');
// }


// Render the settings page
class smw_woo_settings_page {
	// Holds the values to be used in the fields callbacks
	private $options;
			
	// Start up
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}
			
	// Add settings page
	public function add_plugin_page() {
		add_menu_page( 'SMW Webinar', 'SMW Webinar', 'manage_options', 'smwticket', array( $this,'create_admin_page' ), 'dashicons-tickets-alt' );
	}
						
	// Options page callback
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'smw_woo_settings' );
		if(!$this->options){
			smw_woo_set_options();
			$this->options = get_option( 'smw_woo_settings' );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( 'SMW Webinar Settings' ); ?></h1>

			<?php settings_errors(); ?>

			<?php $this->print_inline_assets(); // inline styles + tab JS (guaranteed to print) ?>

			<!-- Tabs -->
			<h2 class="nav-tab-wrapper">
				<a href="#smw-tab-basic" class="nav-tab nav-tab-active">Basic</a>
				<a href="#smw-tab-twilio" class="nav-tab">Twilio</a>
				<a href="#smw-tab-email" class="nav-tab">Email Templates</a>
				<a href="#smw-tab-sms" class="nav-tab">SMS Content</a>
			</h2>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'smw_woo_settings' ); // nonce, option group
				?>
				<div id="smw-tab-basic" class="smw-tab-panel">
					<?php do_settings_sections( 'smw_woo-settings-behaviour' ); ?>
				</div>

				<div id="smw-tab-twilio" class="smw-tab-panel" style="display:none;">
					<?php do_settings_sections( 'smw_woo-settings-twilio' ); ?>
				</div>

				<div id="smw-tab-email" class="smw-tab-panel" style="display:none;">
					<?php do_settings_sections( 'smw_woo-settings-email' ); ?>
				</div>

				<div id="smw-tab-sms" class="smw-tab-panel" style="display:none;">
					<?php do_settings_sections( 'smw_woo-settings-sms' ); ?>
				</div>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
			
	// Register and add settings
	public function page_init() {
		register_setting(
			'smw_woo_settings', // Option group
			'smw_woo_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);
		
        // Sections - unique page slugs so we can render them in separate tab containers
		add_settings_section(
			'smw_woo_settings_behaviour',
			'SMW Webinar Basic Settings',
			array( $this, 'smw_woo_settings_behaviour_header' ),
			'smw_woo-settings-behaviour'
		);

		add_settings_section(
			'smw_woo_settings_twilio',
			'Twilio Settings',
			array( $this, 'smw_woo_settings_twilio_header' ),
			'smw_woo-settings-twilio'
		);
		
		add_settings_section(
			'smw_woo_settings_email_template',
			'Email Template',
			array( $this, 'smw_woo_settings_email_template_header' ),
			'smw_woo-settings-email'
		);
		
		add_settings_section(
			'smw_woo_settings_sms_content',
			'SMS Content',
			array( $this, 'smw_woo_settings_sms_content_header' ),
			'smw_woo-settings-sms'
		);
        
		// Behaviour Fields (page = smw_woo-settings-behaviour)
		add_settings_field('randomGenerator_restrict','Random Seat Generator',array( $this, 'randomGenerator_restrict_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('winner_noto_others','Winner Notification to Others',array( $this, 'winner_noto_others_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('producttab','Enable Participant Tab',array( $this, 'producttab_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('login_restrict','Login Restriction',array( $this, 'login_restrict_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('login_button_text','Login Button Text',array( $this, 'login_button_text_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('winner','Enable Winner Selection',array( $this, 'winner_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('enable_notification','Enable Notification',array( $this, 'enable_notification_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('video_show','Show Video',array( $this, 'video_show_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');
		add_settings_field('bootstrap_show','Add Bootstrap To Winner Page',array( $this, 'bootstrap_show_callback' ),'smw_woo-settings-behaviour','smw_woo_settings_behaviour');

		// Twilio Fields (page = smw_woo-settings-twilio)
		add_settings_field('twilio_sid','SID',array( $this, 'twilio_sid_callback' ),'smw_woo-settings-twilio','smw_woo_settings_twilio');
		add_settings_field('twilio_token','Token',array( $this, 'twilio_token_callback' ),'smw_woo-settings-twilio','smw_woo_settings_twilio');
		add_settings_field('twilio_from','From Number',array( $this, 'twilio_from_callback' ),'smw_woo-settings-twilio','smw_woo_settings_twilio');

		// Email template fields (page = smw_woo-settings-email)
		add_settings_field('new_product_notification_sub','New Product Notification<br>(Subject)',array( $this, 'new_product_notification_sub_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('new_product_notification','New Product Notification<br>(Email Body)',array( $this, 'new_product_notification_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('wwinner_notification_sub','Winner Notification to Winner (Subject)',array( $this, 'wwinner_notification_sub_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('wwinner_notification','Winner Notification to Winner (Email Body)',array( $this, 'wwinner_notification_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('winner_noti_others_sub','Winner Notification to Others (Subject)',array( $this, 'winner_noti_others_sub_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('winner_noti_others','Winner Notification to Others (Email Body)',array( $this, 'winner_noti_others_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('ezoom_notification_sub','Zoom Notification <br>(Subject)',array( $this, 'ezoom_notification_sub_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');
		add_settings_field('ezoom_notification','Zoom Notification <br>(Email Body)',array( $this, 'ezoom_notification_callback' ),'smw_woo-settings-email','smw_woo_settings_email_template');

		// SMS fields (page = smw_woo-settings-sms)
		add_settings_field('sms_new_product_notification','New Product Notification<br>(SMS)',array( $this, 'sms_new_product_notification_callback' ),'smw_woo-settings-sms','smw_woo_settings_sms_content');
		add_settings_field('sms_wwinner_notification','Winner Notification to Winner (SMS)',array( $this, 'sms_wwinner_notification_callback' ),'smw_woo-settings-sms','smw_woo_settings_sms_content');
		add_settings_field('sms_winner_noti_others','Winner Notification to Others (SMS)',array( $this, 'sms_winner_noti_others_callback' ),'smw_woo-settings-sms','smw_woo_settings_sms_content');
		add_settings_field('sms_zoom_notification','Zoom Notification<br>(SMS)',array( $this, 'sms_zoom_notification_callback' ),'smw_woo-settings-sms','smw_woo_settings_sms_content');
	}


	// Sanitize each setting field as needed -  @param array $input Contains all settings fields as array keys
	public function sanitize( $input ) {
		$clean = array();
		// sanitize booleans / small strings
		$clean['randomGenerator_restrict'] = isset($input['randomGenerator_restrict']) ? intval($input['randomGenerator_restrict']) : 0;
		$clean['winner_noto_others'] = isset($input['winner_noto_others']) ? intval($input['winner_noto_others']) : 0;
		$clean['producttab'] = isset($input['producttab']) ? intval($input['producttab']) : 0;
		$clean['login_restrict'] = isset($input['login_restrict']) ? intval($input['login_restrict']) : 0;
		$clean['login_button_text'] = isset($input['login_button_text']) ? sanitize_text_field($input['login_button_text']) : '';
		$clean['winner'] = isset($input['winner']) ? intval($input['winner']) : 0;
		$clean['enable_notification'] = isset($input['enable_notification']) ? intval($input['enable_notification']) : 0;
		$clean['video_show'] = isset($input['video_show']) ? intval($input['video_show']) : 0;
		$clean['bootstrap_show'] = isset($input['bootstrap_show']) ? intval($input['bootstrap_show']) : 0;

		$clean['twilio_sid'] = isset($input['twilio_sid']) ? sanitize_text_field($input['twilio_sid']) : '';
		$clean['twilio_token'] = isset($input['twilio_token']) ? sanitize_text_field($input['twilio_token']) : '';
		$clean['twilio_from'] = isset($input['twilio_from']) ? sanitize_text_field($input['twilio_from']) : '';

		$clean['new_product_notification_sub'] = isset($input['new_product_notification_sub']) ? sanitize_text_field($input['new_product_notification_sub']) : '';
		// allow HTML for email bodies — sanitize using wp_kses_post
		$clean['new_product_notification'] = isset($input['new_product_notification']) ? wp_kses_post($input['new_product_notification']) : '';
		$clean['wwinner_notification_sub'] = isset($input['wwinner_notification_sub']) ? sanitize_text_field($input['wwinner_notification_sub']) : '';
		$clean['wwinner_notification'] = isset($input['wwinner_notification']) ? wp_kses_post($input['wwinner_notification']) : '';
		$clean['winner_noti_others_sub'] = isset($input['winner_noti_others_sub']) ? sanitize_text_field($input['winner_noti_others_sub']) : '';
		$clean['winner_noti_others'] = isset($input['winner_noti_others']) ? wp_kses_post($input['winner_noti_others']) : '';
		$clean['ezoom_notification_sub'] = isset($input['ezoom_notification_sub']) ? sanitize_text_field($input['ezoom_notification_sub']) : '';
		$clean['ezoom_notification'] = isset($input['ezoom_notification']) ? wp_kses_post($input['ezoom_notification']) : '';

		$clean['sms_new_product_notification'] = isset($input['sms_new_product_notification']) ? sanitize_textarea_field($input['sms_new_product_notification']) : '';
		$clean['sms_wwinner_notification'] = isset($input['sms_wwinner_notification']) ? sanitize_textarea_field($input['sms_wwinner_notification']) : '';
		$clean['sms_winner_noti_others'] = isset($input['sms_winner_noti_others']) ? sanitize_textarea_field($input['sms_winner_noti_others']) : '';
		$clean['sms_zoom_notification'] = isset($input['sms_zoom_notification']) ? sanitize_textarea_field($input['sms_zoom_notification']) : '';

		return $clean;
	}

	// Print the Section text
	public function smw_woo_settings_behaviour_header() {
            echo '<p>'.__('Settings for SMW Webinar.', 'smw_woo-settings').'</p>';
	}
	public function smw_woo_settings_twilio_header() {
            echo '<p>'.__('Settings for Twilio.', 'smw_woo-settings').'</p>';
	}
	public function smw_woo_settings_email_template_header() {
            echo '<p>'.__('Set Your Email Template here.', 'smw_woo-settings').'</p>';
	}
	public function smw_woo_settings_sms_content_header() {
            echo '<p>'.__('Set Your SMS Text here.', 'smw_woo-settings').'</p>';
	}

	/* --- field callbacks --- */
	public function randomGenerator_restrict_callback(){
		$val = isset( $this->options['randomGenerator_restrict'] ) ? intval($this->options['randomGenerator_restrict']) : 0;
		echo '<select id="randomGenerator_restrict" name="smw_woo_settings[randomGenerator_restrict]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function winner_noto_others_callback(){
		$val = isset( $this->options['winner_noto_others'] ) ? intval($this->options['winner_noto_others']) : 0;
		echo '<select id="winner_noto_others" name="smw_woo_settings[winner_noto_others]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function producttab_callback() {
		$val = isset( $this->options['producttab'] ) ? intval($this->options['producttab']) : 0;
		echo '<select id="producttab" name="smw_woo_settings[producttab]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function login_restrict_callback() {
		$val = isset( $this->options['login_restrict'] ) ? intval($this->options['login_restrict']) : 0;
		echo '<select id="login_restrict" name="smw_woo_settings[login_restrict]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function login_button_text_callback(){
		$val = isset($this->options['login_button_text']) ? $this->options['login_button_text'] : '';
		echo '<input type="text" id="login_button_text" name="smw_woo_settings[login_button_text]" class="widefat" value="'.esc_attr($val).'" />';
	}
	public function winner_callback(){
		$val = isset( $this->options['winner'] ) ? intval($this->options['winner']) : 0;
		echo '<select id="winner" name="smw_woo_settings[winner]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function enable_notification_callback(){
		$val = isset( $this->options['enable_notification'] ) ? intval($this->options['enable_notification']) : 0;
		echo '<select id="enable_notification" name="smw_woo_settings[enable_notification]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}
	public function video_show_callback(){
		$val = isset( $this->options['video_show'] ) ? intval($this->options['video_show']) : 0;
		echo '<select id="video_show" name="smw_woo_settings[video_show]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>To All</option>
			<option value="0"'.selected(0,$val,false).'>Purchased Customer</option>
		</select>';
	}
	public function bootstrap_show_callback(){
		$val = isset( $this->options['bootstrap_show'] ) ? intval($this->options['bootstrap_show']) : 0;
		echo '<select id="bootstrap_show" name="smw_woo_settings[bootstrap_show]" class="regular-select">
			<option value="1"'.selected(1,$val,false).'>Yes</option>
			<option value="0"'.selected(0,$val,false).'>No</option>
		</select>';
	}

	public function twilio_sid_callback(){
		$val = isset($this->options['twilio_sid']) ? $this->options['twilio_sid'] : '';
		echo '<input type="text" id="twilio_sid" name="smw_woo_settings[twilio_sid]" class="regular-text" value="'.esc_attr($val).'" />';
	}
	public function twilio_token_callback(){
		$val = isset($this->options['twilio_token']) ? $this->options['twilio_token'] : '';
		echo '<input type="text" id="twilio_token" name="smw_woo_settings[twilio_token]" class="regular-text" value="'.esc_attr($val).'" />';
	}
	public function twilio_from_callback(){
		$val = isset($this->options['twilio_from']) ? $this->options['twilio_from'] : '';
		echo '<input type="text" id="twilio_from" name="smw_woo_settings[twilio_from]" class="regular-text" value="'.esc_attr($val).'" />';
	}

	//email template
	public function new_product_notification_sub_callback(){
		$val = isset($this->options['new_product_notification_sub']) ? $this->options['new_product_notification_sub'] : '';
		echo '<p class="description">Enter the subject for the new product notification. You can use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, and <b>%url%</b>.</p>';
		echo '<input type="text" id="new_product_notification_sub" name="smw_woo_settings[new_product_notification_sub]" class="widefat" value="'.esc_attr($val).'" />';
	}
	public function new_product_notification_callback() {
		$content = isset($this->options['new_product_notification']) ? $this->options['new_product_notification'] : '';
		$editor_id = 'new_product_notification';
		echo '<p class="description">Enter the email body for the new product notification. You can use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, and <b>%url%</b>.</p>';
		$settings = array(
			'textarea_name' => 'smw_woo_settings[new_product_notification]',
			'textarea_rows' => 10,
			'teeny' => false,
		);
		wp_editor($content, $editor_id, $settings);
	}
	public function wwinner_notification_sub_callback(){
		$val = isset($this->options['wwinner_notification_sub']) ? $this->options['wwinner_notification_sub'] : '';
		echo '<p class="description">Enter the subject for the winner notification to winner. Use placeholders like <b>%first_name%</b>, <b>%last_name%</b>, <b>%product_name%</b>, <b>%screen_name%</b> and <b>%phone%</b>.</p>';
		echo '<input type="text" id="wwinner_notification_sub" name="smw_woo_settings[wwinner_notification_sub]" class="widefat" value="'.esc_attr($val).'" />';
	}
	public function wwinner_notification_callback() {
		$content1 = isset($this->options['wwinner_notification']) ? $this->options['wwinner_notification'] : '';
		$editor_id1 = 'wwinner_notification';
		echo '<p class="description">Enter the email body for the winner notification to winner. Use placeholders like <b>%first_name%</b>, <b>%last_name%</b>, <b>%product_name%</b>, <b>%screen_name%</b> and <b>%phone%</b>.</p>';
		$settings1 = array(
			'textarea_name' => 'smw_woo_settings[wwinner_notification]',
			'textarea_rows' => 10,
			'teeny' => false,
		);
		wp_editor($content1, $editor_id1, $settings1);
	}
	public function winner_noti_others_sub_callback(){
		$val = isset($this->options['winner_noti_others_sub']) ? $this->options['winner_noti_others_sub'] : '';
		echo '<p class="description">Enter the subject for the winner notification to others. Use placeholders like <b>%first_name%</b>, <b>%last_name%</b>, <b>%product_name%</b>, <b>%screen_name%</b> and <b>%phone%</b>.</p>';
		echo '<input type="text" id="winner_noti_others_sub" name="smw_woo_settings[winner_noti_others_sub]" class="widefat" value="'.esc_attr($val).'" />';
	}
	public function winner_noti_others_callback() {
		$content2 = isset($this->options['winner_noti_others']) ? $this->options['winner_noti_others'] : '';
		$editor_id2 = 'winner_noti_others';
		echo '<p class="description">Enter the email body for the winner notification to others. Use placeholders like <b>%first_name%</b>, <b>%last_name%</b>, <b>%product_name%</b>, <b>%screen_name%</b> and <b>%phone%</b>.</p>';
		$settings2 = array(
			'textarea_name' => 'smw_woo_settings[winner_noti_others]',
			'textarea_rows' => 10,
			'teeny' => false,
		);
		wp_editor($content2, $editor_id2, $settings2);
	}
	public function ezoom_notification_sub_callback(){
		$val = isset($this->options['ezoom_notification_sub']) ? $this->options['ezoom_notification_sub'] : '';
		echo '<p class="description">Enter the subject for zoom notifications. Use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, <b>%event_url%</b> and <b>%event_time%</b>.</p>';
		echo '<input type="text" id="ezoom_notification_sub" name="smw_woo_settings[ezoom_notification_sub]" class="widefat" value="'.esc_attr($val).'" />';
	}
	public function ezoom_notification_callback() {
		$content3 = isset($this->options['ezoom_notification']) ? $this->options['ezoom_notification'] : '';
		$editor_id3 = 'ezoom_notification';
		echo '<p class="description">Enter the email body for Zoom notifications. Use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, <b>%event_url%</b> and <b>%event_time%</b>.</p>';
		$settings3 = array(
			'textarea_name' => 'smw_woo_settings[ezoom_notification]',
			'textarea_rows' => 10,
			'teeny' => false,
		);
		wp_editor($content3, $editor_id3, $settings3);
	}	

	//SMS 
	public function sms_new_product_notification_callback(){
		$val = isset($this->options['sms_new_product_notification']) ? $this->options['sms_new_product_notification'] : '';
		echo '<p class="description">Enter the SMS Text for the new product notification. You can use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, and <b>%url%</b>.</p>';
		echo '<textarea id="sms_new_product_notification" name="smw_woo_settings[sms_new_product_notification]" rows="4" class="widefat">' . esc_textarea($val) . '</textarea>';
	}
	public function sms_wwinner_notification_callback(){
		$val = isset($this->options['sms_wwinner_notification']) ? $this->options['sms_wwinner_notification'] : '';
		echo '<p class="description">Enter the SMS Text for the winner notification to winner. Use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, and <b>%screen_name%</b>.</p>';
		echo '<textarea id="sms_wwinner_notification" name="smw_woo_settings[sms_wwinner_notification]" rows="4" class="widefat">' . esc_textarea($val) . '</textarea>';
	}
	public function sms_winner_noti_others_callback(){
		$val = isset($this->options['sms_winner_noti_others']) ? $this->options['sms_winner_noti_others'] : '';
		echo '<p class="description">Enter the SMS Text for the winner notification to others. Use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, and <b>%screen_name%</b>.</p>';
		echo '<textarea id="sms_winner_noti_others" name="smw_woo_settings[sms_winner_noti_others]" rows="4" class="widefat">' . esc_textarea($val) . '</textarea>';
	}
	public function sms_zoom_notification_callback(){
		$val = isset($this->options['sms_zoom_notification']) ? $this->options['sms_zoom_notification'] : '';
		echo '<p class="description">Enter the SMS Text for the Zoom notification. You can use placeholders like <b>%first_name%</b>, <b>%product_name%</b>, <b>%event_url%</b> and <b>%event_time%</b>.</p>';
		echo '<textarea id="sms_zoom_notification" name="smw_woo_settings[sms_zoom_notification]" rows="4" class="widefat">' . esc_textarea($val) . '</textarea>';
	}

	// Print inline CSS + JS for the tabs (guaranteed to be present on the page)
	private function print_inline_assets() {
		?>
		<style>
			.smw-tab-panel { background: #fff; border:1px solid #e5e5e5; padding:18px; margin-top:12px; }
			.smw-tab-panel .description { margin-bottom:8px; color:#666; }
			.smw-tab-panel .widefat, .smw-tab-panel .regular-text { max-width:100%; }
			.nav-tab-wrapper { margin-bottom: 0; }
			.smw-tab-panel .wp-editor-wrap { margin-top:8px; }
			/* ensure nav tabs look correct in WP admin */
			.nav-tab { cursor: pointer; }
		</style>
		<script>
		(function($){
			$(function(){
				// click handler for tabs
				$(".nav-tab").on("click", function(e){
					e.preventDefault();
					var $t = $(this);
					$(".nav-tab").removeClass("nav-tab-active");
					$t.addClass("nav-tab-active");

					var target = $t.attr("href");
					$(".smw-tab-panel").hide();
					$(target).show();

					// Try to refresh editors on the shown tab (if any)
					if ( typeof tinymce !== "undefined" ) {
						setTimeout(function(){
							if (typeof tinymce.editors !== "undefined") {
								for (var id in tinymce.editors) {
									if (tinymce.editors.hasOwnProperty(id)) {
										try {
											if (tinymce.editors[id].theme && tinymce.editors[id].theme.resizeTo) {
												tinymce.editors[id].theme.resizeTo('100%', tinymce.editors[id].getContainer().clientHeight || 300);
											}
										} catch (err) { /* ignore */ }
									}
								}
							}
						}, 120);
					}
				});

				// show first tab by default
				$(".nav-tab-wrapper .nav-tab").first().trigger("click");
			});
		})(jQuery);
		</script>
		<?php
	}

	// Enqueue admin CSS + JS (jQuery UI + timepicker addon) only on our page
	public function enqueue_admin_assets( $hook ) {
		// only load on our plugin page
		if ( $hook !== 'toplevel_page_smwticket' ) {
			return;
		}

		// ensure jQuery UI core + datepicker are available
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// enqueue plugin's jquery-ui styles & timepicker addon that ship with plugin assets
		// adjust paths if your assets are located elsewhere
		$css_ui = plugins_url( 'asset/css/jquery-ui.min.css', __FILE__ );
		$css_time = plugins_url( 'asset/css/jquery-ui-timepicker-addon.min.css', __FILE__ );
		$js_timepicker = plugins_url( 'asset/js/jquery-ui-timepicker-addon.min.js', __FILE__ );

		// styles
		wp_enqueue_style( 'smw-jquery-ui', $css_ui, array(), SMW_VERSION );
		wp_enqueue_style( 'smw-timepicker-css', $css_time, array(), SMW_VERSION );

		// timepicker addon depends on jQuery and jQuery UI datepicker
		wp_enqueue_script( 'smw-timepicker-addon', $js_timepicker, array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-core' ), SMW_VERSION, true );
	}

}

if( is_admin() ){
	$smw_woo_settings_page = new smw_woo_settings_page();
}