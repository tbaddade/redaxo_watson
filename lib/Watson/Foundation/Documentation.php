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

class Documentation
{
    private $command;
    private $examples;
    private $description;
    private $options;
    private $usage;

    public function __construct($command)
    {
        $this->setCommand($command);
    }

    public function setCommand($value)
    {
        $this->command = $value;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function setExample($example)
    {
        $this->examples[] = $example;
    }

    public function setExamples(array $examples)
    {
        foreach ($examples as $example) {
            $this->setExample($example);
        }
    }

    public function getExamples()
    {
        return $this->examples;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setOption($option, $description)
    {
        $this->options[] = ['option' => $option, 'description' => $description];
    }

    public function setOptions(array $options)
    {
        foreach ($options as $option => $option) {
            $this->setOption($option, $description);
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setUsage($value)
    {
        $this->usage = $value;
    }

    public function getUsage()
    {
        return $this->usage;
    }
}
