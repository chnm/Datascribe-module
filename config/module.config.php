<?php
namespace Datascribe;

return [
    'datascribe_data_types' => [
        'invokables' => [
            'text' => DatascribeDataType\Text::class,
            'textarea' => DatascribeDataType\Textarea::class,
            'number' => DatascribeDataType\Number::class,
            'select' => DatascribeDataType\Select::class,
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
            'Datascribe\DataTypeManager' => Service\DataTypeManagerFactory::class,
        ],
    ],
    'api_adapters' => [
        'invokables' => [
            'datascribe_projects' => Api\Adapter\DatascribeProjectAdapter::class,
            'datascribe_datasets' => Api\Adapter\DatascribeDatasetAdapter::class,
            'datascribe_items' => Api\Adapter\DatascribeItemAdapter::class,
            'datascribe_records' => Api\Adapter\DatascribeRecordAdapter::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Datascribe\Controller\Admin\Index' => Controller\Admin\IndexController::class,
            'Datascribe\Controller\Admin\Project' => Controller\Admin\ProjectController::class,
            'Datascribe\Controller\Admin\Dataset' => Controller\Admin\DatasetController::class,
            'Datascribe\Controller\Admin\Item' => Controller\Admin\ItemController::class,
            'Datascribe\Controller\Admin\Record' => Controller\Admin\RecordController::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'datascribe' => Service\ControllerPlugin\DatascribeFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Datascribe\Form\DatasetForm' => Service\Form\DatasetFormFactory::class,
            'Datascribe\Form\ItemSearchForm' => Service\Form\ItemSearchFormFactory::class,
            'Datascribe\Form\ItemForm' => Service\Form\ItemFormFactory::class,
            'Datascribe\Form\ItemBatchForm' => Service\Form\ItemBatchFormFactory::class,
            'Datascribe\Form\RecordSearchForm' => Service\Form\RecordSearchFormFactory::class,
            'Datascribe\Form\RecordForm' => Service\Form\RecordFormFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'datascribeFormText' => Form\ViewHelper\DatascribeFormText::class,
            'datascribeFormTextarea' => Form\ViewHelper\DatascribeFormTextarea::class,
        ],
        'factories' => [
            'datascribe' => Service\ViewHelper\DatascribeFactory::class,
        ],
        'delegators' => [
            'Zend\Form\View\Helper\FormElement' => [
                Service\Delegator\FormElementDelegatorFactory::class,
            ],
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
                    'datascribe-record' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/:dataset-id/:item-id/record[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'dataset-id' => '\d+',
                                'item-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'record',
                                'action' => 'browse',
                            ],
                        ],
                    ],
                    'datascribe-record-id' =>  [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/datascribe/:project-id/:dataset-id/:item-id/:record-id[/:action]',
                            'constraints' => [
                                'project-id' => '\d+',
                                'dataset-id' => '\d+',
                                'item-id' => '\d+',
                                'record-id' => '\d+',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Datascribe\Controller\Admin',
                                'controller' => 'record',
                                'action' => 'show',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
