<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_cart_index_passes_saved_addresses()
    {
        Order::create([
            'user_id' => $this->user->id,
            'customer_name' => 'John Doe',
            'alamat' => 'Alamat Indah 123',
            'total_price' => 100000,
            'ordered_at' => now(),
            'status_pembayaran' => 'belum dibayar',
        ]);

        $response = $this->actingAs($this->user)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewHas('savedAddresses', function ($addresses) {
            return $addresses->contains('Alamat Indah 123');
        });
    }

    public function test_order_edit_passes_saved_addresses()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'customer_name' => 'John Doe',
            'alamat' => 'Alamat Indah 123',
            'total_price' => 100000,
            'ordered_at' => now(),
            'status_pembayaran' => 'belum dibayar',
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.edit', $order->id));

        $response->assertStatus(200);
        $response->assertViewHas('savedAddresses', function ($addresses) {
            return $addresses->contains('Alamat Indah 123');
        });
    }

    public function test_purchase_order_create_passes_saved_addresses()
    {
        PurchaseOrder::create([
            'user_id' => $this->user->id,
            'supplier_name' => 'Supplier A',
            'alamat' => 'Alamat Supplier 456',
            'total_price' => 200000,
            'ordered_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('purchase-orders.create'));

        $response->assertStatus(200);
        $response->assertViewHas('savedAddresses', function ($addresses) {
            return $addresses->contains('Alamat Supplier 456');
        });
    }

    public function test_checkout_saves_alamat()
    {
        $product = Product::create([
            'name' => 'Product Test',
            'kode_barang' => 'TEST-001',
            'stock' => 10,
            'price' => 50000,
            'category' => 'Test',
        ]);

        $response = $this->actingAs($this->user)->post(route('cart.add', $product->id), [
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Smith',
            'alamat' => 'Jalan Kebangsaan No. 5',
            'status_pembayaran' => 'belum dibayar',
            'metode_pembayaran' => 'transfer',
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Smith',
            'alamat' => 'Jalan Kebangsaan No. 5',
        ]);
    }

    public function test_order_update_saves_pasien_and_alamat()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'customer_name' => 'John Doe',
            'total_price' => 100000,
            'ordered_at' => now(),
            'status_pembayaran' => 'belum dibayar',
        ]);

        $response = $this->actingAs($this->user)->put(route('orders.update', $order->id), [
            'status_pembayaran' => 'lunas',
            'metode_pembayaran' => 'qris',
            'nama_pasien' => 'Pasien A',
            'operator' => 'Dr. B',
            'tanggal_operasi' => '2026-05-27',
            'alamat' => 'Alamat Pasien C',
        ]);

        $response->assertRedirect(route('orders.index'));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status_pembayaran' => 'lunas',
            'metode_pembayaran' => 'qris',
            'nama_pasien' => 'Pasien A',
            'operator' => 'Dr. B',
            'tanggal_operasi' => '2026-05-27',
            'alamat' => 'Alamat Pasien C',
        ]);
    }

    public function test_purchase_order_store_saves_alamat()
    {
        $product = Product::create([
            'name' => 'Product Test PO',
            'kode_barang' => 'TEST-002',
            'stock' => 10,
            'price' => 45000,
            'category' => 'Test',
        ]);

        $response = $this->actingAs($this->user)->post(route('purchase-orders.store'), [
            'supplier_name' => 'Maju Jaya',
            'alamat' => 'Jalan Industri No. 10',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'price' => 45000,
                ]
            ]
        ]);

        $response->assertRedirect(route('purchase-orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'supplier_name' => 'Maju Jaya',
            'alamat' => 'Jalan Industri No. 10',
        ]);
    }
}
