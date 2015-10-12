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

class Extension
{

    public static function head(\rex_extension_point $ep)
    {

        $js_properties = json_encode(
                            array(
                                'resultLimit'      => Watson::getResultLimit(), 
                                'agentHotkey'      => Watson::getAgentHotkey(), 
                                'quicklookHotkey'  => Watson::getQuicklookHotkey(), 
                                'backend'          => true, 
                                'backendUrl'       => \rex_url::backendPage('watson', array('watson_query' => ''), false) . '%QUERY', 
                                'wildcard'         => '%QUERY', 
                            )
                        );


        if ($js_properties) {
            $ep->setSubject( $ep->getSubject() . "\n" . '

                <script type="text/javascript">
                    <!--
                    if (typeof($watsonSettings) == "undefined") {
                        var $watsonSettings = ' . $js_properties . ';
                    }
                    //-->
                </script>'
            );
        }

    }



    public static function agent(\rex_extension_point $ep)
    {
        $panel = '';
        $panel .= '
            <div id="watson-agent">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
            </div>';

        $panel .= '<div id="watson-agent-overlay"></div>';


        $ep->setSubject( str_replace('</body>', $panel . '</body>', $ep->getSubject()));

    }



    public static function run(\rex_extension_point $ep)
    {

        $workflows = $ep->getParam('workflows');

        // Phase 2
        // User Eingabe parsen in $input
        $userInput = rex_request('watson_query', 'string');

        if ($userInput != '') {

            $command = new Command($userInput);

            // Eingabe auf Keywords überprüfen
            $saveWorkflows = array();
            foreach($workflows as $workflow) {

                if (in_array($command->getCommand(), $workflow->commands())) {

                    $saveWorkflows[] = $workflow;

                }

            }

            // registriertes Command gefunden
            if (count($saveWorkflows) > 0) {

                $workflows = $saveWorkflows;
                $command->deleteCommandFromCommandParts();

            }

            // Eingabe an vorher registrierten Workflow übergeben und Ergebnisse einsammeln
            $results = array();
            foreach($workflows as $workflow) {

                $results[] = $workflow->fire($command);

            }

            // Ergebnis rendern
            $renderedResults = array();
            foreach ($results as $result) {

                $renderedResults[] = $result->render($userInput);

            }


            $json = array();
            foreach ($renderedResults as $values) {

                foreach ($values as $value) {

                    $json[] = $value;

                }

            }


            if (count($json) == 0) {

                $json[] = array('displayKey' => $userInput, 'value_name' => Watson::translate('watson_no_results'), 'value' => Watson::translate('watson_no_results'), 'tokens' => array(Watson::translate('watson_no_results')));

            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }

    }
}
