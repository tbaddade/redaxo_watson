<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Workflows\YForm;

use \Watson\Foundation\SupportProvider;

class YFormProvider extends SupportProvider
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
        if (\rex::getUser()->isAdmin() && \rex_addon::get('yform')->isAvailable() && \rex_plugin::get('yform', 'manager')->isAvailable()) {
            return $this->registerYFormSearch();
        }
    }


    /**
     * Register template search.
     *
     * @return void
     */
    public function registerYFormSearch()
    {
        return new YFormSearch();
    }

}
