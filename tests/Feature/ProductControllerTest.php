<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method to return a paginated list of products.
     */
    public function test_index_returns_paginated_products()
    {
        // Create some products
        Product::factory()->count(5)->create();

        // Send the GET request
        $response = $this->actingAsAdmin()->getJson('/api/products');

        // Assert status and structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'price', 'quality', 'sell_in', 'image'], // Ensure 'image' is included
                     ],
                     'meta',
                     'links',
                 ]);
    }

    /**
     * Test the update method to upload a product image.
     */
    public function test_update_product_image_upload()
    {
        // Mock Cloudinary service
        Cloudinary::shouldReceive('upload')
            ->once()
            ->andReturn((object)[
                'getSecurePath' => 'https://cloudinary.test/path/to/image.jpg'
            ]);

        // Create a product
        $product = Product::factory()->create();

        // Simulate file upload
        Storage::fake('cloudinary'); // Ensure we fake the cloudinary disk
        $file = UploadedFile::fake()->image('product.jpg');

        // Send the PUT request to update the image
        $response = $this->actingAsAdmin()->putJson("/api/products/{$product->id}", [
            'image' => $file,
        ]);

        // Assert status and response structure
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id', 'name', 'image',
                     ],
                 ]);

        // Refresh the product instance to get updated values
        $this->assertEquals('https://cloudinary.test/path/to/image.jpg', $product->fresh()->image);
    }

    /**
     * Test the update method to handle upload failure.
     */
    public function test_update_product_image_upload_fails()
    {
        // Simulate Cloudinary failure
        Cloudinary::shouldReceive('upload')
            ->once()
            ->andThrow(new \Exception('Cloudinary error'));

        // Create a product
        $product = Product::factory()->create();

        // Simulate file upload
        Storage::fake('cloudinary');
        $file = UploadedFile::fake()->image('product.jpg');

        // Send the PUT request
        $response = $this->actingAsAdmin()->putJson("/api/products/{$product->id}", [
            'image' => $file,
        ]);

        // Assert status and error message
        $response->assertStatus(500)
                 ->assertJson([
                     'message' => 'Cloudinary error',
                 ]);
    }

    /**
     * Helper function to authenticate a test user with the basic auth.
     */
    protected function actingAsAdmin()
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);
        return $this->actingAs($user)->withHeader('Authorization', 'Basic ' . base64_encode('admin@example.com:admin'));
    }
}
