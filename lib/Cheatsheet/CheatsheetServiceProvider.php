<?php

/**
 * This file is part of the Cheatsheet package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Cheatsheet;

use Cheatsheet\Page;
use Cheatsheet\Support\ServiceProvider;

class CheatsheetServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function i18n()
    {
        return __DIR__.'/lang';
    }

    /**
     * {@inheritdoc}
     */
    public function page()
    {
        $page = \rex_be_controller::getPageObject('cheatsheet/addoff');
        if (!$page) {
            $page = new \rex_be_page('addoff', \rex_i18n::msg('watson_addoff_title'));
            $page->setHref(['page' => 'cheatsheet/addoff/watson']);
        }

        $subpage = new \rex_be_page('watson', \rex_i18n::msg('watson_cheatsheet_docs_title'));
        $subpage->setHref(['page' => 'cheatsheet/addoff/watson']);
        $subpage->setIcon('watson-icon-logo');
        $subpage->setSubPath(\rex_path::addon('watson', 'lib/Cheatsheet/pages/docs.php'));
        $page->addSubpage($subpage);

        return $page;
    }
}
