<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Media;

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Search;
use \Watson\Foundation\SearchCommand;
use \Watson\Foundation\SearchResult;
use \Watson\Foundation\SearchResultEntry;
use \Watson\Foundation\Watson;

class MediaSearch extends Search
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return array('m', 'f');
    }

    /**
     *
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_media_documentation_description'));
        $documentation->setUsage('m keyword');
        $documentation->setExample('m filename');

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
            'filename',
            'title',
        );

        $s = \rex_sql::factory();
        $s->setQuery('SELECT * FROM ' . Watson::getTable('media') .' LIMIT 0');
        $fieldnames = $s->getFieldnames();

        foreach ($fieldnames as $fieldname) {

            if (substr($fieldname, 0, 4) == 'med_') {

                $fields[] = $fieldname;

            }

        }


        $sql_query  = ' SELECT      filename,
                                    title
                        FROM        ' . Watson::getTable('media') . '
                        WHERE       ' . $search->getSqlWhere($fields) . '
                        ORDER BY    filename';

        $results = $this->getDatabaseResults($sql_query);

        if (count($results)) {

            foreach ($results as $result) {

                $title = ($result['title'] != '') ? ' (' . Watson::translate('watson_media_title') . ': ' . $result['title'] . ')' : '';

                $entry = new SearchResultEntry();
                $entry->setValue($result['filename']);
                $entry->setDescription(Watson::translate('watson_open_media') . $title);
                $entry->setIcon('icon_media.png');
                $entry->setUrl('javascript:newPoolWindow(\'' . Watson::getUrl(array('page' => 'mediapool', 'subpage' => 'detail', 'file_name' => $result['filename'])) . '\')');

                $m = \OOMedia::getMediaByFileName($result['filename']);
                if ($m instanceof \OOMedia) {

                    if ($m->isImage()) {

                        $entry->setQuickLookUrl(Watson::getUrl(array('rex_img_type' => 'rex_mediapool_maximized', 'rex_img_file' => $result['filename'])));

                    }
                }

                $search_result->addEntry($entry);

            }
        }


        return $search_result;
    }

}
