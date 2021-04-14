const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                'fade-in-down': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-10px)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'none',
                    },
                },
            },
            animation: {
                'fade-in-down': 'fade-in-down 0.5s cubic-bezier(0.42,0,0.58,1) 1',
            },
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
        margin: ['responsive', 'hover', 'first'],
    },

    plugins: [require('@tailwindcss/forms')],
};
