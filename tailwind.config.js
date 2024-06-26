const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
    colors: {
      brand: {
        dark: '#003B5C',
        light: '#62B5E5',
        gray: '#b1b3b6',
      },
      ...defaultTheme.colors,
    },
  },
  variants: {
    margin: ['last'],
  },
  plugins: [
    require('@tailwindcss/custom-forms'),
    require('@tailwindcss/ui')({
      layout: 'sidebar',
    }),
  ],
}
