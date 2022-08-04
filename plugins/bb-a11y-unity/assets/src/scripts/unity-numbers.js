import { CountUp } from 'countup.js';
import prefersReducedMotion from './util/prefersReducedMotion';

/**
 * Initialize CountUp.js.
 *
 * @param object wrapperElem
 */
const initCountUp = (wrapperElem) => {
  const numberElems = wrapperElem.querySelectorAll('.unity-numbers__count');

  numberElems.forEach(el => {
    const { number, numberSuffix, numberPrefix, numberSeparator, numberDecimalPlaces } = el.dataset;
    const countUpOptions = {
      decimalPlaces: parseInt(numberDecimalPlaces),
      duration: prefersReducedMotion() ? 0 : 1.5,
      suffix: numberSuffix || '',
      prefix: numberPrefix || '',
      separator: numberSeparator || ',',
      useGrouping: true,
    };

    const countUp = new CountUp(el, number, countUpOptions);
    countUp.start();
  });
}

/**
 * Observe and init numbers entering the viewport.
 */
const numbersObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      initCountUp(entry.target);
      numbersObserver.unobserve(entry.target);
    }
  });
}, {
  threshold: 0.25
});

const flBuilderLayout = document.querySelector('.fl-builder-content');
const numbersWrapper = document.querySelectorAll('.unity-numbers');
document.addEventListener('DOMContentLoaded', () => {
  [...numbersWrapper].forEach(wrapperElem => {
    if (prefersReducedMotion()) {
      initCountUp(wrapperElem);
    } else {
      numbersObserver.observe(wrapperElem);
    }
  });
});

flBuilderLayout.addEventListener('fl-builder.preview-rendered', () => {
  [...numbersWrapper].forEach(wrapperElem => {
    if (prefersReducedMotion()) {
      initCountUp(wrapperElem);
    } else {
      numbersObserver.observe(wrapperElem);
    }
  });
});

flBuilderLayout.addEventListener('fl-builder.layout-rendered', () => {
  [...numbersWrapper].forEach(wrapperElem => {
    if (prefersReducedMotion()) {
      initCountUp(wrapperElem);
    } else {
      numbersObserver.observe(wrapperElem);
    }
  });
});
