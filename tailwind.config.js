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
        'bg-home' : "url('/images/network.jpg')",
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('flowbite/plugin'),
  ],
}

