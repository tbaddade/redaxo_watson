<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Sprog;

use Watson\Foundation\SupportProvider;
use Watson\Foundation\Workflow;

class SprogProvider extends SupportProvider
{
    /**
     * Register the directory to search a translation file.
     *
     * @return string
     */
    public function i18n()
    {
        return __DIR__;
    }

    /**
     * Register the service provider.
     *
     * @return array
     */
    public function register()
    {
        $register = [];

        if (\rex_addon::get('sprog')->isAvailable() && \rex::getUser()->hasPerm('sprog[wildcard]')) {
            $register[] = $this->registerWildcardCreate();
            $register[] = $this->registerWildcardSearch();
        }

        return $register;
    }

    /**
     * Register wildcard create.
     *
     * @return Workflow
     */
    public function registerWildcardCreate()
    {
        return new WildcardCreate();
    }

    /**
     * Register wildcard search.
     *
     * @return Workflow
     */
    public function registerWildcardSearch()
    {
        return new WildcardSearch();
    }
}
