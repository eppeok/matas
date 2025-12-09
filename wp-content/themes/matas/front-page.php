<?php
get_header();
?>

<?php $frontpage_id = get_option( 'page_on_front' ); ?>

        <main class="main-content">
            <div class="mainbodyCont">
                <!-- Banner Section -->
                <section class="setPosition commonPadding home_banner_section bg_position_set"
                    style="background-image: url(<?php the_field('banner_image'); ?>);">
                       <div class="container-fluide">
                        <div class="row align-items-center">
                            <div data-aos="fade-up" class="col-lg-6 col-md-8 col-sm-12 px-5">
                                <div class="banner_card">
                                    <h1><?php the_field('banner_heading'); ?></h1>
                                    <p class="sub_heading"><?php the_field('banner_subheading'); ?></p>
                                </div>
                                <div class="button_box">
                                    <a href="<?php the_field('banner_button_link_1'); ?>" class="btn_primary commonHoverEffect" data-aos="flip-up"><?php the_field('banner_button_text_1'); ?></a>
                                    <a href="<?php the_field('banner_button_link_2'); ?>" class="btn_secondary commonHoverEffect" data-aos="flip-up"><?php the_field('banner_button_text_2'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Video Section -->
                <section class="setPosition commonPadding video_section">
                    <div class="video-card shadow-lg">
                          <div
                            class="video-player d-flex flex-column justify-content-center align-items-center position-relative"  style="background-image: url(<?php the_field('video_background'); ?>);">
                            <div class="play-btn" data-bs-toggle="modal" data-bs-target="#videoModal">
                               <img src="<?php echo get_template_directory_uri(); ?>/assets/images/play-icon.png">
                            </div>
                            <div class="video-text text-center">
                                <h5 class="fw-semibold text-white mb-1"><?php the_field('video_heading'); ?></h5>
                                <p class="text-secondary mb-0"><?php the_field('video_sub_heading'); ?></p>
                            </div>
                        </div>

                        <!-- Bottom Info -->
                        <div class="video-info px-4 py-3">
                            <h6 class="text-white mb-1"><?php the_field('video_title'); ?></h6>
                            <p class="text-secondary small mb-0"><?php the_field('video_subtitle'); ?></p>
                        </div>
                    </div>

                    <!-- Video Popup Modal -->
                    <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content bg-dark border-0">
                                <div class="modal-body position-relative p-0">
                                    <button type="button"
                                        class="close-btn position-absolute top-0 end-0 m-2 btn btn-dark"
                                        data-bs-dismiss="modal" aria-label="Close">
                                        <i class="bi bi-x-lg text-white fs-5"></i>
                                    </button>
                                    <div class="ratio ratio-16x9">
                                        <iframe width="560" height="315" src="<?php the_field('video_embedded_code'); ?>" title="YouTube video player" frameborder="0"  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"  referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
               

                   <!-- Featured Equipment -->
                <section class="setPosition commonPadding featured_equipment half_slider">
                    <div class="section-header">
                        <h2><?php the_field('featured_title'); ?></h2>
                        <p><?php the_field('featured_content'); ?></p>
                    </div>
                    <div class="carousel container">

                    <?php
                     $args = array(
                                     'post_type' => 'equipment',
                                            'posts_per_page' => -1,
                                            'order' => 'ASC'
                                        );
                                        $query = new WP_Query( $args );

                          if($query->have_posts()){
                            while($query->have_posts()){ $query->the_post(); ?>
               
                        <div class="slide equipment-card">
                            <img src="<?php the_post_thumbnail_url(); ?>" alt="Premium Tactical Gear"
                                class="equipment-image" />
                            <div class="slide-caption card-overlay">
                                <div class="card-title"><?php the_title(); ?></div>
                                <div class="card-subtitle"><?php echo get_the_content(); ?></div>
                            </div>
                        </div>

                     <?php } } wp_reset_postdata(); ?>  


                    </div>

                </section>

               
                <!-- Dealer Products -->
                <section class="setPosition commonPadding dealer_products half_slider">
                    <div class="section-header">
                        <h2><?php the_field('dealer_product_title'); ?></h2>
                        <p><?php the_field('dealer_product_content'); ?></p>
                    </div>

                    <div class="carousel container">


                    <?php
                     $args = array(
                                     'post_type' => 'dealer-product',
                                            'posts_per_page' => -1,
                                            'order' => 'ASC'
                                        );
                                        $query = new WP_Query( $args );

                          if($query->have_posts()){
                            while($query->have_posts()){ $query->the_post(); ?>
               
                        <div class="slide equipment-card">
                            <img src="<?php the_post_thumbnail_url(); ?>" alt="Rifle Dealer Products"
                                class="equipment-image" />
                            <div class="slide-caption card-overlay">
                                <div class="card-title"><?php the_title(); ?></div>
                                <div class="card-subtitle"><?php echo get_the_content(); ?></div>
                            </div>
                        </div>
                        <?php } } wp_reset_postdata(); ?>  
                      
                    </div>
                </section>

                <!-- New Webinars -->
                <section class="setPosition commonPadding new_webinar">
					<div class="container">
						
                    <h2><?php the_field('webniar_title'); ?></h2>
                    <div class=" container-cards row">

                     <?php

                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => 3,
                        'order'          => 'ASC',
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'term_id',
                                'terms'    => 31,
                            ),
                        ),
                    );

                    $query = new WP_Query( $args );

                    if($query->have_posts()){

                    while($query->have_posts()){ $query->the_post(); 

                         global $product;

                        ?>
                    <div class="col-md-4 col-sm-12">
                        <div class="card">
                            <a href="<?php the_permalink(); ?>"><img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title_attribute(); ?>" /> </a>
                            <div class="card-body">
                                <h5 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                <p class="card-text"><?php echo $product->get_short_description();; ?></p>
                                <div class="price"><?php echo $product ? $product->get_price_html() : ''; ?></div>
                                <a  class="btn-purchase" href="<?php the_permalink(); ?>"><?php the_field('purchased_button_text', $frontpage_id ); ?></a>
                            </div>
                        </div>
						</div>
                         <?php } } wp_reset_postdata(); ?>

                    </div>
                    </div>
                </section>
               <!-- Why to Buy section -->
                <section class="setPosition commonPadding why-to-buy">
                    <div class="container">
                        <div class="section-header">
                            <h2><?php the_field('review_title'); ?></h2>
                            <div class="star-rating d-flex items-center align-items-center justify-content-center gap-2 mb-4">
                               <div class="flex"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="star-rating">
                                        <path
                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                        </path>
                                    </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="star-rating">
                                        <path
                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                        </path>
                                    </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="star-rating">
                                        <path
                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                        </path>
                                    </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="star-rating">
                                        <path
                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                        </path>
                                    </svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="star-rating">
                                        <path
                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                        </path>
                                    </svg>
                                </div>
								<span class="g-reviews">
                                <?php the_field('review_ratings'); ?>
								</span>
                            </div>
                            <div class="trust-text">
                               <?php the_field('review_description'); ?>
                            </div>
                        </div>

                        <div class="metrics">
                            <div class="metric">
                               <img src ="<?php the_field('customer_icon'); ?>" />
                                <div class="metric-value"><?php the_field('customer_count'); ?></div>
                                <div class="metric-label"><?php the_field('customer_text'); ?></div>
                            </div>
                            <div class="metric">
                                <img src ="<?php the_field('experience_icon'); ?>" />
                                <div class="metric-value"><?php the_field('experience_count'); ?></div>
                                <div class="metric-label"><?php the_field('experience_text'); ?></div>
                            </div>
                            <div class="metric">
                               <img src ="<?php the_field('quality_icon'); ?>" />
                                <div class="metric-value"><?php the_field('quality_count'); ?></div>
                                <div class="metric-label"><?php the_field('quality_text'); ?></div>
                            </div>
                        </div>

                        <div class="testimonial">
                       
                   <?php
                      $args = array(
                     'post_type' => 'testimonial',
                            'posts_per_page' => -1,
                            'order' => 'ASC'
                        );
                        $query = new WP_Query( $args );

                      if($query->have_posts()){
                        while($query->have_posts()){ $query->the_post(); 

                             $star_count  = get_field( 'star_raing' );   

                            ?>


                            <div class="item">
                                <div class="testimonial-header">
                                    <img class="testimonial-img" src="<?php the_post_thumbnail_url(); ?>"
                                        alt="<?php echo esc_attr( get_the_title() ); ?>" />
                                    <div class="review-stars">
                                        <div class="flex items-center mb-1">
                                        <?php
                                      
                                        for ( $i = 0; $i < $star_count; $i++ ) {
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4 fill-yellow-400 text-yellow-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>';
                                        }

                                       
                                        for ( $i = $star_count; $i < 5; $i++ ) {
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star w-4 h-4" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>';
                                        }
                        ?>
                                        </div>
                                        <div class="rating-text-number"><?php the_field('rating'); ?></div>
                                    </div>
                                </div>

                                <div class="testimonial-text">
                                   <?php the_content(); ?>
                                </div>
                                <div class="testimonial-author"><?php the_title(); ?></div>
                                <div class="testimonial-role"><?php the_field('role'); ?></div>
                            </div>

                          <?php } } wp_reset_postdata(); ?>
                          
                        </div>
                        <div class="cta-section mt-5">
                            <h3><?php the_field('share_cta_section_title'); ?></h3>
                            <p><?php the_field('share_cta_section_description'); ?></p>
                            <a href ="<?php the_field('share_cta_section_button_link'); ?>" class="btn-submit commonHoverEffect"><?php the_field('share_cta_section_button_text'); ?></a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
       

<?php get_footer();?>
   