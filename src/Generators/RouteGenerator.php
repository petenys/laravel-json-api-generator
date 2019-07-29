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

        $this->routeTemplate = fill_template_stub($this->commandData->dynamicVars, $routeTemplate);
    }

    public function generate()
    {
        $this->routeContents .= "\n\n".$this->routeTemplate;

        file_put_contents($this->path, $this->routeContents);

        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' json api route added.');
    }

    public function rollback()
    {
        if (Str::contains($this->routeContents, $this->routeTemplate)) {
            $this->routeContents = str_replace($this->routeTemplate, '', $this->routeContents);
            file_put_contents($this->path, $this->routeContents);
            $this->commandData->commandComment('json api route deleted');
        }
    }
}
