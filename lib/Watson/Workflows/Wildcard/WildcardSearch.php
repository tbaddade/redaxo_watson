<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Workflows\Wildcard;

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Command;
use \Watson\Foundation\Result;
use \Watson\Foundation\ResultEntry;
use \Watson\Foundation\Watson;
use \Watson\Foundation\Workflow;

class WildcardSearch extends Workflow
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

            $missingWildcards = \Wildcard\Wildcard::getMissingWildcards();

            if (count($missingWildcards)) {

                $counter = 0;

                foreach ($missingWildcards as $name => $params) {
            
                    $url = Watson::getUrl(array('page' => 'wildcard/wildcard', 'wildcard_name' => $params['wildcard'], 'func' => 'add'));

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

            $fields = array(
                '`wildcard`',
                '`replace`',
            );

            $sql_query  = ' SELECT      id,
                                        clang_id, 
                                        wildcard, 
                                        `replace`
                            FROM        ' . Watson::getTable('wildcard') . '
                            WHERE       ' . $command->getSqlWhere($fields) . '
                            ORDER BY    wildcard';

            $items = $this->getDatabaseResults($sql_query);

            if (count($items)) {

                $counter = 0;

                $clangs = \rex_clang::getAll();
                
                foreach ($items as $item) {

                    $url = Watson::getUrl(array('page' => 'wildcard/wildcard', 'wildcard_id' => $item['id'], 'func' => 'edit'));

                    $counter++;

                    $entry = new ResultEntry();
                    if ($counter == 1) {
                        $entry->setLegend(Watson::translate('watson_wildcard_legend'));
                    }

                    $value = $item['wildcard'];
                    $value_suffix = '';
                    if (isset($clangs[ $item['clang_id'] ])) {
                        $value_suffix .= ' ' . $clangs[ $item['clang_id'] ]->getCode();
                    }
                    $value_suffix .= "\n" . $item['replace'];

                    $entry->setValue( $value );
                    $entry->setValueSuffix($value_suffix);
                    $entry->setDescription(Watson::translate('watson_open_wildcard'));
                    $entry->setIcon('watson-icon-wildcard');
                    $entry->setUrl($url);
                    $entry->setQuickLookUrl($url);

                    $result->addEntry($entry);

                }
            }
            
        }

        return $result;
    }

}
