<?php

$basedir = __DIR__;
$dir     = str_replace($REX['INCLUDE_PATH'].'/addons/', '', $basedir);
$myaddon = ltrim(substr($dir, 0, strpos($dir, '/')), DIRECTORY_SEPARATOR);

if (!class_exists('rex')) {
    // B Hilfsklassen
    require_once $basedir . '/b/lib/scan_directory.php';

    // REDAXO 5 Hilfsklassen
    require_once $basedir . '/redaxo5/lib/exception.php';
    require_once $basedir . '/redaxo5/lib/base/factory.php';
    require_once $basedir . '/redaxo5/lib/util/socket/socket.php';
    require_once $basedir . '/redaxo5/lib/util/path.php';
    require_once $basedir . '/redaxo5/lib/util/finder.php';

    // absoluten Pfad initialisieren
    rex_path::init($REX['HTDOCS_PATH'], 'redaxo');


    // Klassen laden ---------------------------------------------------------------
    $dirs = array();
    $dirs[rex_path::addon($myaddon, 'vendor/b/lib')] = true;
    $dirs[rex_path::addon($myaddon, 'vendor/redaxo5/lib')] = true;

    $scan = new b_scan_directory();
    $scan->addDirectories($dirs);
    $files = $scan->get();

    if (count($files) > 0) {
        foreach ($files as $file) {
            require_once $file;
        }
    }

    // i18n hinzufügen
    rex_i18n::addDirectory(rex_path::addon($myaddon, 'vendor/b/lang'));

    // relativen Pfad initialisieren
    rex_url::init($REX['HTDOCS_PATH'], rex_path::backend());

    // start timer at the very beginning
    rex::setProperty('timer', new rex_timer);

    // add backend flag to rex
    rex::setProperty('redaxo', $REX['REDAXO']);

    // add setup flag to rex
    rex::setProperty('setup', $REX['SETUP']);

    // ----------------- VERSION
    rex::setProperty('version', $REX['VERSION']);
    rex::setProperty('subversion', $REX['SUBVERSION']);
    rex::setProperty('minorversion', $REX['MINORVERSION']);

    // aus der config.yml uebernommen
    rex::setProperty('table_prefix', $REX['TABLE_PREFIX']);
    rex::setProperty('temp_prefix', $REX['TEMP_PREFIX']);
    rex::setProperty('accesskeys', $REX['ACKEY']);
    rex::setProperty('use_accesskeys', true);


    if (rex::isBackend() && $REX['USER']) {
        rex::setProperty('user', $REX['USER']);
    }


    switch ($REX['CUR_CLANG']) {
        case '1';
            rex::setProperty('lang', 'en_en');
            break;
        default:
            rex::setProperty('lang', 'de_de');
            break;
    }

    if(rex::getUser()) {
        rex_view::setJsProperty('backend', true);
        rex_view::setJsProperty('backendUrl', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        rex_register_extension('PAGE_HEADER', 'redaxo5_pageHeader', array(), REX_EXTENSION_LATE);
    }


    function redaxo5_pageHeader($params)
    {
        $css_files      = rex_view::getCssFiles();
        $js_files       = rex_view::getJsFiles();
        $js_properties  = json_encode(rex_view::getJsProperties());

        foreach ($css_files as $media => $files) {
            foreach ($files as $file) {
                $params['subject'] .= "\n" . '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
            }
        }

        if ($js_properties) {
            $params['subject'] .= "\n" . '

                <script type="text/javascript">
                    <!--
                    var rex = ' . $js_properties . ';
                    //-->
                </script>';
        }

        foreach ($js_files as $file) {
            $params['subject'] .= "\n" . '<script type="text/javascript" src="' . $file . '"></script>';
        }

        return $params['subject'];
    }
}
