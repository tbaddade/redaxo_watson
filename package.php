<?php

return [
    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Name is same name of addon dir
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'name' => 'watson',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Title
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'title' => 'Watson',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Version
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'version' => '0.1',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Author
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'author' => 'Thomas Blum',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Support Page
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'supportpage' => '',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Permission
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'permission' => 'watson[]',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Permission Options
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'permission_options' => [
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Permission Startpage
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'permission_startpage' => 'watson[]',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Min REDAXO Version
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'rex_version' => '4.5',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Min PHP Version
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'php_version' => '5.3',

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application AddOns Required
     * key      = The key is the required addon name.
     * value    = The value is the required version of the addon.
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'required_addons' => [
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Subpages
     * key 		= The key is translated with prefixed name.
     * value 	= The value is the url subpage parameter.
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'subpages' => [
        'first' => '',
        'second' => 'second-param',
        'third' => ['third-1', 'third-2'],
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Classes Files
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'classes' => [
        'lib/Watson/Foundation/Command.php',
        'lib/Watson/Foundation/Console.php',
        'lib/Watson/Foundation/ConsoleCommand.php',
        'lib/Watson/Foundation/Documentation.php',
        'lib/Watson/Foundation/Extension.php',
        'lib/Watson/Foundation/Search.php',
        'lib/Watson/Foundation/SearchCommand.php',
        'lib/Watson/Foundation/SearchResult.php',
        'lib/Watson/Foundation/SearchResultEntry.php',
        'lib/Watson/Foundation/SupportProvider.php',
        'lib/Watson/Foundation/Watson.php',

        'lib/Watson/Media/MediaSearch.php',
        'lib/Watson/Media/MediaProvider.php',

        'lib/Watson/Module/ModuleSearch.php',
        'lib/Watson/Module/ModuleProvider.php',

        'lib/Watson/Structure/ArticleSearch.php',
        'lib/Watson/Structure/StructureProvider.php',

        'lib/Watson/Template/TemplateConsole.php',
        'lib/Watson/Template/TemplateSearch.php',
        'lib/Watson/Template/TemplateProvider.php',
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Class Aliases
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'aliases' => [
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Providers
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'providers' => [
        'Watson\Media\MediaProvider',
        'Watson\Module\ModuleProvider',
        'Watson\Structure\StructureProvider',
        'Watson\Template\TemplateProvider',
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application CSS Files
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'stylesheets' => [
        'facebox.css',
        'watson.css',
        /*
    	'jquery.terminal.css',
        */
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Javascript Files
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'javascripts' => [
        'facebox.js',
        'hogan-3.0.2.min.js',
        'typeahead.bundle.js',
        'watson_searcher.js',
        /*
        //'jquery.terminal-min.js',
        'jquery.terminal.js',
        'json-rpc.js',
        'watson_console.js',
        */
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Javascript Properties
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'javascript_properties' => [
        '',
    ],

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Application Extensions
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *.
     */

    'extensions' => [
//    	'\Iffi\File::panel' 		=> array('OUTPUT_FILTER', ),
/*
    	'\Watson\Feature\FeatureAgent::panel' 		=> array('OUTPUT_FILTER', ),
    	'\Watson\Feature\FeatureAgent::register' 	=> array('ADDONS_INCLUDED', array(), REX_EXTENSION_LATE, ),
    	'\Watson\Feature\FeatureAgent::run'			=> array('ADDONS_INCLUDED', array(), REX_EXTENSION_LATE, ),
*/
    ],
];
