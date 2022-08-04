const mix = require('laravel-mix');

/**
 * Asset directory paths.
 */
const src = 'assets/src';
const dist = 'assets/dist';

/**
 * Options and other Laravel Mix configs.
 */
mix.options({
  autoprefixer: {
    options: {
      browsers: ['last 2 versions'],
    },
  },
  terser: {
    extractComments: false,
  },
  processCssUrls: false,
}).setPublicPath(`${dist}`);

/**
 * CSS.
 */
mix.sass(`${src}/styles/cdlc-book-carousel.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-call-to-action.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-content-feed.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-icon-box.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-quick-links.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-splash.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-splash-opening-hours.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-layout-card.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});
mix.sass(`${src}/styles/cdlc-post-cards.scss`, `${dist}/styles`, {
  implementation: require('node-sass'),
});

/**
 * JS.
 */
mix.js(`${src}/scripts/cdlc-book-carousel.js`, `${dist}/scripts`);

/**
 * Externally-loaded libraries.
 */
mix.webpackConfig({
  externals: {
    'jquery': 'jQuery',
  }
});

/**
 * Production build.
 */
if (mix.inProduction()) {
  mix.version();
}
