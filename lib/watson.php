<?php

class watson
{
    /**
     * Settings array
     *
     * @var self[]
     */
    private static $settings = array();
    private static $commands = array();


    /**
     * Registers a setting
     *
     * @param self $setting
     */
    public static function register(array $setting)
    {
        self::$settings[] = $setting;
    }


    /**
     * Registers a command
     *
     * @param self $command
     */
    public static function registerCommand(array $command)
    {
        if (!isset($command['keyword'])) {
            throw new rex_exception('Fehler: Keyword nicht gesetzt!');
        }
        if (!isset($command['url'])) {
            throw new rex_exception('Fehler: Url nicht gesetzt!');
        }

        self::$commands[] = $command;
    }

    /**
     * Returns all registered classes
     *
     * @return self[]
     */
    public static function getRegistered()
    {
        return self::$settings;
    }

    /**
     * Returns all registered commands
     *
     * @return self[]
     */
    public static function getRegisteredCommands()
    {
        return self::$commands;
    }

    public static function result($params)
    {
        $q = isset($params['q']) ? $params['q'] : '';

        if ($q != '') {

            $json = array();

            $settings = self::getRegistered();
            $commands = self::getRegisteredCommands();

            if (count($commands) > 0) {
                foreach ($commands as $command) {
                    if ($q == $command['keyword']) {

                        $json[] = array(
                            'url'       => htmlspecialchars_decode($command['url']),
                            'value'     => $command['keyword'],
                            'tokens'    => array($command['keyword'])
                        );
                    }
                }
            }

            if (count($settings) > 0) {

                $add_flag = false;
                if (strpos($q, ' ') !== false) {
                    $explode  = explode(' ', $q);
                    $keyword  = $explode[0];
                    unset($explode[0]);
                    $q_save   = $q;
                    $q        = implode(' ', $explode);

                    if (substr($keyword, -1) == '+') {
                        $keyword = substr($keyword, 0, -1);
                        $add_flag = true;
                    }

                    $settings_save = array();
                    foreach ($settings as $setting) {
                        if (isset($setting['keyword']) && $setting['keyword'] != '' && $setting['keyword'] == $keyword) {

                            if ($add_flag && isset($setting['add_url']) && $setting['add_url'] != '') {
                                $url = $setting['add_url'];

                                if (isset($setting['add_id']) && $setting['add_id'] != '') {
                                    $dividier = (strpos($url, '?') === false) ? '?' : '&';
                                    $url = $url . $dividier . 'watson_id=' . $setting['add_id'] . '&watson_text=' . $q;
                                }


                                $json[] = array(
                                    'name'      => $q_save,
                                    'url'       => htmlspecialchars_decode($url),
                                    'value'     => $q_save,
                                    'tokens'    => array($q_save)
                                );
                            }

                            $settings_save[] = $setting;
                        }
                    }
                    $settings = $settings_save;
                }

                if (!$add_flag) {
                    foreach ($settings as $setting) {

                        if (is_array($setting) && isset($setting['query'])) {

                            $query = str_replace('{{q}}', '"%' . mysql_real_escape_string($q) . '%"', $setting['query']);

                            $sql = rex_sql::factory();
                            $sql->debugsql = true;
                            $sql->setQuery($query);

                            if ($sql->getRows() > 0) {
                                $results = $sql->getArray();

                                foreach ($results as $r) {

                                    // Ergebnissanzeige Field = name verwenden
                                    // ansonsten den ersten Wert des Results
                                    if (isset($r['name'])) {
                                        $value = $r['name'];
                                    } else {
                                        $values = array_values($r);
                                        $value  = $values[0];
                                    }


                                    // übergebene Urls ersetzen
                                    $url = '';
                                    if (isset($setting['url'])) {
                                        $search  = array();
                                        $replace = array();

                                        foreach ($r as $key => $val) {
                                            $search[] = urlencode('{{'.$key.'}}');
                                            $search[] = '{{'.$key.'}}';

                                            $replace[] = $val;
                                            $replace[] = $val;
                                        }

                                        $url = str_replace($search, $replace, $setting['url']);
                                    }

                                    $json[] = array(
                                        'name'      => $value,
                                        'url'       => htmlspecialchars_decode($url),
                                        'value'     => $value,
                                        'tokens'    => array($value),
                                    );
                                }
                            }
                        }
                    }
                }
            }


            if (count($json) == 0) {
                $json[] = array(
                    'value'     => rex_i18n::msg('b_no_results'),
                    'tokens'    => array(rex_i18n::msg('b_no_results'))
                );
            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }
    }
}
