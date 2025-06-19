<?php

namespace App\Repositories;

use App\Models\Tag;

/**
 * @extends BaseRepository<Tag>
 */
class TagRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Tag();
    }
} 