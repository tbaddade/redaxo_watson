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

class SearchCommand extends Command
{

    public function __construct($input)
    {

        parent::__construct($input);

    }



    public function getSqlWhere($fields)
    {
        $where = array();

        foreach ($fields as $field) {

            $w = array();

            foreach ($this->getCommandParts() as $command_part) {

                $w[] = $field . ' LIKE "%' . $command_part . '%"';

            }

            $where[] = '(' . implode(' AND ', $w) . ')';
        }

        return implode(' OR ', $where);
    }



    public function deleteCommandFromCommandParts()
    {
        foreach ($this->command_parts as $key => $command_part) {

            if ($this->command == $command_part) {

                unset($this->command_parts[$key]);

            }
        }
    }


}
