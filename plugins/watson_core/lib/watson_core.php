<?php

class watson_core
{

    public static function registerAll()
    {
        global $REX;

        if (rex_addon::isActivated('watson')) {


            // Logout ----------------------------------------------------------
            $register = array();
            $register['keyword']    = 'logout';
            $register['url']        = watson::url(array('rex_logout' => '1'));
            watson::registerCommand($register);

            // Home ------------------------------------------------------------
            $register = array();
            $register['keyword']    = 'home';
            $register['url']        = watson::url(array('page' => 'structure', 'category_id' => $REX['START_ARTICLE_ID']));
            watson::registerCommand($register);

            // Web -------------------------------------------------------------
            $register = array();
            $register['keyword']    = 'web';
            $register['url']        = '../' . rex_getUrl($REX['START_ARTICLE_ID']);
            watson::registerCommand($register);



            // Artikel und Slices --------------------------------------------------
            // CONCAT(a.name, " - Ctype: ", s.ctype) AS name,
            // CONCAT(a.id, "|", s.ctype) AS groupby
            $register = array();
            $register['keyword']    = '';
            $register['url']        = watson::url(array('page' => 'content', 'article_id' => '{{id}}', 'mode' => 'edit', 'clang' => '{{clang}}', 'ctype' => '{{ctype}}'));
            $register['query']      = 'SELECT       a.id,
                                                    a.name,
                                                    a.clang,
                                                    s.ctype
                                       FROM         ' . watson::getTable('article_slice') . '  AS s
                                        LEFT JOIN   ' . watson::getTable('article') . '        AS a
                                            ON      s.article_id = a.id
                                       WHERE        a.name LIKE {{q}}
                                            OR      s.value1 LIKE {{q}}
                                            OR      s.value2 LIKE {{q}}
                                            OR      s.value3 LIKE {{q}}
                                            OR      s.value4 LIKE {{q}}
                                            OR      s.value5 LIKE {{q}}
                                            OR      s.value6 LIKE {{q}}
                                            OR      s.value7 LIKE {{q}}
                                            OR      s.value8 LIKE {{q}}
                                            OR      s.value9 LIKE {{q}}
                                            OR      s.value10 LIKE {{q}}
                                            OR      s.value11 LIKE {{q}}
                                            OR      s.value12 LIKE {{q}}
                                            OR      s.value13 LIKE {{q}}
                                            OR      s.value14 LIKE {{q}}
                                            OR      s.value15 LIKE {{q}}
                                            OR      s.value16 LIKE {{q}}
                                            OR      s.value17 LIKE {{q}}
                                            OR      s.value18 LIKE {{q}}
                                            OR      s.value19 LIKE {{q}}
                                            OR      s.value20 LIKE {{q}}
                                        GROUP BY    a.id
                                        ORDER BY a.name
                                       ';
            watson::register($register);




            // Module --------------------------------------------------------------
            $register = array();
            $register['keyword']    = 'm';
            $register['add_id']     = 'mname';
            $register['add_url']    = watson::url(array('page' => 'module', 'function' => 'add'));
            $register['url']        = watson::url(array('page' => 'module', 'modul_id' => '{{id}}', 'function' => 'edit'));
            $register['query']      = 'SELECT       id,
                                                    name
                                       FROM         ' . watson::getTable('module') . '
                                       WHERE        name LIKE {{q}}
                                            OR      eingabe LIKE {{q}}
                                            OR      ausgabe LIKE {{q}}
                                       ORDER BY name
                                       ';
            watson::register($register);




            // Templates -----------------------------------------------------------
            $register = array();
            $register['keyword']    = 't';
            $register['add_id']     = 'ltemplatename';
            $register['add_url']    = watson::url(array('page' => 'template', 'function' => 'add'));
            $register['url']        = watson::url(array('page' => 'template', 'template_id' => '{{id}}', 'function' => 'edit'));
            $register['query']      = 'SELECT       id,
                                                    name
                                       FROM         ' . watson::getTable('template') . '
                                       WHERE        name LIKE {{q}}
                                            OR      content LIKE {{q}}
                                       ORDER BY name
                                       ';
            watson::register($register);




            // User --------------------------------------------------------------
            $register = array();
            $register['keyword']    = 'u';
            $register['add_id']     = 'userlogin';
            $register['add_url']    = watson::url(array('page' => 'user', 'FUNC_ADD' => '1'));
            watson::register($register);
        }
    }
}
