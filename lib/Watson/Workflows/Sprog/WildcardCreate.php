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

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Command;
use \Watson\Foundation\Result;
use \Watson\Foundation\ResultEntry;
use \Watson\Foundation\Watson;
use \Watson\Foundation\Workflow;

class WildcardCreate extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return array('w', 'p');
    }

    /**
     *
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_wildcard_documentation_description'));
        $documentation->setUsage('w wildcard');
        $documentation->setExample('wildcard');
        $documentation->setExample('w wildcard');
        $documentation->setOption('--miss', 'w|p --miss');

        return $documentation;
    }

    /**
     *
     * @return an array of registered page params
     */
    public function registerPageParams()
    {

    }

    /**
     * Execute the command for the given Command
     *
     * @param  Command $command
     * @return Result
     */
    public function fire(Command $command)
    {

        $result = new Result();
        
        if ($command->getOption('miss') && \rex_addon::get('structure')->isAvailable() && \rex_plugin::get('structure', 'content')->isAvailable()) {

            $missingWildcards = \Sprog\Wildcard::getMissingWildcards();

            if (count($missingWildcards)) {

                $counter = 0;

                foreach ($missingWildcards as $name => $params) {

                    $url = Watson::getUrl(array('page' => 'sprog/wildcard', 'wildcard_name' => $params['wildcard'], 'func' => 'add'));

                    $counter++;

                    $entry = new ResultEntry();
                    if ($counter == 1) {
                        $entry->setLegend(Watson::translate('watson_wildcard_legend_missing'));
                    }
                    $entry->setValue( $params['wildcard'] );
                    $entry->setDescription(Watson::translate('watson_wildcard_create_description'));
                    $entry->setIcon('watson-icon-wildcard');
                    $entry->setUrl($url);
                    $entry->setQuicklookUrl($url);

                    $result->addEntry($entry);
                }
            }

        } else {

            $sql = \rex_sql::factory();
            $sql->setQuery('SELECT pid FROM ' . Watson::getTable('wildcard') . ' WHERE wildcard = "' . $command->getCommandPartsAsString() . '"');


            if ($sql->getRows() == 0 && count($command->getOptions()) == 0 && in_array($command->getCommand(), $this->commands() )) {
                $url = Watson::getUrl(array('page' => 'sprog/wildcard', 'wildcard_name' => $command->getCommandPartsAsString(), 'func' => 'add'));

                $entry = new ResultEntry();
                $entry->setLegend(Watson::translate('watson_wildcard_legend_create'));
                $entry->setValue( $command->getCommandPartsAsString() );
                $entry->setDescription(Watson::translate('watson_wildcard_create_description'));
                $entry->setIcon('watson-icon-wildcard');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $result->addEntry($entry);
            }
        }

        return $result;
    }

}
