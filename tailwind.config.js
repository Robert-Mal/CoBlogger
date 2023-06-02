/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            colors: {
                'royal-blue': {
                    '50': '#f1f5fd',
                    '100': '#dfe8fa',
                    '200': '#c6d7f7',
                    '300': '#9fbef1',
                    '400': '#719be9',
                    '500': '#4c76e1',
                    '600': '#3b5cd5',
                    '700': '#314ac4',
                    '800': '#2e3d9f',
                    '900': '#2a387e',
                    '950': '#1e244d',
                    'DEFAULT': '#4c76e1',
                },
            },
            fontFamily: {
                'poppins': ['Poppins']
            },
            spacing: {
                '128': '32rem',
            }
        },
    },
    plugins: [],
}

