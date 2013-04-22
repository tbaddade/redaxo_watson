<?php

class watson_feature
{
    private $keyword;
    private $keyword_add_url;
    private $keyword_add_url_open_window;
    private $keyword_add_dom_id;
    private $icon;
    private $description;
    private $quick_look_url;
    private $url;
    private $url_open_window;
    private $sql_query;



    public function __construct()
    {
    }



    /**
     * Sets a keyword
     *
     * @param string $keyword
     * @param string $url
     * @param string $dom_id
     */
    public function setKeyword($keyword, $url = null, $dom_id = null, $open_window = false)
    {
        $this->keyword = $keyword;

        if ($url) {
            $this->keyword_add_url              = $url;
            $this->keyword_add_url_open_window  = $open_window;
        }

        if ($dom_id) {
            $this->keyword_add_dom_id = $dom_id;
        }
    }

    /**
     * Returns the keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Returns whether a keyword is set
     *
     * @return bool
     */
    public function hasKeyword()
    {
        return !empty($this->keyword);
    }


    /**
     * Returns the keyword add url
     *
     * @return string
     */
    public function getKeywordAddUrl()
    {
        return $this->keyword_add_url;
    }

    /**
     * Returns whether a keyword add url is set
     *
     * @return bool
     */
    public function hasKeywordAddUrl()
    {
        return !empty($this->keyword_add_url);
    }


    /**
     * Returns the keyword dom id
     *
     * @return string
     */
    public function getKeywordAddDomId()
    {
        return $this->keyword_add_dom_id;
    }

    /**
     * Returns whether a keyword dom id is set
     *
     * @return bool
     */
    public function hasKeywordAddDomId()
    {
        return !empty($this->keyword_add_dom_id);
    }


    /**
     * Returns the keyword add url open window
     *
     * @return bool
     */
    public function getKeywordAddUrlOpenWindow()
    {
        return $this->keyword_add_url_open_window;
    }



    /**
     * Sets a sql query
     *
     * @param string $value
     */
    public function setSqlQuery($value)
    {
        $this->sql_query = $value;
    }

    /**
     * Returns the Sql Query
     *
     * @return string
     */
    public function getSqlQuery()
    {
        return $this->sql_query;
    }

    /**
     * Returns whether a sql query is set
     *
     * @return bool
     */
    public function hasSqlQuery()
    {
        return !empty($this->sql_query);
    }



    /**
     * Sets a icon
     *
     * @param string $value
     */
    public function setIcon($value)
    {
        $this->icon = $value;
    }

    /**
     * Returns the icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Returns whether a icon is set
     *
     * @return bool
     */
    public function hasIcon()
    {
        return !empty($this->icon) && file_exists($this->icon);
    }



    /**
     * Sets a description
     *
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->description = $value;
    }

    /**
     * Returns the icon
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns whether a icon is set
     *
     * @return bool
     */
    public function hasDescription()
    {
        return !empty($this->description);
    }



    /**
     * Sets a url
     *
     * @param string $url
     * @param bool   $new_window
     */
    public function setUrl($url, $open_window = false)
    {
        $this->url             = $url;
        $this->url_open_window = $open_window;
    }

    /**
     * Returns the url class
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns whether a url is set
     *
     * @return string
     */
    public function hasUrl()
    {
        return !empty($this->url);
    }

    /**
     * Returns the url open window class
     *
     * @return bool
     */
    public function getUrlOpenWindow()
    {
        return $this->url_open_window;
    }



    /**
     * Sets a quick look url
     *
     * @param string $value
     */
    public function setQuickLookUrl($value)
    {
        $this->quick_look_url = $value;
    }

    /**
     * Returns the quick look url class
     *
     * @return string
     */
    public function getQuickLookUrl()
    {
        return $this->quick_look_url;
    }

    /**
     * Returns whether a quick look url is set
     *
     * @return bool
     */
    public function hasQuickLookUrl()
    {
        return !empty($this->quick_look_url);
    }
}
