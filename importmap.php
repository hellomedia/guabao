<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'controlroom' => [
        'path' => './assets/controlroom.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
    '@hotwired/turbo' => [
        'version' => '8.0.12',
    ],
    'stimulus-use' => [
        'version' => '0.52.3',
    ],
    '@rails/request.js' => [
        'version' => '0.0.11',
    ],
    'lodash/debounce' => [
        'version' => '4.17.21',
    ],
    'three' => [
        'version' => '0.175.0',
    ],
    'uevent' => [
        'version' => '2.2.0',
    ],
    '@fancyapps/ui' => [
        'version' => '5.0.36',
    ],
    '@photo-sphere-viewer/core' => [
        'version' => '5.13.2',
    ],
    '@photo-sphere-viewer/core/index.min.css' => [
        'version' => '5.13.2',
        'type' => 'css',
    ],
];
