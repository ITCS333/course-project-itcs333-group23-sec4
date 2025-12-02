// Tailwind theme extension for a professional university-style palette
module.exports = {
  extend: {
    colors: {
      primary: {
        DEFAULT: '#0b3d91', // deep royal blue
        50: '#eaf2ff',
        100: '#d6e7ff',
        200: '#a9cfff',
        300: '#7db8ff',
        400: '#4f95ff',
        500: '#0b3d91',
        600: '#0a357b',
        700: '#082a5e',
        800: '#061f43',
        900: '#04142a'
      },
      universityGreen: {
        DEFAULT: '#1f7a39',
        50: '#eaf8ef',
        100: '#d6f2de',
        200: '#a8e6bd',
        300: '#7bdc9d',
        400: '#4fcd7f',
        500: '#1f7a39',
        600: '#195f2f',
        700: '#134723',
        800: '#0e3018',
        900: '#07170b'
      },
      charcoal: {
        DEFAULT: '#2b2f36'
      },
      paper: {
        DEFAULT: '#f7f4ee'
      },
      gold: {
        DEFAULT: '#b6892e'
      },
      accent: {
        DEFAULT: '#b6892e' // alias to gold
      }
    },
    fontFamily: {
      sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial'],
      serif: ['Merriweather', 'Georgia', 'serif']
    }
  }
};
