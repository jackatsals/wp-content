import prefersReducedMotion from '../util/prefersReducedMotion';

/**
 * Initialize archive.
 */
const initArchive = () => {
  const resultsWrapper = document.querySelector('.facetwp-template');

  document.addEventListener('facetwp-refresh', () => {
    // Scroll only if pager is activated
    FWP.enable_scroll = (FWP.soft_refresh === true) ? true : false;

    // Fade out results
    if (FWP.loaded) {
      resultsWrapper.style.opacity = '0';
    }

    // Add a 'paged' class if not on the first page of results
    if (FWP.paged > 1)  {
      document.body.classList.add('paged');
    } else {
      document.body.classList.remove('paged');
    }
  });

  document.addEventListener('facetwp-loaded', () => {
    if (FWP.loaded && FWP.enable_scroll === true) {
      // Set focus first item in results
      resultsWrapper.querySelector('.entry-link').focus({
        preventScroll: true,
      });

      // Scroll to top of results wrapper
      resultsWrapper.scrollIntoView({
        behavior: prefersReducedMotion() ? 'auto' : 'smooth',
      });
    }

    // Fade in results
    setTimeout(() => {
      resultsWrapper.style.opacity = '1';
    }, 300);
  });
};

export default initArchive;
