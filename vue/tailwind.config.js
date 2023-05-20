/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  theme: {
    extend: {
      keyframes: {
        "fade-in-down": {
          from: {
            transform: "translateY(-0.75rem)",
            opacity: 0,
          },
          to: {
            transform: "translateY(0rem)",
            opacity: 1,
          },
        },
        "up-down": {
          "0% , 100%": {
            transform: "translateY(0)",
          },
          "50%": {
            transform: "translateY(15px)",
          },
        },
      },
      animation: {
        "fade-in-down": "fade-in-down 0.2s ease-in-out both",
        "up-down": "up-down 0.6s infinite linear",
      },
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    require("tailwindcss-animation-delay"),
  ],
};
