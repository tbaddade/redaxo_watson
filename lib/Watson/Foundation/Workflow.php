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

abstract class Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    abstract public function commands();

    /**
     * @return Documention
     */
    abstract public function documentation();

    /**
     * Return array of registered page params.
     *
     * @return array
     */
    abstract public function registerPageParams();

    /**
     * Execute the command for the given Command.
     *
     * @param Command $command
     *
     * @return Result
     */
    abstract public function fire(Command $command);

    protected function getDatabaseResults($query)
    {
        $query = str_replace(["\r\n", "\r", "\n"], '', $query);

        $limit = strpos(strtoupper($query), ' LIMIT ');

        if ($limit !== false) {
            $query = substr($query, 0, $limit);
        }

        $query .= ' LIMIT '.Watson::getResultLimit();

        $sql = \rex_sql::factory();
        //$sql->setDebug();
        $sql->setQuery($query);
        //exit();
        return $sql->getArray();
    }
}
