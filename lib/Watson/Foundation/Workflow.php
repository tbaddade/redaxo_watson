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

use \Watson\Foundation\Command;
use \Watson\Foundation\Watson;

abstract class Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    abstract function commands();

    /**
     *
     * @return Documention
     */
    abstract function documentation();

    /**
     *
     * @return an array of registered page params
     */
    abstract function registerPageParams();

    /**
     * Execute the command for the given Command
     *
     * @param  Command $command
     * @return Result
     */
    abstract function fire(Command $command);


    protected function getDatabaseResults($query)
    {
        $query = str_replace(array("\r\n", "\r", "\n"), '', $query);

        $limit = strpos(strtoupper($query), ' LIMIT ');
        
        if($limit !== false) {
            $query = substr($query, 0, $limit);
        }
        
        $query .= ' LIMIT ' . Watson::getResultLimit();


        $sql = \rex_sql::factory();
        //$sql->setDebug();
        $sql->setQuery($query);
        //exit();
        return $sql->getArray();
    }

}
