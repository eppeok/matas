<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>
	<div class="error__main__content">
        <section class="page404-main-section">
            <div class="container">
                <div class="error-404">
                    <div class="error_inner" data-aos="fade-down" data-aos-easing="linear" data-aos-duration="500">
                        <h1 class="text-uppercase site_title"><?php _e( 'Page Not Found', 'twentytwentyone' ); ?></h1>
                        <div class="intro-text"><p><?php _e( 'It looks like nothing was found at this location.', 'twentytwentyone' ); ?></p></div>
                        <a href="<?php echo site_url();?>" class="btnhome">Home</a>
                    </div>
                </div>
            </div><!-- #site-content -->
        </section>
    </div>


<?php
get_footer();
