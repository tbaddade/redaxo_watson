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

class Command
{
    private $command;
    private $command_parts;
    private $input;
    private $arguments = [];
    private $options = [];

    public function __construct($input)
    {
        $this->input = stripslashes(urldecode($input));
        $this->command_parts = $this->split($this->input);

        $this->parseInput();
    }

    public function parseInput()
    {
        $command_parts = $this->command_parts;

        if (isset($command_parts['0'])) {
            $this->command = $command_parts['0'];
            unset($command_parts[0]);
        }

        foreach ($command_parts as $name => $value) {
            if (0 === strpos($name, '--')) {
                // Check for an option notation.
                // Notation: --option="my option"

                $name = substr($name, 2);

                if (empty($name)) {
                    throw new \Exception('An option name cannot be empty.');
                }

                $this->options[$name] = $value;
                unset($command_parts['--'.$name]);
            } elseif (is_numeric($name) && 0 === strpos($value, '--')) {
                // Check for an option notation.
                // Notation: --option
                $value = substr($value, 2);

                if (empty($value)) {
                    throw new \Exception('An option name cannot be empty.');
                }

                $this->options[$value] = 1;
                unset($command_parts[$name]);
            }
        }

        $this->arguments = $command_parts;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getCommandParts()
    {
        return $this->command_parts;
    }

    public function getCommandPartsAsString()
    {
        return implode(' ', $this->getCommandParts());
    }

    public function deleteCommandFromCommandParts()
    {
        foreach ($this->command_parts as $key => $command_part) {
            if ($this->command == $command_part) {
                unset($this->command_parts[$key]);
            }
        }
    }

    public function getArgument($position)
    {
        return isset($this->arguments[$position]) ? $this->arguments[$position] : null;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getSqlWhere($fields)
    {
        $where = [];

        foreach ($fields as $field) {
            $w = [];

            foreach ($this->getCommandParts() as $command_part) {
                $w[] = $field.' LIKE "%'.$command_part.'%"';
            }

            $where[] = '('.implode(' AND ', $w).')';
        }

        return implode(' OR ', $where);
    }

    /**
     * Splits a string by spaces
     * (Strings with quotes will be regarded).
     *
     * Examples:
     * "a b 'c d'"   -> array('a', 'b', 'c d')
     * "a=1 b='c d'" -> array('a' => 1, 'b' => 'c d')
     *
     * @param string $string
     *
     * @return array
     */
    protected function split($string)
    {
        $string = trim($string);
        if (empty($string)) {
            return [];
        }
        $result = [];
        $spacer = '@@@WATSON@@@';
        $quoted = [];

        $pattern = '@(["\'])((?:.*[^\\\\])?(?:\\\\\\\\)*)\\1@Us';
        $callback = function ($match) use ($spacer, &$quoted) {
            $quoted[] = str_replace(['\\'.$match[1], '\\\\'], [$match[1], '\\'], $match[2]);
            return $spacer;
        };
        $string = preg_replace_callback($pattern, $callback, $string);

        $parts = preg_split('@\s+@', $string);
        $i = 0;
        foreach ($parts as $part) {
            $part = explode('=', $part, 2);
            if (isset($part[1])) {
                $value = $part[1] == $spacer ? $quoted[$i++] : $part[1];
                $result[$part[0]] = $value;
            } else {
                $value = $part[0] == $spacer ? $quoted[$i++] : $part[0];
                $result[] = $value;
            }
        }

        return $result;
    }
}
