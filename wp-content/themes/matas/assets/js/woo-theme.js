jQuery(document).ready(function(){
    jQuery('.flex-control-nav').slick({
        dots: false,
        arrows: true,
        infinite: false,
        speed: 800,
        slidesToShow: 4,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                slidesToShow: 3,
                slidesToScroll: 1
                }
            },
            {
                breakpoint: 481,
                settings: {
                slidesToShow: 2,
                slidesToScroll: 1
                }
            }, 
            {
                breakpoint: 401,
                settings: {
                slidesToShow: 2,
                slidesToScroll: 1
                }
            }
        ]
      });
    
      jQuery('.single-product .related.products ul.products').slick({
          dots: false,
          arrows: true,
          infinite: true,
          speed: 600,
          slidesToShow: 4,
          slidesToScroll: 1,
          responsive: [
              {
                  breakpoint: 1200,
                  settings: {
                  slidesToShow: 3,
                  slidesToScroll: 1
                  }
              },
              {
                  breakpoint: 992,
                  settings: {
                  slidesToShow: 2,
                  slidesToScroll: 1
                  }
              }, 
              {
                  breakpoint: 768,
                  settings: {
                  slidesToShow: 2,
                  slidesToScroll: 1
                  }
              }, 
              {
                  breakpoint: 576,
                  settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                  }
              }
          ]
      });
    });