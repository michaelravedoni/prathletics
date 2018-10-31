module.exports = {
  root: true,

  env: {
    node: true,
  },

  extends: [
    'plugin:vue/essential',
    '@vue/airbnb',
  ],

  rules: {
    'no-console': 'off',
    'no-debugger': 'off',
    'no-tabs': 'off',
    'max-len': 'off',
    'no-unused-vars': 'off',
    'vue/require-v-for-key': 'off',
    'vue/valid-v-for': 'off',
  },

  parserOptions: {
    parser: 'babel-eslint',
  },
};
