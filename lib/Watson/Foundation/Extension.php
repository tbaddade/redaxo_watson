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

use rex_extension_point;
use rex_response;
use rex_url;

class Extension
{
    public static function head(rex_extension_point $ep): void
    {
        $jsProperties = json_encode(
            [
                'resultLimit' => Watson::getResultLimit(),
                'agentHotkey' => Watson::getAgentHotkey(),
                'quicklookHotkey' => Watson::getQuicklookHotkey(),
                'backend' => true,
                'backendUrl' => rex_url::backendPage('watson', [], false),
                'backendRemoteUrl' => rex_url::backendPage('watson', ['watson_query' => ''], false).'%QUERY',
                'wildcard' => '%QUERY',
            ]
        );

        $ep->setSubject($ep->getSubject()."\n".'
            <script type="text/javascript" nonce="'.rex_response::getNonce().'">
                if (typeof(watsonSettings) === "undefined") {
                    let watsonSettings = '.$jsProperties.';
                }
            </script>'
        );
    }

    public static function navigation(rex_extension_point $ep)
    {
        $icon = Watson::getIcon();
        $icon = str_replace('<svg ', '<svg style="fill: currentColor;" ', $icon);
        $ep->setSubject(
            str_replace(
                '<i class="watson-navigation-icon"></i>',
                '<span class="watson-navigation-icon" style="display: inline-block; width: 20px; height: 20px; margin-left: -28px; margin-right: 3px; vertical-align: top;">'.$icon.'</span>',
                $ep->getSubject()
            )
        );
    }

    public static function toggleButton(rex_extension_point $ep)
    {
        $subject = $ep->getSubject();
        array_unshift($subject, '<li><button class="watson-btn" data-watson-toggle="agent">'.Watson::getIcon().'</button></li>');

        $ep->setSubject($subject);
    }

    public static function agent(rex_extension_point $ep)
    {

        $panel = '<watson-agent value="Test"></watson-agent>';
        $ep->setSubject(str_replace('<header class="rex-page-header">', $panel.'<header class="rex-page-header">', $ep->getSubject()));

        $panel = '
            <div id="watson-agent">
                <form action="">
                    <fieldset>
                        <input class="typeahead" type="text" name="q" value="" />
                    </fieldset>
                </form>
                <div class="watson-logo">'.\rex_file::get(\rex_path::addonAssets('watson', 'watson-logo.svg')).'</div>
            </div>';

        $panel .= '<div id="watson-agent-overlay"></div>';

        $ep->setSubject(str_replace('</body>', $panel.'</body>', $ep->getSubject()));
    }

    public static function run(\rex_extension_point $ep)
    {
        /** @var Workflow[] $workflows */
        $workflows = $ep->getParam('workflows');

        if (rex_request('watson_query', 'string', '') == '') {
            Watson::deleteRegisteredPageParams();
            foreach ($workflows as $workflow) {
                if ($workflow->registerPageParams()) {
                    Watson::saveRegisteredPageParams($workflow->registerPageParams());
                }
            }
        }
        // Phase 2
        // User Eingabe parsen in $input
        $userInput = rex_request('watson_query', 'string');

        if ($userInput != '') {
            $command = new Command($userInput);

            // Eingabe auf Keywords überprüfen
            $saveWorkflows = [];
            foreach ($workflows as $key => $workflow) {
                if ($workflow instanceof \Watson\Foundation\GeneratorWorkflow) {
                    if (in_array($command->getCommand(), $workflow->commands())) {
                        $saveWorkflows = [$workflow];
                        break;
                    }
                    unset($workflows[$key]); // Workflow aus dem Sammelarray löschen. Wird ansonsten verwendet, wenn kein Commando gefunden wurde
                } elseif (in_array($command->getCommand(), $workflow->commands()) && count($command->getCommandParts()) >= 2) {
                    // getCommandParts enthält hier noch das Command, sodass mind. 2 Parts enthalten sein müssen. Ansonsten whoops, da keine Suchphrase vorhanden ist
                    $saveWorkflows[] = $workflow;
                }
            }

            // registriertes Command gefunden
            if (count($saveWorkflows) > 0) {
                $workflows = $saveWorkflows;
                $command->deleteCommandFromCommandParts();
            }

            // Eingabe an vorher registrierten Workflow übergeben und Ergebnisse einsammeln
            $results = [];
            foreach ($workflows as $workflow) {
                $results[] = $workflow->fire($command);
            }

            // Ergebnis rendern
            $renderedResults = [];
            foreach ($results as $result) {
                $renderedResults[] = $result->render($userInput);
            }

            $json = [];
            foreach ($renderedResults as $values) {
                foreach ($values as $value) {
                    $json[] = $value;
                }
            }

            if (count($json) == 0) {
                $json[] = ['displayKey' => $userInput, 'value_name' => Watson::translate('watson_no_results'), 'value' => Watson::translate('watson_no_results'), 'tokens' => [Watson::translate('watson_no_results')]];
            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }
    }

    public static function callWatsonFunc(\rex_extension_point $ep)
    {
        $callClass = urldecode(rex_request('watsonCallClass', 'string'));
        $callMethod = urldecode(rex_request('watsonCallMethod', 'string'));
        $callParams = urldecode(rex_request('watsonCallParams', 'string'));
        if ($callClass != '' && $callMethod != '') {
            call_user_func_array([$callClass, $callMethod], [json_decode($callParams, true)]);
        }
    }
}
