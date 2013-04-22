<?php

class watson
{
    private static $features = array();
    private static $feature_add_sign = '+';
    private static $commands = array();



    /**
     * Registers a feature
     *
     * @param watson_feature $feature
     */
    public static function registerFeature(watson_feature $feature)
    {
        self::$features[] = $feature;
    }

    /**
     * Returns all registered features
     *
     * @return self[]
     */
    public static function getRegisteredFeatures()
    {
        return self::$features;
    }


    /**
     * Registers a command
     *
     * @param watson_command $command
     */
    public static function registerCommand(watson_command $command)
    {
        self::$commands[] = $command;
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


    private static function getKeyword($query)
    {
        if (strpos($query, ' ') !== false) {
            $explode = explode(' ', $query);
            return $explode[0];
        }
        return null;
    }

    private static function hasRegisteredKeyword($keyword, $features)
    {
        $keyword = rtrim($keyword, self::$feature_add_sign);

        foreach ($features as $feature) {
            if ($feature->hasKeyword()) {
                if ($feature->getKeyword() == $keyword) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function isAddMode($keyword)
    {
        return (substr($keyword, -1) == '+');
    }

    private static function replaceUrl($url, $result_set = array())
    {
        if (count($result_set) > 0) {
            $search  = array();
            $replace = array();

            foreach ($result_set as $key => $val) {
                $search[] = urlencode('{{'.$key.'}}');
                $search[] = '{{'.$key.'}}';

                $replace[] = $val;
                $replace[] = $val;
            }
            $url = str_replace($search, $replace, $url);
        }

        return htmlspecialchars_decode($url);
    }

    private static function buildNoResultDataset($value)
    {
        $return = array();
        $token  = $value;

        $return['value']  = $value;
        $return['tokens'] = array($token);

        return $return;
    }

    private static function buildCommandDataset($command)
    {
        global $I18N;

        $return = array();
        $value  = $command->getCommand();
        $token  = $value;

        $classes = array();

        $description = '';
        if ($command->hasDescription()) {
            $classes[] = 'watson-description';
            $description .= $command->getDescription();
        }

        $class = count($classes) > 0 ? ' ' . implode(' ', $classes) : '';

        $return['value']            = $value;
        $return['tokens']           = array($token);
        $return['description']      = $description;
        $return['class']            = $class;
        $return['url']              = self::replaceUrl($command->getUrl());
        $return['url_open_window']  = $command->getUrlOpenWindow();

        return $return;
    }

    private static function buildFeatureDataset($value, $feature = null, array $result_set = array())
    {
        $return  = array();
        $token   = $value;

        $classes = array();
        $styles  = array();

        $description = '';
        if ($feature) {
            if ($feature->hasIcon()) {
                $classes[] = 'watson-icon';
                $styles[]  = 'background-image: url(' . $feature->getIcon() . ');';
            }

            if ($feature->hasDescription()) {
                $classes[] = 'watson-description';
                $description = $feature->getDescription();
            }

            if ($feature->hasUrl()) {
                $return['url']             = self::replaceUrl($feature->getUrl(), $result_set);
                $return['url_open_window'] = $feature->getUrlOpenWindow();
            }

            if ($feature->hasQuickLookUrl()) {
                $return['quick_look_url'] = self::replaceUrl($feature->getQuickLookUrl(), $result_set);
            }
        }

        $class = count($classes) > 0 ? ' ' . implode(' ', $classes) : '';
        $style = count($styles) > 0 ? ' ' . implode(' ', $styles) : '';

        $return['value']        = $value;
        $return['tokens']       = array($token);
        $return['description']  = $description;
        $return['class']        = $class;
        $return['style']        = $style;

        return $return;
    }

    public static function result($params)
    {
        global $I18N;

        $q = isset($params['q']) ? $params['q'] : '';

        if ($q != '') {

            $json = array();

            $features = self::getRegisteredFeatures();
            $commands = self::getRegisteredCommands();

            if (count($commands) > 0) {
                foreach ($commands as $command) {
                    if ($q == $command->getCommand()) {
                        $json[] = self::buildCommandDataset($command);
                    }
                }
            }

            if (count($features) > 0) {

                $keyword = self::getKeyword($q);

                if (self::hasRegisteredKeyword($keyword, $features)) {
                    $q_save = $q;
                    $q      = str_replace($keyword . ' ', '', $q);

                    $keyword_check = $keyword;
                    if (self::isAddMode($keyword)) {
                        $keyword_check = substr($keyword, 0, -1);
                    }

                    $features_save = array();
                    foreach ($features as $feature) {
                        if ($feature->hasKeyword() && $feature->getKeyword() == $keyword_check) {

                            if (self::isAddMode($keyword) && $feature->hasKeywordAddUrl()) {

                                $url = $feature->getKeywordAddUrl();

                                if ($feature->hasKeywordAddDomId()) {
                                    $dividier = (strpos($url, '?') === false) ? '?' : '&';
                                    $url = $url . $dividier . 'watson_id=' . $feature->getKeywordAddDomId() . '&watson_text=' . $q;
                                }

                                $feature->setUrl($url, $feature->getKeywordAddUrlOpenWindow());

                                $json[] = self::buildFeatureDataset($q_save, $feature);

                            }

                            $features_save[] = $feature;
                        }
                    }
                    $features = $features_save;
                }

                if (!self::isAddMode($keyword)) {
                    foreach ($features as $feature) {

                        if ($feature->hasSqlQuery()) {

                            $query = str_replace('{{q}}', '"%' . mysql_real_escape_string($q) . '%"', $feature->getSqlQuery());

                            $sql = rex_sql::factory();
                            $sql->debugsql = true;
                            $sql->setQuery($query);

                            if ($sql->getRows() > 0) {
                                $results = $sql->getArray();

                                foreach ($results as $result) {

                                    // Ergebnissanzeige Field = name verwenden
                                    // ansonsten den ersten Wert des Results
                                    if (isset($result['value'])) {
                                        $value = $result['value'];
                                    } elseif (isset($result['name'])) {
                                        $value = $result['name'];
                                    } else {
                                        $values = array_values($result);
                                        $value  = $values[0];
                                    }

                                    $json[] = self::buildFeatureDataset($value, $feature, $result);
                                }
                            }

                        }
                    }
                }
            }

            if (count($json) == 0) {
                $json[] = self::buildNoResultDataset($I18N->msg('b_no_results'));
            }

            ob_clean();
            header('Content-type: application/json');
            echo json_encode($json);
            exit();
        }
    }




    /**
     * Generates URL-encoded query string
     *
     * @param array  $params
     * @param string $argSeparator
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
     */
    public static function url(array $params = array())
    {
        $query = watson::buildQuery($params);
        $query = $query ? '?' . $query : '';
        return htmlspecialchars('index.php' . $query);
    }

    /**
     * Adds the table prefix to the table name
     *
     * @param string $table Table name
     * @return string
     */
    public static function getTable($table)
    {
        global $REX;
        return $REX['TABLE_PREFIX'] . $table;
    }
}
