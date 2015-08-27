<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Structure;

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
        global $REX;


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
                                    a.clang,
                                    CONCAT(a.id, "|", a.clang) as bulldog
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
            's.file'      => range('1', '10'),
            's.filelist'  => range('1', '10'),
        );

        $searchFields = array();
        foreach ($fields as $field => $numbers) {

            foreach ($numbers as $number) {

                $searchFields[] = $field . $number;

            }

        }


        $fields = $searchFields;

        $sql_query  = ' SELECT      s.article_id AS id,
                                    s.clang,
                                    s.ctype,
                                    CONCAT(s.article_id, "|", s.clang) as bulldog
                        FROM        ' . Watson::getTable('article_slice') . ' AS s
                            LEFT JOIN
                                    ' . Watson::getTable('article') . ' AS a
                                ON  (s.article_id = a.id AND s.clang = a.clang)
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

            foreach ($searchResults as $result) {

                $clang       = $result['clang'];
                $article     = \OOArticle::getArticleById($result['id'], $clang);
                $category_id = $article->getCategoryId();


                // Rechte prüfen
                if (in_array($clang, $REX['USER']->getClangPerm()) && $REX['USER']->hasCategoryPerm($category_id)) {

                    $path = array();

                    $tree = $article->getParentTree();
                    foreach ($tree as $o) {
                        $path[] = $o->getName();
                    }

                    if (!$article->isStartArticle()) {
                        $path[] = $article->getName();
                    }

                    $path = '/' . implode('/', $path);

                    
                    $url = Watson::getUrl(array('page' => 'structure', 'category_id' => $article->getCategoryId(), 'clang' => $clang));
                    
                    if (isset($result['ctype'])) {

                        $url = Watson::getUrl(array('page' => 'content', 'article_id' => $article->getId(), 'mode' => 'edit', 'clang' => $clang, 'ctype' => $result['ctype']));

                    }


                    $suffix = array();
                    if ($REX['USER']->hasPerm('advancedMode[]')) {
                        $suffix[] = $article->getId();
                    }

                    if (count($REX['CLANG']) > 1) {
                        $suffix[] = $REX['CLANG'][$clang];
                    }
                    $suffix = implode(', ', $suffix);
                    $suffix = $suffix != '' ? '(' . $suffix . ')' : '';



                    $entry = new SearchResultEntry();
                    $entry->setValue($article->getName(), $suffix);
                    $entry->setDescription($path);
                    $entry->setIcon('icon_article.png');
                    $entry->setUrl($url);
                    $entry->setQuickLookUrl('../index.php?article_id=' . $article->getId() . '&clang=' . $clang);

                    $search_result->addEntry($entry);
                }

            }
        }

        return $search_result;
    }

}
