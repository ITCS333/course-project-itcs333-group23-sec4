const theme = require('./common/theme');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.html',
    './src/**/*.html',
    './src/**/*.js'
  ],
  theme: {
    extend: theme.extend
  },
  plugins: []
};
