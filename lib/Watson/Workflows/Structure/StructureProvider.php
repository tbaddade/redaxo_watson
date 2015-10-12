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

use \Watson\Foundation\SupportProvider;

class StructureProvider extends SupportProvider
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
        $register[] = $this->registerArticleSearch();
        
        return $register;

    }


    /**
     * Register article search.
     *
     * @return void
     */
    public function registerArticleSearch()
    {
        
        return new ArticleSearch();

    }
}
