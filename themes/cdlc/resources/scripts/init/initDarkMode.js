import Cookies from 'js-cookie';

/**
 * Initialize all of the main menu interactions.
 */
 const initDarkMode = () => {
  const btnLightTheme = document.getElementById('btn-light-theme');
  const btnDarkTheme = document.getElementById('btn-dark-theme');

  btnDarkTheme.addEventListener('click', () => {
    document.documentElement.classList.add('dark');
    Cookies.set('cdlc-data-theme', 'dark');
  });

  btnLightTheme.addEventListener('click', () => {
    document.documentElement.classList.remove('dark');
    Cookies.remove('cdlc-data-theme');
  });
};

export default initDarkMode;
