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

class Result
{
    private $entries;
    private $header;
    private $footer;

    public function __construct()
    {
    }

    /**
     * Sets result entry.
     *
     * @param ResultEntry $entry
     */
    public function addEntry($entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * render all result entries.
     */
    public function render($displayKey)
    {
        $entries = $this->entries;

        $returns = [];
        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                $return = [];

                $classes = [];
                $styles = [];

                $value = $entry->getValue();

                $return['value_name'] = $entry->getValue();
                $return['description'] = '';

                if ($entry->hasLegend()) {
                    $return['legend'] = ' '.$entry->getLegend();
                }

                if ($entry->hasValueSuffix()) {
                    // Suffix anhängen, da sonst nur ein Ergebnis erscheint
                    // Bspl. gleicher Artikelname in 2 Sprachen
                    $value .= ' '.$entry->getValueSuffix();

                    $classes[] = 'watson-has-value-suffix';
                    $return['value_suffix'] = $entry->getValueSuffix();
                }

                if ($entry->hasIcon()) {
                    $classes[] = 'watson-has-icon';
                    $return['icon'] = ' '.$entry->getIcon();
                }

                if ($entry->hasDescription()) {
                    $classes[] = 'watson-has-description';
                    $return['description'] = $entry->getDescription();
                }

                if ($entry->hasUrl()) {
                    $return['url'] = $entry->getUrl();
                    $return['url_open_window'] = $entry->getUrlOpenWindow();
                }

                if ($entry->hasQuickLookUrl()) {
                    $classes[] = 'watson-has-quick-look';
                    $return['quick_look_url'] = $entry->getQuickLookUrl();
                }

                if ($entry->hasHtmlFields()) {
                    $classes[] = 'watson-has-html-fields';
                    $return['html_fields'] = $entry->getHtmlFields();
                }

                if ($entry->hasAjax()) {
                    $return['ajax'] = $entry->getAjax();
                }

                $return['value'] = $value;
                $return['tokens'] = [$value];
                $return['displayKey'] = $displayKey;

                $class = count($classes) > 0 ? ' '.implode(' ', $classes) : '';
                $style = count($styles) > 0 ? implode(' ', $styles) : '';

                $return['class'] = $class;
                $return['style'] = $style;

                $returns[] = $return;
            }
        }

        return $returns;
    }
}
