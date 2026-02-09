import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Pastel accent colors
                'pastel': {
                    'sage': '#C8D5B9',      // Primary - soft green
                    'sage-dark': '#A8C094', // Primary hover
                    'sky': '#B8D4E3',       // Info - soft blue
                    'sky-dark': '#96C1D6',  // Info hover
                    'peach': '#F5D5CB',     // Warning - soft orange
                    'peach-dark': '#EEBFAE',// Warning hover
                    'lavender': '#D4C5E2',  // Secondary - soft purple
                    'rose': '#F0D4D8',      // Danger - soft pink
                    'rose-dark': '#E4B8BE', // Danger hover
                },
                // Neutral tones
                'neutral': {
                    'cream': '#FAF8F5',     // Page background
                    'warm': '#F5F3F0',      // Card background
                    'stone': '#E8E4DF',     // Borders, dividers
                    'muted': '#D1CBC4',     // Muted elements
                },
                // Text colors
                'text': {
                    'primary': '#2D3436',   // Main text
                    'secondary': '#636E72', // Secondary text
                    'muted': '#9CA3A8',     // Muted text
                },
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'soft-lg': '0 10px 40px -10px rgba(0, 0, 0, 0.1)',
            },
            borderRadius: {
                'xl': '1rem',
                '2xl': '1.5rem',
            },
            animation: {
                'blob': 'blob 7s infinite',
                'fade-in': 'fadeIn 0.3s ease-out',
            },
            keyframes: {
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                },
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
