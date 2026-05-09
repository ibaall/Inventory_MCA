<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Alat Pemeriksaan & Diagnostik
            ['name' => 'Tensimeter Digital Omron HEM-7120', 'stock' => 25, 'price' => 385000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Tensimeter Manual Riester', 'stock' => 15, 'price' => 275000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Stetoskop Littmann Classic III', 'stock' => 20, 'price' => 1250000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Stetoskop Onemed Dewasa', 'stock' => 30, 'price' => 85000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Termometer Digital Infrared', 'stock' => 40, 'price' => 175000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Termometer Raksa Oral', 'stock' => 50, 'price' => 25000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Pulse Oximeter Fingertip CMS50D', 'stock' => 35, 'price' => 145000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Glukometer Accu-Chek Active', 'stock' => 20, 'price' => 325000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Strip Gula Darah Accu-Chek (50 pcs)', 'stock' => 60, 'price' => 185000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Otoskop Diagnostik Heine Beta 200', 'stock' => 8, 'price' => 1850000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Reflex Hammer Babinski', 'stock' => 18, 'price' => 55000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Timbangan Badan Digital', 'stock' => 12, 'price' => 235000, 'category' => 'Alat Diagnostik'],
            ['name' => 'Pengukur Tinggi Badan Stadiometer', 'stock' => 7, 'price' => 475000, 'category' => 'Alat Diagnostik'],

            // Alat Terapi & Rehabilitasi
            ['name' => 'Nebulizer Omron NE-C28', 'stock' => 18, 'price' => 485000, 'category' => 'Alat Terapi'],
            ['name' => 'Nebulizer Mesh Portable', 'stock' => 22, 'price' => 350000, 'category' => 'Alat Terapi'],
            ['name' => 'TENS Terapi Nyeri Elektrik', 'stock' => 14, 'price' => 285000, 'category' => 'Alat Terapi'],
            ['name' => 'Infrared Lamp Terapi 150W', 'stock' => 10, 'price' => 195000, 'category' => 'Alat Terapi'],
            ['name' => 'Bantal Terapi Leher Cervical', 'stock' => 25, 'price' => 125000, 'category' => 'Alat Terapi'],
            ['name' => 'Korset Pinggang Lumbar Support', 'stock' => 30, 'price' => 175000, 'category' => 'Alat Terapi'],
            ['name' => 'Knee Brace Decker Lutut', 'stock' => 28, 'price' => 95000, 'category' => 'Alat Terapi'],
            ['name' => 'Ankle Brace Penyangga Pergelangan Kaki', 'stock' => 22, 'price' => 85000, 'category' => 'Alat Terapi'],
            ['name' => 'Wrist Brace Pergelangan Tangan', 'stock' => 20, 'price' => 75000, 'category' => 'Alat Terapi'],
            ['name' => 'Hot & Cold Pack Gel', 'stock' => 45, 'price' => 55000, 'category' => 'Alat Terapi'],
            ['name' => 'Bola Pijat Refleksi Kayu', 'stock' => 35, 'price' => 45000, 'category' => 'Alat Terapi'],

            // Alat Bantu Mobilitas
            ['name' => 'Tongkat Kruk Ketiak Aluminium', 'stock' => 15, 'price' => 185000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Tongkat Jalan 4 Kaki', 'stock' => 12, 'price' => 225000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Walker Rollator Roda 4', 'stock' => 8, 'price' => 785000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Kursi Roda Standar Lipat', 'stock' => 5, 'price' => 1650000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Kursi Roda Ringan Portable', 'stock' => 4, 'price' => 2350000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Bed Rail Pengaman Tempat Tidur', 'stock' => 9, 'price' => 425000, 'category' => 'Alat Bantu Mobilitas'],
            ['name' => 'Matras Anti Decubitus', 'stock' => 6, 'price' => 875000, 'category' => 'Alat Bantu Mobilitas'],

            // Perawatan Luka & Sterilisasi
            ['name' => 'Pinset Anatomis 14cm', 'stock' => 40, 'price' => 18000, 'category' => 'Perawatan Luka'],
            ['name' => 'Pinset Chirurgis Bedah', 'stock' => 35, 'price' => 22000, 'category' => 'Perawatan Luka'],
            ['name' => 'Gunting Perban Lister', 'stock' => 30, 'price' => 35000, 'category' => 'Perawatan Luka'],
            ['name' => 'Gunting Jaringan Mayo', 'stock' => 20, 'price' => 55000, 'category' => 'Perawatan Luka'],
            ['name' => 'Kom Bengkok Stainless 26cm', 'stock' => 25, 'price' => 45000, 'category' => 'Perawatan Luka'],
            ['name' => 'Bak Instrumen Stainless Tutup', 'stock' => 15, 'price' => 125000, 'category' => 'Perawatan Luka'],
            ['name' => 'Tourniquet Karet Pembendung', 'stock' => 50, 'price' => 15000, 'category' => 'Perawatan Luka'],
            ['name' => 'Spuit / Syringe 3cc (100 pcs)', 'stock' => 30, 'price' => 95000, 'category' => 'Perawatan Luka'],
            ['name' => 'Spuit / Syringe 10cc (100 pcs)', 'stock' => 25, 'price' => 115000, 'category' => 'Perawatan Luka'],
            ['name' => 'Plester Luka Hansaplast (100 pcs)', 'stock' => 60, 'price' => 55000, 'category' => 'Perawatan Luka'],
            ['name' => 'Kasa Steril Non-Woven (50 pcs)', 'stock' => 55, 'price' => 35000, 'category' => 'Perawatan Luka'],

            // Perlengkapan Pelindung Diri
            ['name' => 'Sarung Tangan Latex Steril (50 pasang)', 'stock' => 40, 'price' => 125000, 'category' => 'APD'],
            ['name' => 'Sarung Tangan Nitril Non-Steril (100 pcs)', 'stock' => 45, 'price' => 145000, 'category' => 'APD'],
            ['name' => 'Masker Medis 3Ply (50 pcs)', 'stock' => 70, 'price' => 65000, 'category' => 'APD'],
            ['name' => 'Masker N95 Respirator (10 pcs)', 'stock' => 35, 'price' => 135000, 'category' => 'APD'],
            ['name' => 'Apron Plastik Disposable (10 pcs)', 'stock' => 30, 'price' => 55000, 'category' => 'APD'],
            ['name' => 'Hazmat Suit APD Lengkap', 'stock' => 15, 'price' => 185000, 'category' => 'APD'],
            ['name' => 'Face Shield Pelindung Wajah', 'stock' => 25, 'price' => 45000, 'category' => 'APD'],
            ['name' => 'Sepatu Boot Medis Anti-Slip', 'stock' => 12, 'price' => 225000, 'category' => 'APD'],
            ['name' => 'Penutup Kepala Bouffant Cap (100 pcs)', 'stock' => 50, 'price' => 45000, 'category' => 'APD'],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'     => $product['name'],
                'stock'    => $product['stock'],
                'price'    => $product['price'],
                'category' => $product['category'],
                'image'    => null,
            ]);
        }
    }
}
