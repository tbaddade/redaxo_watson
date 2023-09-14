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

use rex_addon;
use rex_config;
use rex_extension;
use rex_extension_point;
use rex_file;
use rex_i18n;
use rex_path;
use rex_url;

class Watson
{
    public static function showToggleButton(): bool
    {
        return rex_config::get('watson', 'toggleButton', 0);
    }

    public static function getResultLimit(): int
    {
        return (int) rex_config::get('watson', 'resultLimit', 20);
    }

    public static function getAgentHotkeys(): array
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

    public static function getAgentHotkey(): string
    {
        return rex_config::get('watson', 'agentHotkey', '17-32');
    }

    public static function getQuicklookHotkeys(): array
    {
        return [
            '16' => 'shift',
            '17' => 'ctrl',
            '18' => 'alt',
            '91' => 'cmd',
        ];
    }

    public static function getQuicklookHotkey(): string
    {
        return rex_config::get('watson', 'quicklookHotkey', '91');
    }

    public static function getIcon(): string
    {
        return rex_file::get(rex_path::addonAssets('watson', 'watson-logo.svg'));
    }

    public static function getToggleButton(array $attributes = []): string
    {
        $attributes = array_merge(['class' => 'watson-btn', 'data-watson-toggle' => 'agent'], $attributes);
        return sprintf('<button%s>%s</button>', \rex_string::buildAttributes($attributes), self::getIcon());
    }

    public static function translate($key, ...$params)
    {
        return \rex_i18n::msg($key, ...$params);
    }

    public static function hasProviders(): bool
    {
        $providers = rex_addon::get('watson')->getProperty('providers');
        $providers = rex_extension::registerPoint(new rex_extension_point('WATSON_PROVIDER', $providers));

        return count($providers) > 0;
    }

    public static function loadProviders(): array
    {
        $providers = rex_addon::get('watson')->getProperty('providers');
        $providers = rex_extension::registerPoint(new rex_extension_point('WATSON_PROVIDER', $providers));

        $loadedProviders = [];

        if (count($providers) < 1) {
            return $loadedProviders;
        }

        foreach ($providers as $provider) {
            /** @var SupportProvider $instance */
            $instance = new $provider();

            if (is_dir($instance->i18n())) {
                rex_i18n::addDirectory($instance->i18n());
            }

            $register = $instance->register();
            if (is_array($register)) {
                $loadedProviders = array_merge($loadedProviders, $register);
            } else {
                $loadedProviders[] = $register;
            }
        }

        return $loadedProviders;
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
     * Returns the url to the backend-controller (index.php from backend).
     *
     * @param array $params
     *
     * @return string
     */
    public static function getUrl(array $params = []): string
    {
        return rex_url::backendController($params);
    }
}
