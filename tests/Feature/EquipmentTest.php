<?php

namespace Tests\Feature;

use App\Models\Equipment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EquipmentTest extends TestCase
{

    public function test_list_equipment()
    {
        Equipment::factory()->count(5)->create();

        $this->get('/api/equipment')
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

    public function test_create_equipment()
    {
        $data = Equipment::factory()->make()->toArray();
        Storage::fake('avatars');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $data['photo'] = $file;

        $this->post('/api/equipment', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'description' => 'string',
                'photo' => 'string'
            ])->etc())
            ->assertJsonPath('name', $data['name']);

        $equipment = Equipment::where('name', $data['name'])->first();
        if ($equipment->photo) {
            Storage::disk('s3')->delete($equipment->photo);
        }
    }


    public function test_show_equipment()
    {
        $equipment = Equipment::factory()->create();

        $this->get("/api/equipment/{$equipment->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'description' => 'string|null',
                'photo' => 'string|null'
            ])->etc())
            ->assertJsonPath('name', $equipment->name);
    }


    public function test_update_equipment()
    {
        $equipment = Equipment::factory()->create();

        $this->post("/api/equipment/{$equipment->id}", ['name' => 'Edited equipment'])
            ->assertOk()
            ->assertJsonPath('id', $equipment->id)
            ->assertJsonPath('name', 'Edited equipment');
    }


    public function test_delete_equipment()
    {
        $equipment = Equipment::factory()->create();

        $this->delete("/api/equipment/{$equipment->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The equipment has been removed.'));
    }

}
