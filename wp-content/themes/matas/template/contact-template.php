<?php
/*Template Name: Default Contact Template*/

get_header();
?>
    <!-- Main Content -->
    <main class="main-content">
        <?php 
            while(have_posts()) { the_post();
                if( get_the_post_thumbnail_url() != '' ){
        ?>
        <!-- Inner Banner -->
        <section class="inner_banner_item">
            <img src="<?php echo the_post_thumbnail_url();?>" alt="">
            <div class="banner_content">
                <div class="container">
                    <h1 class="text-uppercase inner_banner_title"><?php the_title(); ?></h1>
                </div>
            </div>
        </section>
        <?php } } wp_reset_postdata();?>
        <!-- End Inner Banner -->

        <!-- Contact Page Info -->
        <section class="contactpageinfo">
        <?php 
            while(have_posts()) { the_post();
                if( get_the_post_thumbnail_url() != '' ){
        ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="messageBox text-center">
                        <?php the_content();?>
                        </div>
                    </div>
                </div>
        <?php } } wp_reset_postdata();?>
        <?php if( get_theme_mod('contact_form_shortcode') != ''){?>
                <div class="contact_frm">
                    <?php echo do_shortcode(get_theme_mod('contact_form_shortcode'));?>
                </div>
        <?php } ?>
            </div>
        </section>
        <!-- End Contact Page Info -->

        <!-- Contact Description -->
        <section class="contactDescription">
            <div class="container">
                <div class="row">
                    <?php if( get_theme_mod('creative_address') != ''){?>
                    <div class="col-md-12 col-lg-4">
                        <div class="innerCon">
                            <div class="iconBoxInner">
                                <img src=<?php echo get_template_directory_uri()."/assets/images/contact-location-icon.png"?> alt="">
                            </div>
                            <div class="iconTextSec">
                            <?php if( get_theme_mod('creative_address_label') != ''){?>
                                <h6><?php echo get_theme_mod('creative_address_label');?></h6>
                            <?php } ?>
                                <p><?php echo get_theme_mod('creative_address');?></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if( get_theme_mod('creative_phone_no') != ''){?>
                    <div class="col-md-12 col-lg-4">
                        <div class="innerCon">
                            <div class="iconBoxInner">
                                <img src=<?php echo get_template_directory_uri()."/assets/images/contact-ph-icon.png"?> alt="">
                            </div>
                            <div class="iconTextSec">
                            <?php if( get_theme_mod('creative_phone_no_label') != ''){?>
                                <h6><?php echo get_theme_mod('creative_phone_no_label');?></h6>
                            <?php } ?>
                                <p><a href="tel:<?php echo preg_replace( '/[^0-9]/', '', get_theme_mod('creative_phone_no') );?>"><?php echo get_theme_mod('creative_phone_no');?></a></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if( get_theme_mod('creative_email_id') != ''){?>
                    <div class="col-md-12 col-lg-4">
                        <div class="innerCon">
                            <div class="iconBoxInner">
                                <img src=<?php echo get_template_directory_uri()."/assets/images/contact-mail-icon.png"?> alt="">
                            </div>
                            <div class="iconTextSec">
                            <?php if( get_theme_mod('creative_email_id_label') != ''){?>
                                <h6><?php echo get_theme_mod('creative_email_id_label');?></h6>
                            <?php } ?>
                                <p><a href="mailto:<?php echo get_theme_mod('creative_email_id');?>"><?php echo get_theme_mod('creative_email_id');?></a></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <!-- End Contact Description -->
        <?php if( get_theme_mod('creative_map') != ''){?>
        <section class="mapBox">
            <div class="contact_map_wrap">
                <h2 class="hidden-title">&nbsp;</h2>
                <?php echo get_theme_mod('creative_map');?>
            </div>
        </section>
        <?php } ?>
    </main>
    <!-- End Main Content -->
<?php get_footer();?>