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

class WildcardCreate extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['sp:make', 'sp:miss'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_wildcard_documentation_description'));
        $documentation->setUsage('sp:make Wildcard');
        $documentation->setExample('sp:make Wildcard');
        $documentation->setExample('sp:miss');

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
        if ($command->getCommand() == 'sp:miss' && \rex_addon::get('structure')->isAvailable() && \rex_plugin::get('structure', 'content')->isAvailable()) {
            return $this->createMissingWildcard($command);
        }
        return $this->createWildcard($command);
    }

    public function createMissingWildcard(Command $command)
    {
        $result = new Result();

        $missingWildcards = \Sprog\Wildcard::getMissingWildcards();

        if (count($missingWildcards)) {
            $counter = 0;

            $urlParams = [
                'page' => 'sprog/wildcard',
                'func' => 'add',
            ];
            if (\Sprog\Wildcard::isClangSwitchMode()) {
                $urlParams['page'] = 'sprog/wildcard/clang1';
            }
            foreach ($missingWildcards as $name => $params) {
                $urlParams['wildcard_name'] = $params['wildcard'];
                $url = Watson::getUrl($urlParams);

                ++$counter;
                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_wildcard_legend_missing'));
                }
                $entry->setValue($params['wildcard']);
                $entry->setDescription(Watson::translate('watson_wildcard_create_description'));
                $entry->setIcon('watson-icon-wildcard');
                $entry->setUrl($url);
                $entry->setQuicklookUrl($url);

                $result->addEntry($entry);
            }
        }
        return $result;
    }

    public function createWildcard(Command $command)
    {
        $result = new Result();

        $sql = \rex_sql::factory();
        $sql->setQuery('SELECT pid FROM '.Watson::getTable('sprog_wildcard').' WHERE wildcard = "'.$command->getCommandPartsAsString().'"');

        if ($sql->getRows() == 0 && count($command->getOptions()) == 0 && in_array($command->getCommand(), $this->commands())) {
            $urlParams = [
                'page' => 'sprog/wildcard',
                'func' => 'add',
                'wildcard_name' => $command->getCommandPartsAsString(),
            ];
            if (\Sprog\Wildcard::isClangSwitchMode()) {
                $urlParams['page'] = 'sprog/wildcard/clang1';
            }

            $url = Watson::getUrl($urlParams);

            $entry = new ResultEntry();
            $entry->setLegend(Watson::translate('watson_wildcard_legend_create'));
            $entry->setValue($command->getCommandPartsAsString());
            $entry->setDescription(Watson::translate('watson_wildcard_create_description'));
            $entry->setIcon('watson-icon-wildcard');
            $entry->setUrl($url);
            $entry->setQuickLookUrl($url);

            $result->addEntry($entry);
        }
        return $result;
    }
}
