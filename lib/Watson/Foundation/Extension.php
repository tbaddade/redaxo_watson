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

class Extension
{
    public static function consoleHead($params)
    {

        $js_properties = json_encode(
                            array(
                                'backend'     => true, 
                                'backendUrl'  => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 
                                'interpreter' => Watson::getConsoleInterpreterUrl(), 
                            )
                        );

        if ($js_properties) {
            $params['subject'] .= "\n" . '

                <script type="text/javascript">
                    <!--
                    if (typeof(WatsonConsole) == "undefined") {
                        var WatsonConsole = ' . $js_properties . ';
                    }
                    //-->
                </script>';
        }

        return $params['subject'];

    }



    public static function consoleAgent($params)
    {
        $panel = '';
        $panel .= '<div id="watson-console"></div>';

        $panel .= '<div id="watson-overlay"></div>';


        $params['subject'] = str_replace('</body>', $panel . '</body>', $params['subject']);
        return $params['subject'];

    }



    public static function consoleRun($params)
    {
        //require_once(realpath(__DIR__ . '/../vendor/json-rpc.php'));

        $console_enter = rex_request('watson_console', 'bool', 0);

        if ($console_enter) {

            $console_instances = $params['console_instances'];

            $consoleCommand = new ConsoleCommand();
            

            $response = '';


            if ($consoleCommand) {

                if ($consoleCommand->getCommand() == 'list') {

                    $response = $consoleCommand->getDocumentationList($console_instances);

                } elseif ($consoleCommand->getCommand() == 'watson:consoleCompletion') {

                    $response = $consoleCommand->getCompletion($console_instances);

                } else {
                    
                    $instances = array();

                    foreach($console_instances as $console_instance) {

                        if (in_array($consoleCommand->getCommand(), $console_instance->commands())) {

                            $instances[] = $console_instance;

                        }

                    }


                    if (count($instances) == 1) {

                        $instance = $instances[0];


                        if ($consoleCommand->getArgument(1) == 'help') {

                            $response = $consoleCommand->getDocumentation($instance);

                        } else {
                            
                            $method = $consoleCommand->getClearCommand();

                            $class   = get_class($instance);
                            $methods = get_class_methods($class);

                            if (in_array($method, $methods)) {


                                // Execute the command
                                $return = $instance->$method($consoleCommand);


                                if ($instance->isSuccess()) {

                                    $response = $consoleCommand->response($instance->getMessage(), null);

                                } elseif ($instance->hasError()) {

                                    $response = $consoleCommand->response(null, $instance->getMessage());

                                } else {

                                    $response = $consoleCommand->response('Befehl wurde ausgeführt', null);

                                }


                            } else {

                                $response = $consoleCommand->response(null, 'Befehl gefunden aber keine dazugehörige Methode.');

                            }

                        }
                        

                    } elseif (count($instances) >= 1) {

                        $response = $consoleCommand->response(null, 'Mehrere Klassen mit demselben Befehl gefunden.');

                    } else {

                        $response = $consoleCommand->response(null, 'Keinen Befehl gefunden.');

                    }

                }

            } else {

                $response = $consoleCommand->response(null, 'Befehl nicht gefunden');

            }

            //ob_clean();
            header('Content-type: application/json');
            echo $response;
            exit();

        }
    }




    public static function searchHead(\rex_extension_point $ep)
    {

        $js_properties = json_encode(
                            array(
                                'resultLimit' => Watson::getSearchResultLimit(), 
                                'backend'     => true, 
                                'backendUrl'  => \rex_url::backendPage('watson', array('watson_search' => ''), false) . '%QUERY', 
                            )
                        );


        if ($js_properties) {
            $ep->setSubject( $ep->getSubject() . "\n" . '

                <script type="text/javascript">
                    <!--
                    if (typeof(WatsonSearch) == "undefined") {
                        var WatsonSearch = ' . $js_properties . ';
                    }
                    //-->
                </script>'
            );
        }

    }



    public static function searchAgent(\rex_extension_point $ep)
    {
        $panel = '';
        $panel .= '
            <div id="watson-searcher">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
                <span class="watson-searcher-help-open"></span>
            </div>';

        $panel .= '
            <div id="watson-searcher-help" class="watson-searcher-help">
                <h1>' . Watson::translate('b_watson_title'). '</h1>
                <span class="watson-searcher-help-close"></span>
            </div>';

        $panel .= '<div id="watson-overlay"></div>';


        $ep->setSubject( str_replace('</body>', $panel . '</body>', $ep->getSubject()));

    }



