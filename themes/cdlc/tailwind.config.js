module.exports = {
  content: ['./index.php', './app/**/*.php', './resources/**/*.{php,vue,js}'],
  darkMode: 'class',
  theme: {
    container: {
      center: true,
      padding: '1rem',
    },
    extend: {
      colors: {},
      fontFamily: {
        sans: ['Helvetica Neue, Helvetica, -apple-system, BlinkMacSystemFont, Avenir Next, Avenir, Segoe UI, Ubuntu, Roboto, Noto, Arial, sans-serif'],
      },
      screens: {
        'lg': '992px',
        'xl': '1200px',
        '2xl': '1400px',
      },
    },
  },
  variants: {
    extend: {},
  },
};
