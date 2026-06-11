<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    private function getRomanMonth($month)
    {
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1];
    }

    public function run(): void
    {
        $admin = User::first();
        if (!$admin) {
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        // Define dummy data structure for April, May, and June 2026
        $data = [
            // ================= APRIL 2026 =================
            [
                'month' => 4,
                'year' => 2026,
                'purchases' => [
                    [
                        'date' => '2026-04-05 10:00:00',
                        'supplier' => 'AMSA',
                        'alamat' => 'Kawasan Industri Jababeka, Bekasi',
                        'items' => [
                            ['index' => 0, 'qty' => 50, 'discount' => 10], // first product
                            ['index' => 10, 'qty' => 30, 'discount' => 10],
                        ],
                        'payments' => [
                            ['date' => '2026-04-05', 'amount_percent' => 50, 'note' => 'DP Pembelian April'],
                            ['date' => '2026-04-20', 'amount_percent' => 50, 'note' => 'Pelunasan Pembelian April'],
                        ],
                        'status' => 'diterima',
                    ],
                    [
                        'date' => '2026-04-18 14:30:00',
                        'supplier' => 'AUXEIN',
                        'alamat' => 'Sentul Industrial Park, Bogor',
                        'items' => [
                            ['index' => 25, 'qty' => 20, 'discount' => 5],
                            ['index' => 45, 'qty' => 15, 'discount' => 5],
                        ],
                        'payments' => [
                            ['date' => '2026-04-18', 'amount_percent' => 100, 'note' => 'Lunas COD'],
                        ],
                        'status' => 'diterima',
                    ]
                ],
                'sales' => [
                    [
                        'date' => '2026-04-08 09:00:00',
                        'customer' => 'RS Santosa Bandung',
                        'alamat' => 'Jl. Kebon Jati No. 38, Bandung',
                        'pasien' => 'Ny. Aminah',
                        'operator' => 'dr. Ronald Sp.OT',
                        'items' => [
                            ['index' => 0, 'qty' => 2], // Cortex Screw
                            ['index' => 5, 'qty' => 1],
                        ],
                        'payments' => [
                            ['date' => '2026-04-08', 'amount_percent' => 100, 'note' => 'Pembayaran Lunas RS Santosa'],
                        ],
                        'status' => 'lunas',
                        'metode' => 'Transfer',
                    ],
                    [
                        'date' => '2026-04-15 11:15:00',
                        'customer' => 'RS Hasan Sadikin',
                        'alamat' => 'Jl. Pasteur No. 38, Bandung',
                        'pasien' => 'Tn. Gunawan',
                        'operator' => 'dr. Faisal Sp.OT',
                        'items' => [
                            ['index' => 15, 'qty' => 4],
                            ['index' => 30, 'qty' => 2],
                        ],
                        'payments' => [
                            ['date' => '2026-04-15', 'amount_percent' => 50, 'note' => 'Pembayaran Tahap 1'],
                        ],
                        'status' => 'belum dibayar', // Will display as "belum lunas"
                        'metode' => 'Kredit',
                    ],
                    [
                        'date' => '2026-04-26 15:00:00',
                        'customer' => 'Apotek Kimia Farma',
                        'alamat' => 'Jl. Dago No. 120, Bandung',
                        'pasien' => '-',
                        'operator' => '-',
                        'items' => [
                            ['index' => 8, 'qty' => 5],
                            ['index' => 12, 'qty' => 5],
                        ],
                        'payments' => [],
                        'status' => 'belum dibayar',
                        'metode' => 'Kredit',
                    ]
                ]
            ],

            // ================= MAY 2026 =================
            [
                'month' => 5,
                'year' => 2026,
                'purchases' => [
                    [
                        'date' => '2026-05-02 11:00:00',
                        'supplier' => 'FALCON',
                        'alamat' => 'Kawasan Industri Cikarang, Bekasi',
                        'items' => [
                            ['index' => 60, 'qty' => 40, 'discount' => 15],
                            ['index' => 80, 'qty' => 40, 'discount' => 15],
                        ],
                        'payments' => [
                            ['date' => '2026-05-02', 'amount_percent' => 40, 'note' => 'DP 40% PO Falcon'],
                            ['date' => '2026-05-25', 'amount_percent' => 60, 'note' => 'Pelunasan PO Falcon'],
                        ],
                        'status' => 'diterima',
                    ],
                    [
                        'date' => '2026-05-15 09:30:00',
                        'supplier' => 'AMSA',
                        'alamat' => 'Kawasan Industri Jababeka, Bekasi',
                        'items' => [
                            ['index' => 110, 'qty' => 25, 'discount' => 10],
                            ['index' => 120, 'qty' => 10, 'discount' => 10],
                        ],
                        'payments' => [],
                        'status' => 'pending',
                    ]
                ],
                'sales' => [
                    [
                        'date' => '2026-05-06 14:00:00',
                        'customer' => 'RS Immanuel',
                        'alamat' => 'Jl. Kopo No. 161, Bandung',
                        'pasien' => 'Anak Dika',
                        'operator' => 'dr. Handoko Sp.OT',
                        'items' => [
                            ['index' => 60, 'qty' => 2],
                            ['index' => 110, 'qty' => 1],
                        ],
                        'payments' => [
                            ['date' => '2026-05-06', 'amount_percent' => 100, 'note' => 'Lunas Transfer RS Immanuel'],
                        ],
                        'status' => 'lunas',
                        'metode' => 'Transfer',
                    ],
                    [
                        'date' => '2026-05-18 10:45:00',
                        'customer' => 'RS Hasan Sadikin',
                        'alamat' => 'Jl. Pasteur No. 38, Bandung',
                        'pasien' => 'Ny. Ratna',
                        'operator' => 'dr. Faisal Sp.OT',
                        'items' => [
                            ['index' => 140, 'qty' => 3],
                            ['index' => 150, 'qty' => 1],
                        ],
                        'payments' => [
                            ['date' => '2026-05-18', 'amount_percent' => 100, 'note' => 'Lunas RS RSHS'],
                        ],
                        'status' => 'lunas',
                        'metode' => 'Transfer',
                    ],
                    [
                        'date' => '2026-05-28 16:30:00',
                        'customer' => 'RS Santosa Bandung',
                        'alamat' => 'Jl. Kebon Jati No. 38, Bandung',
                        'pasien' => 'Tn. Herman',
                        'operator' => 'dr. Ronald Sp.OT',
                        'items' => [
                            ['index' => 200, 'qty' => 1],
                            ['index' => 210, 'qty' => 1],
                        ],
                        'payments' => [],
                        'status' => 'belum dibayar',
                        'metode' => 'Kredit',
                    ]
                ]
            ],

            // ================= JUNE 2026 =================
            [
                'month' => 6,
                'year' => 2026,
                'purchases' => [
                    [
                        'date' => '2026-06-02 14:00:00',
                        'supplier' => 'AMSA',
                        'alamat' => 'Kawasan Industri Jababeka, Bekasi',
                        'items' => [
                            ['index' => 220, 'qty' => 15, 'discount' => 10],
                            ['index' => 230, 'qty' => 15, 'discount' => 10],
                        ],
                        'payments' => [
                            ['date' => '2026-06-02', 'amount_percent' => 100, 'note' => 'Pembelian AMSA Juni Lunas'],
                        ],
                        'status' => 'diterima',
                    ]
                ],
                'sales' => [
                    [
                        'date' => '2026-06-03 09:30:00',
                        'customer' => 'RS Borromeus',
                        'alamat' => 'Jl. Dago No. 80, Bandung',
                        'pasien' => 'Tn. Edward',
                        'operator' => 'dr. Setiawan Sp.OT',
                        'items' => [
                            ['index' => 220, 'qty' => 1],
                            ['index' => 240, 'qty' => 1],
                        ],
                        'payments' => [
                            ['date' => '2026-06-03', 'amount_percent' => 100, 'note' => 'Lunas Borromeus'],
                        ],
                        'status' => 'lunas',
                        'metode' => 'Transfer',
                    ],
                    [
                        'date' => '2026-06-07 13:00:00',
                        'customer' => 'RS Immanuel',
                        'alamat' => 'Jl. Kopo No. 161, Bandung',
                        'pasien' => 'Ny. Linda',
                        'operator' => 'dr. Handoko Sp.OT',
                        'items' => [
                            ['index' => 250, 'qty' => 2],
                            ['index' => 260, 'qty' => 1],
                        ],
                        'payments' => [
                            ['date' => '2026-06-07', 'amount_percent' => 30, 'note' => 'DP Pembayaran RS Immanuel'],
                        ],
                        'status' => 'belum dibayar',
                        'metode' => 'Kredit',
                    ]
                ]
            ]
        ];

        $poCount = 1;
        $orderCount = 1;

        // Seed the records month by month
        foreach ($data as $monthData) {
            $month = $monthData['month'];
            $year = $monthData['year'];
            $roman = $this->getRomanMonth($month);

            // 1. Seed Purchase Orders
            foreach ($monthData['purchases'] as $pData) {
                $orderedAt = Carbon::parse($pData['date']);
                
                // Calculate total price based on items
                $totalPrice = 0;
                $itemDetails = [];
                foreach ($pData['items'] as $itemData) {
                    $prod = $products->get($itemData['index']);
                    if (!$prod) continue;
                    
                    $price = $prod->price;
                    $discountPct = $itemData['discount'];
                    $discountedPrice = $discountPct > 0 ? round($price * (1 - $discountPct / 100)) : $price;
                    $subtotal = $discountedPrice * $itemData['qty'];
                    $totalPrice += $subtotal;
                    
                    $itemDetails[] = [
                        'product' => $prod,
                        'qty' => $itemData['qty'],
                        'price' => $discountedPrice,
                        'original_price' => $price,
                        'discount_percent' => $discountPct,
                        'subtotal' => $subtotal,
                    ];
                }

                $poNum = ($poCount++) . "/PO/MCA/{$roman}/{$year}";

                $po = PurchaseOrder::create([
                    'user_id' => $admin->id,
                    'supplier_name' => $pData['supplier'],
                    'alamat' => $pData['alamat'],
                    'total_price' => $totalPrice,
                    'ordered_at' => $orderedAt,
                    'status' => $pData['status'],
                    'use_ppn' => 1,
                    'po_number' => $poNum,
                    'catatan' => 'Dummy PO generated by seeder',
                ]);

                foreach ($itemDetails as $detail) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $detail['product']->id,
                        'quantity' => $detail['qty'],
                        'price' => $detail['price'],
                        'original_price' => $detail['original_price'],
                        'discount_percent' => $detail['discount_percent'],
                        'subtotal' => $detail['subtotal'],
                    ]);

                    // Adjust product stock if status is 'diterima'
                    if ($pData['status'] === 'diterima') {
                        $detail['product']->increment('stock', $detail['qty']);
                    }
                }

                // Add payments
                $poGrandTotal = $totalPrice + round($totalPrice * 0.11);
                foreach ($pData['payments'] as $payData) {
                    $amount = round($poGrandTotal * ($payData['amount_percent'] / 100));
                    Payment::create([
                        'transaction_type' => 'purchase',
                        'transaction_id' => $po->id,
                        'party_type' => 'supplier',
                        'party_name' => $pData['supplier'],
                        'payment_date' => Carbon::parse($payData['date']),
                        'amount' => $amount,
                        'note' => $payData['note'],
                        'created_by' => $admin->id,
                    ]);
                }
            }

            // 2. Seed Sales Orders
            foreach ($monthData['sales'] as $sData) {
                $orderedAt = Carbon::parse($sData['date']);
                
                // Calculate total price based on items
                $totalPrice = 0;
                $itemDetails = [];
                foreach ($sData['items'] as $itemData) {
                    $prod = $products->get($itemData['index']);
                    if (!$prod) continue;
                    
                    $price = $prod->price;
                    $subtotal = $price * $itemData['qty'];
                    $totalPrice += $subtotal;
                    
                    $itemDetails[] = [
                        'product' => $prod,
                        'qty' => $itemData['qty'],
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }

                $invNum = ($orderCount++) . "/INV/MCA/{$roman}/{$year}";
                $sjNum = ($orderCount - 1) . "/SJ/MCA/{$roman}/{$year}";

                $order = Order::create([
                    'user_id' => $admin->id,
                    'customer_name' => $sData['customer'],
                    'total_price' => $totalPrice,
                    'ordered_at' => $orderedAt,
                    'status_pembayaran' => $sData['status'],
                    'metode_pembayaran' => $sData['metode'],
                    'use_ppn' => 1,
                    'invoice_number' => $invNum,
                    'surat_jalan_number' => $sjNum,
                    'nama_pasien' => $sData['pasien'],
                    'operator' => $sData['operator'],
                    'tanggal_operasi' => $orderedAt->copy()->addDay(),
                    'alamat' => $sData['alamat'],
                    'tanggal_jatuh_tempo' => $orderedAt->copy()->addDays(30),
                ]);

                foreach ($itemDetails as $detail) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $detail['product']->id,
                        'quantity' => $detail['qty'],
                        'price' => $detail['price'],
                        'subtotal' => $detail['subtotal'],
                    ]);

                    // Deduct stock (sales)
                    $detail['product']->decrement('stock', $detail['qty']);
                }

                // Add payments
                $orderGrandTotal = $totalPrice + round($totalPrice * 0.11);
                foreach ($sData['payments'] as $payData) {
                    $amount = round($orderGrandTotal * ($payData['amount_percent'] / 100));
                    Payment::create([
                        'transaction_type' => 'sale',
                        'transaction_id' => $order->id,
                        'party_type' => 'customer',
                        'party_name' => $sData['customer'],
                        'payment_date' => Carbon::parse($payData['date']),
                        'amount' => $amount,
                        'note' => $payData['note'],
                        'created_by' => $admin->id,
                    ]);
                }
            }
        }
    }
}
