<?php

class watson_core
{

    public static function registerAll()
    {
        global $REX, $I18N;

        if (rex_addon::isActivated('watson')) {
            $icon_path = '/watson';

            // Home ------------------------------------------------------------
            $url      = watson::url(array('page' => 'structure', 'category_id' => $REX['START_ARTICLE_ID']));
            $register = new watson_command();
            $register->setCommand('home', $url);
            $register->setDescription($I18N->msg('b_go_to_backend_startarticle'));
            watson::registerCommand($register);


            // Web -------------------------------------------------------------
            $url      = '../' . rex_getUrl($REX['START_ARTICLE_ID']);
            $register = new watson_command();
            $register->setCommand('web', $url, true);
            $register->setDescription($I18N->msg('b_go_to_frontend'));
            watson::registerCommand($register);


            // Logout ----------------------------------------------------------
            $url      = watson::url(array('rex_logout' => '1'));
            $register = new watson_command();
            $register->setCommand('logout', $url);
            $register->setDescription($I18N->msg('b_logout_from_backend'));
            watson::registerCommand($register);


            $url            = watson::url(array('page' => 'content', 'article_id' => '{{id}}', 'mode' => 'edit', 'clang' => '{{clang}}', 'ctype' => '{{ctype}}'));
            $quick_look_url = '../index.php?article_id={{id}}';
            $sql_query  = '
                SELECT      a.id,
                            a.name,
                            a.clang,
                            s.ctype
                FROM            ' . watson::getTable('article_slice') . '   AS s
                    LEFT JOIN   ' . watson::getTable('article') . '         AS a
                        ON      s.article_id = a.id
                WHERE       a.name LIKE {{q}}
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
            $register = new watson_feature();
            $register->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . $icon_path . '/icon_article.png');
            $register->setDescription($I18N->msg('b_open_article_in_backend'));
            $register->setUrl($url);
            $register->setQuickLookUrl($quick_look_url);
            $register->setSqlQuery($sql_query);
            watson::registerFeature($register);



            // Module --------------------------------------------------------------
            $keyword_add_url = watson::url(array('page' => 'module', 'function' => 'add'));
            $url             = watson::url(array('page' => 'module', 'modul_id' => '{{id}}', 'function' => 'edit'));
            $sql_query       = '
                SELECT      id,
                            name
                FROM        ' . watson::getTable('module') . '
                WHERE       name LIKE {{q}}
                    OR      eingabe LIKE {{q}}
                    OR      ausgabe LIKE {{q}}
                ORDER BY name';
            $register = new watson_feature();
            $register->setKeyword('m', $keyword_add_url, 'mname');
            $register->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . $icon_path . '/icon_module.png');
            $register->setDescription($I18N->msg('b_open_module'));
            $register->setUrl($url);
            $register->setSqlQuery($sql_query);
            watson::registerFeature($register);



            // Templates -------------------------------------------------------
            $keyword_add_url = watson::url(array('page' => 'template', 'function' => 'add'));
            $url             = watson::url(array('page' => 'template', 'template_id' => '{{id}}', 'function' => 'edit'));
            $sql_query       = '
                SELECT      id,
                            name
                FROM        ' . watson::getTable('template') . '
                WHERE       name LIKE {{q}}
                    OR      content LIKE {{q}}
                ORDER BY name';
            $register = new watson_feature();
            $register->setKeyword('t', $keyword_add_url, 'ltemplatename');
            $register->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . $icon_path . '/icon_template.png');
            $register->setDescription($I18N->msg('b_open_template'));
            $register->setUrl($url);
            $register->setSqlQuery($sql_query);
            watson::registerFeature($register);



            // Users -----------------------------------------------------------
            $keyword_add_url = watson::url(array('page' => 'user', 'FUNC_ADD' => '1'));
            $register = new watson_feature();
            $register->setKeyword('u', $keyword_add_url, 'userlogin');
            $register->setIcon('../' . $REX['MEDIA_ADDON_DIR'] . $icon_path . '/icon_user.png');
            watson::registerFeature($register);

        }
    }
}
