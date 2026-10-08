import es5 from 'eslint-plugin-es5';
export default [
  {
    plugins: {
      es5: es5
    },
    rules: {
    },
  },
  {
    // ignores should be in its own configuration object, not mixed with plugins and rules
    ignores: [
      'wp_data/**',
      'node_modules/**',
      'themes/twentytwentythree',
      'themes/twentytwentyfive',
    ]
  }
];
