<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Watson\Workflows\Wildcard;

use \Watson\Foundation\SupportProvider;

class WildcardProvider extends SupportProvider
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
        $registered = array();

        if (\rex_addon::get('wildcard')->isAvailable() && \rex::getUser()->hasPerm('wildcard[]')) {

            $registered[] = $this->registerWildcardCreate();
            $registered[] = $this->registerWildcardSearch();

        }

        return $registered;

    }


    /**
     * Register wildcard create.
     *
     * @return void
     */
    public function registerWildcardCreate()
    {
    
        return new WildcardCreate();

    }


    /**
     * Register wildcard search.
     *
     * @return void
     */
    public function registerWildcardSearch()
    {
    
        return new WildcardSearch();

    }


}
