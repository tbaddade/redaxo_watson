<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Parsers;

class OptionFieldsParser
{
    /**
     * Parse a string of fields, like
     * name:text, text:textarea:textile, categories:select(SELECT id, name FROM table), yesno:select([0 => 'no', 1 => 'yes']).
     *
     * @param string $fields
     *
     * @return array
     */
    public function parse($fields)
    {
        if (!$fields) {
            return [];
        }

        // name:string, age:integer
        // name:string(10,2), age:integer
        $fields = $this->splitFields($fields);

        $parsed = [];
        foreach ($fields as $index => $field) {
            // Example:
            // name:text:nullable => ['name', 'string', 'nullable']
            // name:text(15):nullable
            $chunks = preg_split('/\s?:\s?/', $field, null);

            // The first item will be our label
            $label = array_shift($chunks);

            // The next will be the schema type
            $type = array_shift($chunks);
            $args = null;
            $args_input = [];

            // See if args were provided, like:
            // name:select('SELECT)
            if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches)) {
                $type = $matches[1];
                $args = $this->splitArgs($matches[2]);
                foreach ($args as $i => $arg) {
                    if (substr($arg, 0, 1) == '[' && substr($arg, -1) == ']') {
                        $arg = substr($arg, 1, -1);
                        $buildArray = [];
                        if ($arg != '') {
                            $pairs = explode(',', $arg);
                            foreach ($pairs as $j => $pair) {
                                $data = explode('=>', $pair);
                                if (count($data) == 1) {
                                    $buildArray[$j] = trim($data[0]);
                                } elseif (count($data) == 2) {
                                    $buildArray[trim(trim($data[0]), "'")] = trim(trim($data[1]), "'");
                                }
                            }
                        }
                        $args_input[$i] = $args[$i];
                        $args[$i] = $buildArray;
                    }
                }
            }

            // Finally, anything that remains will
            // be our decorators
            $decorators = $chunks;
            $parsed[$index] = ['label' => $label, 'type' => $type];
            if (isset($args)) {
                $parsed[$index]['args'] = $args;
                $parsed[$index]['args_input'] = $args_input;
            }
            if ($decorators) {
                $parsed[$index]['decorators'] = $this->removeEmptyValuesAndRebuildKeys($decorators);
            }
        }

        return $parsed;
    }

    protected function splitFields($string)
    {
        // number of nested sets of brackets
        $level = 0;
        // array to return
        $return = [];
        // current index in the array to return, for convenience
        $currentIndex = 0;

        for ($i = 0; $i < strlen($string); ++$i) {
            switch ($string[$i]) {
                case '(':
                    $level++;
                    $return[$currentIndex] .= '(';
                    break;
                case ')':
                    $level--;
                    $return[$currentIndex] .= ')';
                    break;
                case ',':
                    if ($level == 0) {
                        ++$currentIndex;
                        $return[$currentIndex] = '';
                        break;
                    }
                    // else fallthrough
                    // no break
                default:
                    if (isset($return[$currentIndex])) {
                        $return[$currentIndex] .= $string[$i];
                    } else {
                        $return[$currentIndex] = $string[$i];
                    }
            }
        }

        return array_map('trim', $return);
    }

    protected function splitArgs($string)
    {
        preg_match_all("/\[(?:[^\[\]]|(?R))+\]|'[^']*'|[^\[\],]+/", $string, $matches);
        return $this->removeEmptyValuesAndRebuildKeys($matches[0]);
    }

    protected function removeEmptyValuesAndRebuildKeys($array)
    {
        return array_values(array_filter(array_map('trim', $array)));
    }
}
