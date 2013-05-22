
Watson
================================================================================

Ein Suchagent für REDAXO 4.5+

Watson spart Zeit bei der Suche nach Artikeln, Modulen, Templates, Benutzer und Dateien und … im REDAXO Backend.


### Siehe auch



### Download




### Voraussetzungen

* **REDAXO** 4.5
* **PHP:** 5.3



### Installation

* Ordner **redaxo_watson** in **watson** umbenennen
* AddOn installieren und aktivieren
* Plugins installieren und aktivieren



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


### Credits

* Watson Logo **Ralph Zumkeller**



Benutzung
--------------------------------------------------------------------------------


### Watson

* **öffnen**
    * ctrl + space (um im Firefox das Contextmenü zu vermeiden, **ctrl + alt + space** drücken)
    * ctrl + alt + space
    * ctrl + cmd + space
* **schließen**
    * ESC
    * ctrl + space
    * ctrl + alt + space
    * ctrl + cmd + space




### Quick look

* **öffnen**
    * Cursortaste rechts
* **schließen**
    * Cursortaste links
    * Cursortaste oben
    * Cursortaste unten




### Keywords

Sind Keywords registriert, wird die Suche entsprechend eingegrenzt.<br />

`t text` :: **t** grenzt die Suche auf Templates ein

#### Add-Modus

Gibt man ein Keyword und nachfolgend ein **+** ein, gelangt man in den Add-Modus (hinzufügen/anlegen) der angegebenen Url.<br />

`t+ Neues Template` :: **t+** wird ein neues Template mit dem Namen "Neues Template" anlegen


### Kommandos

Ein Kommando ist ein Keyword ohne weitere Texteingabe und löst bei **enter** eine Aktion aus.<br />

`logout` :: hierdurch wird man vom REDAXO Backend ausgeloggt



Plugins
--------------------------------------------------------------------------------

### watson_core

* **Suchen**
    * **Artikel**<br />
        a+; c+; on; off - um eine(n) Kategorie/Arikel anzulegen, muss man sich in der Struktur befinden
    * **Medien**<br />
        m, m+; f, f+
    * **Module**<br />
        m, m+
    * **Templates**<br />
        t, t+
    * **Benutzer**<br />
        u+
* **Kommandos**
    * **start**<br />
        zur Startseite im Backend
    * **home**<br />
        zur Startseite im Frontend
    * **logout**<br />
        REDAXO logout




### watson_calculator

Ein einfacher Rechner

* **Keywords**<br />
  *abhängig von der Backendsprache*
    * =
    * brutto / gross
    * netto / net
    * ust / vat
* **Konstanten**
    * Pi (Kreiszahl π), 3.141592653589793
    * G (Gravitationskonstante), 6.67384E-11