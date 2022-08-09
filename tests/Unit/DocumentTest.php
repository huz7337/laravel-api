<?php

namespace Tests\Unit;

use App\Models\Document;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_document_creation()
    {
        Document::factory()->create();
        $this->assertDatabaseCount(Document::class, 3);
    }
}
