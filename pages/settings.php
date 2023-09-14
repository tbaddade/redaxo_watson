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

use rex;
use rex_addon;
use rex_fragment;
use rex_i18n;
use rex_select;
use rex_url;
use rex_view;

$addon = rex_addon::get('watson');

if ('update' === rex_post('func', 'string', '')) {
    $addon->setConfig(rex_post('config', [
        ['resultLimit', 'int'],
        ['agentHotkey', 'string'],
        ['quicklookHotkey', 'string'],
        ['toggleButton', 'bool'],
    ]));

    echo rex_view::info($addon->i18n('config_saved'));
}

$content = '
    <fieldset>
        <input type="hidden" name="func" value="update" />
';

$formElements = [];
$n = [];
$n['label'] = '<label for="watson-result-limit">'.$addon->i18n('resultLimit').'</label>';
$n['field'] = '<input class="form-control" type="text" id="watson-result-limit" name="config[resultLimit]" value="'.rex_escape(Watson::getResultLimit()).'" />';
$formElements[] = $n;

$agentHotkeysSelect = new rex_select();
$agentHotkeysSelect->setName('config[agentHotkey]');
$agentHotkeysSelect->setSize(1);
$agentHotkeysSelect->setAttribute('class', 'form-control');
$agentHotkeysSelect->setAttribute('id', 'watson-agent-hotkey');
foreach (Watson::getAgentHotkeys() as $hotkeyValue => $hotkeyLabel) {
    $agentHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
}
$agentHotkeysSelect->setSelected(Watson::getAgentHotkey());

$n = [];
$n['label'] = '<label for="watson-agent-hotkey">'.$addon->i18n('agentHotkey').'</label>';
$n['field'] = $agentHotkeysSelect->get();
$formElements[] = $n;

$quicklookHotkeysSelect = new rex_select();
$quicklookHotkeysSelect->setName('config[quicklookHotkey]');
$quicklookHotkeysSelect->setSize(1);
$quicklookHotkeysSelect->setAttribute('class', 'form-control');
$quicklookHotkeysSelect->setAttribute('id', 'watson-quicklook-hotkey');
foreach (Watson::getQuicklookHotkeys() as $hotkeyValue => $hotkeyLabel) {
    $quicklookHotkeysSelect->addOption($hotkeyLabel, $hotkeyValue);
}
$quicklookHotkeysSelect->setSelected(Watson::getQuicklookHotkey());

$n = [];
$n['label'] = '<label for="watson-quicklook-hotkey">'.$addon->i18n('quicklookHotkey').'</label>';
$n['field'] = $quicklookHotkeysSelect->get();
$formElements[] = $n;

$toggleButtonSelect = new rex_select();
$toggleButtonSelect->setName('config[toggleButton]');
$toggleButtonSelect->setSize(1);
$toggleButtonSelect->setAttribute('class', 'form-control');
$toggleButtonSelect->setAttribute('id', 'watson-toggle-button');
$toggleButtonSelect->addOption($addon->i18n('no'), 0);
$toggleButtonSelect->addOption($addon->i18n('yes'), 1);
$toggleButtonSelect->setSelected(Watson::showToggleButton());

$n = [];
$n['label'] = '<label for="watson-toggle-button">'.$addon->i18n('showToggleButton').'</label>';
$n['field'] = $toggleButtonSelect->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$formElements = [];

$n = [];
$n['field'] = '<button class="btn btn-apply rex-form-aligned" type="submit" name="send" value="1"'.rex::getAccesskey(rex_i18n::msg('update'), 'apply').'>'.rex_i18n::msg('update').'</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$content .= '</fieldset>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $addon->i18n('settings'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

echo '
    <form action="'.rex_url::currentBackendPage().'" method="post">
        '.$content.'
    </form>';
