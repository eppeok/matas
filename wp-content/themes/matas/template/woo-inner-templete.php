<?php 
/*Template Name: Woo Inner Template*/
get_header();
?>
    <main class="main-content">
   		<div class="woo-inner-page">
	   		<div class="container">
                <h1 class='page-title'><?php the_title();?></h1>
                <?php
                    if(have_posts()){
                        while(have_posts()) { the_post();
                            the_content();
                        }
                    }
                    wp_reset_query();
                ?>
            </div>
        </div>
    </main>


<?php get_footer();?>