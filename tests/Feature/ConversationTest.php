<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ConversationTest extends TestCase
{

    public function test_list_conversations()
    {
        $this->get('/api/conversations')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->whereAllType([
                'currentPage' => 'integer',
                'perPage' => 'integer',
                'lastPage' => 'integer',
                'total' => 'integer',
                'items' => 'array',
            ])->etc())
            ->assertJsonPath('currentPage', 1)
            ->assertJsonPath('perPage', 30);
    }


    public function test_mark_as_read()
    {
        $randomSid = Str::random(34);
        $this->post("/api/conversations/{$randomSid}/read")
            ->assertNoContent();
    }

}
