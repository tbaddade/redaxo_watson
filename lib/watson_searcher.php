<?php


abstract class watson_searcher
{
    /**
    * @return string[] an array of supported keywords
    */
    abstract function keywords();

    /**
     * Search for the given SearchTerm
     *
     * @param watson_search_term $search
     * @return watson_search_result
     */
    abstract function search(watson_search_term $search);



    public static function registerExtension($params)
    {
        $params['subject'][] = $params['searcher'];
        return $params['subject'];
    }
}
