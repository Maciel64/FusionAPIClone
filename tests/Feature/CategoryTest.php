<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CategoryTest extends TestCase
{
  use RefreshDatabase;
  
  public function setUp(): void
  {
    parent::setUp();
  }

  public function test_category_index()
  {
    $this->login();
    Category::factory(3)->create();
    $response = $this->get(route('category.index'));
    $response->assertStatus(200);
    $this->assertCount(3, $response->json('data'));
    $this->assertEquals(__('response.list.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_category_store()
  {
    $this->login();
    $response = $this->post(route('category.store'), [
      'name' => 'test',
      'description' => 'test',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(__('response.store.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_category_show()
  {
    $this->login();
    $category = Category::factory()->create();
    $response = $this->get(route('category.show', ['uuid' => $category->uuid]));

    $response->assertStatus(200);
    $this->assertEquals(__('response.show.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_category_update()
  {
    $this->login();
    $category = Category::factory()->create();
    $response = $this->put(route('category.update', ['uuid' => $category->uuid]), [
      'name' => 'test updated',
      'description' => 'test',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(__('response.update.success', ['resource' => 'Category']), $response->json('message'));
  }

  public function test_category_destroy()
  {
    $this->login();
    $category = Category::factory()->create();
    $response = $this->delete(route('category.destroy', ['uuid' => $category->uuid]));
    $response->assertStatus(200);
    $this->assertEquals(__('response.destroy.success', ['resource' => 'Category']), $response->json('message'));
  }
}
