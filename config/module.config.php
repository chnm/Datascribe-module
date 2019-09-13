<?php
return [
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
            'Datascribe\FieldDataTypeManager' => Datascribe\Service\FieldDataTypeManagerFactory::class,
        ],
    ],
    'datascribe_field_data_types' => [
        'invokables' => [
            'text' => Datascribe\FieldDataType\Text::class,
        ],
    ],
];
