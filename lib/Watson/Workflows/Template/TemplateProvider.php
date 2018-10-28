<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Template;

use Watson\Foundation\SupportProvider;
use Watson\Foundation\Workflow;

class TemplateProvider extends SupportProvider
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
     * @return Workflow|array
     */
    public function register()
    {
        if (\rex::getUser()->isAdmin()) {
            return $this->registerTemplateSearch();
        }
        return [];
    }

    /**
     * Register template search.
     *
     * @return Workflow
     */
    public function registerTemplateSearch()
    {
        return new TemplateSearch();
    }
}
