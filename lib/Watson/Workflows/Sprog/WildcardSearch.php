<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Sprog;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class WildcardSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['sp'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_wildcard_documentation_description'));
        $documentation->setUsage('sp wildcard');
        $documentation->setExample('wildcard');
        $documentation->setExample('sp wildcard');

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
            '`wildcard`',
            '`replace`',
        ];

        $sql_query = ' SELECT      id,
                                    clang_id,
                                    wildcard,
                                    `replace`
                        FROM        '.Watson::getTable('sprog_wildcard').'
                        WHERE       '.$command->getSqlWhere($fields).'
                        ORDER BY    wildcard';

        $items = $this->getDatabaseResults($sql_query);

        if (count($items)) {
            $counter = 0;

            $clangs = \rex_clang::getAll();

            foreach ($items as $item) {
                $url = Watson::getUrl(['page' => 'sprog/wildcard', 'wildcard_id' => $item['id'], 'func' => 'edit']);

                ++$counter;

                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_wildcard_legend'));
                }

                $value = $item['wildcard'];
                $value_suffix = '';
                if (isset($clangs[$item['clang_id']])) {
                    $value_suffix .= ' › '.$clangs[$item['clang_id']]->getCode();
                }
                if ($item['replace'] != '') {
                    $value_suffix .= ' › '.$item['replace'];
                }

                $entry->setValue($value);
                $entry->setValueSuffix($value_suffix);
                $entry->setDescription(Watson::translate('watson_open_wildcard'));
                $entry->setIcon('watson-icon-wildcard');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $result->addEntry($entry);
            }
        }

        return $result;
    }
}
