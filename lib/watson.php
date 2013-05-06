<?php

class watson
{


    public static function debug($value) {

        echo '<pre style="text-align: left">';
        print_r($value);
        echo '</pre>';
        exit();

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
