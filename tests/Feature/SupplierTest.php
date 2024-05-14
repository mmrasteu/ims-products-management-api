<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_get_all_suppliers(): void
    {
        // Create 5 fake suppliers in the database
        Supplier::factory(5)->create();

        // Make a request to get all suppliers
        $response = $this->get(route('suppliers'));

        // Assert that the request is successful and returns an HTTP status code 200
        $response->assertStatus(200);

        // Verify that the response contains the expected number of suppliers in JSON format
        $response->assertJsonCount(5, 'suppliers');
    }

    /**
     * Test filtering suppliers.
     */
    public function test_filter_suppliers(): void
    {
        // Arrange
        $supplier = Supplier::factory()->create();

        // Act
        $response = $this->post(route('suppliers_filter'), [
            'name' => $supplier->name
        ]);

        // Assert
        $response->assertStatus(200)->assertJsonStructure(['suppliers']);
    }

    /**
     * A basic feature test example.
     */
    public function test_get_single_supplier(): void
    {
        // Create a fake supplier in the database
        $supplier = Supplier::factory()->create();

        // Make a request to get a specific supplier by its ID
        $response = $this->get(route('supplier', ['id' => $supplier->id]));

        // Assert that the request is successful and returns an HTTP status code 200
        $response->assertStatus(200);

        // Verify that the response contains the expected supplier in JSON format
        $response->assertJson(['supplier' => $supplier->toArray()]);
    }

    /**
     * A basic feature test example.
     */
    public function test_store_supplier(): void
    {
        // Define data for a new supplier
        $data = [
            'name' => 'Test Supplier',
            'cif' => 'A12345678',            
            'description' => 'Description', 
            'email' => 'supplier@example.com', 
            'phone' => '123456789', 
            'address' => '123  Street', 
            'location' => ' City', 
            'zip_code' => '12345', 
            'contact_name' => ' Contact Name', 
            'contact_title' => ' Contact Title', 
            'notes' => 'Notes', 
        ];

        // Make a request to create a new supplier
        $response = $this->post(route('store_supplier'), $data);

        // Assert that the request is successful and returns an HTTP status code 201
        $response->assertStatus(201);

        // Verify that the response contains the created supplier in JSON format
        $response->assertJsonFragment(['name' => 'Test Supplier']);
    }

    /**
     * A basic feature test example.
     */
    public function test_update_supplier(): void
    {
        // Create a fake supplier in the database
        $supplier = Supplier::factory()->create();

        // Define updated data for the supplier
        $updatedData = [
            'name' => 'Updated Supplier',
            'cif' => 'B87654321',
            'description' => 'Updated description',
            'email' => 'updated@example.com',
            'phone' => '123456789',
            'address' => '123 Updated Street',
            'location' => 'Updated City',
            'zip_code' => '12345',
            'contact_name' => 'Updated Contact Name',
            'contact_title' => 'Updated Contact Title',
            'notes' => 'Updated notes',
        ];

        // Make a request to update the supplier
        $response = $this->put(route('update_supplier', ['id' => $supplier->id]), $updatedData);

        // Assert that the request is successful and returns an HTTP status code 200
        $response->assertStatus(200);

        // Verify that the response contains the updated supplier in JSON format
        $response->assertJsonFragment(['name' => 'Updated Supplier']);
    }

    /**
     * A basic feature test example.
     */
    public function test_partial_update_supplier(): void
    {
        // Create a fake supplier in the database
        $supplier = Supplier::factory()->create();

        // Define partially updated data for the supplier
        $partialUpdatedData = [
            'name' => 'Partially Updated Supplier'
        ];

        // Make a request to partially update the supplier
        $response = $this->patch(route('partial_update_supplier', ['id' => $supplier->id]), $partialUpdatedData);

        // Assert that the request is successful and returns an HTTP status code 200
        $response->assertStatus(200);

        // Verify that the response contains the updated supplier in JSON format
        $response->assertJsonFragment(['name' => 'Partially Updated Supplier']);
    }

    /**
     * A basic feature test example.
     */
    public function test_delete_supplier(): void
    {
        // Create a fake supplier in the database
        $supplier = Supplier::factory()->create();

        // Make a request to delete the supplier
        $response = $this->delete(route('delete_supplier', ['id' => $supplier->id]));

        // Assert that the request is successful and returns an HTTP status code 200
        $response->assertStatus(200);
    }
}
