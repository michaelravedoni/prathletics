module.exports = {
  pwa: {
    name: 'Prathletics',
    iconPaths: {
      favicon32: 'images/icons/favicon-32x32.png',
      favicon16: 'images/icons/favicon-16x16.png',
      appleTouchIcon: 'images/icons/apple-touch-icon-152x152.png',
      maskIcon: 'images/icons/safari-pinned-tab.svg',
      msTileImage: 'images/icons/msapplication-icon-144x144.png',
    },
    workboxPluginMode: 'GenerateSW',
    workboxOptions: {
      skipWaiting: true,
      clientsClaim: true,
    },
  },
  lintOnSave: false,
  baseUrl: process.env.NODE_ENV === 'production' ? './' : '/',
};
