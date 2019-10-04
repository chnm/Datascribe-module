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
    'admin/datascribe-project-id' => [
        'breadcrumbs' => ['admin/datascribe'],
        'text' => 'Projects', // @translate
        'params' => [],
    ],
    'admin/datascribe-dataset' => [
        'breadcrumbs' => ['admin/datascribe', 'admin/datascribe-project'],
        'text' => 'Datasets', // @translate
        'params' => ['project-id'],
    ],
    'admin/datascribe-dataset-id' => [
        'breadcrumbs' => ['admin/datascribe', 'admin/datascribe-project'],
        'text' => 'Datasets', // @translate
        'params' => ['project-id'],
    ],
];
