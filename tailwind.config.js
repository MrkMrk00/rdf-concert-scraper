/** @type {import('tailwindcss').Config} */
export default {
  content: [
      './resources/**/*.{blade.php,js,ts,jsx,tsx,html}',
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
        colors: {

            primary: '#22C55E',
        },
    },
  },
  plugins: [],
}

