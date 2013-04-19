<?php

/**
 *
 * @author blumbeet - web.studio
 * @author Thomas Blum
 * @author mail[at]blumbeet[dot]com Thomas Blum
 *
 */


$basedir  = __DIR__;
$myaddon  = ltrim(substr($basedir, strrpos($basedir, '/')), DIRECTORY_SEPARATOR);


$REX['ADDON']['rxid'][$myaddon] = '';

// Credits
$REX['ADDON']['version'][$myaddon]     = '0.0';
$REX['ADDON']['author'][$myaddon]      = 'blumbeet - web.studio';
$REX['ADDON']['supportpage'][$myaddon] = '';
$REX['ADDON']['perm'][$myaddon]        = 'admin[]';



// Check AddOns und Versionen --------------------------------------------------
if (OOPlugin::isActivated('watson', $myaddon)) {

    if ($REX['USER']) {
        require_once($basedir . '/lib/' . $myaddon . '.php');
        rex_register_extension('ADDONS_INCLUDED', $myaddon . '::registerAll');

    }
}
