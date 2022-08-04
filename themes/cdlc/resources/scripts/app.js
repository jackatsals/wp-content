import { domReady } from '@roots/sage/client';
import initDarkMode from './init/initDarkMode';
import initMainMenu from './init/initMainMenu';
import initDropdowns from './init/initDropdowns';
import initArchive from './init/initArchive';
import initEventsList from './init/initEventsList';
import 'focus-visible';

/**
 * app.main
 */
const main = async (err) => {
  if (err) {
    // handle hmr errors
    console.error(err);
  }

  // application code
  initDarkMode();
  initMainMenu();
  initDropdowns();
  initArchive();

  if (document.body.classList.contains('post-type-archive-tribe_events')) {
    initEventsList();
  }
};

/**
 * Initialize
 *
 * @see https://webpack.js.org/api/hot-module-replacement
 */
domReady(main);
import.meta.webpackHot?.accept(main);
