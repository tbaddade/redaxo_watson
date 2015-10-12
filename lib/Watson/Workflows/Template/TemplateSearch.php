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

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Search;
use \Watson\Foundation\SearchCommand;
use \Watson\Foundation\SearchResult;
use \Watson\Foundation\SearchResultEntry;
use \Watson\Foundation\Watson;

class TemplateSearch extends Search
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return array('t');
    }

    /**
     *
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
     *
     * @return an array of registered page params
     */
    public function registerPageParams()
    {
        
    }

    /**
     * Execute the search for the given SearchCommand
     *
     * @param  SearchCommand $search
     * @return SearchResult
     */
    public function fire(SearchCommand $search)
    {
        
        $search_result = new SearchResult();


        $fields = array(
            'name',
            'content',
        );

        $sql_query  = ' SELECT      id,
                                    name
                        FROM        ' . Watson::getTable('template') . '
                        WHERE       ' . $search->getSqlWhere($fields) . '
                        ORDER BY    name
                        LIMIT       ' . Watson::getSearchResultLimit();

        $results = $this->getDatabaseResults($sql_query);

        if (count($results)) {

            $counter = 0;
            
            foreach ($results as $result) {

                $url = Watson::getUrl(array('page' => 'templates', 'template_id' => $result['id'], 'function' => 'edit'));

                $counter++;

                $entry = new SearchResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_template_legend'));
                }
                $entry->setValue($result['name']);
                $entry->setDescription(Watson::translate('watson_open_template'));
                $entry->setIcon('watson-icon-template');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $search_result->addEntry($entry);

            }
        }


        return $search_result;
    }

}
