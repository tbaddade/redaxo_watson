<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Workflows\Structure;

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Command;
use \Watson\Foundation\Result;
use \Watson\Foundation\ResultEntry;
use \Watson\Foundation\Watson;
use \Watson\Foundation\GeneratorWorkflow;

class CategoryGenerator extends GeneratorWorkflow
{
    /**
     * Provide the commands of the workflow.
     *
     * @return array
     */
    public function commands()
    {
        return array('c:make');
    }

    /**
     *
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();

        return $documentation;
    }

    /**
     *
     * @return array of registered page params
     */
    public function registerPageParams()
    {
        return array('category_id');
    }

    /**
     * Execute the command for the given Command
     *
     * @param  Command $command
     * @return Result
     */
    public function fire(Command $command)
    {
        //$category_id = rex_request('category_id', 'int');
        ///$category_id = Watson::getRequest('category_id', 'int', 0);
        $category_id = Watson::getRegisteredPageParam('category_id');
        $result = new Result();
        $categoriesAsString = $command->getArgument(1);

        $commandOptions = $command->getOptions();

        $status = isset($commandOptions['offline']) ? false : true;

        $url = Watson::getUrl(array('page' => 'modules/modules', 'function' => 'add'));
        $pre = Watson::buildQuery(\rex_request::session('watson_params'));
        $entry = new ResultEntry();
        $entry->setLegend(Watson::translate('watson_structure_legend'));
        $entry->setValue($pre . ' :: ' . $category_id . ' :: ' . Watson::translate('watson_structure_add_categories'));
        $entry->setIcon('watson-icon-category');
        //$entry->setUrl($url);
        //$entry->setQuickLookUrl($url);

        $ajax = array();
        $ajax['class'] = '\Watson\Workflows\Structure\CategoryGenerator';
        $ajax['method'] = 'call';
        $ajax['params']['categories'] = $categoriesAsString;
        $ajax['params']['status'] = $status;
        $entry->setAjax(json_encode($ajax));

        $result->addEntry($entry);
        return $result;
    }

    public static function call($params)
    {
        $data = new CategoryData($params['name'], $params['fields']);
        $data->setStatus($params['status']);
        $data->create();
        exit();
    }

    /**
     * Get the path to the generator template.
     *
     * @return mixed
     */
    protected function getTemplatePath()
    {
        return false;
    }
}
