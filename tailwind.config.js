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
                sans: ['Karla', 'Segoe UI', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['"Playfair Display"', 'Georgia', 'serif'],
            },
            colors: {
                cream: {
                    50: '#FBF8F3',
                    100: '#F6F0E6',
                    200: '#EDE3D3',
                },
                ink: {
                    50: '#F5F4F2',
                    100: '#E7E5E0',
                    300: '#B9B4A9',
                    400: '#8D867A',
                    500: '#6E675C',
                    600: '#55504A',
                    700: '#3F3B36',
                    800: '#2A2723',
                    900: '#1C1917',
                },
                sambal: {
                    50: '#FEF3EC',
                    100: '#FCE5D5',
                    200: '#F8C8A9',
                    300: '#F3A275',
                    400: '#EC7440',
                    500: '#E1551F',
                    600: '#C2410C',
                    700: '#A1330A',
                    800: '#7F2B0E',
                    900: '#672510',
                },
            },
        },
    },

    plugins: [forms],
};
