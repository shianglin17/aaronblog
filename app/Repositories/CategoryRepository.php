<?php

namespace App\Repositories;

use App\Models\Category;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Category();
    }
} 