<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all categories.
     */
    public function test_get_all_categories(): void
    {
        // Arrange
        Category::factory(5)->create();

        // Act
        $response = $this->get(route('categories'));

        // Assert
        $response->assertStatus(200)->assertJsonCount(5, 'categories');
    }

    /**
     * Test filtering categories.
     */
    public function test_filter_categories(): void
    {
        // Arrange
        Category::factory(5)->create();

        // Act
        $response = $this->get(route('categories'), [
            'name' => 'Category'
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonCount(5, 'categories');
    }

    /**
     * Test getting category tree.
     */
    public function test_get_category_tree(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->get(route('category_tree', $category->id));

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['category_tree']);
    }

    /**
     * Test storing a new category.
     */
    public function test_store_category(): void
    {
        // Act
        $response = $this->post(route('store_category'), [
            'name' => 'Test Category',
            'description' => 'Test Description',
            'parent_id' => null
        ]);

        // Assert
        $response->assertStatus(201)->assertJsonStructure(['category']);
    }

    /**
     * Test updating a category.
     */
    public function test_update_category(): void
    {
        // Arrange
        // Create a category using the factory
        $category = Category::factory()->create();

        // Act
        // Send a PUT request to update the category
        $updatedData = [
            'name' => 'Updated Category Name',
            'description' => 'Updated category description',
            'parent_id' => 2, // Updated parent category ID
        ];
        
        $response = $this->put(route('update_category', ['id' => $category->id]), $updatedData);

        // Assert
        // Check that the response status is 200 OK and it contains the 'category' key in the JSON structure
        $response->assertStatus(200)->assertJsonStructure(['category']);
    }

    /**
     * Test partially updating a category.
     */
    public function test_partial_update_category(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->patch(route('partial_update_category', $category->id), [
            'name' => 'Updated Category Name'
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['category']);
    }

    /**
     * Test deleting a category.
     */
    public function test_delete_category(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->delete(route('delete_category', $category->id));

        // Assert
        $response->assertStatus(200)->assertJson(['message' => 'Category deleted successfully']);
    }
}
