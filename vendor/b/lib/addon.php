<?php

class b_addon
{

    static function getUrl($page = '', $subpage = '', $params = '', $dividier = '&amp;')
    {
        global $REX;

        $page = $page == '' ? rex_request('page', 'string') : $page;
        $subpage = $subpage == '' ? rex_request('subpage', 'string') : $subpage;

        $param_string = rex_param_string($params, $dividier);

        return 'index.php?page=' . $page . '&amp;subpage=' . $subpage . $param_string;
    }
}
