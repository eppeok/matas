<?php 
/*Template Name: General Template*/
get_header();
?>
    <main class="main-content">
   		<div class="general-inner-page">
	   		<div class="container">
               <h1 class='page-title'><?php the_title();?></h1>
                <?php
                    if(have_posts()){
                        while(have_posts()) { the_post();
                            the_content();
                        }
                    }else{
                        echo "<p>No Post Found</p>";
                    }
                    wp_reset_query();
                ?>
            </div>
        </div>
    </main>

<?php get_footer();?>