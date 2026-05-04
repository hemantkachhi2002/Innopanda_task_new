<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WooCommerceApiTest extends TestCase
{
    /**
     * Test getting products list.
     */
    public function test_can_fetch_products()
    {
        $response = $this->getJson('/api/woocommerce/products');

       
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'fetched',
            'products'
        ]);
    }

  
    public function test_create_product_validation()
    {
        $response = $this->postJson('/api/woocommerce/products', []);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Validation failed'
        ]);
    }
}
