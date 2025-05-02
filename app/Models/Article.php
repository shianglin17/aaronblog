<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $fillable = ["id", "created_at", "updated_at", "title", "content", "tag_ids"];
}