<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    */

    'path' => [

        'controller'    => app_path('Http/Controllers/Json/'),

        'json_api'    => app_path('JsonApi/'),

        'migration'         => base_path('database/migrations/'),

        'model'             => app_path('Models/'),

        'observer'        => app_path('Observers/'),

        'observer_provider'        => app_path('Providers/ObserverServiceProvider.php'),

        'policy'        => app_path('Policies/'),

        'repository'        => app_path('Repositories/'),

        'json_api_route'        => base_path('routes/json-api.php'),

        'json_api_resource_config'        => base_path('config/json-api-version.php'),

        'test_trait'        => base_path('tests/Traits/'),

        'policy_test'   => base_path('tests/Policies/'),

        'repository_test'   => base_path('tests/Repositories/'),

        'json_api_test'          => base_path('tests/JsonApis/'),

        'tests'             => base_path('tests/'),

        'templates_dir'     => base_path('resources/petenys/cl-generator-templates/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => [

        'controller'        => 'App\Http\Controllers\Json',

        'json_api'    => 'App\JsonApi',

        'model'             => 'App\Models',

        'observer'        => 'App\Observers',

        'policy'        => 'App\Policies',

        'repository'        => 'App\Repositories',

        'test_trait'        => 'Tests\Traits',

        'policy_test'   => 'Tests\Policies',

        'repository_test'   => 'Tests\Repositories',

        'json_api_test'          => 'Tests\JsonApis',

        'tests'             => 'Tests',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model extend class
    |--------------------------------------------------------------------------
    |
    */

    'model_extend_class' => 'Eloquent',

    /*
    |--------------------------------------------------------------------------
    | Json Api routes prefix & version
    |--------------------------------------------------------------------------
    |
    */

    'json_api_prefix'  => 'json/api',

    'json_api_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    */

    'options' => [

        'softDelete' => true,

        'tables_searchable_default' => false,

        'excluded_fields' => ['id'], // Array of columns that doesn't required while creating module
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefixes
    |--------------------------------------------------------------------------
    |
    */

    'prefixes' => [

        'route' => '',  // using admin will create route('admin.?.index') type routes

        'path' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Add-Ons
    |--------------------------------------------------------------------------
    |
    */

    'add_on' => [

        'tests'         => true,

    ],

    /*
    |--------------------------------------------------------------------------
    | Timestamp Fields
    |--------------------------------------------------------------------------
    |
    */

    'timestamps' => [

        'enabled'       => true,

        'created_at'    => 'created_at',

        'updated_at'    => 'updated_at',

        'deleted_at'    => 'deleted_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Save model files to `App/Models` when use `--prefix`.
    |--------------------------------------------------------------------------
    |
    */
    'ignore_model_prefix' => false,

    /*
    |--------------------------------------------------------------------------
    | Specify custom doctrine mappings as per your need
    |--------------------------------------------------------------------------
    |
    */
    'from_table' => [

        'doctrine_mappings' => [],
    ],

];
