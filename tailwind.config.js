/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'graySecondary': '#9D9D9D',
        'grayPrimary': '#4d4d4d',
        'Active': '#007FDF',
        'Inactive': '#788BA5',
        'HeaderTable': '#E8F5FF',
        'BlackPrimary': '#181E25'
      }
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
}