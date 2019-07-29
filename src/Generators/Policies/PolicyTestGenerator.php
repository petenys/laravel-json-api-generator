<?php

namespace PeteNys\Generator\Generators\Repositories;

use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Utils\FileUtil;
use PeteNys\Generator\Generators\BaseGenerator;

class PolicyTestGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $fileName;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = config('petenys.laravel_json_api_generator.path.policy_test', base_path('tests/Policies/'));
        $this->fileName = $this->commandData->modelName.'PolicyTest.php';
    }

    public function generate()
    {
        $templateData = get_template_stub('test.policy_test', 'laravel-generator');

        $templateData = $this->fillTemplate($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandObj->comment("\nPolicyTest created: ");
        $this->commandData->commandObj->info($this->fileName);
    }

    private function fillTemplate($templateData)
    {
        $templateData = fill_template_stub($this->commandData->dynamicVars, $templateData);

        return $templateData;
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->commandData->commandComment('Policy Test file deleted: '.$this->fileName);
        }
    }
}
