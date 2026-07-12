<?php
return [
    'title' => __('Create') . ' :label',
    'breadcrumb' => __('Create'),
    'form' => [
        'actions' => [
            'cancel' => ['label' => __('Cancel')],
            'create' => ['label' => __('Create')],
            'create_another' => ['label' => __('Create & create another')],
        ],
    ],
    'notifications' => [
        'created' => ['title' => __('Saved')],
    ],
];