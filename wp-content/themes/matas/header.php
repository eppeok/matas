<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

 <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">



	<?php wp_head();?>
</head>
<body <?php body_class();?>>

  <?php $frontpage_id = get_option( 'page_on_front' ); ?>

    <div class="wrapper">
        <header class="site-header">
            <div class="topInfo" id="myHeader">
                <div class="announcement-bar">
                    <span><?php the_field('top_banner_text', $frontpage_id  ); ?></span>
                </div>
                <div class="main_header">
                    <div class="container">
                        <nav class="navbar navbar-expand-lg navbar-custom px-4">
                             <a class="navbar-brand logo" href="<?php echo esc_url( home_url( '/' ) ) ;?>">
                                <img src="<?php echo get_theme_mod('site_main_logo');?>" alt="" class="">
                              </a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                                aria-label="Toggle navigation">
                               <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu w-4 h-4"><line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line></svg>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                    <?php wp_nav_menu( array('menu' => 'Header Menu' , 'container' => '' , 'items_wrap' => '%3$s' )); ?>
                                </ul>
                            
                                <form role="search" method="get" class="d-flex" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                  <input type="search" id="woocommerce-product-search-field" class="search-field" placeholder="Search product" value="<?php echo get_search_query(); ?>" name="s" />
                                  <button type="submit" value="Search"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search w-4 h-4"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg></button>
                                  <input type="hidden" name="post_type" value="product" />
                                </form>

                                <a href ="<?php echo esc_url( home_url( '/' ) ) ;?>cart" class="cart-btn">
                                    <img src="<?php echo esc_url( home_url( '/' ) ) ;?>wp-content/uploads/2025/11/SVG_margin.png" /> Cart
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>