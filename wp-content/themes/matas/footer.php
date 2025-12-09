<?php $frontpage_id = get_option( 'page_on_front' ); ?>

 <footer class="setPosition site-footer">
            <div class="container">
                <div class="footer_img">
                  
          
                    <img src="<?php echo the_field('footer_image_1', $frontpage_id ) ?>" alt="flag">
                    <img src="<?php echo the_field('footer_image_2', $frontpage_id ) ?>" alt="flag">
                    <img src="<?php echo the_field('footer_image_3', $frontpage_id ) ?>" alt="flag">

                  
                </div>
                <div class="lower_part">
                    <div class="row">
                        <div class="col-lg-8  col-md-8 col-sm-12 footer-newsletter">
                            <p class="newsletter-text">
                               <?php echo the_field('newsletter_heading', $frontpage_id ) ?>
                            </p>
                           
                            <?php echo do_shortcode('[newsletter_form button_label="Subscribe now" ][newsletter_field name="email"  label="" placeholder="Insert your email address here"][/newsletter_form]'); ?>

                            <div class="social-icons">
                                <a href="<?php echo esc_url( home_url( '/' ) ) ;?>" class="footer_logo" aria-label="User Profile">
                                  <img src="<?php echo the_field('footer_logo', $frontpage_id ) ?>" alt="footer-logo"> </a>

                                <a href="<?php echo the_field('facebook_link', $frontpage_id ) ?>" class="social-icon" aria-label="Facebook" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-facebook w-6 h-6 cursor-pointer hover:text-blue-200">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z">
                                        </path>
                                    </svg>
                                </a>
                                <a href="<?php echo the_field('instagram_link', $frontpage_id ) ?>" class="social-icon" aria-label="Instagram" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-instagram w-6 h-6 cursor-pointer hover:text-blue-200">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 footer-links d-flex justify-content-start">
                            <div class="footer-links-column">
                                <strong><?php echo the_field('footer_menu_title_1', $frontpage_id ) ?></strong>
                                <ul>
                                     <?php wp_nav_menu( array('menu' => 'Footer Menu 1' , 'container' => '' , 'items_wrap' => '%3$s' )); ?>
                                </ul>
                            </div>
                            <div class="footer-links-column">
                                <strong><?php echo the_field('footer_menu_title_2', $frontpage_id ) ?></strong>
                                <ul>
                                     <?php wp_nav_menu( array('menu' => 'Footer Menu 2' , 'container' => '' , 'items_wrap' => '%3$s' )); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </footer>
    </div>
 
<script>
document.addEventListener('DOMContentLoaded', () => {
    const videoModal = document.getElementById('videoModal');
    const videoFrame = document.getElementById('videoFrame');
    if (!videoModal || !videoFrame) return; // Safety check
    const originalSrc = videoFrame.src;

    videoModal.addEventListener('hidden.bs.modal', () => {
        videoFrame.src = "";
    });

    videoModal.addEventListener('shown.bs.modal', () => {
        videoFrame.src = originalSrc;
    });
});
</script>

</div>
<?php wp_footer();?>
</body>

</html>