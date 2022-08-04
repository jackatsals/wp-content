import $ from 'jquery';
import '@accessible360/accessible-slick';
import prefersReducedMotion from './util/prefersReducedMotion';

const sliders = document.querySelectorAll('.unity-slider');

if (sliders.length) {
  sliders.forEach(slider => {
    $(slider).slick({
      adaptiveHeight: true,
      dots: false,
      slidesToShow: 2,
      slidesToScroll: 2,
      speed: prefersReducedMotion() ? 0 : 300,
      infinite: false,
      mobileFirst: true,
    });
  });
}
