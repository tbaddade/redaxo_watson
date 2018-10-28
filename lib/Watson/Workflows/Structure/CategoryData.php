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

class CategoryData
{
    private $categories;
    private $categoryId;
    private $clangId;
    private $status;

    public function __construct($categories)
    {
        $this->categories = explode(',', trim($categories, '"'));
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function setClangId($clangId)
    {
        $this->clangId = $clangId;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function create()
    {
        foreach ($this->categories as $category) {
            $data = [];
            $data['catpriority'] = 1;
            $data['catname'] = $category;
            $data['name'] = $category;
            $data['status'] = $this->status;
            \rex_category_service::addCategory($this->categoryId, $data);
        }
        echo json_encode(['url' => \rex_url::backendPage('structure', ['category_id' => $this->categoryId, 'clang' => $this->clangId], false)]);
    }
}
