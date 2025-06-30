import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js', // ✅ Ensure JavaScript files are scanned
    ],

    safelist: [
        "bg-green-200",
        "bg-red-500",
        "bg-blue-500",
        "bg-blue-700",
        "bg-blue-200",
        "text-blue-800",
        "bg-yellow-500",
        "bg-purple-500",
        "bg-blue-200",
        
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            // ✅ Floating Animation
            animation: {
                float: "float 2s ease-in-out infinite",
                pulseGlow: "pulseGlow 1.5s infinite alternate",
            },

            keyframes: {
                float: {
                    "0%": { transform: "translateY(0px)" },
                    "50%": { transform: "translateY(-5px)" },
                    "100%": { transform: "translateY(0px)" },
                },

                pulseGlow: {
                    "0%": { boxShadow: "0 0 10px rgba(255, 99, 71, 0.6)" },
                    "100%": { boxShadow: "0 0 20px rgba(255, 99, 71, 1)" },
                },
            },
        },
    },

    darkMode: 'class', // ✅ Enables dark mode using a 'class'

    plugins: [forms],
};
