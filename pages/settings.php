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

$content = '';

$func = rex_request('func', 'string');
$resultLimit = rex_request('resultLimit', 'int');
$agentHotkey = rex_request('agentHotkey', 'string');
$quicklookHotkey = rex_request('quicklookHotkey', 'string');
$toggleButton = rex_request('toggleButton', 'bool');

if ($func == 'update') {
    echo \rex_view::info($this->i18n('config_saved'));
    \rex_config::set('watson', 'resultLimit', $resultLimit);
    \rex_config::set('watson', 'agentHotkey', $agentHotkey);
    \rex_config::set('watson', 'quicklookHotkey', $quicklookHotkey);
    \rex_config::set('watson', 'toggleButton', $toggleButton);
}

$content .= '
    <fieldset>
        <input type="hidden" name="func" value="update" />
';

        $formElements = [];
        $n = [];
        $n['label'] = '<label for="watson-result-limit">'.$this->i18n('resultLimit').'</label>';
        $n['field'] = '<input class="form-control" type="text" id="watson-result-limit" name="resultLimit" value="'.htmlspecialchars(Watson::getResultLimit()).'" />';
        $formElements[] = $n;

        $agentHotkeysSelect = new \rex_select();
        $agentHotkeysSelect->setName('agentHotkey');
        $agentHotkeysSelect->setSize(1);
        $agentHotkeysSelect->setAttribute('class', 'form-control');
        $agentHotkeysSelect->setAttribute('id', 'watson-agent-hotkey');
        foreach (Watson::getAgentHotkeys() as $hotkeyValue => $hotkeyLabel) {
            $agentHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
        }
        $agentHotkeysSelect->setSelected(Watson::getAgentHotkey());

        $n = [];
        $n['label'] = '<label for="watson-agent-hotkey">'.$this->i18n('agentHotkey').'</label>';
        $n['field'] = $agentHotkeysSelect->get();
        $formElements[] = $n;

        $quicklookHotkeysSelect = new \rex_select();
        $quicklookHotkeysSelect->setName('quicklookHotkey');
        $quicklookHotkeysSelect->setSize(1);
        $quicklookHotkeysSelect->setAttribute('class', 'form-control');
        $quicklookHotkeysSelect->setAttribute('id', 'watson-quicklook-hotkey');
        foreach (Watson::getQuicklookHotkeys() as $hotkeyValue => $hotkeyLabel) {
            $quicklookHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
        }
        $quicklookHotkeysSelect->setSelected(Watson::getQuicklookHotkey());

        $n = [];
        $n['label'] = '<label for="watson-quicklook-hotkey">'.$this->i18n('quicklookHotkey').'</label>';
        $n['field'] = $quicklookHotkeysSelect->get();
        $formElements[] = $n;

        $toggleButtonSelect = new \rex_select();
        $toggleButtonSelect->setName('toggleButton');
        $toggleButtonSelect->setSize(1);
        $toggleButtonSelect->setAttribute('class', 'form-control');
        $toggleButtonSelect->setAttribute('id', 'watson-toggle-button');
        $toggleButtonSelect->addOption($this->i18n('no'), 0);
        $toggleButtonSelect->addOption($this->i18n('yes'), 1);
        $toggleButtonSelect->setSelected(Watson::getToggleButtonStatus());

        $n = [];
        $n['label'] = '<label for="watson-toggle-button">'.$this->i18n('showToggleButton').'</label>';
        $n['field'] = $toggleButtonSelect->get();
        $formElements[] = $n;

        $fragment = new \rex_fragment();
        $fragment->setVar('flush', true);
        $fragment->setVar('elements', $formElements, false);
        $content .= $fragment->parse('core/form/form.php');

        $formElements = [];

        $n = [];
        $n['field'] = '<a class="btn btn-abort" href="'.\rex_url::currentBackendPage().'">'.\rex_i18n::msg('form_abort').'</a>';
        $formElements[] = $n;

        $n = [];
        $n['field'] = '<button class="btn btn-apply rex-form-aligned" type="submit" name="send" value="1"'.\rex::getAccesskey(\rex_i18n::msg('update'), 'apply').'>'.\rex_i18n::msg('update').'</button>';
        $formElements[] = $n;

        $fragment = new \rex_fragment();
        $fragment->setVar('elements', $formElements, false);
        $buttons = $fragment->parse('core/form/submit.php');

$content .= '
    </fieldset>';

$fragment = new \rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('settings'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
    <form action="'.\rex_url::currentBackendPage().'" method="post">
        '.$content.'
    </form>

    ';

echo $content;
