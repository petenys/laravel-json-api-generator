<?php

namespace PeteNys\Generator\Common;

use Illuminate\Support\Str;

class GeneratorFieldRelation
{
    /** @var string */
    public $type;
    public $inputs;
    public $relationName;

    public static function parseRelation($relationInput)
    {
        $inputs = explode(',', $relationInput);

        $relation = new self();
        $relation->type = array_shift($inputs);
        $modelWithRelation = explode(':', array_shift($inputs)); //e.g ModelName:relationName
        if (count($modelWithRelation) == 2) {
            $relation->relationName = $modelWithRelation[1];
            unset($modelWithRelation[1]);
        }
        $relation->inputs = array_merge($modelWithRelation, $inputs);

        return $relation;
    }

    public function getRelationFunctionText($builderType="model")
    {
        $singularRelation = (!empty($this->relationName)) ? $this->relationName : Str::camel($this->inputs[0]);
        $singularResource = Str::kebab($singularRelation);
        $pluralRelation = (!empty($this->relationName)) ? $this->relationName : Str::camel(Str::plural($this->inputs[0]));
        $pluralResource = Str::kebab($singularRelation);

        switch ($this->type) {
            case '1t1':
                $functionName = $singularRelation;
                $resourceName = $singularResource;
                $relation = 'hasOne';
                $relationClass = 'HasOne';
                break;
            case '1tm':
                $functionName = $pluralRelation;
                $resourceName = $pluralResource;
                $relation = 'hasMany';
                $relationClass = 'HasMany';
                break;
            case 'mt1':
                if (!empty($this->relationName)) {
                    $singularRelation = $this->relationName;
                } elseif (isset($this->inputs[1])) {
                    $singularRelation = Str::camel(str_replace('_id', '', strtolower($this->inputs[1])));
                }
                $functionName = $singularRelation;
                $resourceName = $singularResource;
                $relation = 'belongsTo';
                $relationClass = 'BelongsTo';
                break;
            case 'mtm':
                $functionName = $pluralRelation;
                $resourceName = $pluralResource;
                $relation = 'belongsToMany';
                $relationClass = 'BelongsToMany';
                break;
            case 'hmt':
                $functionName = $pluralRelation;
                $resourceName = $pluralResource;
                $relation = 'hasManyThrough';
                $relationClass = 'HasManyThrough';
                break;
            default:
                $functionName = '';
                $resourceName = '';
                $relation = '';
                $relationClass = '';
                break;
        }

        if (!empty($functionName) and !empty($relation)) {
            if($builderType=="adapter_relations") {
                return $this->generateAdapterRelation($functionName, $relation, $relationClass);
            } elseif($builderType=="adapter_relationships") {
                return $resourceName;
            } elseif($builderType=="model") {
                return $this->generateModelRelation($functionName, $relation, $relationClass);
            } elseif($builderType=="schema") {
                return $this->generateSchemaRelation($functionName, $pluralResource);
            }
        }

        return '';
    }

    private function generateAdapterRelation($functionName, $relation, $relationClass)
    {
        $template = get_template_stub('json_api.adapter_relation', 'laravel-json-api-generator');

        $template = str_replace('$RELATIONSHIP_CLASS$', $relationClass, $template);
        $template = str_replace('$FUNCTION_NAME$', $functionName, $template);
        $template = str_replace('$RELATION$', $relation, $template);

        return $template;
    }

    private function generateAdapterRelationship($functionName, $relation, $relationClass)
    {
        $template = get_template_stub('json_api.adapter_relationship', 'laravel-json-api-generator');

        $template = str_replace('$RELATIONSHIP_CLASS$', $relationClass, $template);
        $template = str_replace('$FUNCTION_NAME$', $functionName, $template);
        $template = str_replace('$RELATION$', $relation, $template);

        return $template;
    }

    private function generateModelRelation($functionName, $relation, $relationClass)
    {
        $inputs = $this->inputs;
        $modelName = array_shift($inputs);

        $template = get_template_stub('model.relationship', 'laravel-json-api-generator');

        $template = str_replace('$RELATIONSHIP_CLASS$', $relationClass, $template);
        $template = str_replace('$FUNCTION_NAME$', $functionName, $template);
        $template = str_replace('$RELATION$', $relation, $template);
        $template = str_replace('$RELATION_MODEL_NAME$', $modelName, $template);

        if (count($inputs) > 0) {
            $inputFields = implode("', '", $inputs);
            $inputFields = ", '".$inputFields."'";
        } else {
            $inputFields = '';
        }

        $template = str_replace('$INPUT_FIELDS$', $inputFields, $template);

        return $template;
    }

    private function generateSchemaRelation($functionName, $pluralResource)
    {
        $inputs = $this->inputs;
        $modelName = array_shift($inputs);

        $template = get_template_stub('json_api.schema_relationship', 'laravel-json-api-generator');

        $template = str_replace('$FUNCTION_NAME$', $functionName, $template);
        $template = str_replace('$RELATION_MODEL_NAME$', $pluralResource, $template);

        return $template;
    }
    
}
