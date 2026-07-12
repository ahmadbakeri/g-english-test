<?php

$locales = ['id', 'zh_CN', 'zh_PY', 'ja', 'ja_RM', 'ar', 'ar_RM'];

$createRecord = <<<'EOD'
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
EOD;

$editRecord = <<<'EOD'
<?php
return [
    'title' => __('Edit') . ' :label',
    'breadcrumb' => __('Edit'),
    'form' => [
        'actions' => [
            'cancel' => ['label' => __('Cancel')],
            'save' => ['label' => __('Save changes')],
        ],
    ],
    'notifications' => [
        'saved' => ['title' => __('Saved')],
    ],
];
EOD;

$createAction = <<<'EOD'
<?php
return [
    'single' => [
        'label' => __('Create') . ' :label',
        'modal' => [
            'heading' => __('Create') . ' :label',
            'actions' => [
                'create' => ['label' => __('Create')],
                'create_another' => ['label' => __('Create & create another')],
            ],
        ],
        'notifications' => [
            'created' => ['title' => __('Saved')],
        ],
    ],
];
EOD;

$dashboard = <<<'EOD'
<?php
return [
    'title' => __('Dashboard'),
];
EOD;

foreach ($locales as $locale) {
    // 1. Create Record Page
    $dir = "lang/vendor/filament-panels/{$locale}/resources/pages";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    file_put_contents("{$dir}/create-record.php", $createRecord);
    file_put_contents("{$dir}/edit-record.php", $editRecord);

    // 2. Create Action Modal
    $dir2 = "lang/vendor/filament-actions/{$locale}";
    if (!is_dir($dir2)) mkdir($dir2, 0777, true);
    file_put_contents("{$dir2}/create.php", $createAction);

    // 3. Dashboard Page
    $dir3 = "lang/vendor/filament-panels/{$locale}/pages";
    if (!is_dir($dir3)) mkdir($dir3, 0777, true);
    file_put_contents("{$dir3}/dashboard.php", $dashboard);
}

echo "Translation overrides created successfully.\n";
