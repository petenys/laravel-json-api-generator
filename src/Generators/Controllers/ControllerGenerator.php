<?php

namespace PeteNys\Generator\Generators\Controllers;

use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Generators\BaseGenerator;
use PeteNys\Generator\Utils\FileUtil;

class ControllerGenerator extends BaseGenerator
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
        $this->path = $commandData->config->pathController;
        $this->fileName = $this->commandData->modelName.'Controllers.php';
    }

    public function generate()
    {
        $templateData = get_template_stub('controller.json_controller', 'laravel-json-api-generator');

        $templateData = fill_template_stub($this->commandData->dynamicVars, $templateData);
        $templateData = $this->fillDocs($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nJsonApi Controllers created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    private function fillDocs($templateData)
    {
        $methods = ['controller', 'index', 'store', 'show', 'update', 'destroy'];

        $templatePrefix = 'api.docs.controller';
        $templateType = 'laravel-json-api-generator';

        foreach ($methods as $method) {
            $key = '$DOC_'.strtoupper($method).'$';
            $docTemplate = get_template_stub($templatePrefix.'.'.$method, $templateType);
            $docTemplate = fill_template_stub($this->commandData->dynamicVars, $docTemplate);
            $templateData = str_replace($key, $docTemplate, $templateData);
        }

        return $templateData;
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->commandData->commandComment('JsonApi Controllers file deleted: '.$this->fileName);
        }
    }
}
