<?php

class watson_command
{
    private $command;
    private $url;
    private $url_open_window;
    private $description;



    public function __construct()
    {
    }



    /**
     * Sets a command
     *
     * @param string $command
     * @param string $url
     * @param bool   $open_window
     */
    public function setCommand($command, $url, $open_window = false)
    {
        $this->command          = $command;
        $this->url              = $url;
        $this->url_open_window  = $open_window;
    }

    /**
     * Returns the command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }


    /**
     * Returns the command url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Returns the command url open window
     *
     * @return bool
     */
    public function getUrlOpenWindow()
    {
        return $this->url_open_window;
    }



    /**
     * Sets a description
     *
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->description = $value;
    }

    /**
     * Returns the icon
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns whether a icon is set
     *
     * @return bool
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }
}
