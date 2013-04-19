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

$REX['ADDON']['install'][$myaddon] = 0;
