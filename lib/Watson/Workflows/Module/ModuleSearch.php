<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Module;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class ModuleSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['m', 'm:inuse'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_module_documentation_description'));
        $documentation->setUsage('m keyword');
        $documentation->setExample('$headline');
        $documentation->setExample('m $headline');
        $documentation->setExample('m module name');

        return $documentation;
    }

    /**
     * Return array of registered page params.
     *
     * @return array
     */
    public function registerPageParams()
    {
        return [];
    }

    /**
     * Execute the command for the given Command.
     *
     * @param Command $command
     *
     * @return Result
     */
    public function fire(Command $command)
    {
        if ($command->getCommand() == 'm:inuse') {
            return $this->searchModuleInUse($command);
        }
        return $this->searchInModules($command);
    }

    /**
     * Execute the command for the given Command.
     *
     * @param Command $command
     *
     * @return Result
     */
    protected function searchInModules(Command $command)
    {
        $result = new Result();

        $fields = [
            'name',
            'input',
            'output',
        ];

        $sql_query = ' SELECT      id,
                                    name
                        FROM        '.Watson::getTable('module').'
                        WHERE       '.$command->getSqlWhere($fields).'
                        ORDER BY    name';

        $items = $this->getDatabaseResults($sql_query);

        if (count($items)) {
            $counter = 0;

            foreach ($items as $item) {
                $url = Watson::getUrl(['page' => 'modules/modules', 'module_id' => $item['id'], 'function' => 'edit']);

                ++$counter;

                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_module_legend'));
                }
                $entry->setValue($item['name']);
                $entry->setDescription(Watson::translate('watson_open_module'));
                $entry->setIcon('watson-icon-module');
                $entry->setUrl($url);
                $entry->setQuickLookUrl($url);

                $result->addEntry($entry);
            }
        }

        return $result;
    }

    /**
     * Execute the command for the given m:inuse Command.
     *
     * @param Command $command
     *
     * @return Result
     */
    protected function searchModuleInUse(Command $command)
    {
        $result = new Result();

        if ((int) $command->getArgument(1) > 0) {
            $moduleId = (int) $command->getArgument(1);
            $query = ' SELECT  s.article_id AS id,
                                s.clang_id,
                                s.ctype_id,
                                m.name AS module_name,
                                CONCAT(s.article_id, "|", s.clang_id) as bulldog
                        FROM    '.Watson::getTable('article_slice').' AS s
                            LEFT JOIN
                                '.Watson::getTable('article').' AS a
                                ON  (s.article_id = a.id AND s.clang_id = a.clang_id)
                            LEFT JOIN
                                '.Watson::getTable('module').' AS m
                                ON s.module_id = m.id
                        WHERE   s.module_id = "'.$moduleId.'"
                        GROUP BY bulldog';

            $items = $this->getDatabaseResults($query);

            $searchResults = [];
            if (count($items)) {
                foreach ($items as $item) {
                    $searchResults[$item['bulldog']] = $item;
                }
            }

            // Ergebnisse auf Rechte prüfen und bereitstellen
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            if (count($searchResults)) {
                $counter = 0;
                foreach ($searchResults as $item) {
                    $clang_id = $item['clang_id'];
                    $article = \rex_article::get($item['id'], $clang_id);
                    $category_id = $article->getCategoryId();

                    // Rechte prüfen
                    if (\rex::getUser()->getComplexPerm('clang')->hasPerm($clang_id) && \rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
                        $path = [];
                        $tree = $article->getParentTree();
                        foreach ($tree as $o) {
                            $path[] = $o->getName();
                        }

                        if (!$article->isStartArticle()) {
                            $path[] = $article->getName();
                        }

                        $path = '/'.implode('/', $path);
                        $url = Watson::getUrl(['page' => 'content/edit', 'article_id' => $article->getId(), 'mode' => 'edit', 'clang' => $clang_id, 'ctype' => $item['ctype_id']]);

                        $suffix = [];
                        $suffix[] = $article->getId();
                        if (count(\rex_clang::getAll()) > 1) {
                            $suffix[] = \rex_clang::get($clang_id)->getName();
                        }
                        $suffix = implode(', ', $suffix);
                        $suffix = $suffix != '' ? '('.$suffix.')' : '';

                        ++$counter;
                        $entry = new ResultEntry();
                        if ($counter == 1) {
                            $entry->setLegend(str_replace('{0}', $item['module_name'], Watson::translate('watson_module_inuse_legend')));
                        }
                        $entry->setValue($article->getName(), $suffix);
                        $entry->setDescription($path);
                        $entry->setIcon('watson-icon-article');
                        $entry->setUrl($url);
                        $entry->setQuickLookUrl('../index.php?article_id='.$article->getId().'&clang='.$article->getClang());

                        $result->addEntry($entry);
                    }
                }
            }
        }

        return $result;
    }
}
