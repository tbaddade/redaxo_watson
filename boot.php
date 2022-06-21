<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (rex::isBackend() && rex::getUser() && \Watson\Foundation\Watson::hasProviders()) {
    if (rex_get('watson_query')) {
        $providers = \Watson\Foundation\Watson::loadProviders();

        $workflows = [];
        foreach ($providers as $provider) {
            if ($provider instanceof \Watson\Foundation\Workflow) {
                $workflows[] = $provider;
            }
        }

        rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::run', rex_extension::LATE, ['workflows' => $workflows]);
        rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::callWatsonFunc', rex_extension::LATE);
        //rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::legend', rex_extension::LATE, ['workflows' => $workflows]);
    }

    rex_extension::register('PAGE_HEADER', '\Watson\Foundation\Extension::head');
    rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::navigation');


    rex_extension::register('PAGES_PREPARED', static function() {
        if (rex_be_controller::getCurrentPageObject() && rex_be_controller::getCurrentPageObject()->hasLayout() && !rex_be_controller::getCurrentPageObject()->isPopup()) {
            rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::agent');
        }
    });

    if (\Watson\Foundation\Watson::getToggleButtonStatus()) {
        rex_extension::register('META_NAVI', '\Watson\Foundation\Extension::toggleButton');
    }

    foreach ($this->getProperty('stylesheets', []) as $stylesheet) {
        rex_view::addCssFile($this->getAssetsUrl($stylesheet));
    }

    foreach ($this->getProperty('javascripts', []) as $javascript) {
        rex_view::addJsFile($this->getAssetsUrl($javascript));
    }
}
