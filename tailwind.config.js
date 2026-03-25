/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./admin/**/*.{php,html}",
    "./views/**/*.{php,html}",
  ],
  theme: {
    extend: {
      colors: {
        htec: {
          red: "#E31837",
          dark: "#0A0A0A",
          gray: "#111111",
          mid: "#1A1A1A",
          border: "#2A2A2A",
          text: "#A0A0A0",
          "text-light": "#D0D0D0",
        },
      },
      fontFamily: {
        display: ["Syne", "sans-serif"],
        body: ["DM Sans", "sans-serif"],
      },
    },
  },
  plugins: [],
}

