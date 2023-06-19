<?php

namespace Tests\Feature\API;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductPhotosControllerTest extends TestCase
{
    use RefreshDatabase;
   
    public function test_should_post_product_photos_endpoint_should_save_a_photo_by_upload(): void
    {
        $token = $this->makeUserToken();
        $product = Product::factory()->create();

        $image = UploadedFile::fake()->image('produto-foto.jpg');

        $response = $this->post(
            '/api/products/1/photos',
            [
                'photos' => [
                    $image
                ]
            ],
            [
                'Content-Type' => 'application/form-data',
                'Authorization' => 'Bearer ' . $token
            ]
        );

        Storage::disk('public')->assertExists('products/' . $image->hashName());

        $this->assertEquals('products/' . $image->hashName(), $product->photos->first()->photo);
    }

    public function test_should_validate_upload_product_photos_as_image_mime_type()
    {
        $token = $this->makeUserToken();
        $product = Product::factory()->create();

        $pdf = UploadedFile::fake()->create('book.pdf', 1024, 'application/pdf');

        $response = $this->post(
            '/api/products/1/photos',
            [
                'photos' => [
                    $pdf
                ]
            ],
            [
                'Content-Type' => 'application/form-data',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertUnprocessable();
        $response->dump();
        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);
        });

        $response->assertJsonValidationErrorFor('photos.0');

        $this->assertEquals('Arquivo de imagem inválido!', $response->json('errors')['photos.0'][0]);

    }

    public function test_should_validate_payload_data_when_new_photo()
    {
        $token = $this->makeUserToken();

        $response = $this->postJson('/api/products', [], ['Authorization' => 'Bearer' . $token]);

        $response->assertUnprocessable();
    }
}
