<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'slug'
    ];


    /**
     * Find user by email or return an error
     * @param string $slug
     * @return User
     * @throws ModelNotFoundException
     */
    public static function findBySlug(string $slug): Page
    {
        return self::where('slug', $slug)->firstOrFail();
    }
}
