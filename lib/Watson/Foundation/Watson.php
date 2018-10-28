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
    public static function getResultLimit()
    {
        return \rex_config::get('watson', 'resultLimit', 20);
    }

    public static function getToggleButtonStatus()
    {
        return \rex_config::get('watson', 'toggleButton', 0);
    }

    public static function getAgentHotkeys()
    {
        return [
                     '16-32' => 'shift - space',
                     '16-17-32' => 'shift - ctrl - space',
                     '16-18-32' => 'shift - alt - space',
                     '17-32' => 'ctrl - space',
                     '17-18-32' => 'ctrl - alt - space',
                     '17-91-32' => 'ctrl - cmd - space',
                     '18-32' => 'alt - space',
                ];
    }

    public static function getAgentHotkey()
    {
        return \rex_config::get('watson', 'agentHotkey', '17-32');
    }

    public static function getQuicklookHotkeys()
    {
        return [
                     '16' => 'shift',
                     '17' => 'ctrl',
                     '18' => 'alt',
                     '91' => 'cmd',
                ];
    }

    public static function getQuicklookHotkey()
    {
        return \rex_config::get('watson', 'quicklookHotkey', '91');
    }

    public static function getConsoleInterpreterUrl()
    {
        global $REX;

        return realpath($REX['HTDOCS_PATH'].'redaxo').'/index.php?watson_console=1';
    }

    public static function getAssetsDir()
    {
        return \rex_path::addonAssets('watson');
    }

    public static function translate($key, ...$params)
    {
        return \rex_i18n::msg($key, ...$params);
    }

    public static function loadProviders()
    {
        $providers = \rex_addon::get('watson')->getProperty('providers');

        $providers = \rex_extension::registerPoint(new \rex_extension_point('WATSON_PROVIDER', $providers));

        $loaded_providers = [];

        if (count($providers) > 0) {
            foreach ($providers as $provider) {
                $instance = new $provider();

                if (is_dir($instance->i18n())) {
                    \rex_i18n::addDirectory($instance->i18n());
                }

                $register = $instance->register();
                if (is_array($register)) {
                    $loaded_providers = array_merge($loaded_providers, $register);
                } else {
                    $loaded_providers[] = $register;
                }
            }
        }

        return $loaded_providers;
    }

    public static function deleteRegisteredPageParams()
    {
        \rex_request::setSession('watson_params', []);
    }

    public static function getRegisteredPageParam($param, $default = false)
    {
        $watsonParams = \rex_request::session('watson_params', 'array', []);
        if (isset($watsonParams[$param])) {
            return $watsonParams[$param];
        }

        return $default;
    }

    public static function saveRegisteredPageParams(array $providerParams)
    {
        $watsonParams = \rex_request::session('watson_params', []);
        foreach ($providerParams as $providerParam) {
            if (isset($_REQUEST[$providerParam])) {
                $watsonParams[$providerParam] = $_REQUEST[$providerParam];
            }
        }
        \rex_request::setSession('watson_params', $watsonParams);
    }

    /**
     * Generates URL-encoded query string.
     *
     * @param array  $params
     * @param string $argSeparator
     *
     * @return string
     */
    public static function buildQuery(array $params, $argSeparator = '&')
    {
        $query = [];
        $func = function (array $params, $fullkey = null) use (&$query, &$func) {
            foreach ($params as $key => $value) {
                $key = $fullkey ? $fullkey.'['.urlencode($key).']' : urlencode($key);

                if (is_array($value)) {
                    $func($value, $key);
                } else {
                    $query[] = $key.'='.str_replace('%2F', '/', urlencode($value));
                }
            }
        };

        $func($params);

        return implode($argSeparator, $query);
    }

    /**
     * Returns the url to the backend-controller (index.php from backend).
     *
     * @param array $params
     *
     * @return string
     */
    public static function getUrl(array $params = [], $backend = true)
    {
        return \rex_url::backendController($params);

        if ($backend) {
            return htmlspecialchars(\rex_url::backendController($params));
        }
        return \rex_url::backendController($params);
    }

    /**
     * Adds the table prefix to the table name.
     *
     * @param string $table Table name
     *
     * @return string
     */
    public static function getTable($table)
    {
        return \rex::getTable($table);
    }

    /**
     * Call REDAXO Function.
     *
     * Durchsucht das Array $haystack nach dem Schlüssel $needle.
     *
     * Falls ein Wert gefunden wurde wird dieser nach
     * $vartype gecastet und anschließend zurückgegeben.
     *
     * Falls die Suche erfolglos endet, wird $default zurückgegeben
     */
    public static function arrayCastVar($haystack, $needle, $vartype, $default = '')
    {
        return _rex_array_key_cast($haystack, $needle, $vartype, $default = '');
    }
}
