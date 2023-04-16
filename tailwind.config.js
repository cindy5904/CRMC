/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {
      backgroundImage: {
        'desk': "url('/public/images/bgDesk.jpg')",
        'fond_profil_user':"url('/public/images/Fond.jpg')",
      },
      fontFamily: {
        'poppins': ['Poppins', 'sans-serif'],
      },   
    },
  }, 
  plugins: [
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
  ],
}



