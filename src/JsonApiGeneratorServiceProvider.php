<?php

namespace PeteNys\Generator;

use Illuminate\Support\ServiceProvider;
use PeteNys\Generator\Commands\JsonApiGeneratorCommand;
use PeteNys\Generator\Commands\Controllers\ControllerGeneratorCommand;
use PeteNys\Generator\Commands\JsonApi\AdapterGeneratorCommand;
use PeteNys\Generator\Commands\JsonApi\SchemaGeneratorCommand;
use PeteNys\Generator\Commands\JsonApi\ValidatorGeneratorCommand;
use PeteNys\Generator\Commands\Observers\ObserverGeneratorCommand;
use PeteNys\Generator\Commands\Policies\PolicyGeneratorCommand;
use PeteNys\Generator\Commands\Models\MigrationGeneratorCommand;
use PeteNys\Generator\Commands\Models\ModelGeneratorCommand;
use PeteNys\Generator\Commands\Repositories\RepositoryGeneratorCommand;
use PeteNys\Generator\Commands\Publish\GeneratorPublishCommand;
use PeteNys\Generator\Commands\RollbackGeneratorCommand;
use PeteNys\Generator\Commands\TestsGeneratorCommand;

class JsonApiGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/laravel_json_api_generator.php';

        $this->publishes([
            $configPath => config_path('petenys/laravel_json_api_generator.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('petenys.api.json_api', function ($app) {
            return new JsonApiGeneratorCommand();
        });

        $this->app->singleton('petenys.api.controller', function ($app) {
            return new AdapterGeneratorCommand();
        });

        $this->app->singleton('petenys.api.migration', function ($app) {
            return new MigrationGeneratorCommand();
        });

        $this->app->singleton('petenys.api.controller', function ($app) {
            return new ControllerGeneratorCommand();
        });

        $this->app->singleton('petenys.api.adapter', function ($app) {
            return new AdapterGeneratorCommand();
        });

        $this->app->singleton('petenys.api.schema', function ($app) {
            return new SchemaGeneratorCommand();
        });

        $this->app->singleton('petenys.api.validator', function ($app) {
            return new ValidatorGeneratorCommand();
        });

        $this->app->singleton('petenys.api.model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('petenys.api.observer', function ($app) {
            return new ObserverGeneratorCommand();
        });

        $this->app->singleton('petenys.api.policy', function ($app) {
            return new PolicyGeneratorCommand();
        });

        $this->app->singleton('petenys.api.repository', function ($app) {
            return new RepositoryGeneratorCommand();
        });

        $this->app->singleton('petenys.api.tests', function ($app) {
            return new TestsGeneratorCommand();
        });

        $this->app->singleton('petenys.api.rollback', function ($app) {
            return new RollbackGeneratorCommand();
        });

        $this->commands([
            'petenys.api.controller',
            'petenys.api.json_api',
            'petenys.api.adapter',
            'petenys.api.schema',
            'petenys.api.validator',
            'petenys.api.migration',
            'petenys.api.model',
            'petenys.api.observer',
            'petenys.api.policy',
            'petenys.api.repository',
            'petenys.api.tests',
            'petenys.api.rollback',
        ]);
    }
}
