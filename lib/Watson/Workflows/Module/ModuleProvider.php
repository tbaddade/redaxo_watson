<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Module;

use Watson\Foundation\GeneratorWorkflow;
use Watson\Foundation\SupportProvider;
use Watson\Foundation\Workflow;

class ModuleProvider extends SupportProvider
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
        if (\rex::getUser()->isAdmin()) {
            $register[] = $this->registerModuleSearch();
            $register[] = $this->registerModuleGenerator();
        }

        return $register;
    }

    /**
     * Register module search.
     *
     * @return Workflow
     */
    public function registerModuleSearch()
    {
        return new ModuleSearch();
    }

    /**
     * Register module generator.
     *
     * @return GeneratorWorkflow
     */
    public function registerModuleGenerator()
    {
        return new ModuleGenerator();
    }
}
