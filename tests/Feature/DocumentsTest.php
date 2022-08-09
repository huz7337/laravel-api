<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DocumentsTest extends TestCase
{

    public function test_list_all_documents()
    {

        $this->get('/api/documents', $this->_headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->whereAllType([
                    'currentPage' => 'integer',
                    'perPage' => 'integer',
                    'lastPage' => 'integer',
                    'total' => 'integer',
                    'items.0.id' => 'integer',
                    'items.0.type' => 'string',
                    'items.0.title' => 'string',
                    'items.0.content' => 'string',
                    'items.0.created_at' => 'string',
                    'items.0.updated_at' => 'string|null',
                ])->etc()
            );
        ;
    }

    public function test_show_document()
    {
        $this->get('/api/documents/1', $this->_headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->whereAllType([
                    'id' => 'integer',
                    'type' => 'string',
                    'title' => 'string',
                    'content' => 'string',
                    'created_at' => 'string',
                    'updated_at' => 'string|null',
                ])->etc()
            );
    }


    public function test_get_inexistent_document()
    {
        $this->get('/api/documents/100', $this->_headers)
            ->assertNotFound();
    }


    public function test_latest_terms()
    {
        $this->get('/api/documents/terms/latest')
            ->assertOk();
    }


    public function test_latest_privacy_policy()
    {
        $this->get('/api/documents/privacy/latest', $this->_headers)
            ->assertOk();
    }


    public function test_create_new_terms()
    {
        $data = Document::factory()->make();

        $this->put('/api/documents', $data->toArray())
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->whereAllType([
                    'id' => 'integer',
                    'type' => 'string',
                    'title' => 'string',
                    'content' => 'string',
                    'created_at' => 'string',
                    'updated_at' => 'string|null',
                ])->etc()
            );
    }


    public function test_update_document()
    {
        $data = [
            'title' => 'Updated Terms & Conditions'
        ];

        $this->post('/api/documents/1', $data)
            ->assertOk()
            ->assertJsonPath('title', $data['title']);
    }
}
