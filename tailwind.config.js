import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Bricolage Grotesque"', ...defaultTheme.fontFamily.sans],
                body: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                ink: {
                    DEFAULT: '#0B0E14',
                    soft: '#151A24',
                    line: '#232A3A',
                },
                paper: {
                    DEFAULT: '#FBF8F2',
                    100: '#F5F1E8',
                },
                ember: {
                    50: '#FFF3EC',
                    100: '#FFE4D3',
                    200: '#FFC7A6',
                    300: '#FFA26E',
                    400: '#FF7A3D',
                    500: '#FF5A1F',
                    600: '#F0430A',
                    700: '#C63508',
                    800: '#9C2B0B',
                    900: '#7A250D',
                    950: '#431004',
                },
                gold: {
                    50: '#FFF8EB',
                    100: '#FFEFC7',
                    200: '#FFDD8A',
                    300: '#FFC94D',
                    400: '#FFB238',
                    500: '#F59A0F',
                    600: '#D67D06',
                    700: '#AD5F08',
                    800: '#8A4A0E',
                    900: '#723D10',
                    950: '#5C3208',
                },
                // App-wide rebrand: every existing `indigo-*`/`purple-*`/`violet-*`
                // utility (dashboard, login, admin, all CRUD pages, dark: variants
                // included) picks up the new palette with zero template edits.
                indigo: {
                    50: '#FFF3EC',
                    100: '#FFE4D3',
                    200: '#FFC7A6',
                    300: '#FFA26E',
                    400: '#FF7A3D',
                    500: '#FF5A1F',
                    600: '#F0430A',
                    700: '#C63508',
                    800: '#9C2B0B',
                    900: '#7A250D',
                    950: '#431004',
                },
                purple: {
                    50: '#FFF8EB',
                    100: '#FFEFC7',
                    200: '#FFDD8A',
                    300: '#FFC94D',
                    400: '#FFB238',
                    500: '#F59A0F',
                    600: '#D67D06',
                    700: '#AD5F08',
                    800: '#8A4A0E',
                    900: '#723D10',
                    950: '#5C3208',
                },
                violet: {
                    50: '#FFF8EB',
                    100: '#FFEFC7',
                    200: '#FFDD8A',
                    300: '#FFC94D',
                    400: '#FFB238',
                    500: '#F59A0F',
                    600: '#D67D06',
                    700: '#AD5F08',
                    800: '#8A4A0E',
                    900: '#723D10',
                    950: '#5C3208',
                },
                aqua: {
                    50: '#ECFDF9',
                    100: '#CFFAF0',
                    200: '#99F1E1',
                    300: '#5FE2CE',
                    400: '#2FCBB6',
                    500: '#16B8A0',
                    600: '#0E9484',
                    700: '#0C776B',
                    800: '#0E5F56',
                    900: '#0F4F48',
                },
            },
        },
    },

    plugins: [forms],
};

