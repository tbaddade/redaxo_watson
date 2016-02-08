<?php

/**
 * This file is part of the Watson package.
 *
 * @author (c) Thomas Blum <thomas@addoff.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Watson\Workflows\Structure;

use Watson\Foundation\Watson;
use Watson\Parsers\OptionFieldsParser;

class CategoryData
{
    private $categories;

    public function __construct($categories)
    {
        $this->categories = explode(',', trim($categories, '"'));
    }

    public function create()
    {
        foreach ($this->categories as $category) {

        }

        echo json_encode(['url' => \rex_url::backendPage('modules/modules', ['function' => 'edit', 'module_id' => $this->id], false)]);
    }

}