    public static function searchRun(\rex_extension_point $ep)
    {
        //global $REX, $I18N;

        $searchers = $ep->getParam('searchers');

        // registrierte Page Params speichern
        //watson::saveRegisteredPageParams($searchers);
        //watson::setPageRequest();


        // Phase 2
        // User Eingabe parsen in $input
        $userInput = rex_request('watson_search', 'string');

        if ($userInput != '') {

            $searchCommand = new SearchCommand($userInput);

            // Eingabe auf Keywords überprüfen
            $saveSearchers = array();
            foreach($searchers as $searcher) {

                if (in_array($searchCommand->getCommand(), $searcher->commands())) {

                    $saveSearchers[] = $searcher;

                }

            }

            // registriertes Command gefunden
            if (count($saveSearchers) > 0) {

                $searchers = $saveSearchers;
                $searchCommand->deleteCommandFromCommandParts();

            }

            // Eingabe an vorher registrierte Search übergeben und Ergebnisse einsammeln
            /** @var $searchResults SearchResult */
            $searchResults = array();
            foreach($searchers as $searcher) {

                $searchResults[] = $searcher->fire($searchCommand);

            }

            // Ergebnis rendern
            $renderedResults = array();
            foreach ($searchResults as $searchResult) {

                $renderedResults[] = $searchResult->render($userInput);

            }


            $json = array();
            foreach ($renderedResults as $values) {

                foreach ($values as $value) {

                    $json[] = $value;

                }

            }


            if (count($json) == 0) {

                $json[] = array('value_name' => $I18N->msg('b_no_results'), 'value' => Watson::translate('b_no_results'), 'tokens' => array(Watson::translate('b_no_results')));

            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }

    }


    public static function agents($params)
    {
        global $REX, $I18N;
        
        $panel = '';
        /**
        * Watson Searcher
        */
        $panel .= '
            <div id="watson-searcher">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
                <span class="watson-legend-open"></span>
            </div>';

        /**
        * Watson Console
        */
        $panel .= '
            <div id="watson-console">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
                <span class="watson-legend-open"></span>
            </div>';

        /**
        * Watson Terminal
        */
        $panel .= '
            <div id="watson-terminal"></div>';
        
        /**
        * Watson Legend
        */
        $panel .= '
            <div id="watson-searcher-legend" class="watson-legend">
                <h1>' . $I18N->msg('b_watson_title'). '</h1>
                <span class="watson-legend-close"></span>
                <!-- WATSON_LEGEND //-->
            </div>';

        $panel .= '
            <div id="watson-console-legend" class="watson-legend">
                <h1>' . $I18N->msg('b_watson_title'). '</h1>
                <span class="watson-legend-close"></span>
                <!-- WATSON_CONSOLE //-->
            </div>';
        
        /**
        * Watson save the request
        */
        $panel .= '<div id="watson-request">' . watson::getRegisteredPageRequestAsJson() . '</div>';
        
        /**
        * Watson Overlay
        */
        $panel .= '<div id="watson-overlay"></div>';


        $params['subject'] = str_replace('</body>', $panel . '</body>', $params['subject']);
        return $params['subject'];
    }


    public static function legend($params)
    {
        if (isset($params['replace'])) {
            $params['subject'] = str_replace($params['replace'], $params['html'] . $params['replace'], $params['subject']);
        }
        return $params['subject'];
    }


    public static function searcher()
    {
        global $REX, $I18N;

        // Phase 1
        /** @var $searcher watson_searcher[] */
        $searchers = array();
        $get_searchers = rex_register_extension_point('WATSON_SEARCHER');
        if (isset($get_searchers['searchers'])) {
            $searchers = $get_searchers['searchers'];
        }

        // registrierte Page Params speichern
        watson::saveRegisteredPageParams($searchers);
        watson::setPageRequest();


        // Phase 2
        // Legenden holen
        if (is_array($searchers) && count($searchers) > 0) {
            $legends = array();
            foreach($searchers as $searcher) {
                $legend = $searcher->legend();
                if ($legend instanceof watson_legend) {
                    $legends[] = $legend->get();
                }
            }

            $html = implode('', $legends);
            $html = '   <table class="watson-legend-data">
                            <thead>
                            <tr>
                                <th class="watson-legend-title">' . $I18N->msg('b_title'). '</th>
                                <th class="watson-legend-keyword">' . $I18N->msg('b_keyword'). '</th>
                                <th class="watson-legend-search">' . $I18N->msg('b_search'). '</th>
                                <th class="watson-legend-add">' . $I18N->msg('b_add'). '</th>
                                <th class="watson-legend-description">' . $I18N->msg('b_description'). '</th>
                            </tr>
                            </thead>
                            <tbody>
                            ' . $html . '
                            </tbody>
                        </table>';
            $params = array('html' => $html, 'replace' => '<!-- WATSON_LEGEND //-->');

            rex_register_extension('OUTPUT_FILTER', 'watson_extensions::legend', $params);
        }


        // Phase 2
        // User Eingabe parsen in $input
        $input = rex_request('watson', 'string');
        if ($input != '' && is_array($searchers) && count($searchers) > 0) {

            $watson_search_term = new watson_search_term($input);

            // Eingabe auf Keywords überprüfen
            $save_searchers = array();
            foreach($searchers as $searcher) {
                if (in_array($watson_search_term->getKeyword(), $searcher->keywords())) {
                    $save_searchers[] = $searcher;
                }
            }

            // registriertes Keyword gefunden
            if (count($save_searchers) > 0) {
                $searchers = $save_searchers;
                $watson_search_term->deleteKeywordFromTerms();
            }

            // Eingabe an vorher registrierte Search übergeben und Ergebnisse einsammeln
            /** @var $search_results watson_search_result[] */
            $search_results = array();
            foreach($searchers as $searcher) {
                $search_results[] = $searcher->search($watson_search_term);
            }

            // Ergebnis rendern
            $results = array();
            foreach ($search_results as $search_result) {
                // render json/html whatever
                $results[] = $search_result->render();
            }

            $json = array();
            foreach ($results as $values) {
                foreach ($values as $value) {
                    $json[] = $value;
                }
            }


            if (count($json) == 0) {
                $json[] = array('value_name' => $I18N->msg('b_no_results'), 'value' => $I18N->msg('b_no_results'), 'tokens' => array($I18N->msg('b_no_results')));
            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }
    }


    public static function console()
    {
        global $REX, $I18N;

        // Phase 1
        /** @var $searcher watson_searcher[] */
        $console_commands = rex_register_extension_point('WATSON_CONSOLE');

        // registrierte Page Params speichern
        watson::saveRegisteredPageParams($console_commands);
        watson::setPageRequest();
        

        // Phase 2
        // User Eingabe parsen in $input
        $input        = rex_request('watson_console', 'string');

        if ($input != '' && is_array($console_commands) && count($console_commands) > 0) {

            $watson_console_command = new watson_console_command($input);

            // Eingabe auf Keywords überprüfen
            $save_console_commands = array();
            foreach($console_commands as $console_command) {
                if (in_array($watson_console_command->getKeyword(), $console_command->keywords())) {
                    $save_console_commands[] = $console_command;
                }
            }

            /**
             * Unterschied zum Searcher
             * Trifft kein Keyword zu, geht es nicht weiter
            */
            $console_commands = $save_console_commands;

            // Eingabe an vorher registriertes Kommando übergeben und Ergebnisse einsammeln
            /** @var $search_results watson_search_result[] */
            $console_command_results = array();
            foreach($console_commands as $console_command) {
                $console_command_results[] = $console_command->run($watson_console_command);
            }

            // Ergebnis rendern
            $results = array();
            foreach ($console_command_results as $console_command_result) {
                // render json/html whatever
                $results[] = $console_command_result->render();
            }

            $json = array();
            foreach ($results as $values) {
                foreach ($values as $value) {
                    $json[] = $value;
                }
            }


            if (count($json) == 0) {
                $json[] = array('value_name' => $I18N->msg('b_no_results'), 'value' => $I18N->msg('b_no_results'), 'tokens' => array($I18N->msg('b_no_results')));
            }

                $json[] = array('value_name' => 'Test', 'value' => 'Test Value', 'tokens' => array('Test Token'));

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }
    }


    public static function terminal()
    {
        global $REX, $I18N;

        // Phase 1
        /** @var $commands watson_terminal[] */
        $commands = rex_register_extension_point('WATSON_TERMINAL');
        $commands = $commands['terminal'];

        $terminal_input = rex_request('watson_terminal', 'bool');
        if ($terminal_input && is_array($commands) && count($commands) > 0) {
            handle_json_rpc($commands);
        }
    }


    public static function page_header($params)
    {
        global $REX;
        $myaddon = 'watson';

        $css_files      = $params['css'];
        $js_files       = $params['js'];
        $js_properties  = json_encode(array('resultLimit' => watson::getResultLimit(), 'backend' => true, 'backendUrl' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        $addon_assets = '../' . $REX['MEDIA_ADDON_DIR'] . '/' . $myaddon . '/';

        foreach ($css_files as $media => $files) {
            foreach ($files as $file) {
                $params['subject'] .= "\n" . '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $addon_assets . $file . '" />';
            }
        }

        if ($js_properties) {
            $params['subject'] .= "\n" . '

                    <script type="text/javascript">
                        <!--
                        if (typeof(watson) == "undefined") {
                            var watson = ' . $js_properties . ';
                            var isMac  = navigator.platform.toUpperCase().indexOf(\'MAC\')>=0;
                        }
                        //-->
                    </script>';
        }

        foreach ($js_files as $file) {
            $params['subject'] .= "\n" . '<script type="text/javascript" src="' . $addon_assets . $file . '"></script>';
        }
/*
echo '<pre style="color: #fff; text-align: left">';
print_r($REX);
echo '</pre>';
echo 'Article' . $REX['ARTICLE_ID'];
echo 'Article' . REX_CATEGORY_ID;
*/
        return $params['subject'];
    }



    public static function call_user_func($params)
    {
        $call_class  = urldecode(rex_request('watson_call_class', 'string'));
        $call_method = urldecode(rex_request('watson_call_method', 'string'));
        $call_params = urldecode(rex_request('watson_call_params', 'string'));

        if ($call_class != '' && $call_method != '') {
            call_user_func_array(array($call_class, $call_method), array($call_params));
        }
    }
}
