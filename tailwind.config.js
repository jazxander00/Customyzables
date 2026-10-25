/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // Warm paper surfaces (page + cards), taken from the mockups.
                sand: {
                    50: '#FDFBF8',
                    100: '#FAF6F0',
                    200: '#F4EDE3',
                    300: '#EBE1D3',
                    400: '#DDCFBC',
                },
                // Terracotta primary — buttons, links, active states.
                clay: {
                    50: '#FDF4EF',
                    100: '#FAE6DC',
                    200: '#F4CDBC',
                    300: '#EDA98F',
                    400: '#E58E70',
                    500: '#DE7A5A',
                    600: '#C96345',
                    700: '#A54E36',
                    800: '#83402E',
                },
                // Forest green — progress, verification, positive states.
                forest: {
                    50: '#EFF5F0',
                    100: '#DEEBE2',
                    200: '#B9D6C3',
                    300: '#8CBAA0',
                    400: '#5E9878',
                    500: '#3F7D5D',
                    600: '#2F6B4F',
                    700: '#245440',
                },
                ink: {
                    DEFAULT: '#2B2724',
                    soft: '#4A443F',
                    muted: '#6F6862',
                    faint: '#9A918A',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                card: '1.25rem',
                pill: '999px',
            },
            boxShadow: {
                card: '0 1px 2px rgba(43, 39, 36, 0.04), 0 8px 24px -12px rgba(43, 39, 36, 0.12)',
                lift: '0 18px 40px -18px rgba(43, 39, 36, 0.28)',
                ring: '0 0 0 4px rgba(222, 122, 90, 0.16)',
            },
            transitionTimingFunction: {
                soft: 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            keyframes: {
                'fade-rise': {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
    plugins: [],
};
