<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Uninstall package
 *
 */

if (! class_exists('\Package\Package')) {
    require  __DIR__ . '/lib/Package/Package.php';
}

\Package\Package::uninstall();