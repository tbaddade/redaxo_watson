<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (rex::isBackend() && rex::getUser()) {

    $providers = \Watson\Foundation\Watson::loadProviders();

    if (count($providers)) {
    
        $searchers = array();

        foreach ($providers as $provider) {

            if ($provider instanceof \Watson\Foundation\Search) {

                $searchers[] = $provider;

            }

        }
        

        if (count($searchers)) {
        
            rex_extension::register('PAGE_HEADER'    , '\Watson\Foundation\Extension::searchHead');
        
            rex_extension::register('OUTPUT_FILTER'  , '\Watson\Foundation\Extension::searchAgent');

            rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::searchRun', rex_extension::LATE, array('searchers' => $searchers));

        }

    }


    $stylesheets = $this->getProperty('stylesheets');

    if (count($stylesheets)) {

        foreach ($stylesheets as $stylesheet) {

            rex_view::addCssFile($this->getAssetsUrl( $stylesheet ));
            
        }
    }


    $javascripts = $this->getProperty('javascripts');

    if (count($javascripts)) {

        foreach ($javascripts as $javascript) {

            rex_view::addJsFile($this->getAssetsUrl( $javascript ));
            
        }
    }

}
