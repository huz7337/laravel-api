<?php

namespace Tests\Feature;

use App\Models\InfluencerPost;
use App\Models\InfluencerPostComment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InfluencerTest extends TestCase
{

    public function test_list_influencer_all_posts()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        Sanctum::actingAs($user);
        $this->get("/api/influencer/post")
            ->assertOk()
            ->assertJsonPath('currentPage', 1);
    }

    public function test_list_influencer_posts_by_user()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        Sanctum::actingAs($user);
        $this->get("/api/influencer/post/{$user->id}")
            ->assertOk()
            ->assertJsonPath('currentPage', 1);
    }

    public function test_create_influencer_post()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $data = InfluencerPost::factory()->make()->toArray();
        Storage::fake('attachment');
        $file = UploadedFile::fake()->image('attachment.jpg');
        $data['attachments']['file'] = [$file];

        Sanctum::actingAs($user);
        $this->post('/api/influencer/post', $data)
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->whereAllType([
                'id' => 'integer',
                'description' => 'string',
                'attachments' => 'array'
            ])->etc());

        $post = InfluencerPost::first();
        $attachment = $post->attachments()->first();

        if ($attachment->getAttributes()) {
            Storage::disk('s3')->delete($attachment->getAttributes()['file']);
        }
    }

    public function test_update_influencer_post()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        Storage::fake('attachment');
        $file = UploadedFile::fake()->image('attachment.jpg');
        $data['attachments']['file'] = [$file];

        Sanctum::actingAs($user);
        $this->post("/api/influencer/post/{$post->id}", $data)
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->whereAllType([
                'id' => 'integer',
                'description' => 'string',
                'attachments' => 'array'
            ])->etc());

        $post = InfluencerPost::first();
        $attachment = $post->attachments()->first();

        if ($attachment->getAttributes()['file']) {
            Storage::disk('s3')->delete($attachment->getAttributes()['file']);
        }
    }

    public function test_delete_influencer_post()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user);
        $this->delete("/api/influencer/post/{$post->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The post :id has been removed.', ['id' => $post->id]));
    }

    public function test_list_influencer_comments()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user);
        $this->get("/api/influencer/comment/all/{$post->id}")
            ->assertOk()
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }

    public function test_show_influencer_post_comment()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        $comment = InfluencerPostComment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        Sanctum::actingAs($user);
        $this->get("/api/influencer/comment/{$comment->id}")
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->whereAllType([
                'body' => 'string'
            ])->etc())
            ->assertJsonPath('body', $comment->body);
    }

    public function test_create_influencer_post_comment()
    {
        $data = InfluencerPostComment::factory()->make()->toArray();
        Storage::fake('attachment');
        $file = UploadedFile::fake()->image('attachment.jpg');
        $data['attachments']['file'] = [$file];

        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user);
        $this->post("/api/influencer/comment/create/{$post->id}", $data)
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->whereAllType([
                'id' => 'integer',
                'body' => 'string',
                'attachments' => 'array'
            ])->etc());

        $comment = InfluencerPostComment::first();
        $attachment = $comment->attachments()->first();

        if ($attachment->getAttributes()['file']) {
            Storage::disk('s3')->delete($attachment->getAttributes()['file']);
        }
    }

    public function test_update_influencer_post_comment()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);
        $data = InfluencerPostComment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ])->toArray();

        Storage::fake('attachment');
        $file = UploadedFile::fake()->image('attachment.jpg');
        $data['attachments']['file'] = [$file];

        Sanctum::actingAs($user);
        $this->post("/api/influencer/comment/update/{$data['id']}", $data)
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->whereAllType([
                'id' => 'integer',
                'body' => 'string',
                'attachments' => 'array'
            ])->etc());

        $comment = InfluencerPostComment::first();
        $attachment = $comment->attachments()->first();

        if ($attachment->getAttributes()['file']) {
            Storage::disk('s3')->delete($attachment->getAttributes()['file']);
        }
    }

    public function test_delete_influencer_post_comment()
    {
        $user = User::role(User::ROLE_INFLUENCER)->first();
        $post = InfluencerPost::factory()->create([
            'user_id' => $user->id
        ]);

        $comment = InfluencerPostComment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        Sanctum::actingAs($user);
        $this->delete("/api/influencer/comment/{$comment->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The comment :id has been removed.', ['id' => $comment->id]));
    }
}
