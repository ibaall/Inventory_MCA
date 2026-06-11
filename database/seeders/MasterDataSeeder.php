<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Customer;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Suppliers
        $suppliers = [
            ['name' => 'AMSA',    'alamat' => 'Kawasan Industri Jababeka, Bekasi',   'telepon' => '021-89835678'],
            ['name' => 'AUXEIN',  'alamat' => 'Sentul Industrial Park, Bogor',       'telepon' => '0251-8765432'],
            ['name' => 'FALCON',  'alamat' => 'Kawasan Industri Cikarang, Bekasi',   'telepon' => '021-89001234'],
            ['name' => 'ORTHO-X', 'alamat' => 'Tangerang Selatan, Banten',           'telepon' => '021-74567890'],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // Customers
        $customers = [
            ['name' => 'RS Santosa Bandung',  'alamat' => 'Jl. Kebon Jati No. 38, Bandung',  'telepon' => '022-4218888'],
            ['name' => 'RS Hasan Sadikin',    'alamat' => 'Jl. Pasteur No. 38, Bandung',      'telepon' => '022-2034953'],
            ['name' => 'RS Immanuel',         'alamat' => 'Jl. Kopo No. 161, Bandung',        'telepon' => '022-5201656'],
            ['name' => 'RS Borromeus',        'alamat' => 'Jl. Dago No. 80, Bandung',         'telepon' => '022-2552000'],
            ['name' => 'Apotek Kimia Farma',  'alamat' => 'Jl. Dago No. 120, Bandung',        'telepon' => '022-2503871'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['name' => $c['name']], $c);
        }
    }
}
