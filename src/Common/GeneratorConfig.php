<?php

namespace PeteNys\Generator\Common;

use Illuminate\Support\Str;

class GeneratorConfig
{
    /* Namespace variables */
    public $nsApp;
    public $nsController;
    public $nsControllerExtend;
    public $nsJsonApi;
    public $nsModel;
    public $nsModelExtend;
    public $nsObserver;
    public $nsPolicy;
    public $nsRepository;
    public $nsDataTables;

    public $nsJsonApiTests;
    public $nsPolicyTests;
    public $nsRepositoryTests;
    public $nsTestTraits;
    public $nsTests;

    /* Path variables */
    public $pathController;
    public $pathJsonApi;
    public $pathModel;
    public $pathObserver;
    public $pathPolicy;
    public $pathRepository;
    public $pathJsonApiRoute;
    public $pathJsonApiTests;
    public $pathTests;
    public $pathTestTraits;

    /* Model Names */
    public $mName;
    public $mPlural;
    public $mCamel;
    public $mCamelPlural;
    public $mSnake;
    public $mSnakePlural;
    public $mDashed;
    public $mDashedPlural;
    public $mSlash;
    public $mSlashPlural;
    public $mHuman;
    public $mHumanPlural;

    /* Generator Options */
    public $options;

    /* Prefixes */
    public $prefixes;

    private $commandData;

    /* Command Options */
    public static $availableOptions = [
        'fieldsFile',
        'jsonFromGUI',
        'tableName',
        'fromTable',
        'ignoreFields',
        'include',
        'save',
        'primary',
        'prefix',
        'paginate',
        'skip',
        'relations',
        'plural',
        'softDelete',
        'forceMigrate',
    ];

    public $tableName;

    /** @var string */
    protected $primaryName;

    /* Generator AddOns */
    public $addOns;

    public function init(CommandData &$commandData, $options = null)
    {
        if (!empty($options)) {
            self::$availableOptions = $options;
        }

        $this->mName = $commandData->modelName;

        $this->prepareAddOns();
        $this->prepareOptions($commandData);
        $this->prepareModelNames();
        $this->preparePrefixes();
        $this->loadPaths();
        $this->prepareTableName();
        $this->preparePrimaryName();
        $this->loadNamespaces($commandData);
        $commandData = $this->loadDynamicVariables($commandData);
        $this->commandData = &$commandData;
    }

    public function loadNamespaces(CommandData &$commandData)
    {
        $prefix = $this->prefixes['ns'];

        if (!empty($prefix)) {
            $prefix = '\\'.$prefix;
        }

        $this->nsApp = $commandData->commandObj->getLaravel()->getNamespace();
        $this->nsApp = substr($this->nsApp, 0, strlen($this->nsApp) - 1);
        $this->nsController = config(
                'petenys.laravel_json_api_generator.namespace.json_api_controller',
                'App\Http\Controllers\Json'
            ).$prefix;
        $this->nsJsonApi = config('petenys.laravel_json_api_generator.namespace.json_api', 'App\JsonApi').$prefix;
        $this->nsObserver = config('petenys.laravel_json_api_generator.namespace.observer', 'App\Observers').$prefix;
        $this->nsPolicy = config('petenys.laravel_json_api_generator.namespace.policy', 'App\Policies').$prefix;
        $this->nsRepository = config('petenys.laravel_json_api_generator.namespace.repository', 'App\Repositories').$prefix;
        $this->nsModel = config('petenys.laravel_json_api_generator.namespace.model', 'App\Models').$prefix;
        if (config('petenys.laravel_json_api_generator.ignore_model_prefix', false)) {
            $this->nsModel = config('petenys.laravel_json_api_generator.namespace.model', 'App\Models');
        }
        $this->nsModelExtend = config(
            'petenys.laravel_json_api_generator.model_extend_class',
            'Illuminate\Database\Eloquent\Model'
        );


        $this->nsJsonApiTests = config('petenys.laravel_json_api_generator.namespace.json_api_test', 'Tests\JsonApis');
        $this->nsPolicyTests = config('petenys.laravel_json_api_generator.namespace.policy_test', 'Tests\Policies');
        $this->nsRepositoryTests = config('petenys.laravel_json_api_generator.namespace.repository_test', 'Tests\Repositories');
        $this->nsTestTraits = config('petenys.laravel_json_api_generator.namespace.test_trait', 'Tests\Traits');
        $this->nsTests = config('petenys.laravel_json_api_generator.namespace.tests', 'Tests');
    }

