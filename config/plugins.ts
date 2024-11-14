export default ({ env }) => ({
    'users-permissions': {
      config: {
        jwtSecret: env('JWT_SECRET', 'px5the9D2bBOnWe/tXnh+g=='), // replace 'yourGeneratedJwtSecretHere' with the actual secret
      },
    },
  });  