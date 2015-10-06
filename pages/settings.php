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

use \Watson\Foundation\Watson;

$content = '';

$func = rex_request('func', 'string');
$searchResultLimit = rex_request('search_result_limit', 'int');
$searchAgentHotkey = rex_request('search_agent_hotkey', 'string');
$searchQuicklookHotkey = rex_request('search_quicklook_hotkey', 'string');

if ($func == 'update') {
    echo \rex_view::info($this->i18n('config_saved'));
    \rex_config::set('watson', 'search_result_limit', $searchResultLimit);
    \rex_config::set('watson', 'search_agent_hotkey', $searchAgentHotkey);
    \rex_config::set('watson', 'search_quicklook_hotkey', $searchQuicklookHotkey);
}

$content .= '
    <fieldset>
        <input type="hidden" name="func" value="update" />
';

        $formElements = [];
        $n = [];
        $n['label'] = '<label for="watson-search-result-limit">' . $this->i18n('search_result_limit') . '</label>';
        $n['field'] = '<input class="form-control" type="text" id="watson-search-result-limit" name="search_result_limit" value="' . htmlspecialchars(Watson::getSearchResultLimit()) . '" />';
        $formElements[] = $n;

        
        $agentHotkeysSelect = new \rex_select();
        $agentHotkeysSelect->setName('search_agent_hotkey');
        $agentHotkeysSelect->setSize(1);
        $agentHotkeysSelect->setAttribute('class', 'form-control');
        $agentHotkeysSelect->setAttribute('id', 'watson-search-agent-hotkey');
        foreach (Watson::getSearchAgentHotkeys() as $hotkeyValue => $hotkeyLabel) {
            $agentHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
        }
        $agentHotkeysSelect->setSelected(Watson::getSearchAgentHotkey());

        $n = [];
        $n['label'] = '<label for="watson-search-agent-hotkey">' . $this->i18n('search_agent_hotkey') . '</label>';
        $n['field'] = $agentHotkeysSelect->get();
        $formElements[] = $n;

        
        $quicklookHotkeysSelect = new \rex_select();
        $quicklookHotkeysSelect->setName('search_quicklook_hotkey');
        $quicklookHotkeysSelect->setSize(1);
        $quicklookHotkeysSelect->setAttribute('class', 'form-control');
        $quicklookHotkeysSelect->setAttribute('id', 'watson-search-quicklook-hotkey');
        foreach (Watson::getSearchQuicklookHotkeys() as $hotkeyValue => $hotkeyLabel) {
            $quicklookHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
        }
        $quicklookHotkeysSelect->setSelected(Watson::getSearchQuicklookHotkey());

        $n = [];
        $n['label'] = '<label for="watson-search-quicklook-hotkey">' . $this->i18n('search_quicklook_hotkey') . '</label>';
        $n['field'] = $quicklookHotkeysSelect->get();
        $formElements[] = $n;



        $fragment = new \rex_fragment();
        $fragment->setVar('flush', true);
        $fragment->setVar('elements', $formElements, false);
        $content .= $fragment->parse('core/form/form.php');

        $formElements = [];

        $n = [];
        $n['field'] = '<a class="btn btn-abort" href="' . \rex_url::currentBackendPage() . '"><i class="rex-icon rex-icon-back"></i> ' . \rex_i18n::msg('form_abort') . '</a>';
        $formElements[] = $n;

        $n = [];
        $n['field'] = '<button class="btn btn-apply" type="submit" name="send" value="1"' . \rex::getAccesskey(\rex_i18n::msg('update'), 'apply') . '>' . \rex_i18n::msg('update') . '</button>';
        $formElements[] = $n;

        $fragment = new \rex_fragment();
        $fragment->setVar('elements', $formElements, false);
        $buttons = $fragment->parse('core/form/submit.php');

$content .= '
    </fieldset>';

$fragment = new \rex_fragment();
$fragment->setVar('title', $this->i18n('search_config'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
    <form action="' . \rex_url::currentBackendPage() . '" method="post">
        ' . $content . '
    </form>

    ';

echo $content;
