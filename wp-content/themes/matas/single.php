<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>
<?php while(have_posts()){the_post();?>
	<main class="main-content">
		<div class="blog_detail_page">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-lg-12 col-blog-detail">
						<div class="blog_detail_wrap">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-blog-detail-inner">
									<div class="blog_detail_inner">
										<div class="blog_detail_img">
											<a href="javascript:void(0)">
												<img src="<?php the_post_thumbnail_url();?>" alt="<?php echo get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );?>">
											</a>
										</div>
										<div class="blog_detail_content">
											<div class="blog_title_detail_wrap">
												<div class="blog_title_detail_lt">
													<h1 class="site_title"><span><?php the_title();?></span></h1>
												</div>
												<div class="blog_title_detail_rt">
													<span class="blog_detail_date"><i class="fa fa-calendar" aria-hidden="true"></i> <?php the_time('j-d-Y');?></span>
												</div>
											</div>
											<?php the_content();?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
</div>
</main>

<?php
}
get_footer();
