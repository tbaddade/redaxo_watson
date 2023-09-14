<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

if (rex::isBackend() && rex::getUser() && Watson::hasProviders()) {
    if (rex_get('watson_query')) {
        $providers = Watson::loadProviders();

        $workflows = [];
        foreach ($providers as $provider) {
            if ($provider instanceof Workflow) {
                $workflows[] = $provider;
            }
        }

        rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::run', rex_extension::LATE, ['workflows' => $workflows]);
        rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::callWatsonFunc', rex_extension::LATE);
    }

    //rex_extension::register('PAGE_HEADER', '\Watson\Foundation\Extension::head');

    rex_view::setJsProperty('watson', [
        'resultLimit' => Watson::getResultLimit(),
        'agentHotkey' => Watson::getAgentHotkey(),
        'quicklookHotkey' => Watson::getQuicklookHotkey(),
        'backend' => true,
        'backendUrl' => rex_url::backendPage('watson', [], false),
        'backendRemoteUrl' => rex_url::backendPage('watson', ['watson_query' => ''], false).'%QUERY',
        'wildcard' => '%QUERY',
        'version' => rex_addon::get('watson')->getVersion(),
    ]);

    rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::navigation');

    rex_extension::register('PAGES_PREPARED', static function() {
        $currentPage = rex_be_controller::getCurrentPageObject();
        if ($currentPage && $currentPage->hasLayout() && !$currentPage->isPopup()) {
            rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::agent');
        }
    });

    if (Watson::showToggleButton()) {
        rex_extension::register('META_NAVI', '\Watson\Foundation\Extension::toggleButton');
    }

    foreach ($this->getProperty('stylesheets', []) as $stylesheet) {
        rex_view::addCssFile($this->getAssetsUrl($stylesheet));
    }

    foreach ($this->getProperty('javascripts', []) as $javascript) {
        rex_view::addJsFile($this->getAssetsUrl($javascript), [rex_view::JS_DEFERED => true]);
    }
}
