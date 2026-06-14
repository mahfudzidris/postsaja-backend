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
            colors: {
                surface: '#FFFFFF',
                muted: '#64748B',
                primary: {
                    50: '#EEF2FF',
                    100: '#E0E7FF',
                    500: '#4F46E5',
                    600: '#4338CA',
                    700: '#3730A3',
                },
                secondary: {
                    500: '#7C3AED',
                    600: '#6D28D9',
                },
                accent: {
                    500: '#10B981',
                    600: '#059669',
                },
                danger: {
                    500: '#EF4444',
                },
                text: {
                    main: '#0F172A',
                    muted: '#64748B',
                },
                border: {
                    DEFAULT: '#E2E8F0',
                    50: '#F1F5F9',
                    100: '#CBD5E1',
                },
                background: {
                    DEFAULT: '#F8FAFC',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'gradient-brand': 'linear-gradient(135deg, #4F46E5, #7C3AED)',
                'gradient-soft': 'linear-gradient(135deg, #EEF2FF, #F5F3FF)',
            },
        },
    },

    plugins: [forms],
};
