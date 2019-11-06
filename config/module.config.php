<?php
return [
    'datascribe_data_types' => [
        'invokables' => [
            'text' => Datascribe\DatascribeDataType\Text::class,
            'number' => Datascribe\DatascribeDataType\Number::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => OMEKA_PATH . '/modules/Datascribe/language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            sprintf('%s/../view', __DIR__),
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            sprintf('%s/../src/Entity', __DIR__),
        ],
        'proxy_paths' => [
            sprintf('%s/../data/doctrine-proxies', __DIR__),
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Datascribe\DataTypeManager' => Datascribe\Service\DataTypeManagerFactory::class,
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'datascribe_projects' => Datascribe\Api\Adapter\DatascribeProjectAdapter::class,
            'datascribe_datasets' => Datascribe\Api\Adapter\DatascribeDatasetAdapter::class,
            'datascribe_items' => Datascribe\Api\Adapter\DatascribeItemAdapter::class,
            'datascribe_records' => Datascribe\Api\Adapter\DatascribeRecordAdapter::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Datascribe\Controller\Admin\Index' => Datascribe\Controller\Admin\IndexController::class,
            'Datascribe\Controller\Admin\Project' => Datascribe\Controller\Admin\ProjectController::class,
            'Datascribe\Controller\Admin\Dataset' => Datascribe\Controller\Admin\DatasetController::class,
            'Datascribe\Controller\Admin\Item' => Datascribe\Controller\Admin\ItemController::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'datascribe' => Datascribe\Service\ControllerPlugin\DatascribeFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Datascribe\Form\ItemBatchForm' => Datascribe\Service\Form\ItemBatchFormFactory::class,
            'Datascribe\Form\ItemSearchForm' => Datascribe\Service\Form\ItemSearchFormFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'datascribe' => Datascribe\Service\ViewHelper\DatascribeFactory::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'DataScribe', // @translate
                'route' => 'admin/datascribe',
                'resource' => 'Datascribe\Controller\Admin\Index',
                'privilege' => 'index',
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'datascribe' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'index',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'datascribe-project' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/project[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'project',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                    'datascribe-project-id' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'project',
                                'action' => 'show',
                            ],
                        ],
                    ],
                    'datascribe-dataset' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/dataset[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'dataset',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                    'datascribe-dataset-id' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/:dataset-id[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'dataset-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'dataset',
                                'action' => 'show',
                            ],
                        ],
                    ],
                    'datascribe-item' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/:dataset-id/item[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'dataset-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'item',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                    'datascribe-item-id' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/:dataset-id/:item-id[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'dataset-id' => '\d+',
                                'item-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'item',
                                'action' => 'show',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
