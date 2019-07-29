<?php

namespace PeteNys\Generator\Commands;

use PeteNys\Generator\Generators\JsonApi\AdapterGenerator;
use PeteNys\Generator\Generators\JsonApi\SchemaGenerator;
use PeteNys\Generator\Generators\JsonApi\ValidatorGenerator;
use PeteNys\Generator\Generators\Observers\ObserverGenerator;
use PeteNys\Generator\Generators\Policies\PolicyGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\Controllers\ControllerGenerator;
use PeteNys\Generator\Generators\RouteGenerator;
use PeteNys\Generator\Generators\JsonApiTestGenerator;
use PeteNys\Generator\Generators\Models\MigrationGenerator;
use PeteNys\Generator\Generators\Models\ModelGenerator;
use PeteNys\Generator\Generators\Repositories\RepositoryGenerator;
use PeteNys\Generator\Generators\Repositories\RepositoryTestGenerator;
use PeteNys\Generator\Generators\TestTraitGenerator;
use PeteNys\Generator\Utils\FileUtil;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BaseCommand extends Command
{
    /**
     * The command Data.
     *
     * @var CommandData
     */
    public $commandData;

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

    public function handle()
    {
        $this->commandData->modelName = $this->argument('model');

        $this->commandData->initCommandData();
        $this->commandData->getFields();
    }

    public function generateController()
    {
        if ($this->isInclude('controller')) {
            $controllerGenerator = new ControllerGenerator($this->commandData);
            $controllerGenerator->generate();
        }
    }

    public function generateRoute()
    {

        if (!$this->isSkip('route')) {
            $routeGenerator = new RouteGenerator($this->commandData);
            $routeGenerator->generate();
        }
    }

    public function generateJsonApiItems()
    {
        if (!$this->isSkip('adapter')) {
            $adapterGenerator = new AdapterGenerator($this->commandData);
            $adapterGenerator->generate();
        }

        if (!$this->isSkip('schema')) {
            $schemaGenerator = new SchemaGenerator($this->commandData);
            $schemaGenerator->generate();
        }

        if (!$this->isSkip('validator')) {
            $validatorGenerator = new ValidatorGenerator($this->commandData);
            $validatorGenerator->generate();
        }
    }

    public function generateModelItems()
    {
        if (!$this->commandData->getOption('fromTable') and $this->isInclude('migration')) {
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        if (!$this->isSkip('model')) {
            $modelGenerator = new ModelGenerator($this->commandData);
            $modelGenerator->generate();
        }

        if (!$this->isSkip('repository')) {
            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
        }
    }

    public function generateObserver()
    {
        if (!$this->isInclude('observer')) {
            $observerGenerator = new ObserverGenerator($this->commandData);
            $observerGenerator->generate();
        }
    }

    public function generatePolicy()
    {
        if (!$this->isInclude('policy')) {
            $policyGenerator = new PolicyGenerator($this->commandData);
            $policyGenerator->generate();
        }
    }

    public function generateRepository()
    {
        if (!$this->isSkip('repository')) {
            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
        }
    }

    public function performPostActions($runMigration = false)
    {

        if ($runMigration) {
            if ($this->commandData->getOption('forceMigrate')) {
                $this->runMigration();
            } elseif (!$this->commandData->getOption('fromTable') and !$this->isSkip('migration')) {
                $requestFromConsole = (php_sapi_name() == 'cli') ? true : false;
                if ($this->commandData->getOption('jsonFromGUI') && $requestFromConsole) {
                    $this->runMigration();
                } elseif ($requestFromConsole && $this->confirm("\nDo you want to migrate database? [y|N]", false)) {
                    $this->runMigration();
                }
            }
        }
        if (!$this->isSkip('dump-autoload')) {
            $this->info('Generating autoload files');
            $this->composer->dumpOptimized();
        }
    }

    public function runMigration()
    {
        $migrationPath = config('petenys.laravel_json_api_generator.path.migration', 'database/migrations/');
        $path = Str::after($migrationPath, base_path()); // get path after base_path
        $this->call('migrate', ['--path' => $path, '--force' => true]);

        return true;
    }

    public function isInclude($include)
    {
        if ($this->commandData->getOption('include')) {
            return in_array($include, (array) $this->commandData->getOption('include'));
        }

        return false;
    }

    public function isSkip($skip)
    {
        if ($this->commandData->getOption('skip')) {
            return in_array($skip, (array) $this->commandData->getOption('skip'));
        }

        return false;
    }

    public function performPostActionsWithMigration()
    {
        $this->performPostActions(true);
    }

    /**
     * @param $fileName
     * @param string $prompt
     *
     * @return bool
     */
    protected function confirmOverwrite($fileName, $prompt = '')
    {
        $prompt = (empty($prompt))
            ? $fileName.' already exists. Do you want to overwrite it? [y|N]'
            : $prompt;

        return $this->confirm($prompt, false);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['fieldsFile', null, InputOption::VALUE_REQUIRED, 'Fields input as json file'],
            ['jsonFromGUI', null, InputOption::VALUE_REQUIRED, 'Direct Json string while using GUI interface'],
            ['plural', null, InputOption::VALUE_REQUIRED, 'Plural Model name'],
            ['tableName', null, InputOption::VALUE_REQUIRED, 'Table Name'],
            ['fromTable', null, InputOption::VALUE_NONE, 'Generate from existing table'],
            ['ignoreFields', null, InputOption::VALUE_REQUIRED, 'Ignore fields while generating from table'],
            ['include', null, InputOption::VALUE_REQUIRED, 'Include Specific Items to Generate (controller,migration,observer,policy,route,tests,dump-autoload)'],
            ['save', null, InputOption::VALUE_NONE, 'Save model schema to file'],
            ['primary', null, InputOption::VALUE_REQUIRED, 'Custom primary key'],
            ['prefix', null, InputOption::VALUE_REQUIRED, 'Prefix for all files'],
            ['paginate', null, InputOption::VALUE_REQUIRED, 'Pagination for index.blade.php'],
            ['skip', null, InputOption::VALUE_REQUIRED, 'Skip Specific Items to Generate (adapter,schema,validator,model,repository,route,tests,dump-autoload)'],
            ['relations', null, InputOption::VALUE_NONE, 'Specify if you want to pass relationships for fields'],
            ['softDelete', null, InputOption::VALUE_NONE, 'Soft Delete Option'],
            ['forceMigrate', null, InputOption::VALUE_NONE, 'Specify if you want to run migration or not'],
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
