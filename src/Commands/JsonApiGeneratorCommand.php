<?php

namespace PeteNys\Generator\Commands;

use PeteNys\Generator\Common\CommandData;

class JsonApiGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'petenys:json_api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a all JsonApi classes for given model';

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

        $this->generateController();

        $this->generateJsonApiItems();

        $this->generateModelItems();

        $this->generateRoute();

        $this->generateObserver();

        $this->generatePolicy();

        $this->generateRepository();

        $this->performPostActionsWithMigration();
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
