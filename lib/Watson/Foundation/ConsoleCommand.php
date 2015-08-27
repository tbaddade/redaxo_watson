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

class ConsoleCommand extends Command
{


    private $json_response_id;



    public function __construct()
    {

        $input = file_get_contents('php://input');

        $command = $this->handleInput($input);
        
        parent::__construct($command);

    }



    public function getClearCommand()
    {

        return substr($this->getCommand(), strpos($this->getCommand(), ':') + 1);
        
    }


    protected function isJson($string)
    {

        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);

    }


    protected function handleInput($input)
    {
        
        if ($this->isJson($input)) {

            $input = json_decode($input, true);

            if (isset($input['id'])) {

                $this->json_response_id = $input['id'];

            }


            $command = '';
            if (isset($input['method'])) {
                $command .= $input['method'];

                if (isset($input['params'])) {
                    $command .= ' ' . implode(' ', $input['params']);
                }

                return $command;
            }

        } else {

            return $input;

        }

    }



    // Create json-rpc response
    public function getJsonResponse($result, $error) 
    {

        if ($error) {
            $error['name'] = 'JSONRPCError';
        }

        return json_encode(
                    array(
                        'jsonrpc' => '2.0',
                        'result'  => $result,
                        'id'      => $this->json_response_id,
                        'error'   => $error
                    )
                );
    }


    // Create raw response
    protected function getRawResponse($result, $error)
    {

        $allowable_tags = '<a><br><table><tr><th><td><div>';

        $return = '';
        if ($error) {
            $return .= 'ERROR: ' . strip_tags( 
                                        str_replace(
                                            "\t", 
                                            str_repeat('&nbsp;', 4), 
                                            nl2br($error)
                                        ), 
                                        $allowable_tags
                                    );
        }

        if ($result) {
            $return .= strip_tags( 
                            str_replace(
                                "\t", 
                                str_repeat('&nbsp;', 4), 
                                nl2br($result)
                            ), 
                            $allowable_tags
                        );       
        }

        return $return;
    }


    public function response($result, $error, $raw = true)
    {
        if ($raw) {
            
            return $this->getRawResponse($result, $error);

        }

        return $this->getJsonResponse($result, $error);

    }


    public function getCompletion($instances)
    {
        if (count($this->getArguments()) == 1) {

            $registered_commands = array();
            foreach($instances as $instance) {

                foreach ($instance->commands() as $command) {
                    $registered_commands[] = $command . ' ';
                }

            }
            
            return $this->response($registered_commands, null, false);

        }

        return false;
    }


    public function getDocumentation($instance)
    {
        $documentations = array();

        if (is_array($instance->documentation())) {

            foreach ($instance->documentation() as $documentation) {

                $documentations[$documentation->getCommand()] = $documentation;

            }

        } else {

            $documentations[$instance->documentation()->getCommand()] = $instance->documentation();

        }


        if (isset($documentations[$this->getCommand()])) {

            $documentation = $documentations[$this->getCommand()];
            

            $lines = array();

            $lines[] = 'COMMAND';
            $lines[] = "\t" . $documentation->getCommand();
            $lines[] = '';

            if ($documentation->getUsage()) {

                $lines[] = 'SYNOPSIS';
                $lines[] = "\t" . $documentation->getUsage();
                $lines[] = '';

            }

            if ($documentation->getDescription()) {

                $lines[] = 'DESCRIPTION';
                $lines[] = "\t" . $documentation->getDescription();
                $lines[] = '';

            }

            if (count($documentation->getExamples())) {

                $lines[] = count($documentation->getExamples()) == 1 ? 'EXAMPLE' :  'EXAMPLES';

                foreach ($documentation->getExamples() as $example) {
                
                    $lines[] = "\t" . $example;

                }

            }

            
            return $this->response('<div class="documentation">' . implode("\n", $lines) . '</div>', null);

        }

           
        return $this->response(null, 'Keine Dokumentation gefunden.');
        

    }



    public function getDocumentationList($instances)
    {

        $documentations = array();
        foreach($instances as $instance) {

            if (is_array($instance->documentation())) {

                foreach ($instance->documentation() as $documentation) {

                    $documentations[ $documentation->getCommand() ] = $documentation;

                }

            } else {

                $documentations[ $instance->documentation()->getCommand() ] = $instance->documentation;

            }

        }
        

        
        if (count($documentations)) {
            
            ksort($documentations);

            $rows = array();

            foreach ($documentations as $documentation) {

                $row = '';
                $row .= '<th>' . $documentation->getCommand() . '</th>';
                $row .= '<td>' . $documentation->getUsage() . '</td>';
                $row .= '<td>' . $documentation->getDescription() . '</td>';

                $rows[] = '<tr>' . $row . '</tr>';
            }


            return $this->response('<table>' . implode('', $rows) . '</table>', null);
        }

        return false;
    }
}
