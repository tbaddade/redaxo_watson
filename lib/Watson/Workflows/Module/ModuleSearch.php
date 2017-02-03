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

use Watson\Foundation\Documentation;
use Watson\Foundation\Command;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class ModuleSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['m'];
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
     * Return array of registered page params
     *
     * @return array
     */
    public function registerPageParams()
    {
        return [];
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

        $fields = [
            'name',
            'input',
            'output',
        ];

        $sql_query = ' SELECT      id,
                                    name
                        FROM        ' . Watson::getTable('module') . '
                        WHERE       ' . $command->getSqlWhere($fields) . '
                        ORDER BY    name';

        $items = $this->getDatabaseResults($sql_query);

        if (count($items)) {
            $counter = 0;

            foreach ($items as $item) {
                $url = Watson::getUrl(['page' => 'modules/modules', 'module_id' => $item['id'], 'function' => 'edit']);

                ++$counter;

                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_module_legend'));
                }
                $entry->setValue($item['name']);
                $entry->setDescription(Watson::translate('watson_open_module'));
                $entry->setIcon('watson-icon-module');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $result->addEntry($entry);
            }
        }

        return $result;
    }
}
