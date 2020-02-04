<?php
return [
    'admin/datascribe' => [
        'breadcrumbs' => [],
        'text' => 'Dashboard', // @translate
        'params' => [],
    ],
    'admin/datascribe-project' => [
        'breadcrumbs' => ['admin/datascribe'],
        'text' => 'Projects', // @translate
        'params' => [],
    ],
    'admin/datascribe-dataset' => [
        'breadcrumbs' => ['admin/datascribe', 'admin/datascribe-project'],
        'text' => 'Datasets', // @translate
        'params' => ['project-id'],
    ],
    'admin/datascribe-item' => [
        'breadcrumbs' => ['admin/datascribe', 'admin/datascribe-project', 'admin/datascribe-dataset'],
        'text' => 'Items', // @translate
        'params' => ['project-id', 'dataset-id'],
    ],
    'admin/datascribe-record' => [
        'breadcrumbs' => ['admin/datascribe', 'admin/datascribe-project', 'admin/datascribe-dataset', 'admin/datascribe-item'],
        'text' => 'Records', // @translate
        'params' => ['project-id', 'dataset-id', 'item-id'],
    ],
];
