<?php


class watson_core_articles extends watson_searcher
{

    public function keywords()
    {
        return array('a', 'c');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if (watson::getPageParam('page') == 'structure' && $watson_search_term->isAddMode()) {

            // Artikel oder Kategorie anlegen

            $name = $watson_search_term->getTermsAsString();

            switch ($watson_search_term->getKeyword()) {

                case 'a':
                    $icon           = '../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_article.png';
                    $description    = $I18N->msg('b_create_article', $name);
                    $function       = 'add_art';
                    break;

                case 'c':
                    $icon           = '../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_article.png';
                    $description    = $I18N->msg('b_create_category', $name);
                    $function       = 'add_cat';
                    break;

                default:
                    $icon           = '';
                    $description    = '';
                    $function       = '';
                    break;

            }

            $entry = new watson_search_entry();
            $entry->setValue($name);
            $entry->setDescription($description);
            $entry->setIcon($icon);
            $entry->setUrl(watson::url(array('page' => 'structure', 'function' => $function, 'category_id' => watson::getPageParam('category_id'), 'watson_id' => 'rex-form-field-name', 'watson_text' => $name)));

            $watson_search_result->addEntry($entry);

        } elseif ($watson_search_term->getTerms()) {

            $fields = array(
                'a.name'      => '',
                's.value'     => range('1', '20'),
                's.file'      => range('1', '10'),
                's.filelist'  => range('1', '10'),
            );

            $search_fields = array();
            foreach ($fields as $field => $numbers) {
                if (is_array($numbers)) {
                    foreach ($numbers as $number) {
                        $search_fields[] = $field . $number;
                    }
                } else {
                    $search_fields[] = $field;
                }
            }
            $fields = $search_fields;

            $sql_query  = ' SELECT      a.id,
                                        a.name,
                                        a.clang,
                                        s.ctype
                            FROM            ' . watson::getTable('article') . '         AS a
                                LEFT JOIN   ' . watson::getTable('article_slice') . '   AS s
                                    ON      a.id = s.article_id
                            WHERE       ' . $watson_search_term->getSqlWhere($fields) . '
                            GROUP BY    a.id
                            ORDER BY    a.name
                ';

            $s = rex_sql::factory();
            $s->debugsql = true;
            $s->setQuery($sql_query);
            $results = $s->getArray();

            if ($s->getRows() >= 1) {

                foreach ($results as $result) {

                    $article = OOArticle::getArticleById($result['id']);

                    // Rechte prüfen
                    if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm('article[' . $article->getId() . ']')) {

                        $tree = $article->getParentTree();

                        $path = array();
                        foreach ($tree as $o) {
                            $path[] = $o->getName();
                        }
                        if (!$article->isStartArticle()) {
                            $path[] = $article->getName();
                        }

                        $path = '/' . implode('/', $path);

                        $entry = new watson_search_entry();
                        $entry->setValue($article->getName());
                        $entry->setDescription($path);
                        $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_article.png');
                        $entry->setUrl(watson::url(array('page' => 'content', 'article_id' => $article->getId(), 'mode' => 'edit', 'clang' => $result['clang'], 'ctype' => $result['ctype'])));
                        $entry->setQuickLookUrl('../index.php?article_id=' . $article->getId());

                        $watson_search_result->addEntry($entry);
                    }

                }
            }
        }

        return $watson_search_result;
    }
}
