<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryTest extends TestCase
{
//    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public  function create_demo()
    {
        return $this->json('POST', 'api/product', [
//            'id'=>1,
            'name' => 'Test Product',
            'category_id' => 1,
            'brand_id' => 1,
            'description' => 'This is a test product',
            'is_publish' => 1,
            'product_image' => UploadedFile::fake()->image('avatar.jpg'),
            'variations' => [
                [
                    'size_id' => 1,
                    'color_id' => 1,
                    'unit_price' => 100,
                    'stock' => 10,
                    'discount' => 10,
                    'quantity' => 10,
                    'discount_type' => 'percentage',
                    'discount_start_date' => '2022-05-13',
                    'discount_end_date' => '2022-05-13',
                    'variant_images' => [
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg')
                    ]
                ]
            ]
        ]);
    }
    public function test_can_create_product()
    {
        $this->withoutExceptionHandling();

        $user=User::find(1);

        //$id=User::Where('id',1)->value('id');


        $this->actingAs($user, 'api');
        $this->withoutMiddleware();
        $response = $this->create_demo();

        if(!$user)
        {
            $response->assertStatus(500);
        }
        else
        {
            $response->assertStatus(201);
        }

    }

    public function test_can_update_product()
    {
        $this->withoutExceptionHandling();
//        $user = User::factory()->create();
//        Brand::factory()->create();
//        Category::factory()->create();
//        Color::factory()->create();
//        Size::factory()->create();


        $user=User::find(3);
        $this->actingAs($user, 'api');
        $this->withoutMiddleware();

        $this->create_demo();

        $response = $this->json('PUT', 'api/product/5', [
//            'id'=>1,
            'name' => 'Test Product',
            'category_id' => 1,
            'brand_id' => 1,
            'description' => 'This is a test product',
            'is_publish' => 1,
            'product_image' => UploadedFile::fake()->image('avatar.jpg'),
            'variations' => [
                [
                    'size_id' => 1,
                    'color_id' => 1,
                    'unit_price' => 100,
                    'stock' => 10,
                    'discount' => 10,
                    'quantity' => 10,
                    'discount_type' => 'percentage',
                    'discount_start_date' => '2022-05-13',
                    'discount_end_date' => '2022-05-13',
                    'variant_images' => [
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg'),
                        UploadedFile::fake()->image('avatar.jpg')
                    ]
                ]
            ]
        ]);
        if(!$user)
        {
            $response->assertStatus(404);

        }
        else
        {
            $response->assertStatus(201);
        }


    }

    public function test_can_delete_product()
    {
        $this->withoutExceptionHandling();
//        $user = User::factory()->create();
//        Brand::factory()->create();
//        Category::factory()->create();
//        Color::factory()->create();
//        Size::factory()->create();

        $user=User::find(3);
        $this->actingAs($user, 'api');
        $this->withoutMiddleware();

//         $this->create_demo();


//        $this->assertDatabaseHas('products', [
//            'id' =>1
//        ]);


        $response = $this->json('DELETE', 'api/product/5');

        if(!$user)
        {
            $response->assertStatus(404);
        }
        else
        {
            $response->assertStatus(200);
        }


        $this->assertDatabaseMissing('products', [
            'id' => 1
        ]);
    }


}
