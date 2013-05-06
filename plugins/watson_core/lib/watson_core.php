<?php


class watson_core_articles extends watson_searcher
{

    public function keywords()
    {
        return array('a');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();


        if ($watson_search_term->getTerms()) {

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








class watson_core_modules extends watson_searcher
{

    public function keywords()
    {
        return array('m');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if ($watson_search_term->getTerms()) {

            $fields = array(
                'name',
                'eingabe',
                'ausgabe',
            );

            $sql_query  = ' SELECT      id,
                                        name
                            FROM        ' . watson::getTable('module') . '
                            WHERE       ' . $watson_search_term->getSqlWhere($fields) . '
                            ORDER BY    name';

            $s = rex_sql::factory();
            $s->debugsql = true;
            $s->setQuery($sql_query);
            $results = $s->getArray();

            if ($s->getRows() >= 1) {

                foreach ($results as $result) {
                    $url = watson::url(array('page' => 'module', 'modul_id' => $result['id'], 'function' => 'edit'));

                    $entry = new watson_search_entry();
                    $entry->setValue($result['name']);
                    $entry->setDescription($I18N->msg('b_open_module'));
                    $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_module.png');
                    $entry->setUrl($url);
                    $entry->setQuickLookUrl($url);

                    $watson_search_result->addEntry($entry);

                }
            }
        }

        return $watson_search_result;
    }
}








class watson_core_templates extends watson_searcher
{

    public function keywords()
    {
        return array('t');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if ($watson_search_term->getTerms()) {

            if ($watson_search_term->isAddMode()) {
                $name = implode(' ', $watson_search_term->getTerms());

                $entry = new watson_search_entry();
                $entry->setValue($name);
                $entry->setDescription($I18N->msg('b_create_template', $name));
                $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_template.png');
                $entry->setUrl(watson::url(array('page' => 'template', 'function' => 'add', 'watson_id' => 'ltemplatename', 'watson_text' => $name)));

                $watson_search_result->addEntry($entry);

            } else {
                $fields = array(
                    'name',
                    'content',
                );

                $sql_query  = ' SELECT      id,
                                            name
                                FROM        ' . watson::getTable('template') . '
                                WHERE       ' . $watson_search_term->getSqlWhere($fields) . '
                                ORDER BY    name';

                $s = rex_sql::factory();
                $s->debugsql = true;
                $s->setQuery($sql_query);
                $results = $s->getArray();

                if ($s->getRows() >= 1) {

                    foreach ($results as $result) {
                        $url = watson::url(array('page' => 'template', 'template_id' => $result['id'], 'function' => 'edit'));

                        $entry = new watson_search_entry();
                        $entry->setValue($result['name']);
                        $entry->setDescription($I18N->msg('b_open_template'));
                        $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_template.png');
                        $entry->setUrl($url);
                        $entry->setQuickLookUrl($url);

                        $watson_search_result->addEntry($entry);

                    }
                }
            }
        }

        return $watson_search_result;
    }
}








class watson_core_users extends watson_searcher
{

    public function keywords()
    {
        return array('u');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if ($watson_search_term->getTerms()) {

            if ($watson_search_term->isAddMode()) {
                $name = implode(' ', $watson_search_term->getTerms());

                $entry = new watson_search_entry();
                $entry->setValue($name);
                $entry->setDescription($I18N->msg('b_create_user', $name));
                $entry->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . '/watson/icon_user.png');
                $entry->setUrl(watson::url(array('page' => 'user', 'FUNC_ADD' => '1', 'watson_id' => 'userlogin', 'watson_text' => $name)));

                $watson_search_result->addEntry($entry);

            }
        }

        return $watson_search_result;
    }
}








class watson_core_logout extends watson_searcher
{

    public function keywords()
    {
        return array('logout');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if (!$watson_search_term->getTerms()) {

            $entry = new watson_search_entry();
            $entry->setValue($I18N->msg('b_logout_from_backend'));
            $entry->setUrl(watson::url(array('rex_logout' => '1')));

            $watson_search_result->addEntry($entry);

        }

        return $watson_search_result;
    }
}








class watson_core_start extends watson_searcher
{

    public function keywords()
    {
        return array('start');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if (!$watson_search_term->getTerms()) {

            $entry = new watson_search_entry();
            $entry->setValue($I18N->msg('b_go_to_backend_startarticle'));
            $entry->setUrl(watson::url(array('page' => 'structure', 'category_id' => $REX['START_ARTICLE_ID'])));

            $watson_search_result->addEntry($entry);

        }

        return $watson_search_result;
    }
}








class watson_core_home extends watson_searcher
{

    public function keywords()
    {
        return array('home');
    }

    public function search(watson_search_term $watson_search_term)
    {
        global $REX, $I18N;

        $watson_search_result = new watson_search_result();

        if (!$watson_search_term->getTerms()) {

            $entry = new watson_search_entry();
            $entry->setValue($I18N->msg('b_go_to_frontend'));
            $entry->setUrl('../' . rex_getUrl($REX['START_ARTICLE_ID']), true);

            $watson_search_result->addEntry($entry);

        }

        return $watson_search_result;
    }
}
