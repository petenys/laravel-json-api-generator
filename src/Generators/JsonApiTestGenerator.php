<?php

namespace PeteNys\Generator\Generators;

use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Utils\FileUtil;

class JsonApiTestGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $fileName;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathJsonApiTests;
        $this->fileName = $this->commandData->modelName.'JsonApiTest.php';
    }

    public function generate()
    {
        $templateData = get_template_stub('json_api_test', 'laravel-generator');

        $templateData = fill_template_stub($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandObj->comment("\nJsonApiTest created: ");
        $this->commandData->commandObj->info($this->fileName);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->commandData->commandComment('JsonApi Test file deleted: '.$this->fileName);
        }
    }
}
