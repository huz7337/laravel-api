<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class PostsService
{
    /**
     * Add a new page
     * @param array $data
     * @return Post
     */
    public function createPost(array $data): Post
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $post = Post::create($data);

        return $post;
    }

    /**
     * Update new page
     * @param array $data
     * @return Post
     */
    public function updatePost(Post $post, array $data): Post
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['active'] = $data['active'] ?? 0;
        $post->update($data);

        return $post;
    }

}
