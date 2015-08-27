<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ($REX['REDAXO'] && $REX['USER']) {

    $package = require __DIR__ . '/lib/Package/start.php';
    $name    = $package::get('name', 'watson', 'watson');

    
    //if ($package::get('rex.backend', false, $name) && $package::get('rex.user', false, $name)) {

    $providers = $package::get('package.providers', false, $name);


    if (count($providers)) {
    
        $searchers = array();

        foreach ($providers as $provider) {

            if ($provider instanceof \Watson\Foundation\Search) {

                $searchers[] = $provider;

            }

            if ($provider instanceof \Watson\Foundation\Console) {

                $console_instances[] = $provider;

            }
        }
        

        if (count($searchers)) {
        
            rex_register_extension('PAGE_HEADER'    , '\Watson\Foundation\Extension::searchHead');
        
            rex_register_extension('OUTPUT_FILTER'  , '\Watson\Foundation\Extension::searchAgent');

            rex_register_extension('ADDONS_INCLUDED', '\Watson\Foundation\Extension::searchRun', array('searchers' => $searchers), REX_EXTENSION_LATE);

        }
        

        if (count($console_instances)) {
        
            rex_register_extension('PAGE_HEADER'    , '\Watson\Foundation\Extension::consoleHead');
        
            rex_register_extension('OUTPUT_FILTER'  , '\Watson\Foundation\Extension::consoleAgent');

            rex_register_extension('ADDONS_INCLUDED', '\Watson\Foundation\Extension::consoleRun', array('console_instances' => $console_instances), REX_EXTENSION_LATE);

        }

    }

}
