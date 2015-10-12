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

use \Watson\Foundation\Documentation;
use \Watson\Foundation\Search;
use \Watson\Foundation\SearchCommand;
use \Watson\Foundation\SearchResult;
use \Watson\Foundation\SearchResultEntry;
use \Watson\Foundation\Watson;

class ArticleSearch extends Search
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return array('a');
    }

    /**
     *
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate(''));
        $documentation->setUsage('a keyword');
        $documentation->setExample('article content');
        $documentation->setExample('a article content');

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


        $searchResults = array();

        $command_parts = $search->getCommandParts();

        // Artikelnamen in der Struktur durchsuchen
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $fields = array(
            'a.name',
        );

        $where = $search->getSqlWhere($fields);

        if (count($command_parts) == 1 && (int)$command_parts[0] >= 1) {

            $where = 'a.id = "' . (int)$command_parts[0] .'"';

        }

        $sql_query  = ' SELECT      a.id,
                                    a.clang_id,
                                    CONCAT(a.id, "|", a.clang_id) as bulldog
                        FROM        ' . Watson::getTable('article') . ' AS a
                        WHERE       ' . $where . '
                        GROUP BY    bulldog
                        ';

        $results = $this->getDatabaseResults($sql_query);

        if (count($results)) {
            foreach ($results as $result) {

                $searchResults[ $result['bulldog'] ] = $result;

            }
        }

    

        // Slices der Artikel durchsuchen
        // Werden Slices gefunden, dann die Strukturartikel überschreiben
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        $fields = array(
            's.value'     => range('1', '20'),
            's.media'     => range('1', '10'),
            's.medialist' => range('1', '10'),
        );

        $searchFields = array();
        foreach ($fields as $field => $numbers) {

            foreach ($numbers as $number) {

                $searchFields[] = $field . $number;

            }

        }


        $fields = $searchFields;

        $sql_query  = ' SELECT      s.article_id AS id,
                                    s.clang_id,
                                    s.ctype_id,
                                    CONCAT(s.article_id, "|", s.clang_id) as bulldog
                        FROM        ' . Watson::getTable('article_slice') . ' AS s
                            LEFT JOIN
                                    ' . Watson::getTable('article') . ' AS a
                                ON  (s.article_id = a.id AND s.clang_id = a.clang_id)
                        WHERE       ' . $search->getSqlWhere($fields) . '
                        GROUP BY    bulldog';

        $results = $this->getDatabaseResults($sql_query);

        if (count($results)) {
            foreach ($results as $result) {

                $searchResults[ $result['bulldog'] ] = $result;

            }
        }
        

        // Ergebnisse auf Rechte prüfen und bereitstellen
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        if (count($searchResults)) {

            $counter = 0;
            foreach ($searchResults as $result) {

                $clang_id    = $result['clang_id'];
                $article     = \rex_article::get($result['id'], $clang_id);
                $category_id = $article->getCategoryId();


                // Rechte prüfen

                if (\rex::getUser()->getComplexPerm('clang')->hasPerm($clang_id) && \rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {

                    $path = array();

                    $tree = $article->getParentTree();
                    foreach ($tree as $o) {
                        $path[] = $o->getName();
                    }

                    if (!$article->isStartArticle()) {
                        $path[] = $article->getName();
                    }

                    $path = '/' . implode('/', $path);

                    
                    $url = Watson::getUrl(array('page' => 'structure', 'category_id' => $article->getCategoryId(), 'clang' => $clang_id));
                    
                    if (isset($result['ctype_id'])) {

                        $url = Watson::getUrl(array('page' => 'content/edit', 'article_id' => $article->getId(), 'mode' => 'edit', 'clang' => $clang_id, 'ctype' => $result['ctype_id']));

                    }


                    $suffix = array();
                    $suffix[] = $article->getId();

                    if (count(\rex_clang::getAll()) > 1) {
                        $suffix[] = \rex_clang::get($clang_id)->getName();
                    }
                    $suffix = implode(', ', $suffix);
                    $suffix = $suffix != '' ? '(' . $suffix . ')' : '';

                    $counter++;

                    $entry = new SearchResultEntry();
                    if ($counter == 1) {
                        $entry->setLegend(Watson::translate('watson_structure_legend'));
                    }
                    $entry->setValue($article->getName(), $suffix);
                    $entry->setDescription($path);
                    $entry->setIcon('watson-icon-article');
                    $entry->setUrl($url);
                    $entry->setQuickLookUrl('../index.php?article_id=' . $article->getId() . '&clang=' . $article->getClang());

                    $search_result->addEntry($entry);
                }

            }
        }

        return $search_result;
    }

}
