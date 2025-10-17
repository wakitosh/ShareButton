<?php

/**
 * @file
 * ShareButton module configuration.
 */

return [
  'view_manager' => [
    'template_path_stack' => [
      __DIR__ . '/../view',
    ],
    'strategies' => [
      'ViewJsonStrategy',
    ],
  ],
  'view_helpers' => [
    'invokables' => [
      'shareButton' => 'ShareButton\\View\\Helper\\ShareButton',
      'sharing' => 'ShareButton\\View\\Helper\\ShareButton',
    ],
  ],
  'block_layouts' => [
    'invokables' => [
      'sharebutton' => 'ShareButton\\Site\\BlockLayout\\ShareButton',
    ],
    'aliases' => [
      // Legacy alias to keep backward compatibility
      // without duplicating UI entries.
      'sharing' => 'sharebutton',
    ],
  ],
  'resource_page_block_layouts' => [
    'invokables' => [
      'sharebutton' => 'ShareButton\\Site\\ResourcePageBlockLayout\\ShareButton',
    ],
    'aliases' => [
      // Legacy alias to keep backward compatibility
      // without duplicating UI entries.
      'sharing' => 'sharebutton',
    ],
  ],
  'controllers' => [
    'invokables' => [
      'ShareButton\\Controller\\Index' => 'ShareButton\\Controller\\IndexController',
      'ShareButton\\Controller\\Oembed' => 'ShareButton\\Controller\\OembedController',
    ],
  ],
  'router' => [
    'routes' => [
      'embed-item' => [
        'type' => 'Segment',
        'options' => [
          'route' => '/embed-item/:site-slug/:item-id',
          'defaults' => [
            '__NAMESPACE__' => 'ShareButton\\Controller',
            'controller' => 'Index',
            'action' => 'embedItem',
          ],
        ],
      ],
      'embed-media' => [
        'type' => 'Segment',
        'options' => [
          'route' => '/embed-media/:site-slug/:media-id',
          'defaults' => [
            '__NAMESPACE__' => 'ShareButton\\Controller',
            'controller' => 'Index',
            'action' => 'embedMedia',
          ],
        ],
      ],
      'embed-page' => [
        'type' => 'Segment',
        'options' => [
          'route' => '/embed-page/:page-id',
          'defaults' => [
            '__NAMESPACE__' => 'ShareButton\\Controller',
            'controller' => 'Index',
            'action' => 'embedPage',
          ],
        ],
      ],
      'oembed' => [
        'type' => 'Literal',
        'options' => [
          'route' => '/oembed',
          'defaults' => [
            'controller' => 'ShareButton\\Controller\\Oembed',
            'action' => 'index',
          ],
        ],
      ],
    ],
  ],
  'translator' => [
    'translation_file_patterns' => [
      [
        'type' => 'gettext',
        'base_dir' => dirname(__DIR__) . '/language',
        'pattern' => '%s.mo',
        'text_domain' => NULL,
      ],
    ],
  ],
];
