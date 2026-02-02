<?php 
/*Template Name: FAQ Template*/
get_header();
?>
    <main class="main-content">
   		<div class="general-inner-page setPosition commonPadding">
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


<?php

$args = array(
    'post_type'      => 'matas_faqs',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'DESC'
);

$faq_query = new WP_Query($args);
?>

<?php if ( $faq_query->have_posts() ) : ?>
<div class="container">
    <div class="accordion" id="accordionExample">

        <?php 
        $count = 0;
        while ( $faq_query->have_posts() ) : 
            $faq_query->the_post(); 
            $count++;

            $collapse_id = 'collapse'.$count;
            $heading_id  = 'heading'.$count;
        ?>

        <div class="accordion-item">
            <h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                <button class="accordion-button <?php echo $count > 1 ? 'collapsed' : ''; ?>"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#<?php echo esc_attr($collapse_id); ?>"
                    aria-expanded="<?php echo $count == 1 ? 'true' : 'false'; ?>"
                    aria-controls="<?php echo esc_attr($collapse_id); ?>">
                    <?php the_title(); ?>
                </button>
            </h2>

            <div id="<?php echo esc_attr($collapse_id); ?>" 
                class="accordion-collapse collapse <?php echo $count == 1 ? 'show' : ''; ?>" 
                aria-labelledby="<?php echo esc_attr($heading_id); ?>" 
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <?php endwhile; wp_reset_postdata(); ?>

    </div>
</div>
<?php endif; ?>




      </div>
    </main>

<?php get_footer();?>