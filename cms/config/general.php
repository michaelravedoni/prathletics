<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see craft\config\GeneralConfig
 */

return [
    // Global settings
    '*' => [
        // Default Week Start Day (0 = Sunday, 1 = Monday...)
        'defaultWeekStartDay' => 1,

        // Enable CSRF Protection (recommended)
        'enableCsrfProtection' => true,

        // Whether generated URLs should omit "index.php"
        'omitScriptNameInUrls' => true,

        // Control Panel trigger word
        'cpTrigger' => 'admin',

        // The secure key Craft will use for hashing and encrypting data
        'securityKey' => getenv('SECURITY_KEY'),
        'siteUrl' => [
            'default' => getenv('SITE_URL'),
        ],
        'aliases' => [
          '@baseUrl' => getenv('BASE_URL'),
          '@assetBasePath' => getenv('ASSET_BASE_PATH'),
          '@assetBaseUrl' => getenv('SITE_URL') . getenv('ASSET_BASE_URL'),
          '@contenuBasePath' => getenv('CONTENU_BASE_PATH'),
		],
    ],

    // Dev environment settings
    'dev' => [
        'devMode' => true,
    ],

    // Staging environment settings
    'staging' => [
		'devMode' => true,
    ],

    // Production environment settings
    'production' => [
        'devMode' => false,
    ],
];
