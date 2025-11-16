import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'system-blue': '#0A84FF',
                'system-gray': '#F2F2F7',
                'card-white': '#FFFFFF',
            },
            fontFamily: {
                'system': ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Text"', '"SF Pro Display"', '"Helvetica Neue"', 'Arial', 'sans-serif'],
            },
            borderRadius: {
                'system': '12px',
                'system-lg': '16px',
            },
            boxShadow: {
                'system': '0 4px 16px rgba(0, 0, 0, 0.08)',
                'system-lg': '0 8px 32px rgba(0, 0, 0, 0.1)',
                'system-focus': '0 0 0 4px rgba(10, 132, 255, 0.1)',
            }
        },
    },

    plugins: [forms, typography],
};
