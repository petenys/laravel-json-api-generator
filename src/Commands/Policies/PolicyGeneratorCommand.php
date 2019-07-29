<?php

namespace PeteNys\Generator\Commands\Policies;

use PeteNys\Generator\Commands\BaseCommand;
use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\Policies\PolicyGenerator;

class PolicyGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'petenys:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create policy command';

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

        $policyGenerator = new PolicyGenerator($this->commandData);
        $policyGenerator->generate();

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
