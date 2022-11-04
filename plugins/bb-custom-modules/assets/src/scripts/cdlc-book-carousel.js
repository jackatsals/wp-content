import '@accessible360/accessible-slick';
import prefersReducedMotion from './util/prefersReducedMotion';

const slickIcons = {
  prev: '<button class="slick-prev" type="button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44"><path d="M27 22L5 44l-2.1-2.1L22.8 22 2.9 2.1 5 0l22 22z"></path></svg><span class="slick-sr-only">Previous</span></button>',
  next: '<button class="slick-next" type="button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44"><path d="M27 22L5 44l-2.1-2.1L22.8 22 2.9 2.1 5 0l22 22z"></path></svg><span class="slick-sr-only">Next</span></button>',
};

const carousels = document.querySelectorAll('.cdlc-book-carousel__wrapper');
if (carousels) {
  carousels.forEach(carousel => {
    jQuery(carousel).slick({
      arrows: true,
      dots: true,
      infinite: true,
      mobileFirst: true,
      nextArrow: slickIcons.next,
      prevArrow: slickIcons.prev,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 6,
            slidesToScroll: 6,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
          },
        },
        {
          breakpoint: 580,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
          },
        },
      ],
      speed: prefersReducedMotion() ? 0 : 750,
    });
  });
}
