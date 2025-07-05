/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#2d5016',
        'secondary': '#4a7c28',
        'accent': '#8fb548',
      },
      backgroundColor: {
        'primary': '#2d5016',
        'secondary': '#4a7c28',
        'accent': '#8fb548',
      }
    },
  },
  plugins: [],
}
