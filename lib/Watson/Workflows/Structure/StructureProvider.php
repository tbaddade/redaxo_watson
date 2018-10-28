<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Structure;

use Watson\Foundation\GeneratorWorkflow;
use Watson\Foundation\SupportProvider;
use Watson\Foundation\Workflow;

class StructureProvider extends SupportProvider
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
        $register[] = $this->registerArticleSearch();

        if (\rex::getUser()->isAdmin()) {
            $register[] = $this->registerCategoryGenerator();
        }

        return $register;
    }

    /**
     * Register article search.
     *
     * @return Workflow
     */
    public function registerArticleSearch()
    {
        return new ArticleSearch();
    }

    /**
     * Register category generator.
     *
     * @return GeneratorWorkflow
     */
    public function registerCategoryGenerator()
    {
        return new CategoryGenerator();
    }
}
