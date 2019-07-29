<?php

namespace PeteNys\Generator\Commands;

use Illuminate\Console\Command;
use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\Controllers\ControllerGenerator;
use PeteNys\Generator\Generators\RouteGenerator;
use PeteNys\Generator\Generators\JsonApiTestGenerator;
use PeteNys\Generator\Generators\JsonApi\AdapterGenerator;
use PeteNys\Generator\Generators\JsonApi\SchemaGenerator;
use PeteNys\Generator\Generators\JsonApi\ValidatorGenerator;
use PeteNys\Generator\Generators\Models\MigrationGenerator;
use PeteNys\Generator\Generators\Models\ModelGenerator;
use PeteNys\Generator\Generators\Repositories\RepositoryGenerator;
use PeteNys\Generator\Generators\Repositories\RepositoryTestGenerator;
use PeteNys\Generator\Generators\Observers\ObserverGenerator;
use PeteNys\Generator\Generators\Policies\PolicyGenerator;
use PeteNys\Generator\Generators\TestTraitGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RollbackGeneratorCommand extends Command
{
    /**
     * The command Data.
     *
     * @var CommandData
     */
    public $commandData;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'petenys:rollback';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback a full JsonApi objects for given model';

    /**
     * @var Composer
     */
    public $composer;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->composer = app()['composer'];
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (!in_array($this->argument('type'), [
            CommandData::$COMMAND_TYPE_JSON_API
        ])) {
            $this->error('invalid rollback type');
        }

        $this->commandData = new CommandData($this, $this->argument('type'));
        $this->commandData->config->mName = $this->commandData->modelName = $this->argument('model');

        $this->commandData->config->init($this->commandData, ['tableName', 'prefix', 'plural']);

        $controllerGenerator = new ControllerGenerator($this->commandData);
        $controllerGenerator->rollback();

        $routeGenerator = new RouteGenerator($this->commandData);
        $routeGenerator->rollback();

        $adapterGenerator = new AdapterGenerator($this->commandData);
        $adapterGenerator->rollback();

        $schemaGenerator = new SchemaGenerator($this->commandData);
        $schemaGenerator->rollback();

        $validatorGenerator = new ValidatorGenerator($this->commandData);
        $validatorGenerator->rollback();

        $migrationGenerator = new MigrationGenerator($this->commandData);
        $migrationGenerator->rollback();

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->rollback();

        $observerGenerator = new ObserverGenerator($this->commandData);
        $observerGenerator->rollback();

        $policyGenerator = new PolicyGenerator($this->commandData);
        $policyGenerator->rollback();

        $repositoryGenerator = new RepositoryGenerator($this->commandData);
        $repositoryGenerator->rollback();

        $routesGenerator = new RouteGenerator($this->commandData);
        $routesGenerator->rollback();

        if ($this->commandData->getAddOn('tests')) {
            $repositoryTestGenerator = new RepositoryTestGenerator($this->commandData);
            $repositoryTestGenerator->rollback();

            $testTraitGenerator = new TestTraitGenerator($this->commandData);
            $testTraitGenerator->rollback();

            $JsonApiTestGenerator = new JsonApiTestGenerator($this->commandData);
            $JsonApiTestGenerator->rollback();
        }

        $this->info('Generating autoload files');
        $this->composer->dumpOptimized();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['tableName', null, InputOption::VALUE_REQUIRED, 'Table Name'],
            ['prefix', null, InputOption::VALUE_REQUIRED, 'Prefix for all files'],
            ['plural', null, InputOption::VALUE_REQUIRED, 'Plural Model name'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'Singular Model name'],
        ];
    }
}
