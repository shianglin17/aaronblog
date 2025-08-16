<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Article 文章模型
 *
 * @property int $id
 * @property string $title 文章標題
 * @property string|null $description 文章描述
 * @property string $content 文章內容
 * @property string $status 文章狀態: draft(草稿), published(已發佈)
 * @property int $category_id 分類 ID
 * @property int $user_id 作者 ID
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Category $category
 * @property-read User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 */
class Article extends Model
{
    use HasFactory;

    /**
     * 文章狀態：草稿
     */
    public const STATUS_DRAFT = 'draft';

    /**
     * 文章狀態：已發佈
     */
    public const STATUS_PUBLISHED = 'published';

    /**
     * 可批量賦值的屬性
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'status',
        'category_id',
        'user_id'
    ];

    /**
     * 屬性轉換
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * 檢查文章是否為草稿
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * 檢查文章是否已發佈
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * 同步文章標籤
     * 
     * 統一處理文章標籤的多對多關聯同步邏輯
     * 消除服務層重複代碼
     * 
     * @param array $data 包含標籤資料的陣列
     * @return void
     */
    public function syncTagsFromData(array $data): void
    {
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->tags()->sync($data['tags']);
        }
    }
}