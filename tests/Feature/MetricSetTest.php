<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\MetricSet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MetricSetTest extends TestCase
{

    public function test_list_metric_sets()
    {
        MetricSet::factory()->count(5)->create();

        $this->get('/api/metric-sets')
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

    public function test_create_metric_set()
    {
        $data = MetricSet::factory()->metrics()->make()->toArray();

        $this->post('/api/metric-sets', $data)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'description' => 'string',
                'metrics' => 'array',
                'metrics.0.id' => 'integer',
                'metrics.0.name' => 'string',
                'metrics.0.unit' => 'array|null',
                'metrics.0.unit.id' => 'integer',
                'metrics.0.unit.name' => 'string'
            ])->etc())
            ->assertJsonPath('name', $data['name']);
    }


    public function test_show_metric_set()
    {
        $metric = MetricSet::factory()->create();

        $this->get("/api/metric-sets/{$metric->id}")
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'id' => 'integer',
                'name' => 'string',
                'description' => 'string'
            ])->etc())
            ->assertJsonPath('name', $metric->name);
    }


    public function test_update_metric_set()
    {
        $metric = MetricSet::factory()->create();

        $this->post("/api/metric-sets/{$metric->id}", ['name' => 'Edited set'])
            ->assertOk()
            ->assertJsonPath('id', $metric->id)
            ->assertJsonPath('name', 'Edited set');
    }


    public function test_delete_metric_set()
    {
        $metric = MetricSet::factory()->create();

        $this->delete("/api/metric-sets/{$metric->id}")
            ->assertOk()
            ->assertJsonPath('message', __('The metric set has been removed.'));
    }

}
