/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/views/**/*.php",

    // Nuevas vistas MVC de la aplicación.
    "./app/views/**/*.php",

    "./app/controllers/**/*.php",
    "./public/**/*.php",
    "./resources/js/**/*.js",
  ],

  theme: {
    extend: {},
  },

  plugins: [],
};

