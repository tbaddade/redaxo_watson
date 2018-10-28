<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Media;

use Watson\Foundation\Command;
use Watson\Foundation\Documentation;
use Watson\Foundation\Result;
use Watson\Foundation\ResultEntry;
use Watson\Foundation\Watson;
use Watson\Foundation\Workflow;

class MediaSearch extends Workflow
{
    /**
     * Provide the commands of the search.
     *
     * @return array
     */
    public function commands()
    {
        return ['m', 'f'];
    }

    /**
     * @return Documentation
     */
    public function documentation()
    {
        $documentation = new Documentation();
        $documentation->setDescription(Watson::translate('watson_media_documentation_description'));
        $documentation->setUsage('m keyword');
        $documentation->setExample('m filename');

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
        $result = new Result();

        $fields = [
            'filename',
            'title',
        ];

        $s = \rex_sql::factory();
        $s->setQuery('SELECT * FROM '.Watson::getTable('media').' LIMIT 0');
        $fieldnames = $s->getFieldnames();

        foreach ($fieldnames as $fieldname) {
            if (substr($fieldname, 0, 4) == 'med_') {
                $fields[] = $fieldname;
            }
        }

        $sql_query = ' SELECT      id, 
                                    filename,
                                    title
                        FROM        '.Watson::getTable('media').'
                        WHERE       '.$command->getSqlWhere($fields).'
                        ORDER BY    filename';

        $items = $this->getDatabaseResults($sql_query);

        if (count($items)) {
            $counter = 0;

            foreach ($items as $item) {
                $title = ($item['title'] != '') ? ' ('.Watson::translate('watson_media_title').': '.$item['title'].')' : '';

                ++$counter;

                $entry = new ResultEntry();
                if ($counter == 1) {
                    $entry->setLegend(Watson::translate('watson_media_legend'));
                }
                $entry->setValue($item['filename']);
                $entry->setDescription(Watson::translate('watson_open_media').$title);
                $entry->setIcon('watson-icon-media');
                $entry->setUrl('javascript:newPoolWindow(\''.Watson::getUrl(['page' => 'mediapool/media', 'file_id' => $item['id']]).'\')');

                $m = \rex_media::get($item['filename']);
                if ($m instanceof \rex_media) {
                    if ($m->isImage()) {
                        $entry->setQuickLookUrl(Watson::getUrl(['rex_media_type' => 'rex_mediapool_maximized', 'rex_media_file' => $item['filename']], false));
                    }
                }

                $result->addEntry($entry);
            }
        }

        return $result;
    }
}
