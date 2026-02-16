/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './views/**/*.php',
    './admin/**/*.php',
    './api/**/*.php',
    './resources/**/*.php',
    './extension/**/*.{html,js}',
    './js/**/*.js',
    './templates/**/*.twig',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

