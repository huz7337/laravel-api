<?php

namespace Tests\Feature;

use App\Models\Exercise;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExerciseTest extends TestCase
{

    public function test_list_exercises()
    {
        Exercise::factory()->count(5)->create();

        $this->get('/api/exercises')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'currentPage' => 'integer',
                'perPage' => 'integer',
                'lastPage' => 'integer',
                'total' => 'integer',
                'items' => 'array',
                'items.0.id' => 'integer',
                'items.0.name' => 'string',
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }

    public function test_create_warmup()
    {
        $data = Exercise::factory()->warmup()->make()->toArray();
        $data['video'] = UploadedFile::fake()->create('video.mp4', 5000, 'video/mp4');

        $this->post('/api/exercises', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'instructions' => 'string',
            ])->etc())
            ->assertJsonPath('name', $data['name']);
    }


    public function test_show_exercise()
    {
        $item = Exercise::factory()->create();

        $this->get("/api/exercises/{$item->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'instructions' => 'string'
            ])->etc())
            ->assertJsonPath('name', $item->name);
    }


    public function test_update_exercise()
    {
        $item = Exercise::factory()->create();

        $this->post("/api/exercises/{$item->id}", ['name' => 'Edited exercise'])
            ->assertOk()
            ->assertJsonPath('id', $item->id)
            ->assertJsonPath('name', 'Edited exercise');
    }


    public function test_delete_exercise()
    {
        $item = Exercise::factory()->create();

        $this->delete("/api/exercises/{$item->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The exercise has been removed.'));
    }

}
