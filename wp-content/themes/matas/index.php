

<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>
	<main class="main-content">
		<div class="blog_list_page">
			<div class="container">
				
				<div class="text-center blog_list_title">
					<h1 class="site_title"><span><?php echo single_post_title();?></span></h1>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-3 col-blog-list-lt">
						<div class="blog_cat_wrap">
							<h2>CategorieS</h2>
							<ul class="clearfix cat_list">
								<?php
								
									$wsubcats = get_categories(array(
										'hierarchical' => 1,
										'hide_empty' => 1,
										'taxonomy' => 'category'
										));
										foreach ($wsubcats as $wsc){
								?>
								<li><a href="<?php echo get_term_link( $wsc->slug, $wsc->taxonomy );?>"><?php echo $wsc->name;?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-9 col-blog-list-rt">
						<div class="blog_list_wrap">
							<div class="row">
								
								<?php 
						        	if ( have_posts() ) {

						                // Load posts loop.
						                while ( have_posts() ) {
						                    the_post();
						        ?>
								<div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 col-blog-list-inner">
									<div class="blog_list_inner">
										<div class="blog_list_img">
											<a href="<?php the_permalink();?>">
												<img src="<?php the_post_thumbnail_url();?>" alt="<?php echo get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );?>">
											</a>
										</div>
										<div class="blog_list_content">
											<h5><a href="<?php the_permalink();?>"><?php echo substr(get_the_title(), 0, 20); ?></a></h5>
											<span class="blog_list_date"><i class="fa fa-calendar" aria-hidden="true"></i> <?php the_time('j-d-Y');?></span>
											<p><?php echo substr(strip_tags(get_the_content()), 0, 86); ?>...</p>
											<a href="<?php the_permalink();?>" class="btn_primary btn_blog">Read More</a>
										</div>
									</div>
								</div>

								<?php }
        
						        } else { ?>
						            <div class="no_content_found">
						                <h3>Nothing Found!</h3>
						            </div>

						        <?php } ?>

						        <div class="clearfix"></div>
						        <div class="pagination">
						            <?php 
						                the_posts_pagination( array(
						                    'mid_size'  => 2,
						                    'prev_text' => __( '<i class="fa fa-angle-double-left" aria-hidden="true"></i>', 'textdomain' ),
						                    'next_text' => __( '<i class="fa fa-angle-double-right" aria-hidden="true"></i>', 'textdomain' ),
						                    'screen_reader_text' => __( '&nbsp;' )
						                ) );
						            ?>
						        </div>


							
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>



<?php
get_footer();
