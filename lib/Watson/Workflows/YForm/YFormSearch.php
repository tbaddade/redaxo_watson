<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\YForm;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class YFormSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['yf'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_yform_documentation_description'));
        $documentation->setUsage('yf keyword');
        $documentation->setExample('yf Phrase');

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

        $tables = \rex_yform_manager_table::getAll();

        if (count($tables)) {
            $results = [];
            $viewFields = ['title', 'titel', 'name', 'lastname', 'last_name', 'surname'];
            foreach ($tables as $table) {
                if ($table->isActive() && \rex::getUser()->getComplexPerm('yform_manager_table')->hasPerm($table->getTableName())) {
                    $fields = $table->getValueFields();

                    foreach ($fields as $fieldName => $field) {
                        //if (!$field->isSearchable()) {
                        if ($field->getTypeName() != 'text' && $field->getTypeName() != 'textarea') {
                            unset($fields[$fieldName]);
                        }
                    }

                    if (count($fields)) {
                        $selectFields = 'id';
                        foreach ($viewFields as $viewField) {
                            if (isset($fields[$viewField])) {
                                $selectFields .= ', '.$viewField.' AS name';
                                break;
                            }
                        }
                        $searchFields = array_keys($fields);
                        $orderByField = $table->getSortFieldName();

                        $query = '
                                SELECT      '.$selectFields.'
                                FROM        '.$table.'
                                WHERE       '.$command->getSqlWhere($searchFields).'
                                ORDER BY    '.$orderByField;

                        $results[$table->getTableName()] = $this->getDatabaseResults($query);
                    }
                }
            }

            foreach ($results as $tableName => $items) {
                if (count($items)) {
                    $counter = 0;

                    foreach ($items as $item) {
                        $url = Watson::getUrl(['page' => 'yform/manager/data_edit', 'table_name' => $tableName, 'data_id' => $item['id'], 'func' => 'edit']);
                        ++$counter;
                        $entry = new ResultEntry();
                        if ($counter == 1) {
                            $entry->setLegend(Watson::translate('watson_yform_legend').' :: '.$tableName);
                        }

                        if (isset($item['name'])) {
                            $entry->setValue($item['name'], '('.$item['id'].')');
                        } else {
                            $entry->setValue($item['id']);
                        }
                        $entry->setDescription(Watson::translate('watson_open_yform'));
                        $entry->setIcon('watson-icon-yform');
                        $entry->setUrl($url);
                        $entry->setQuickLookUrl($url);

                        $result->addEntry($entry);
                    }
                }
            }
        }

        return $result;
    }
}
