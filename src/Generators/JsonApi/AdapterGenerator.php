<?php

namespace PeteNys\Generator\Generators\JsonApi;

use Illuminate\Support\Str;
use PeteNys\Generator\Common\CommandData;
use PeteNys\Generator\Common\GeneratorFieldRelation;
use PeteNys\Generator\Utils\FileUtil;
use PeteNys\Generator\Utils\TableFieldsGenerator;
use PeteNys\Generator\Generators\BaseGenerator;

class AdapterGenerator extends BaseGenerator
{
    /**
     * Fields not included in the generator by default.
     *
     * @var array
     */
    protected $excluded_fields = [
        'created_at',
        'updated_at',
    ];

    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;
    private $fileName;
    private $table;

    /**
     * AdapterGenerator constructor.
     *
     * @param CommandData $commandData
     */
    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $this->commandData->dynamicVars['$JSON_API_MODEL_FILE_PATH$'];
        $this->fileName = 'Adapter.php';
        $this->table = $this->commandData->dynamicVars['$TABLE_NAME$'];
    }

    public function generate()
    {
        $templateData = get_template_stub('json_api.adapter', 'laravel-json-api-generator');

        $templateData = $this->fillTemplate($templateData);

        FileUtil::createFile($this->path, $this->fileName, $templateData);

        $this->commandData->commandComment("\nAdapter created: ");
        $this->commandData->commandInfo($this->fileName);
    }

    private function fillTemplate($templateData)
    {
        $templateData = fill_template_stub($this->commandData->dynamicVars, $templateData);

        $relationships = "";
        foreach ($this->commandData->relations as $relation) {
            $relationText = $relation->getRelationFunctionText("adapter_relationships");
            if (!empty($relationText)) {
                $template = get_template_stub('json_api.adapter_relationship', 'laravel-json-api-generator');
                $relationships .= str_replace('$RELATIONSHIP_DASHED$', $relationText, $template);
            }
        }

        $templateData = str_replace(
            '$ADAPTER_RELATIONSHIPS$',
            $relationships,
            $templateData
        );

        $templateData = str_replace(
            '$ADAPTER_RELATIONS$',
            fill_template_stub($this->commandData->dynamicVars, implode(PHP_EOL.petenys_nl_tab(1, 1), $this->generateRelations())),
            $templateData
        );

        return $templateData;
    }

    private function generateRelationships()
    {
        $relations = "";

        foreach ($this->commandData->relations as $relation) {
            $relationText = $relation->getRelationFunctionText("adapter_relationships");
            if (!empty($relationText)) {
                $template = get_template_stub('json_api.adapter_relationship', 'laravel-json-api-generator');
                $relations .= str_replace('$RELATIONSHIP_DASHED$', $relationText, $template);
            }
        }

        return $relations;
    }

    private function generateRelations()
    {
        $relations = [];

        foreach ($this->commandData->relations as $relation) {
            $relationText = $relation->getRelationFunctionText("adapter_relations");
            if (!empty($relationText)) {
                $relations[] = $relationText;
            }
        }

        return $relations;
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->fileName)) {
            $this->commandData->commandComment('Adapter file deleted: '.$this->fileName);
        }
    }
}
