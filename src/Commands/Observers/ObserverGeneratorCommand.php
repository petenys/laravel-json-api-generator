<?php

namespace PeteNys\Generator\Commands\Observers;

use PeteNys\Generator\Commands\BaseCommand;
use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\Observers\ObserverGenerator;

class ObserverGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'petenys:observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create observer command';

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

        $observerGenerator = new ObserverGenerator($this->commandData);
        $observerGenerator->generate();

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
