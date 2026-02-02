<?php
/**
 * MINIORANGE OAuth client apps
 *
 * @package    Admin\Faq
 */

/**
 * Admin FAQ
 */
class MoCognito_Admin_Faq {

	/**
	 * FAQ
	 */
	public static function faq() {
		self::faq_page();
	}

	/**
	 * FAQ page
	 */
	public static function faq_page() {
		?>
			<div class="mo_table_layout">
				<object type="text/html" data="https://faq.miniorange.com/kb/oauth-openid-connect/" width="100%" height="600px" > 
				</object>
			</div>
		<?php
	}
}
