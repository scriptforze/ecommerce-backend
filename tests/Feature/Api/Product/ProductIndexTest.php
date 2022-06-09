<?php

namespace Tests\Feature\Api\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testGetAllProducts()
    {
        $response = $this->json('GET', route('api.v1.products.index'));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'slug',
                    'short_description',
                    'description',
                ]
            ]
        ]);
    }
}