<?php

namespace PeteNys\Generator\Generators\Observers;

use Illuminate\Support\Str;
use PeteNys\Generator\Common\CommandData;

class ObserverProviderGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $currentContents;

    /** @var string */
    private $templateContents;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathObserverProvider;
        $this->currentContents = file_get_contents($this->path);

        $template = get_template_stub('observer.observer_provider_entry', 'laravel-json-api-generator');

        $this->templateContents = fill_template_stub($this->commandData->dynamicVars, $template);
    }

    public function generate()
    {
        if(Str::contains($this->currentContents, $this->commandData->config->mName."::")) {
            $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' json api observer provider already present.');
        } else {
            if(Str::contains($this->currentContents, "/* End Generated Content */")) {
                Str::replaceLast($this->currentContents, "/* End Generated Content */", $this->templateContents);
            } elseif(Str::contains($this->currentContents, "public function boot()\n\t{")) {
                Str::replaceLast($this->currentContents, "public function boot()\n\t{", "public function boot()\n\t{\n\t\t/* Start Generated Content */".$this->templateContents);
            }

            file_put_contents($this->path, $this->currentContents);

            $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' json api observer provider added.');
        }
    }

    public function rollback()
    {
        if (Str::contains($this->currentContents, $this->templateContents)) {
            $this->configContents = str_replace($this->templateContents, '', $this->currentContents);
            file_put_contents($this->path, $this->currentContents);
            $this->commandData->commandComment('Observer Provider entry added');
        }
    }
}
