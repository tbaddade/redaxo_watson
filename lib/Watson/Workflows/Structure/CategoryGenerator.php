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

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\GeneratorWorkflow;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;

class CategoryGenerator extends GeneratorWorkflow
{
    /**
     * Provide the commands of the workflow.
     *
     * @return array
     */
    public function commands()
    {
        return ['c:make'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();

        return $documentation;
    }

    /**
     * @return array of registered page params
     */
    public function registerPageParams()
    {
        return ['category_id', 'clang'];
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
        //$category_id = rex_request('category_id', 'int');
        $categoryId = Watson::getRegisteredPageParam('category_id');
        $categoryId = (int) $categoryId > 0 ? $categoryId : 0;

        $clangId = Watson::getRegisteredPageParam('clang');
        $clangId = (int) $clangId > 0 ? $clangId : 1;

        $categoriesAsString = implode(',', $command->getArguments());
        $commandOptions = $command->getOptions();

        $status = isset($commandOptions['offline']) ? false : true;

        $entry = new ResultEntry();
        $entry->setLegend(Watson::translate('watson_structure_legend'));
        $entry->setValue($categoriesAsString);
        $entry->setDescription(Watson::translate('watson_structure_add_categories'));
        $entry->setIcon('watson-icon-category');

        $ajax = [];
        $ajax['class'] = '\Watson\Workflows\Structure\CategoryGenerator';
        $ajax['method'] = 'call';
        $ajax['params']['categories'] = $categoriesAsString;
        $ajax['params']['categoryId'] = $categoryId;
        $ajax['params']['clangId'] = $clangId;
        $ajax['params']['status'] = $status;
        $entry->setAjax(json_encode($ajax));

        $result = new Result();
        $result->addEntry($entry);
        return $result;
    }

    public static function call($params)
    {
        $data = new CategoryData($params['categories']);
        $data->setCategoryId($params['categoryId']);
        $data->setClangId($params['clangId']);
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
