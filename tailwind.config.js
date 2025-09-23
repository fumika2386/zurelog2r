// tailwind.config.js
module.exports = {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // 基本色：オレンジ（Tailwind既定の orange を採用）
        primary: require('tailwindcss/colors').orange,   // primary-500 など
        // 差し色：グレー（slate系が知的で相性◎）
        accent:  require('tailwindcss/colors').slate,    // accent-500 など
      },
      boxShadow: {
        soft: '0 1px 2px rgba(0,0,0,.04), 0 10px 30px -18px rgba(0,0,0,.25)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  
  ],
}
