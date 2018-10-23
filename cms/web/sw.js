/**
 * Welcome to your Workbox-powered service worker!
 *
 * You'll need to register this file in your web app and you should
 * disable HTTP caching for this file too.
 * See https://goo.gl/nhQhGp
 *
 * The rest of the code is auto-generated. Please don't update this file
 * directly; instead, make changes to your Workbox build configuration
 * and re-run your build process.
 * See https://goo.gl/2aRDsh
 */

importScripts("https://storage.googleapis.com/workbox-cdn/releases/3.6.2/workbox-sw.js");

workbox.skipWaiting();
workbox.clientsClaim();

/**
 * The workboxSW.precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */
self.__precacheManifest = [
  {
    "url": "assets/css/theme-white-darkblue.css",
    "revision": "52ad5386e6de78c908801e8f0d522172"
  },
  {
    "url": "contenu/doc/EX-Reforcement-Pieds-Swiss-athletics.pdf",
    "revision": "a37aacbf205f043a82b24c826297baf9"
  },
  {
    "url": "contenu/doc/EX-Theraband-Suva-2005.pdf",
    "revision": "d79d39f942225ff2e702ae4c2001a0aa"
  },
  {
    "url": "contenu/exercices/sauts-haies-joints.png",
    "revision": "31ad16a445b21e06e2dca5690673bac0"
  },
  {
    "url": "contenu/exercices/sauts-plinth-cloche-pied.png",
    "revision": "b6f2d5dbb0e1641c7132634cb637d7ed"
  },
  {
    "url": "contenu/exercices/sauts-plinth-dg.png",
    "revision": "60b4df8d55664f20ad440a15a898b671"
  },
  {
    "url": "contenu/exercices/sauts-plinth-haies-pj-dg.png",
    "revision": "d93bbbbddfdc20177d7ca89d6264357f"
  },
  {
    "url": "contenu/exercices/sauts-plinth-pieds-joints-gd.png",
    "revision": "5877f6985b12674dcd3a4054da815413"
  },
  {
    "url": "images/icons/icon-128x128.png",
    "revision": "711f9ebb06c570a7aaffc658e44cc6d9"
  },
  {
    "url": "images/icons/icon-144x144.png",
    "revision": "155766a14734b51d00a1b745ca4e08ec"
  },
  {
    "url": "images/icons/icon-152x152.png",
    "revision": "19567108ff52bc9c807bdeff09662afe"
  },
  {
    "url": "images/icons/icon-192x192.png",
    "revision": "dfc35313ed3db0b0757a5da7fa1e9c06"
  },
  {
    "url": "images/icons/icon-384x384.png",
    "revision": "973210bf41371618e7c0f8ccd3b89240"
  },
  {
    "url": "images/icons/icon-512x512.png",
    "revision": "6d6d16954b515711aec453bca791422a"
  },
  {
    "url": "images/icons/icon-72x72.png",
    "revision": "f47cc0fceedc73079c242788e3829094"
  },
  {
    "url": "images/icons/icon-96x96.png",
    "revision": "4307c334fd30fc784d6a0f1850bdaa64"
  },
  {
    "url": "index.php",
    "revision": "ec0a30d01595c95a451db6e774d1cd4f"
  },
  {
    "url": "pwabuilder-sw-register.js",
    "revision": "8aeb4d88dcf4416341d2159e017695cd"
  }
].concat(self.__precacheManifest || []);
workbox.precaching.suppressWarnings();
workbox.precaching.precacheAndRoute(self.__precacheManifest, {});

workbox.routing.registerRoute(/sessions/, workbox.strategies.staleWhileRevalidate(), 'GET');
workbox.routing.registerRoute(/fontawesome/, workbox.strategies.cacheFirst(), 'GET');
workbox.routing.registerRoute(/uikit/, workbox.strategies.cacheFirst(), 'GET');
