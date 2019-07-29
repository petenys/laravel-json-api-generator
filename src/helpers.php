<?php

use Illuminate\Support\Str;
use PeteNys\Generator\Common\GeneratorField;

if (!function_exists('petenys_tab')) {
    /**
     * Generates tab with spaces.
     *
     * @param int $spaces
     *
     * @return string
     */
    function petenys_tab($spaces = 4)
    {
        return str_repeat(' ', $spaces);
    }
}

if (!function_exists('petenys_tabs')) {
    /**
     * Generates tab with spaces.
     *
     * @param int $tabs
     * @param int $spaces
     *
     * @return string
     */
    function petenys_tabs($tabs, $spaces = 4)
    {
        return str_repeat(petenys_tab($spaces), $tabs);
    }
}

if (!function_exists('petenys_nl')) {
    /**
     * Generates new line char.
     *
     * @param int $count
     *
     * @return string
     */
    function petenys_nl($count = 1)
    {
        return str_repeat(PHP_EOL, $count);
    }
}

if (!function_exists('petenys_nls')) {
    /**
     * Generates new line char.
     *
     * @param int $count
     * @param int $nls
     *
     * @return string
     */
    function petenys_nls($count, $nls = 1)
    {
        return str_repeat(petenys_nl($nls), $count);
    }
}

if (!function_exists('petenys_nl_tab')) {
    /**
     * Generates new line char.
     *
     * @param int $lns
     * @param int $tabs
     *
     * @return string
     */
    function petenys_nl_tab($lns = 1, $tabs = 1)
    {
        return petenys_nls($lns).petenys_tabs($tabs);
    }
}

if (!function_exists('get_templates_file_path')) {
    /**
     * get path for template file.
     *
     * @param string $templateName
     * @param string $templateType
     *
     * @return string
     */
    function get_templates_file_path($templateName, $templateType)
    {
        $templateName = str_replace('.', '/', $templateName);

        $templatesPath = config(
            'petenys.laravel_json_api_generator.path.templates_dir',
            base_path('resources/petenys/petenys-generator-templates/')
        );

        $path = $templatesPath.$templateName.'.stub';

        if (file_exists($path)) {
            return $path;
        }

        return base_path('vendor/petenys/'.$templateType.'/templates/'.$templateName.'.stub');
    }
}

if (!function_exists('get_template_stub')) {
    /**
     * get template contents.
     *
     * @param string $templateName
     * @param string $templateType
     *
     * @return string
     */
    function get_template_stub($templateName, $templateType)
    {
        $path = get_templates_file_path($templateName, $templateType);

        return file_get_contents($path);
    }
}

if (!function_exists('fill_template_stub')) {
    /**
     * fill template with variable values.
     *
     * @param array  $variables
     * @param string $template
     *
     * @return string
     */
    function fill_template_stub($variables, $template)
    {
        foreach ($variables as $variable => $value) {
            $template = str_replace($variable, $value, $template);
        }

        return $template;
    }
}

if (!function_exists('fill_field_template_contents')) {
    /**
     * fill field template with variable values.
     *
     * @param array          $variables
     * @param string         $template
     * @param GeneratorField $field
     *
     * @return string
     */
    function fill_field_template_contents($variables, $template, $field)
    {
        foreach ($variables as $variable => $key) {
            $template = str_replace($variable, $field->$key, $template);
        }

        return $template;
    }
}

if (!function_exists('fill_template_with_fields')) {
    /**
     * fill template with field data.
     *
     * @param array          $variables
     * @param array          $fieldVariables
     * @param string         $template
     * @param GeneratorField $field
     *
     * @return string
     */
    function fill_template_with_fields($variables, $fieldVariables, $template, $field)
    {
        $template = fill_template_stub($variables, $template);

        return fill_field_template_contents($fieldVariables, $template, $field);
    }
}

if (!function_exists('get_model_name_from_table_name')) {
    /**
     * generates model name from table name.
     *
     * @param string $tableName
     *
     * @return string
     */
    function get_model_name_from_table_name($tableName)
    {
        return Str::ucfirst(Str::camel(Str::singular($tableName)));
    }
}
