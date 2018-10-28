<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Foundation;

abstract class SupportProvider
{
    /**
     * Register the directory to search a translation file.
     *
     * @return string
     */
    abstract public function i18n();

    /**
     * Register the search provider.
     *
     * @return Workflow|array
     */
    abstract public function register();
}
