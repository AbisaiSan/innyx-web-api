<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
   
    public function test_if_products_get_endpoint_list_all_products(): void
    {
        Product::factory(3)->create();

        $response = $this->getJson('/api/products');

        $response//->dd()
        ->assertStatus(200);

        $response->assertJson(function(AssertableJson $json) {
            $json->hasAll(['data', 'meta', 'links']);
            $json->hasAll(['data.0.name', 'data.0.price']);
            $json->whereAllType([
                'data.0.name' => 'string', 
                'data.0.price' => 'integer'
            ]);
            $json->count('data', 3)->etc();
        });
    }

    public function test_if_products_get_endpoint_returns_a_single_product(): void
    {
        Product::factory(1)->create(['name' => 'Produto 1', 'price' => 399]);

        $response = $this->getJson('/api/products/1');

        $response->dd()
            ->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data');
            $json->hasAll(['data.name', 'data.price']);
            $json->whereAllType([
                'data.name' => 'string',
                'data.price' => 'integer'
            ]);
            $json->whereAll([
                'data.name' => 'Produto 1',
                'data.price' => 399
            ]);
        });
    }

    public function test_should_product_post_endpoint_throw_an_unauthorized_status(): void
    {
        $response = $this->postJson('/api/products', []);

        $response
            ->assertUnauthorized();
    }

    public function test_should_validate_payload_data_when_new_product()
    {
        $token = User::factory()->create();
        $token = $token->createToken('default')->plainTextToken;

        $response = $this->postJson('/api/products', [], ['Authorization' => 'Bearer' . $token]);

        $response->assertUnprocessable();
    }

    public function test_should_product_post_endpoint_create_a_new_product(): void
    {
        $product = [
            'name' => 'Produto teste 5',
            'price' => 4999,
            'description' => 'Descrição do produto',
            
        ];

        $token = User::factory()->create();
        $token = $token->createToken('default')->plainTextToken;

        $response = $this->postJson('/api/products', $product, ['Authorization' => 'Bearer' . $token]);

        $response->assertCreated();

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data');
            $json->hasAll(['data.name', 'data.price']);
            $json->whereAllType([
                'data.name' => 'string',
                'data.price' => 'integer'
            ]);
            $json->whereAll([
                'data.name' => 'Produto teste 5',
                'data.price' => 4999
            ]);
        });

    }

    public function test_should_product_put_endpoint_throw_an_unauthorized_status(): void
    {
        Product::factory()->create(['name' => 'Produto put', 'price' => 199]);
        $response = $this->putJson('/api/products/1', []);

        $response
            ->assertUnauthorized();
    }

    public function test_should_product_put_endpoint_create_a_new_product(): void
    {
        Product::factory()->create(['name' => 'Produto put', 'price' => 199]);

        $productUpdate = [
            'name' => 'Produto teste atualizado5',
            'price' => 99,
        ];

        $token = User::factory()->create();
        $token = $token->createToken('default')->plainTextToken;

        $response = $this->putJson('/api/products/1', $productUpdate, ['Authorization' => 'Bearer' . $token]);

        $response->assertCreated();

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data');
            $json->hasAll(['data.name', 'data.price']);
            $json->whereAllType([
                'data.name' => 'string',
                'data.price' => 'integer'
            ]);
            $json->whereAll([
                'data.name' => 'Produto teste atualizado5',
                'data.price' => 99
            ]);
        });
    }

    public function test_should_product_delete_endpoint_throw_an_unauthorized_status(): void
    {
        Product::factory()->create(['name' => 'Produto put', 'price' => 199]);
        $response = $this->deleteJson('/api/products/1', []);

        $response
            ->assertUnauthorized();
    }

    public function test_should_product_delete_endpoint_remove_a_product(): void
    {
        Product::factory()->create(['name' => 'Produto put', 'price' => 199]);

        $token = User::factory()->create();
        $token = $token->createToken('default')->plainTextToken;

        $response = $this->deleteJson('/api/products/1', [], ['Authorization' => 'Bearer' . $token]);

        $response->assertNoContent();

        $response = $this->getJson('/api/products/1');
        $response->assertNotFound();

    }
}
