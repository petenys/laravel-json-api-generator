<?php

namespace PeteNys\Generator\Commands;

use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\JsonApiTestGenerator;
use PeteNys\Generator\Generators\Repositories\PolicyTestGenerator;
use PeteNys\Generator\Generators\Repositories\RepositoryTestGenerator;
use PeteNys\Generator\Generators\TestTraitGenerator;

class TestsGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'petenys:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tests command';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_JSON_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $policyTestGenerator = new PolicyTestGenerator($this->commandData);
        $policyTestGenerator->generate();

        $repositoryTestGenerator = new RepositoryTestGenerator($this->commandData);
        $repositoryTestGenerator->generate();

        $testTraitGenerator = new TestTraitGenerator($this->commandData);
        $testTraitGenerator->generate();

        $apiTestGenerator = new JsonApiTestGenerator($this->commandData);
        $apiTestGenerator->generate();

        $this->performPostActions();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }
}
