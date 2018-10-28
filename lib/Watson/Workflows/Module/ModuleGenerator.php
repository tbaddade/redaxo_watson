<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Module;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\GeneratorWorkflow;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;

class ModuleGenerator extends GeneratorWorkflow
{
    /**
     * Provide the commands of the workflow.
     *
     * @return array
     */
    public function commands()
    {
        return ['m:make', 'module:make'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_module_documentation_description'));
        $documentation->setUsage('m keyword');
        $documentation->setExample('$headline');
        $documentation->setExample('m $headline');
        $documentation->setExample('m module name');

        return $documentation;
    }

    /**
     * @return array of registered page params
     */
    public function registerPageParams()
    {
    }

    /**
     * Execute the command for the given Command.
     *
     * @param Command $command
     *
     * @return Result
     */
    public function fire(Command $command)
    {
        $result = new Result();
        $moduleName = $command->getArgument(1);

        $commandOptions = $command->getOptions();

        $moduleFields = isset($commandOptions['fields']) ? $commandOptions['fields'] : [];
        $moduleInputTabs = isset($commandOptions['tabs']) ? $commandOptions['tabs'] : 0;

        $url = Watson::getUrl(['page' => 'modules/modules', 'function' => 'add']);

        $entry = new ResultEntry();
        $entry->setLegend(Watson::translate('watson_module_legend'));
        $entry->setValue($moduleName);
        $entry->setDescription(Watson::translate('watson_create_module'));
        $entry->setIcon('watson-icon-module');
        //$entry->setUrl($url);
        //$entry->setQuickLookUrl($url);

        $ajax = [];
        $ajax['class'] = '\Watson\Workflows\Module\ModuleGenerator';
        $ajax['method'] = 'call';
        $ajax['params']['name'] = $moduleName;
        $ajax['params']['fields'] = $moduleFields;
        $ajax['params']['inputTabs'] = $moduleInputTabs;
        $ajax['params']['templatePath'] = $this->getTemplatePath();
        $entry->setAjax(json_encode($ajax));

        $result->addEntry($entry);
        return $result;
    }

    public static function call($params)
    {
        $module = new ModuleData($params['name'], $params['fields']);
        $module->setInputTabs($params['inputTabs']);
        $module->setTemplatePath($params['templatePath']);
        $module->create();
        exit();
    }

    /**
     * Get the path to the generator template.
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return __DIR__.'/templates/';
    }
}
