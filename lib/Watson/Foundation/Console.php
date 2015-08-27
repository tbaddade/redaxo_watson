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

use \Watson\Foundation\SearchCommand;
use \Watson\Foundation\Watson;

abstract class Console
{

    private $error;

    private $message;

    private $success;


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




    public function hasError()
    {
        
        return $this->error;
        
    }



    protected function setError($message)
    {
        
        $this->error = true;
        $this->setMessage($message);
        
    }



    public function isSuccess()
    {
        
        return $this->success;
        
    }



    protected function setSuccess($message)
    {
        
        $this->success = true;
        $this->setMessage($message);
        
    }



    public function getMessage()
    {
        
        return $this->message;
        
    }



    protected function setMessage($message)
    {
        
        return $this->message = $message;
        
    }

}
