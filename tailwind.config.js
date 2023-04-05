/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",  
    "./templates/**/*.html.twig",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {
      'fond_profil_user':"url('../public/images/Fond.jpg')",
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
  ],
}

