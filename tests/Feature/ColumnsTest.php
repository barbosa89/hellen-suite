<?php

namespace Tests\Feature;

use App\Helpers\Columns;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColumnsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    public function test_get_model_columns_in_array_format()
    {
        $this->assertEquals(
            ['id', 'description', 'slug', 'user_id', 'created_at', 'updated_at'],
            Columns::get('tags', 'sqlite')
        );
    }

    public function test_get_empty_array_when_model_is_unknown()
    {
        $this->assertEquals([], Columns::get('unknown', 'sqlite'));
    }
}
