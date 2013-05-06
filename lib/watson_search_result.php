<?php


class watson_search_result
{
    private $entries;
    private $header;
    private $footer;



    public function __construct()
    {

    }



    /**
     * Sets result entry
     *
     * @param watson_search_entry $entry
     */
    public function addEntry($entry)
    {
        $this->entries[] = $entry;
    }



    /**
     * render all result entries
     */
    public function render()
    {
        $entries = $this->entries;

        $returns = array();
        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                $return = array();

                $classes = array();
                $styles  = array();

                $return['value']        = $entry->getValue();
                $return['tokens']       = array($entry->getValue());
                $return['description']  = '';

                if ($entry->hasIcon()) {
                    $classes[]                  = 'watson-icon';
                    $styles[]                   = 'background-image: url(' . $entry->getIcon() . ');';
                }

                if ($entry->hasDescription()) {
                    $classes[]                  = 'watson-description';
                    $return['description']      = $entry->getDescription();
                }

                if ($entry->hasUrl()) {
                    $return['url']              = $entry->getUrl();
                    $return['url_open_window']  = $entry->getUrlOpenWindow();
                }

                if ($entry->hasQuickLookUrl()) {
                    $classes[]                  = 'watson-quick-look';
                    $return['quick_look_url']   = $entry->getQuickLookUrl();
                }


                $class = count($classes) > 0 ? ' ' . implode(' ', $classes) : '';
                $style = count($styles) > 0 ? implode(' ', $styles) : '';

                $return['class']        = $class;
                $return['style']        = $style;

                $returns[] = $return;
            }

        }

        return $returns;
    }
}
