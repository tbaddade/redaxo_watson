<?php

$requestIndex = rex_request('index', 'string', '');

$sidebar = '';
$content = '';

$navigation = [
    '' => 'Allgemein',
    'use' => 'Benutzung',
];

$nav = [];
foreach ($navigation as $index => $label) {
    $navAttributes = [
        'href' => rex_url::currentBackendPage(['index' => $index]),
    ];
    if ($index == $requestIndex) {
        $navAttributes['class'][] = 'active';
    }
    if (strpos($index, '/') !== false) {
        $navAttributes['class'][] = 'is-plugin';
    }
    $nav[] = '<a'.rex_string::buildAttributes($navAttributes).'>'.$label.'</a>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('watson_cheatsheet_docs_title'));
$fragment->setVar('body', '<nav class="cheatsheet-docs-navigation"><ul><li>'.implode('</li><li>', $nav).'</li></ul></nav>', false);
$sidebar = $fragment->parse('core/page/section.php');

if ($requestIndex == 'use') {
    $body = '
        <blockquote>
        <b>Hinweis: </b> Diese Beschreibung ist veraltet und gilt für die REDAXO 4.x Version
        </blockquote>
        
        <hr />
        
        <h3>Watson</h3>

        <ul>
            <li>
                <strong>öffnen</strong>

                <ul>
                    <li>ctrl + space <br /><small>(um im Firefox das Contextmenü zu vermeiden, "ctrl + alt + space" drücken)</small></li>
                    <li>ctrl + alt + space</li>
                    <li>ctrl + cmd + space</li>
                </ul>
            </li>
            <li>
                <strong>schließen</strong>

                <ul>
                    <li>ESC</li>
                    <li>ctrl + space</li>
                    <li>ctrl + alt + space</li>
                    <li>ctrl + cmd + space</li>
                </ul>
            </li>
        </ul><h3>Quick look</h3>

        <ul>
            <li>
                <strong>öffnen</strong>

                <ul>
                    <li>Cursortaste rechts</li>
                </ul>
            </li>
            <li>
                <strong>schließen</strong>

                <ul>
                    <li>Cursortaste links</li>
                    <li>Cursortaste oben</li>
                    <li>Cursortaste unten</li>
                </ul>
            </li>
        </ul><h3>Keywords</h3>

        <p>Sind Keywords registriert, wird die Suche entsprechend eingegrenzt.<br></p>

        <dl><dt><code>t text</code></dt><dd><strong>t</strong> grenzt die Suche auf Templates ein</dd></dl>

        <h4>Add-Modus</h4>

        <p>Gibt man ein Keyword und nachfolgend ein <strong>+</strong> ein, gelangt man in den Add-Modus (hinzufügen/anlegen) der angegebenen Url.<br></p>

        <dl><dt><code>t+ Neues Template</code></dt><dd><strong>t+</strong> wird ein neues Template mit dem Namen <b>Neues Template</b> anlegen</dd></dl>

        <h3>Kommandos</h3>

        <p>Ein Kommando ist ein Keyword ohne weitere Texteingabe und löst bei <strong>enter</strong> eine Aktion aus.<br></p>

        <dl><dt><code>logout</code></dt><dd>hierdurch wird man vom REDAXO Backend ausgeloggt</dd></dl>

        <hr />

        <h2>Plugins</h2>

        <h3>watson_core</h3>

        <ul>
            <li>
                <strong>Suchen</strong>

                <ul>
                    <li>
                        <strong>Artikel</strong><br>
                        a+; c+; on; off - um eine(n) Kategorie/Arikel anzulegen, muss man sich in der Struktur befinden</li>
                    <li>
                        <strong>Medien</strong><br>
                        m, m+; f, f+</li>
                    <li>
                        <strong>Module</strong><br>
                        m, m+</li>
                    <li>
                        <strong>Templates</strong><br>
                        t, t+</li>
                    <li>
                        <strong>Benutzer</strong><br>
                        u+</li>
                </ul>
            </li>
            <li>
                <strong>Kommandos</strong>

                <ul>
                    <li>
                        <strong>start</strong><br>
                        zur Startseite im Backend</li>
                    <li>
                        <strong>home</strong><br>
                        zur Startseite im Frontend</li>
                    <li>
                        <strong>logout</strong><br>
                        REDAXO logout</li>
                </ul>
            </li>
        </ul>';

    $fragment = new rex_fragment();
    $fragment->setVar('title', $navigation[$requestIndex]);
    $fragment->setVar('body', $body, false);
    $content .= $fragment->parse('core/page/section.php');
} else {
    $fragment = new rex_fragment();
    $fragment->setVar('title', $navigation[$requestIndex]);
    $fragment->setVar('body', \rex_markdown::factory()->parse(\rex_file::get(\rex_path::addon('watson', 'README.md'))), false);
    $content .= $fragment->parse('core/page/section.php');
}

echo '
<section class="cheatsheet-docs">
    <div class="cheatsheet-docs-sidebar">'.$sidebar.'</div>
    <div class="cheatsheet-docs-content">'.$content.'</div>
</section>';
