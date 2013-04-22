
Watson
=================

stellt eine globale Suche bereit an der sich andere AddOns andocken können.

Zum Bspl. dockt sich das **[watson_core](https://github.com/tbaddade/redaxo_watson/blob/master/README.md#watson_core)** Plugin mit einer Artikel-, Modul- und Templatesuche an.


Voraussetzungen
-----------------
* **REDAXO** 4.5
* **PHP:** 5.3

Keywords
-----------------

Sind Keywords registriert, wird die Suche entsprechend eingegrenzt.

Gibt man ein Keyword und nachfolgend ein **+** ein, gelangt man in den Add-Modus der angegebenen Url.


### Hotkey

* Watson öffnen: **ctrl + space** (um im Firefox das Contextmenü zu vermeiden, einfach **ctrl + alt + space** drücken)
* Quick look: **rechter Cursor**



Plugins
-----------------

### watson_core

#### Suchen

* Artikel
* Module (Keyword: m)
* Templates (Keyword: t)
* Benutzer (Keyword: u)


#### Kommandos

* home :: zur Startseite im Backend
* logout :: REDAXO logout
* web :: zur Startseite im Frontend



Screenshot
--------------------------------------------

### Watson
![Watson](http://blumbeet.com/screens/github/watson/watson.png)

### Quick look
![Watson](http://blumbeet.com/screens/github/watson/quick_look.png)
