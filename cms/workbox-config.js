module.exports = {
  globDirectory: 'web/',
  globPatterns: [
    '**/*.{html,pdf,js,css,scss,eot,svg,ttf,woff,woff2,json,jpg,png,gif,php,config}'
  ],
  globIgnores: ['cpresources/**', 'web.config'],
  swDest: 'web/sw.js',
  clientsClaim: true,
  skipWaiting: true,
  runtimeCaching: [
    {
    urlPattern: new RegExp('sessions'),
    handler: 'staleWhileRevalidate'
  },
  {
    urlPattern: new RegExp('fontawesome'),
    handler: 'cacheFirst'
  },
  {
    urlPattern: new RegExp('uikit'),
    handler: 'cacheFirst'
  }
  ]
};
