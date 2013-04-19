<?php

class watson_load
{

    static function init()
    {
        if(!defined('APPLICATION_ENV')) {
            if(false === stripos($_SERVER['SERVER_NAME'], 'localhost')) {
                define('APPLICATION_ENV', 'development');
            } else {
                define('APPLICATION_ENV', 'production');
            }
        }


        $myaddon = 'watson';

        // Klassen laden -------------------------------------------------------
        $dirs = array();
        $dirs[rex_path::addon($myaddon, 'lib')] = true;

        $scan = new b_scan_directory();
        $scan->addDirectories($dirs);
        $files = $scan->get();

        if (count($files) > 0) {
            foreach ($files as $file) {
                require_once $file;
            }
        }


        rex_fragment::addDirectory(rex_path::addon($myaddon, 'fragments'));
    }

    static function check_install()
    {
        global $REX;

        // Einstellungen -------------------------------------------------------
        $myaddon = 'watson';
        $basedir = $REX['INCLUDE_PATH'] . '/addons/' . $myaddon;

        // Check AddOns und Versionen ------------------------------------------
        require_once $basedir . '/vendor/b/lib/check.php';

        $min_php_version    = REX_MIN_PHP_VERSION;
        $min_redaxo_version = '4.5';
        $addons_needed      = array();

        if (b_check::install($min_redaxo_version, $min_php_version, $addons_needed)) {
            $REX['ADDON']['install'][$myaddon] = 1;
        } else {
            $REX['ADDON']['installmsg'][$myaddon] = '&nbsp;';
        }
    }
}
