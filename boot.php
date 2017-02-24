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
        $workflows = [];

        foreach ($providers as $provider) {
            if ($provider instanceof \Watson\Foundation\Workflow) {
                $workflows[] = $provider;
            }
        }

        if (count($workflows)) {
            rex_extension::register('PAGE_HEADER', '\Watson\Foundation\Extension::head');

            rex_extension::register('OUTPUT_FILTER', '\Watson\Foundation\Extension::agent');

            rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::run', rex_extension::LATE, ['workflows' => $workflows]);
            rex_extension::register('PACKAGES_INCLUDED', '\Watson\Foundation\Extension::callWatsonFunc', rex_extension::LATE);

            if (\Watson\Foundation\Watson::getToggleButtonStatus()) {
                rex_extension::register('META_NAVI', '\Watson\Foundation\Extension::toggleButton');
            }
        }
    }

    rex_extension::register('CHEATSHEET_PROVIDER', function(\rex_extension_point $ep) {
        $subject = $ep->getSubject();
        $subject[] = '\Watson\Cheatsheet\CheatsheetServiceProvider';
        $ep->setSubject($subject);
    });

    $stylesheets = $this->getProperty('stylesheets');

    if (count($stylesheets)) {
        foreach ($stylesheets as $stylesheet) {
            rex_view::addCssFile($this->getAssetsUrl($stylesheet));
        }
    }

    $javascripts = $this->getProperty('javascripts');

    if (count($javascripts)) {
        foreach ($javascripts as $javascript) {
            rex_view::addJsFile($this->getAssetsUrl($javascript));
        }
    }
}
