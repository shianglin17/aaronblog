<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Article 文章模型
 *
 * @property int $id
 * @property string $title 文章標題
 * @property string $content 文章內容
 * @property int $category_id 分類 ID
 * @property int $user_id 作者 ID
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read Category $category
 * @property-read User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 */
class Article extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 可批量賦值的屬性
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
    ];

    /**
     * 應該被轉換成日期的屬性
     *
     * @var array<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * 獲取文章所屬分類
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 獲取文章作者
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 獲取文章的所有標籤
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withTimestamps();
    }
}