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

use \Watson\Foundation\SupportProvider;

class ModuleProvider extends SupportProvider
{

    /**
     * Register the directory to search a translation file.
     *
     * @return void
     */
    public function i18n()
    {
        return __DIR__;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $register = array();
        $register[] = $this->registerModuleSearch();
        if (\rex::getUser()->isAdmin()) {
            $register[] = $this->registerModuleGenerator();
        }

        return $register;
    }


    /**
     * Register module search.
     *
     * @return void
     */
    public function registerModuleSearch()
    {
        return new ModuleSearch();
    }


    /**
     * Register module generator.
     *
     * @return void
     */
    public function registerModuleGenerator()
    {
        return new ModuleGenerator();
    }
}
