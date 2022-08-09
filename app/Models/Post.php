<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'slug', 'active', 'category_id'
    ];

    /**
     * Find user by email or return an error
     * @param string $slug
     * @return Post
     * @throws ModelNotFoundException
     */
    public static function findBySlug(string $slug): Post
    {
        return self::where('slug', $slug)->firstOrFail();
    }

    /**
     * The post's attachment details
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
