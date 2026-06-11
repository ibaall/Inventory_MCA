<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Customer;

class MasterDataController extends Controller
{
    /**
     * Halaman utama Master Data (Supplier & Customer dalam satu halaman)
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('master-data.index', compact('suppliers', 'customers'));
    }

    // ===== SUPPLIER =====

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:suppliers,name',
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        Supplier::create($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function updateSupplier(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        $supplier->update($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroySupplier(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('master-data.index')->with('success', 'Supplier berhasil dihapus.');
    }

    // ===== CUSTOMER =====

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:customers,name',
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        Customer::create($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'alamat'  => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:50',
        ]);

        $customer->update($request->only('name', 'alamat', 'telepon'));

        return redirect()->route('master-data.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroyCustomer(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('master-data.index')->with('success', 'Customer berhasil dihapus.');
    }

    // ===== API: JSON untuk dropdown AJAX =====

    public function getSuppliers()
    {
        return response()->json(Supplier::orderBy('name')->get());
    }

    public function getCustomers()
    {
        return response()->json(Customer::orderBy('name')->get());
    }
}
