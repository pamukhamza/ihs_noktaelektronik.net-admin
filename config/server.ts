export default ({ env }) => ({
  host: env('HOST', '0.0.0.0'),
  port: env.int('PORT', 1337),
  app: {
    keys: env.array('APP_KEYS', ['1N10VQ0qJfUrM/ekqyVBVQ==', 'ULXYzNlxs2fBJTalDqT2gg==', '1NFr5jsoY7z26zXNG74dpg==', 'gFdXtviGrmQvSHiRVrRhTQ==']), // Provide default values in case the variable is missing
  },
});
