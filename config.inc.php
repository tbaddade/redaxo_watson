<?php

/**
 *
 * @author blumbeet - web.studio
 * @author Thomas Blum
 * @author mail[at]blumbeet[dot]com Thomas Blum
 *
 */


$basedir = __DIR__;
$myaddon = ltrim(substr($basedir, strrpos($basedir, '/')), DIRECTORY_SEPARATOR);



// Sprachdateien anhaengen
// muss wegen Developer-AddOn-Block extra sein
if ($REX['REDAXO']) {
    $I18N->appendFile($basedir . '/lang/');
}



$REX['ADDON']['rxid'][$myaddon] = '';
//$REX['ADDON']['name'][$myaddon] = $I18N->msg('b_watson_title');

// Credits
$REX['ADDON']['version'][$myaddon]     = '0.0';
$REX['ADDON']['author'][$myaddon]      = 'blumbeet - web.studio';
$REX['ADDON']['supportpage'][$myaddon] = '';
$REX['ADDON']['perm'][$myaddon]        = 'admin[]';
//$REX['ADDON']['navigation'][$myaddon]  = array('block' => 'developer');



// Check AddOns und Versionen --------------------------------------------------
if (OOAddon::isActivated($myaddon)) {

    require_once($basedir . '/lib/watson_load.php');
    require_once($basedir . '/vendor/init.php');

    if (rex::getUser()) {

        watson_load::init();

        rex_register_extension('PAGE_TITLE', $myaddon . '_load::check_install');
        rex_register_extension('OUTPUT_FILTER', $myaddon . '_extensions::watson');

        $watson = rex_request('watson', 'string');
        if ($watson) {
            rex_register_extension('ADDONS_INCLUDED', $myaddon . '::result', array('q' => $watson), REX_EXTENSION_LATE);
        }

        rex_view::addCssFile(rex_url::addonAssets($myaddon, 'watson.css'));

        rex_view::addJsFile(rex_url::addonAssets($myaddon, 'hogan.min.js'));
        rex_view::addJsFile(rex_url::addonAssets($myaddon, 'typeahead.js'));
        rex_view::addJsFile(rex_url::addonAssets($myaddon, 'watson.js'));

    }
}