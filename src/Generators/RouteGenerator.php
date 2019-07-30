<?php

namespace PeteNys\Generator\Generators;

use Illuminate\Support\Str;
use PeteNys\Generator\Common\CommandData;

class RouteGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $routeContents;

    /** @var string */
    private $routeTemplate;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathJsonApiRoute;

        $this->routeContents = file_get_contents($this->path);

        $routeTemplate = get_template_stub('json_api_route', 'laravel-json-api-generator');

        if($this->commandData->hasOneRouteRelations) {
            $routeTemplate = str_replace('$MODEL_HAS_ONE_RELATION$',
                implode(', ', array_map(function($val){return sprintf("'%s'", $val);},
                    $this->commandData->hasOneRouteRelations)), $routeTemplate);
        }

        $hasOneStr = $this->commandData->hasManyRouteRelations ?
            implode(', ', array_map(function($val){return sprintf("'%s'", $val);},
                $this->commandData->hasOneRouteRelations)) :
            "";
        $routeTemplate = str_replace('$MODEL_HAS_ONE_RELATION$', $hasOneStr, $routeTemplate);
        $hasManyStr = $this->commandData->hasManyRouteRelations ?
            implode(', ', array_map(function($val){return sprintf("'%s'", $val);},
                $this->commandData->hasManyRouteRelations)) :
            "";
        $routeTemplate = str_replace('$MODEL_HAS_MANY_RELATION$', $hasManyStr, $routeTemplate);

        $this->routeTemplate = fill_template_stub($this->commandData->dynamicVars, $routeTemplate);
    }

    public function generate()
    {
        $routeContents = Str::before($this->routeContents, "/* End Generated Routes */") .
            "\n\n".$this->routeTemplate ."\n\n/* End Generated Routes */".
            Str::after($this->routeContents, "/* End Generated Routes */");

        file_put_contents($this->path, $routeContents);

        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' json api route added.');
    }

    public function rollback()
    {
        if (Str::contains($this->routeContents, "/* Start ".$this->commandData->config->mName." Route */")) {

            $this->routeContents = Str::before($this->routeContents, "/* Start ".$this->commandData->config->mName." Route */") .
                Str::after($this->routeContents, "/* End ".$this->commandData->config->mName." Route */")
            ;
            file_put_contents($this->path, $this->routeContents);
            $this->commandData->commandComment('json api route deleted');
        }
    }
}
