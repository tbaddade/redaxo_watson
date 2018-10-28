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

abstract class GeneratorWorkflow extends Workflow
{
    /**
     * Get the path to the generator template.
     *
     * @return mixed
     */
    abstract protected function getTemplatePath();

    /**
     * Try to mold user's input
     * to one of the CRUD operations.
     *
     * @param $commandName
     *
     * @return string
     */
    protected function normalizeCommandName($commandName)
    {
        $pieces = explode(':', $commandName);
        $action = array_pop($pieces);
        switch ($action) {
            case 'add':
            case 'create':
            case 'make':
                return 'create';
            case 'append':
            case 'update':
            case 'insert':
                return 'update';
            case 'delete':
            case 'destroy':
            case 'drop':
                return 'delete';
            default:
                return $action;
        }
    }
}
