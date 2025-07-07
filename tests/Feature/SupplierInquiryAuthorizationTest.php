<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\SupplierInquiry;
use App\Models\SupplierInquiryResponse;
use App\Models\QuotationRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SupplierInquiryAuthorizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $supplier;
    protected $admin;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $supplierRole = Role::create(['name' => 'supplier', 'display_name' => 'Supplier']);
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);

        // Create supplier user
        $this->supplier = User::factory()->create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
        ]);
        $this->supplier->role()->associate($supplierRole);
        $this->supplier->save();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
        ]);
        $this->admin->role()->associate($adminRole);
        $this->admin->save();

        // Create category
        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test category for testing'
        ]);

        // Create product
        $this->product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'description' => 'Test product description',
            'category_id' => $this->category->id,
            'price_aed' => 100.00,
        ]);
    }

    /** @test */
    public function it_prioritizes_new_supplier_inquiry_over_legacy_quotation_request()
    {
        // Create a legacy QuotationRequest with ID 1
        $legacyInquiry = QuotationRequest::create([
            'id' => 1, // Force ID to be 1
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'quantity' => 10,
            'status' => 'pending',
            'supplier_response' => 'pending',
        ]);

        // Create a new SupplierInquiry with the same ID (1)
        $newInquiry = SupplierInquiry::create([
            'id' => 1, // Force ID to be 1 (same as legacy)
            'product_id' => $this->product->id,
            'quantity' => 5,
            'status' => 'broadcast',
            'reference_number' => 'INQ-2025-00001',
            'broadcast_at' => now(),
        ]);

        // Create supplier response for the new inquiry
        SupplierInquiryResponse::create([
            'supplier_inquiry_id' => $newInquiry->id,
            'user_id' => $this->supplier->id,
            'status' => 'pending',
        ]);

        // Act as supplier and try to access the inquiry
        $response = $this->actingAs($this->supplier)
            ->get('/supplier/inquiries/1');

        // Should return 200 (success) and show the new inquiry, not the legacy one
        $response->assertStatus(200);
        
        // The response should contain the new inquiry's reference number
        $response->assertSee('INQ-2025-00001');
        
        // Should not contain legacy inquiry data
        $response->assertDontSee('QR-');
    }

    /** @test */
    public function it_returns_403_for_unauthorized_access_to_new_inquiry()
    {
        // Create a new SupplierInquiry
        $inquiry = SupplierInquiry::create([
            'product_id' => $this->product->id,
            'quantity' => 5,
            'status' => 'broadcast',
            'reference_number' => 'INQ-2025-00002',
            'broadcast_at' => now(),
        ]);

        // Don't create a supplier response (no access)

        // Act as supplier and try to access the inquiry
        $response = $this->actingAs($this->supplier)
            ->get('/supplier/inquiries/' . $inquiry->id);

        // Should return 403 (unauthorized)
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_403_for_unauthorized_access_to_legacy_inquiry()
    {
        // Create a legacy QuotationRequest for a different supplier
        $otherSupplier = User::factory()->create([
            'name' => 'Other Supplier',
            'email' => 'other@test.com',
        ]);
        $otherSupplier->role()->associate(Role::where('name', 'supplier')->first());
        $otherSupplier->save();

        $legacyInquiry = QuotationRequest::create([
            'product_id' => $this->product->id,
            'supplier_id' => $otherSupplier->id, // Different supplier
            'quantity' => 10,
            'status' => 'pending',
            'supplier_response' => 'pending',
        ]);

        // Act as current supplier and try to access the inquiry
        $response = $this->actingAs($this->supplier)
            ->get('/supplier/inquiries/' . $legacyInquiry->id);

        // Should return 403 (unauthorized)
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_inquiry()
    {
        // Act as supplier and try to access a non-existent inquiry
        $response = $this->actingAs($this->supplier)
            ->get('/supplier/inquiries/99999');

        // Should return 404 (not found)
        $response->assertStatus(404);
    }
} 