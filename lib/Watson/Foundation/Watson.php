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


class Watson
{

    public static function getSearchResultLimit()
    {
        return 20;
    }


    public static function getConsoleInterpreterUrl()
    {
        global $REX;

        return realpath($REX['HTDOCS_PATH'] . 'redaxo') . '/index.php?watson_console=1';
        
    }


    public static function getMediaDir()
    {
        global $REX;

        return '../' . $REX['MEDIA_ADDON_DIR'] . '/watson/';
    }



    public static function translate($key)
    {
        global $I18N;

        return $I18N->msg($key);
    }



    /**
     * Generates URL-encoded query string
     *
     * @param  array  $params
     * @param  string $argSeparator
     * @return string
     */
    public static function buildQuery(array $params, $argSeparator = '&')
    {
        $query = array();
        $func = function (array $params, $fullkey = null) use (&$query, &$func) {

            foreach ($params as $key => $value) {

                $key = $fullkey ? $fullkey . '[' . urlencode($key) . ']' : urlencode($key);

                if (is_array($value)) {

                    $func($value, $key);

                } else {

                    $query[] = $key . '=' . str_replace('%2F', '/', urlencode($value));

                }
            }
        };

        $func($params);

        return implode($argSeparator, $query);
    }



    /**
     * Returns the url to the backend-controller (index.php from backend)
     *
     * @param  array $params
     * @return string
     */
    public static function getUrl(array $params = array())
    {
        $query = Watson::buildQuery($params);
        $query = $query ? '?' . $query : '';

        return htmlspecialchars('../redaxo/index.php' . $query);
    }



    /**
     * Adds the table prefix to the table name
     *
     * @param  string $table Table name
     * @return string
     */
    public static function getTable($table)
    {
        global $REX;
        return $REX['TABLE_PREFIX'] . $table;
    }




    /**
     * Call REDAXO Function
     *
     * Durchsucht das Array $haystack nach dem Schlüssel $needle.
     *
     * Falls ein Wert gefunden wurde wird dieser nach
     * $vartype gecastet und anschließend zurückgegeben.
     *
     * Falls die Suche erfolglos endet, wird $default zurückgegeben
     *
     * @access private
     */
    public static function arrayCastVar($haystack, $needle, $vartype, $default = '')
    {

        return _rex_array_key_cast($haystack, $needle, $vartype, $default = '');

    }

}
