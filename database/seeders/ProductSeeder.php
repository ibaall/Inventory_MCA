<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [];

        // 1. Mini L-Plate (AMSA, 400.000)
        // TNAMS-MINI-LP-02L to 05L and 02R to 05R
        for ($h = 2; $h <= 5; $h++) {
            $products[] = [
                'kode_barang' => "TNAMS-MINI-LP-0{$h}L",
                'name' => "2.0mm Mini L-Plate {$h} Holes Left",
                'vendor' => 'AMSA',
                'category' => 'Mini L-Plate',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }
        for ($h = 2; $h <= 5; $h++) {
            $products[] = [
                'kode_barang' => "TNAMS-MINI-LP-0{$h}R",
                'name' => "2.0mm Mini L-Plate {$h} Holes Right",
                'vendor' => 'AMSA',
                'category' => 'Mini L-Plate',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 2. Mini L-Plate (AUXEIN, 400.000)
        // TNAUX-MINI-LP-2002L, 2004L, 2006L, 2008L and 2002R, 2004R, 2006R, 2008R
        $aux_holes = [2, 4, 6, 8];
        foreach ($aux_holes as $h) {
            $products[] = [
                'kode_barang' => "TNAUX-MINI-LP-200{$h}L",
                'name' => "2.0mm Mini \"L\" Plate, Left, Shaft {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'Mini L-Plate',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }
        foreach ($aux_holes as $h) {
            $products[] = [
                'kode_barang' => "TNAUX-MINI-LP-200{$h}R",
                'name' => "2.0mm Mini \"L\" Plate, Right, Shaft {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'Mini L-Plate',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 3. Mini L-Plate Oblique (AMSA, 500.000)
        // TNAMS-MINI-LPOB-02L to 05L and 02R to 05R
        for ($h = 2; $h <= 5; $h++) {
            $products[] = [
                'kode_barang' => "TNAMS-MINI-LPOB-0{$h}L",
                'name' => "2.0mm Mini L Plate {$h} Holes Oblique Left",
                'vendor' => 'AMSA',
                'category' => 'Mini L-Plate',
                'price' => 500000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }
        for ($h = 2; $h <= 5; $h++) {
            $products[] = [
                'kode_barang' => "TNAMS-MINI-LPOB-0{$h}R",
                'name' => "2.0mm Mini L Plate {$h} Holes Oblique Right",
                'vendor' => 'AMSA',
                'category' => 'Mini L-Plate',
                'price' => 500000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 4. Mini Straight Plate (AMSA, 350.000)
        // TNAMS-MINI-SP-2005 to 2010
        for ($h = 5; $h <= 10; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNAMS-MINI-SP-20%02d", $h),
                'name' => "2.0mm Mini Straight Plate, {$h} Holes",
                'vendor' => 'AMSA',
                'category' => 'Mini Plate',
                'price' => 350000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 5. Mini Straight Plate (AUXEIN, 350.000)
        // TNAUX-MINI-SP-2002 to 2008
        for ($h = 2; $h <= 8; $h++) {
            $products[] = [
                'kode_barang' => "TNAUX-MINI-SP-200{$h}",
                'name' => "2.0mm Mini Straight Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'Mini Plate',
                'price' => 350000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 6. Mini I-Plate & T-Plate (AMSA, 400.000)
        $products[] = [
            'kode_barang' => 'TNAMS-MINI-IP-2002',
            'name' => '2.0mm Mini "I" Plate, 2 Holes',
            'vendor' => 'AMSA',
            'category' => 'Mini I-Plate',
            'price' => 400000,
            'stock' => 15,
            'satuan' => 'Pcs',
        ];
        for ($h = 3; $h <= 6; $h++) {
            $products[] = [
                'kode_barang' => "TNAMS-MINI-TP-200{$h}",
                'name' => "2.0mm Mini \"T\" Plate, {$h} Holes",
                'vendor' => 'AMSA',
                'category' => 'Mini T-Plate',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 7. Cortical Screw 2.0 (AMSA, mini cortex, 125.000)
        // TNAMS-MINICOR-2006 to 2020 (even numbers: 6, 8, 10, 12, 14, 16, 18, 20)
        for ($l = 6; $l <= 20; $l += 2) {
            $products[] = [
                'kode_barang' => sprintf("TNAMS-MINICOR-20%02d", $l),
                'name' => sprintf("Cortical Screw 2.0 Ø %02d mm", $l),
                'vendor' => 'AMSA',
                'category' => 'mini cortex',
                'price' => 125000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 8. Cortical Screw 1.5 (FALCON, mini cortex, 125.000)
        // TNFAC-MINICOR-1506 to 1520 (even numbers: 6, 8, 10, 12, 14, 16, 18, 20)
        for ($l = 6; $l <= 20; $l += 2) {
            $products[] = [
                'kode_barang' => sprintf("TNFAC-MINICOR-15%02d", $l),
                'name' => sprintf("Cortical Screw 1.5 Ø %02d mm", $l),
                'vendor' => 'FALCON',
                'category' => 'mini cortex',
                'price' => 125000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 9. Cortical Screw 2.0 (FALCON, mini cortex, 125.000)
        // TNFAC-MINICOR-2006 to 2024 (even numbers: 6, 8, 10, 12, 14, 16, 18, 20, 22, 24)
        for ($l = 6; $l <= 24; $l += 2) {
            $products[] = [
                'kode_barang' => sprintf("TNFAC-MINICOR-20%02d", $l),
                'name' => sprintf("Cortical Screw 2.0 Ø %02d mm", $l),
                'vendor' => 'FALCON',
                'category' => 'mini cortex',
                'price' => 125000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 10. Cortical Screw Self Tapping 1.5 (AUXEIN, mini cortex st, 150.000)
        // TNAUX-MINI-SFC-1506 to 1515
        // Lengths: 6, 7, 8, 9, 10, 11, 12, 14, 16, 18, 20 mm. Suffix: 06 to 15?
        // Let's do loops or direct array mapping to be perfectly accurate:
        $sfc_15_mapping = [
            '1506' => 6,
            '1507' => 7,
            '1508' => 8,
            '1509' => 9,
            '1510' => 10,
            '1511' => 12, // From screenshot: 1511 = 12mm
            '1512' => 14, // 1512 = 14mm
            '1513' => 16, // 1513 = 16mm
            '1514' => 18, // 1514 = 18mm
            '1515' => 20, // 1515 = 20mm
        ];
        foreach ($sfc_15_mapping as $code => $length) {
            $products[] = [
                'kode_barang' => "TNAUX-MINI-SFC-{$code}",
                'name' => "1.5mm Cortical Screw, Self-Tapping, (Hex Head), Length {$length}mm",
                'vendor' => 'AUXEIN',
                'category' => 'mini cortex st',
                'price' => 150000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 11. Cortical Screw Self Tapping 2.0 (AUXEIN, mini cortex st, 150.000)
        // TNAUX-MINI-SFC-2006 to 2015
        $sfc_20_mapping = [
            '2006' => 6,
            '2007' => 7,
            '2008' => 8,
            '2009' => 9,
            '2010' => 10,
            '2012' => 12,
            '2014' => 14,
            '2016' => 16,
            '2018' => 18,
            '2020' => 20,
        ];
        foreach ($sfc_20_mapping as $code => $length) {
            $products[] = [
                'kode_barang' => "TNAUX-MINI-SFC-{$code}",
                'name' => "2.0mm Cortical Screw, Self-Tapping, (Hex Head), Length {$length}mm",
                'vendor' => 'AUXEIN',
                'category' => 'mini cortex st',
                'price' => 150000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 12. Self Tapping Cortical Screw 1.5 (FALCON, mini cortex st, 150.000)
        // TNFAC-MINI-SFC-1506 to 1520 (even numbers: 6, 8, 10, 12, 14, 16, 18, 20)
        for ($l = 6; $l <= 20; $l += 2) {
            $products[] = [
                'kode_barang' => sprintf("TNFAC-MINI-SFC-15%02d", $l),
                'name' => sprintf("Self Tapping Cortical Screw 1.5 Ø %02d mm", $l),
                'vendor' => 'FALCON',
                'category' => 'mini cortex st',
                'price' => 150000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 13. Self Tapping Cortical Screw 2.0 (FALCON, mini cortex st, 150.000)
        // TNFAC-MINI-SFC-2006 to 2024 (even numbers: 6, 8, 10, 12, 14, 16, 18, 20, 22, 24)
        for ($l = 6; $l <= 24; $l += 2) {
            $products[] = [
                'kode_barang' => sprintf("TNFAC-MINI-SFC-20%02d", $l),
                'name' => sprintf("Self Tapping Cortical Screw 2.0 Ø %02d mm", $l),
                'vendor' => 'FALCON',
                'category' => 'mini cortex st',
                'price' => 150000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 14. One-Third Tubular Plate (AUXEIN, 1/3 TUB, 400.000)
        // TNAUX-1/3 TUB-04 to 10
        for ($h = 4; $h <= 10; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNAUX-1/3 TUB-%02d", $h),
                'name' => "3.5mm One-Third Tubular Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => '1/3 TUB',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 15. One-Third Tubular Plate (FALCON, 1/3 TUB, 400.000)
        // TNFAC-1/3 TUB-04 to 10
        for ($h = 4; $h <= 10; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNFAC-1/3 TUB-%02d", $h),
                'name' => "One Third Tubular Plate {$h} Holes",
                'vendor' => 'FALCON',
                'category' => '1/3 TUB',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 16. One-Third Tubular Plate (AMSA, 1/3 TUB, 400.000)
        // TNAMS-1/3 TUB-04 to 10
        for ($h = 4; $h <= 10; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNAMS-1/3 TUB-%02d", $h),
                'name' => "One Third Tubular Plate {$h} Holes",
                'vendor' => 'AMSA',
                'category' => '1/3 TUB',
                'price' => 400000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 17. Pelvic Reconstruction Plate (AUXEIN, ACC, 500.000 - 700.000)
        // TNAUX-ACC-05 to 14
        for ($h = 5; $h <= 14; $h++) {
            $price = 500000;
            if ($h >= 7 && $h <= 11) {
                $price = 600000;
            } elseif ($h >= 12) {
                $price = 700000;
            }
            $products[] = [
                'kode_barang' => sprintf("TNAUX-ACC-%02d", $h),
                'name' => "3.5mm Pelvic Reconstruction Plate, Straight, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'ACC',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 18. Straight Reconstruction Plate (FALCON, ACC, 500.000 - 600.000)
        // TNFAC-ACC-05 to 10
        for ($h = 5; $h <= 10; $h++) {
            $price = ($h <= 6) ? 500000 : 600000;
            $products[] = [
                'kode_barang' => sprintf("TNFAC-ACC-%02d", $h),
                'name' => "Straight Reconstruction Plate {$h} Holes",
                'vendor' => 'FALCON',
                'category' => 'ACC',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 19. Broad Limited Contact Dynamic Compression Plate (AUXEIN, BROAD, 650.000 - 1.050.000)
        // TNAUX-LCBRO-08 to 16
        for ($h = 8; $h <= 16; $h++) {
            $price = 650000 + ($h - 8) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNAUX-LCBRO-%02d", $h),
                'name' => "4.5mm Broad Limited Contact Dynamic Compression Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'BROAD',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 20. LC-DCP Broad Plate (ORTHO-X, LC BROAD, 650.000 - 1.050.000)
        // TNTHO-LCBRO-08 to 16
        for ($h = 8; $h <= 16; $h++) {
            $price = 650000 + ($h - 8) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNTHO-LCBRO-%02d", $h),
                'name' => "LC-DCP Broad Plate {$h} Holes",
                'vendor' => 'ORTHO-X',
                'category' => 'LC BROAD',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 21. Narrow Limited Contact Dynamic Compression Plate (AUXEIN, LC NARROW, 500.000 - 1.000.000)
        // TNAUX-LCNAR-06 to 16
        for ($h = 6; $h <= 16; $h++) {
            $price = 500000 + ($h - 6) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNAUX-LCNAR-%02d", $h),
                'name' => "4.5mm Narrow Limited Contact Dynamic Compression Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'LC NARROW',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 22. LC-DCP Narrow Plate (ORTHO-X, LC NARROW, 550.000 - 1.000.000)
        // TNTHO-LCNAR-07 to 16
        for ($h = 7; $h <= 16; $h++) {
            $price = 550000 + ($h - 7) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNTHO-LCNAR-%02d", $h),
                'name' => "LC-DCP Narrow Plate {$h} Holes",
                'vendor' => 'ORTHO-X',
                'category' => 'LC NARROW',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 23. Proximal Humeral Plate (AUXEIN, Prox Humeral, 1.050.000 - 1.200.000)
        // TNAUX-PROHUM-04 to 08
        for ($h = 4; $h <= 8; $h++) {
            $price = ($h <= 5) ? 1050000 : 1050000 + ($h - 5) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNAUX-PROHUM-%02d", $h),
                'name' => "Proximal Humeral Plate {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'Prox Humeral',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 24. Cloverleaf Plate (AUXEIN, Cloverleaf, 500.000 - 650.000)
        // TNAUX-CLOVER-04 to 08
        for ($h = 4; $h <= 8; $h++) {
            $price = 500000;
            if ($h >= 5 && $h <= 7) {
                $price = 600000;
            } elseif ($h == 8) {
                $price = 650000;
            }
            $products[] = [
                'kode_barang' => sprintf("TNAUX-CLOVER-%02d", $h),
                'name' => "3.5mm Cloverleaf Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'Cloverleaf',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 25. Small Limited Contact Dynamic Compression Plate (AUXEIN, SMALL DCP, 650.000 - 900.000)
        // TNAUX-LCSMALL-05 to 10
        for ($h = 5; $h <= 10; $h++) {
            $price = 650000 + ($h - 5) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNAUX-LCSMALL-%02d", $h),
                'name' => "3.5mm Small Limited Contact Dynamic Compression Plate, {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'SMALL DCP',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 26. Small Narrow DCP Plate (FALCON, SMALL DCP, 650.000 - 900.000)
        // TNFAC-SMALL DCP-05 to 10
        // Wait, the code is TNFAC-SMALL DCP-05 or TNFAC-SMALL DCP-05? Let's check:
        // In the image it says "TNFAC-SMALL DCP-05". Note the space or hyphen.
        // It says "TNFAC-SMALL DCP-05" or "TNFAC-SMALL DCP-05". Let's use hyphen or space.
        // Looking at Section 13: "TNFAC-SMALL DCP-05" has a space!
        // Let's use space: "TNFAC-SMALL DCP-05".
        for ($h = 5; $h <= 10; $h++) {
            $price = 650000 + ($h - 5) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNFAC-SMALL DCP-%02d", $h),
                'name' => "Small Narrow DCP Plate {$h} Holes",
                'vendor' => 'FALCON',
                'category' => 'SMALL DCP',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 27. Small DCP Plate (ORTHO-X, SMALL DCP, 650.000 - 900.000)
        // TNTHO-SMALL DCP-05 to 10
        for ($h = 5; $h <= 10; $h++) {
            $price = 650000 + ($h - 5) * 50000;
            $products[] = [
                'kode_barang' => sprintf("TNTHO-SMALL DCP-%02d", $h),
                'name' => "Small DCP Plate {$h} Holes",
                'vendor' => 'ORTHO-X',
                'category' => 'SMALL DCP',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 28. Small T-Plate Right Angled (AUXEIN, SMALL T-PLATE, 500.000)
        // TNAUX-SMALLT-03 to 08
        for ($h = 3; $h <= 8; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNAUX-SMALLT-%02d", $h),
                'name' => "3.5mm Small \"T\" Plate, Right Angled, (3 Head Holes), {$h} Holes",
                'vendor' => 'AUXEIN',
                'category' => 'SMALL T-PLATE',
                'price' => 500000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 29. Small T-Plate (ORTHO-X, SMALL T-PLATE, 500.000)
        // TNTHO-SMALLT-03 to 08
        for ($h = 3; $h <= 8; $h++) {
            $products[] = [
                'kode_barang' => sprintf("TNTHO-SMALLT-%02d", $h),
                'name' => "Small T-Plate {$h} Holes Shaft",
                'vendor' => 'ORTHO-X',
                'category' => 'SMALL T-PLATE',
                'price' => 500000,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 30. T-Plate 4.5 mm (ORTHO-X, 4.5 T PLATE, 500.000 - 750.000)
        // TNTHO-45TP-04 to 10
        for ($h = 4; $h <= 10; $h++) {
            $price = 500000;
            if ($h >= 6) {
                $price = 500000 + ($h - 5) * 50000;
            }
            $products[] = [
                'kode_barang' => sprintf("TNTHO-45TP-%02d", $h),
                'name' => "T-Plate 4.5 mm {$h} Holes",
                'vendor' => 'ORTHO-X',
                'category' => '4.5 T PLATE',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // 31. T-Plate 4.5 mm (AUXEIN, 4.5 T PLATE, 500.000 - 750.000)
        // TNAUX-45TP-04 to 10
        for ($h = 4; $h <= 10; $h++) {
            $price = 500000;
            if ($h >= 6) {
                $price = 500000 + ($h - 5) * 50000;
            }
            $products[] = [
                'kode_barang' => sprintf("TNAUX-45TP-%02d", $h),
                'name' => "4.5mm \"T\" Plate, {$h} Holes, Stainless Steel",
                'vendor' => 'AUXEIN',
                'category' => '4.5 T PLATE',
                'price' => $price,
                'stock' => 15,
                'satuan' => 'Pcs',
            ];
        }

        // Save to Database
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
