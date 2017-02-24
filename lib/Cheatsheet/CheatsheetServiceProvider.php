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
        return __DIR__ . '/lang';
    }

    /**
     * {@inheritdoc}
     */
    public function page()
    {
        $subpage = new Page();
        $subpage->setKey('watson');
        $subpage->setHref('index.php?page=cheatsheet/rex-var/watson');
        $subpage->setPath('../watson/lib/Cheatsheet/pages/docs.php');
        $subpage->setTitle(\rex_i18n::msg('watson_cheatsheet_docs_title'));

        $page = \rex_be_controller::getPageObject('cheatsheet/addoff');
        if ($page) {
            $page->addSubpage($subpage->get());
            return $page;
        }

        $page = new Page();
        $page->setKey('addoff');
        $page->setTitle(\rex_i18n::msg('watson_addoff_title'));
        $page->addSubpage($subpage->get());

        return $page->get();
    }
}
