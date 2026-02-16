/** @type {import('tailwindcss').Config} */
/** Homepage-only build - smaller CSS for / and index.php */
module.exports = {
  content: [
    './index.php',
    './views/partials/head.php',
    './views/partials/header.php',
    './views/partials/home.php',
    './views/partials/home-pricing.php',
    './views/partials/footer.php',
    './views/partials/logo.php',
    './views/partials/auth-modals.php',
    './views/partials/feedback-widget.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