    public function loadPaths()
    {
        $prefix = $this->prefixes['path'];

        if (!empty($prefix)) {
            $prefix .= '/';
        }

        $this->pathController = config(
                'petenys.laravel_json_api_generator.path.json_api_controller',
                app_path('Http/Controllers/Json/')
            ).$prefix;

        $this->pathJsonApi = config(
                'petenys.laravel_json_api_generator.path.json_api',
                app_path('JsonApi/')
            ).$prefix;

        $this->pathModel = config('petenys.laravel_json_api_generator.path.model', app_path('Models/')).$prefix;

        $this->pathObserver = config('petenys.laravel_json_api_generator.path.observer', app_path('Observers/')).$prefix;

        $this->pathPolicy = config('petenys.laravel_json_api_generator.path.policy', app_path('Policies/')).$prefix;

        $this->pathRepository = config(
            'petenys.laravel_json_api_generator.path.repository',
            app_path('Repositories/')
        ).$prefix;

        $this->pathJsonApiRoute = config('petenys.laravel_json_api_generator.path.json_api_route', base_path('routes/json-api.php'));

        $this->pathJsonApiTests = config('petenys.laravel_json_api_generator.path.json_api_test', base_path('tests/JsonApis/'));
        $this->pathPolicyTests = config('petenys.laravel_json_api_generator.path.policy_test', base_path('tests/Policies/'));
        $this->pathRepositoryTests = config('petenys.laravel_json_api_generator.path.repository_test', base_path('tests/Repositories/'));

        $this->pathTestTraits = config('petenys.laravel_json_api_generator.path.test_trait', base_path('tests/Traits/'));

    }

    public function loadDynamicVariables(CommandData &$commandData)
    {
        $commandData->addDynamicVariable('$NAMESPACE_APP$', $this->nsApp);
        $commandData->addDynamicVariable('$NAMESPACE_CONTROLLER$', $this->nsController);
        $commandData->addDynamicVariable('$NAMESPACE_CONTROLLER_EXTEND$', $this->nsControllerExtend);
        $commandData->addDynamicVariable('$NAMESPACE_JSON_API$', $this->nsJsonApi);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL$', $this->nsModel);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL_EXTEND$', $this->nsModelExtend);
        $commandData->addDynamicVariable('$NAMESPACE_OBSERVER$', $this->nsObserver);
        $commandData->addDynamicVariable('$NAMESPACE_POLICY$', $this->nsPolicy);
        $commandData->addDynamicVariable('$NAMESPACE_REPOSITORY$', $this->nsRepository);

        $commandData->addDynamicVariable('$NAMESPACE_API_TESTS$', $this->nsJsonApiTests);
        $commandData->addDynamicVariable('$NAMESPACE_POLICIES_TESTS$', $this->nsPolicyTests);
        $commandData->addDynamicVariable('$NAMESPACE_REPOSITORIES_TESTS$', $this->nsRepositoryTests);
        $commandData->addDynamicVariable('$NAMESPACE_TEST_TRAITS$', $this->nsTestTraits);
        $commandData->addDynamicVariable('$NAMESPACE_TESTS$', $this->nsTests);

        $commandData->addDynamicVariable('$TABLE_NAME$', $this->tableName);
        $commandData->addDynamicVariable('$TABLE_NAME_TITLE$', Str::studly($this->tableName));
        $commandData->addDynamicVariable('$PRIMARY_KEY_NAME$', $this->primaryName);

