
Watson
================================================================================
### Inhalt
1. [Beschreibung](#beschreibung)
1. [Voraussetzungen](#voraussetzungen)
1. [Installation](#installation)
1. [Benutzung](#benutzung)
1. [Workflows](#workflows)
1. [Bugtracker](#bugtracker)
1. [Changelog](#changelog)
1. [Lizenz](#lizenz)
1. [Autor](#autor)


### Beschreibung

Ein Suchagent für REDAXO 5+

Watson spart Zeit bei der Suche nach Artikeln, Modulen, Templates, Benutzer, Dateien und YForm Daten und … im REDAXO Backend.

Eine ausführlichere Beschreibung und die Benutzung findet man auf [tbaddade.github.io/redaxo_watson/](http://tbaddade.github.io/redaxo_watson/)



### Voraussetzungen

* REDAXO 5.4



### Installation

Im REDAXO via Backend ...

1. über den Installer die letzte Version vom Watson herunterladen
1. AddOn installieren und aktivieren



### Benutzung

#### Watson

* **öffnen**
    * ctrl + space <br /><small>(um im Firefox das Contextmenü zu vermeiden, "ctrl + alt + space" drücken)</small>
    * ctrl + alt + space
    * ctrl + cmd + space
* **schließen**
    * ESC
    * ctrl + space
    * ctrl + alt + space
    * ctrl + cmd + space

#### Quick look

* **öffnen**
    * Cursortaste rechts
* **schließen**
    * Cursortaste links
    * Cursortaste oben
    * Cursortaste unten

### Workflows

#### Suchen

Werden Keywords verwendet, wird die Suche entsprechend eingegrenzt.

| Keyword | Suche in  | wird ohne Keyword durchsucht |
| ------- | --------- | ---------------------------- |
| a       | Artikel   | ja                           |
| m, f    | Medien    | ja                           |
| m       | Module    | ja                           |
| sp      | Sprog     | ja                           |
| t       | Templates | ja                           |
| yf      | YForm     | ja                           |


**Spezielle Suchen**

| Keyword             | Suche in                                                       | Beispiel     | Aktion nach Enter auf Ergebnis | 
| ------------------- | -------------------------------------------------------------- | ------------ | ------------------------------ |
| m:inuse [Module ID] | Artikel nach verwendeten Module                                | `m:inuse 15` | Artikel wird aufgerufen        | 
| sp:miss             | Sucht nach nicht angelegten Platzhalter innerhalb der Struktur | `sp:miss`    | Platzhalter wird angelegt      |


#### Generatoren

| Keyword               | Optionen                             | Beschreibung          | Beispiel                         |
| --------------------- | ------------------------------------ | --------------------- | -------------------------------- |
| c:make                | status=[online(default)/offine]      | Erstellt Kategorien   | `c:make Home Kontakt "Über uns"` |
|                       |                                      |                       | `c:make Home Kontakt "Über uns" --status="offline"` |
| m:make [Modulname]    | fields                               | Erstellt Module       | siehe Modulbeispiele |
| sp:make [Platzhalter] | fields                               | Erstellt Platzhalter  | `sp:make Platzhalter` |


**Modulbeispiel: Überschrift**

Watsoneingabe

```
m:make Überschrift --fields="Überschrift:text"
```

erstellt folgende Moduleingabe

```html
<div class="form-horizontal">

    <div class="form-group">
        <label class="col-sm-2 control-label">Überschrift</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" />
        </div>
    </div>
</div>
```

erstellt folgende Modulausgabe

```php
<?php

$ueberschrift = '';
if(REX_VALUE[id="1" isset="1"]) {
    $ueberschrift = REX_VALUE[id="1"];
}
echo $ueberschrift;
?>
```

**Modulbeispiel: Komplex**

Watsoneingabe

```
m:make Komplex --fields="Überschrift:text, Intro:textarea(['class'=>'redactor']), Text:textarea:textile, Bild:media, Bilder:medialist, Auswahl:select( [1 => 'ja', 0 => 'nein'] ), Status:checkbox()"
```

erstellt folgende Moduleingabe

```html
<div class="form-horizontal">

    <div class="form-group">
        <label class="col-sm-2 control-label">Überschrift</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Intro</label>
        <div class="col-sm-10">
            <textarea class="form-control redactor" rows="10" name="REX_INPUT_VALUE[2]">REX_VALUE[2]</textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Text</label>
        <div class="col-sm-10">
            <textarea class="form-control" rows="10" name="REX_INPUT_VALUE[3]">REX_VALUE[3]</textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Bild</label>
        <div class="col-sm-10">
            REX_MEDIA[id="1" widget="1"]
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Bilder</label>
        <div class="col-sm-10">
            REX_MEDIALIST[id="1" widget="1"]
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Auswahl</label>
        <div class="col-sm-10">
            <?php
            $select = new rex_select();
            $select->setName('REX_INPUT_VALUE[4]');
            $select->setAttribute('class', 'form-control');
            $select->setAttributes([]);
            $select->addOptions([1 => 'ja', 0 => 'nein']);
            $select->setSelected('REX_VALUE[4]');
            echo $select->get();
            ?>
        </div>
    </div>
</div>
```

erstellt folgende Modulausgabe

```php
<?php

$ueberschrift = '';
if(REX_VALUE[id="1" isset="1"]) {
    $ueberschrift = REX_VALUE[id="1"];
}
echo $ueberschrift;

$intro = '';
if (rex_addon::get('textile')->isAvailable()) {
    if(REX_VALUE[id="2" isset="1"]) {
        $textile = REX_VALUE[id="2"];
        $textile = str_replace('<br />', '', $textile);
        $intro = rex_textile::parse($textile);
    }
}
echo $intro;

$text = '';
if (rex_addon::get('textile')->isAvailable()) {
    if(REX_VALUE[id="3" isset="1"]) {
        $textile = REX_VALUE[id="3"];
        $textile = str_replace('<br />', '', $textile);
        $text = rex_textile::parse($textile);
    }
}
echo $text;

$bild = '';
if (REX_MEDIA[id="1" isset="1"]) {
    $media = rex_media::get(REX_MEDIA[id="1"]);
    $bild .= $media->toImage();
}
echo $bild;

$bilder = '';
if (REX_MEDIALIST[id="1" isset="1"]) {
    $mediaList = explode(',', REX_MEDIALIST[id="1"]);
    foreach ($mediaList as $mediaName) {
        $media = rex_media::get($mediaName);
        $bilder .= $media->toImage();
    }
}
echo $bilder;

$auswahl = '';
if(REX_VALUE[id="4" isset="1"]) {
    $auswahl = REX_VALUE[id="4"];
}
echo $auswahl;
?>
```





### Bugtracker

Du hast einen Fehler gefunden oder ein nettes Feature parat? [Lege ein Issue an](https://github.com/tbaddade/redaxo_watson/issues). Bevor du ein neues Issue erstellts, suche bitte ob bereits eines mit deinem Anliegen existiert und lese die [Issue Guidelines (englisch)](https://github.com/necolas/issue-guidelines) von [Nicolas Gallagher](https://github.com/necolas/).


### Changelog

siehe [CHANGELOG.md](https://github.com/tbaddade/redaxo_watson/blob/master/CHANGELOG.md)

### Lizenz

siehe [LICENSE.md](https://github.com/tbaddade/redaxo_watson/blob/master/LICENSE.md)


### Autor

**Thomas Blum**

* http://blumbeet.com
* https://github.com/tbaddade