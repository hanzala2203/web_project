/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{php,html,js}",
    "./public/**/*.{php,html,js}",
    "./views/**/*.{php,html,js}",
    "./templates/**/*.{php,html,js,twig}"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#3b82f6',
          hover: '#2563eb'
        },
        secondary: {
          DEFAULT: '#4CAF50',
          hover: '#45a049'
        },
        error: '#ef4444',
        background: '#f1f5f9',
      },
      boxShadow: {
        'sm': '0 1px 2px rgba(0, 0, 0, 0.05)',
        'md': '0 4px 6px rgba(0, 0, 0, 0.1)',
        'lg': '0 10px 15px rgba(0, 0, 0, 0.1)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/line-clamp'),
  ],
}