        $commandData->addDynamicVariable('$MODEL_NAME$', $this->mName);
        $commandData->addDynamicVariable('$MODEL_NAME_CAMEL$', $this->mCamel);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL$', $this->mPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_CAMEL$', $this->mCamelPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SNAKE$', $this->mSnake);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SNAKE$', $this->mSnakePlural);
        $commandData->addDynamicVariable('$MODEL_NAME_DASHED$', $this->mDashed);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_DASHED$', $this->mDashedPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SLASH$', $this->mSlash);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SLASH$', $this->mSlashPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_HUMAN$', $this->mHuman);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_HUMAN$', $this->mHumanPlural);

        $commandData->addDynamicVariable('$JSON_API_MODEL_PATH$', $this->pathJsonApi.$this->mPlural.'/');

        if (!empty($this->prefixes['route'])) {
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', $this->prefixes['route'].'.');
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', str_replace('.', '/', $this->prefixes['route']).'/');
            $commandData->addDynamicVariable('$RAW_ROUTE_PREFIX$', $this->prefixes['route']);
        } else {
            $commandData->addDynamicVariable('$ROUTE_PREFIX$', '');
            $commandData->addDynamicVariable('$ROUTE_NAMED_PREFIX$', '');
        }

        if (!empty($this->prefixes['ns'])) {
            $commandData->addDynamicVariable('$PATH_PREFIX$', $this->prefixes['ns'].'\\');
        } else {
            $commandData->addDynamicVariable('$PATH_PREFIX$', '');
        }

        $commandData->addDynamicVariable(
            '$JSON_API_PREFIX$',
            config('petenys.laravel_json_api_generator.json_api_prefix', 'json/api')
        );

        $commandData->addDynamicVariable(
            '$JSON_API_VERSION$',
            config('petenys.laravel_json_api_generator.api_version', 'v1')
        );

        return $commandData;
    }

    public function prepareTableName()
    {
        if ($this->getOption('tableName')) {
            $this->tableName = $this->getOption('tableName');
        } else {
            $this->tableName = $this->mSnakePlural;
        }
    }

    public function preparePrimaryName()
    {
        if ($this->getOption('primary')) {
            $this->primaryName = $this->getOption('primary');
        } else {
            $this->primaryName = 'id';
        }
    }

    public function prepareModelNames()
    {
        if ($this->getOption('plural')) {
            $this->mPlural = $this->getOption('plural');
        } else {
            $this->mPlural = Str::plural($this->mName);
        }
        $this->mCamel = Str::camel($this->mName);
        $this->mCamelPlural = Str::camel($this->mPlural);
        $this->mSnake = Str::snake($this->mName);
        $this->mSnakePlural = Str::snake($this->mPlural);
        $this->mDashed = str_replace('_', '-', Str::snake($this->mSnake));
        $this->mDashedPlural = str_replace('_', '-', Str::snake($this->mSnakePlural));
        $this->mSlash = str_replace('_', '/', Str::snake($this->mSnake));
        $this->mSlashPlural = str_replace('_', '/', Str::snake($this->mSnakePlural));
        $this->mHuman = Str::title(str_replace('_', ' ', Str::snake($this->mSnake)));
        $this->mHumanPlural = Str::title(str_replace('_', ' ', Str::snake($this->mSnakePlural)));
    }

    public function prepareOptions(CommandData &$commandData)
    {
        foreach (self::$availableOptions as $option) {
            $this->options[$option] = $commandData->commandObj->option($option);
        }

        if (isset($options['fromTable']) and $this->options['fromTable']) {
            if (!$this->options['tableName']) {
                $commandData->commandError('tableName required with fromTable option.');
                exit;
            }
        }

        $this->options['softDelete'] = config('petenys.laravel_json_api_generator.options.softDelete', false);
        if (!empty($this->options['skip'])) {
            $this->options['skip'] = array_map('trim', explode(',', $this->options['skip']));
        }
    }

    public function preparePrefixes()
    {
        $this->prefixes['route'] = explode('/', config('petenys.laravel_json_api_generator.prefixes.route', ''));
        $this->prefixes['path'] = explode('/', config('petenys.laravel_json_api_generator.prefixes.path', ''));

        if ($this->getOption('prefix')) {
            $multiplePrefixes = explode(',', $this->getOption('prefix'));

            $this->prefixes['route'] = array_merge($this->prefixes['route'], $multiplePrefixes);
            $this->prefixes['path'] = array_merge($this->prefixes['path'], $multiplePrefixes);
        }

        $this->prefixes['route'] = array_diff($this->prefixes['route'], ['']);
        $this->prefixes['path'] = array_diff($this->prefixes['path'], ['']);

        $routePrefix = '';

        foreach ($this->prefixes['route'] as $singlePrefix) {
            $routePrefix .= Str::camel($singlePrefix).'.';
        }

        if (!empty($routePrefix)) {
            $routePrefix = substr($routePrefix, 0, strlen($routePrefix) - 1);
        }

        $this->prefixes['route'] = $routePrefix;

        $nsPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $nsPrefix .= Str::title($singlePrefix).'\\';
        }

        if (!empty($nsPrefix)) {
            $nsPrefix = substr($nsPrefix, 0, strlen($nsPrefix) - 1);
        }

        $this->prefixes['ns'] = $nsPrefix;

        $pathPrefix = '';

        foreach ($this->prefixes['path'] as $singlePrefix) {
            $pathPrefix .= Str::title($singlePrefix).'/';
        }

        if (!empty($pathPrefix)) {
            $pathPrefix = substr($pathPrefix, 0, strlen($pathPrefix) - 1);
        }

        $this->prefixes['path'] = $pathPrefix;
    }

    public function overrideOptionsFromJsonFile($jsonData)
    {
        $options = self::$availableOptions;

        foreach ($options as $option) {
            if (isset($jsonData['options'][$option])) {
                $this->setOption($option, $jsonData['options'][$option]);
            }
        }

        // prepare prefixes than reload namespaces, paths and dynamic variables
        if (!empty($this->getOption('prefix'))) {
            $this->preparePrefixes();
            $this->loadPaths();
            $this->loadNamespaces($this->commandData);
            $this->loadDynamicVariables($this->commandData);
        }

        $addOns = ['tests'];

        foreach ($addOns as $addOn) {
            if (isset($jsonData['addOns'][$addOn])) {
                $this->addOns[$addOn] = $jsonData['addOns'][$addOn];
            }
        }
    }

    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return false;
    }

    public function getAddOn($addOn)
    {
        if (isset($this->addOns[$addOn])) {
            return $this->addOns[$addOn];
        }

        return false;
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function prepareAddOns()
    {
        $this->addOns['tests'] = config('petenys.laravel_json_api_generator.add_on.tests', false);
    }
}
