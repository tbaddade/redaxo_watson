<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Template;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class TemplateSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['t'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_template_documentation_description'));
        $documentation->setUsage('t keyword');
        $documentation->setExample('$navi');
        $documentation->setExample('t $navigation');

        return $documentation;
    }

    /**
     * Return array of registered page params.
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
            'content',
        ];

        $sql_query = ' SELECT      id,
                                    name
                        FROM        '.Watson::getTable('template').'
                        WHERE       '.$command->getSqlWhere($fields).'
                        ORDER BY    name';

        $items = $this->getDatabaseResults($sql_query);

        if (count($items)) {
            $counter = 0;

            foreach ($items as $item) {
                $url = Watson::getUrl(['page' => 'templates', 'template_id' => $item['id'], 'function' => 'edit']);

                ++$counter;

                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_template_legend'));
                }
                $entry->setValue($item['name']);
                $entry->setDescription(Watson::translate('watson_open_template'));
                $entry->setIcon('watson-icon-template');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $result->addEntry($entry);
            }
        }

        return $result;
    }
}
