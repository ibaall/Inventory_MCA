<?php

namespace Database\Seeders;

use App\Models\BuktiKas;
use App\Models\BuktiBank;
use App\Models\JurnalKoreksi;
use App\Models\NoPerkiraan;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        // Get perkiraan references
        $perkiraans = NoPerkiraan::pluck('id', 'kode_perkiraan')->toArray();

        if (empty($perkiraans)) {
            $this->command->info('No Perkiraan data not found. Run NoPerkiraanSeeder first.');
            return;
        }

        $userId = 1; // Admin user
        $bulan = '2026-06';

        // =========================================
        // BKK (Bukti Kas Keluar) - Pengeluaran Kas
        // =========================================
        $bkkData = [
            [
                'tanggal' => $bulan . '-02',
                'pihak' => 'Toko Sinar Abadi',
                'keterangan_utama' => 'Pembelian Kertas A4 dan ATK',
                'items' => [
                    ['kode' => '808', 'ket' => 'Kertas HVS A4 5 Rim', 'jumlah' => 250000],
                    ['kode' => '808', 'ket' => 'Bolpoin, Spidol, Penghapus', 'jumlah' => 85000],
                ],
            ],
            [
                'tanggal' => $bulan . '-03',
                'pihak' => 'Kasir BBM',
                'keterangan_utama' => 'BBM Kendaraan Operasional',
                'items' => [
                    ['kode' => '819', 'ket' => 'BBM Solar Truk B 1234 AB', 'jumlah' => 500000],
                    ['kode' => '819', 'ket' => 'BBM Pertamax Mobil Operasional', 'jumlah' => 350000],
                ],
            ],
            [
                'tanggal' => $bulan . '-05',
                'pihak' => 'Tol Surabaya - Mojokerto',
                'keterangan_utama' => 'Biaya Tol Pengiriman',
                'items' => [
                    ['kode' => '803', 'ket' => 'Tol Surabaya - Mojokerto (PP)', 'jumlah' => 150000],
                ],
            ],
            [
                'tanggal' => $bulan . '-05',
                'pihak' => 'Parkir',
                'keterangan_utama' => 'Biaya Parkir',
                'items' => [
                    ['kode' => '803', 'ket' => 'Parkir di Gudang Customer', 'jumlah' => 25000],
                    ['kode' => '803', 'ket' => 'Parkir di Mall untuk Meeting', 'jumlah' => 15000],
                ],
            ],
            [
                'tanggal' => $bulan . '-07',
                'pihak' => 'Warung Pak Slamet',
                'keterangan_utama' => 'Konsumsi Rapat Internal',
                'items' => [
                    ['kode' => '812', 'ket' => 'Makan siang rapat 10 orang', 'jumlah' => 450000],
                    ['kode' => '812', 'ket' => 'Snack dan minuman rapat', 'jumlah' => 150000],
                ],
            ],
            [
                'tanggal' => $bulan . '-10',
                'pihak' => 'PLN',
                'keterangan_utama' => 'Pembayaran Listrik Kantor',
                'items' => [
                    ['kode' => '804', 'ket' => 'Tagihan listrik bulan Juni', 'jumlah' => 2500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-10',
                'pihak' => 'PDAM',
                'keterangan_utama' => 'Pembayaran Air Kantor',
                'items' => [
                    ['kode' => '805', 'ket' => 'Tagihan PDAM bulan Juni', 'jumlah' => 350000],
                ],
            ],
            [
                'tanggal' => $bulan . '-12',
                'pihak' => 'Tol Surabaya - Gresik',
                'keterangan_utama' => 'Biaya Tol Pengiriman',
                'items' => [
                    ['kode' => '803', 'ket' => 'Tol Surabaya - Gresik', 'jumlah' => 75000],
                ],
            ],
            [
                'tanggal' => $bulan . '-15',
                'pihak' => 'Telkom',
                'keterangan_utama' => 'Tagihan Telepon & Internet',
                'items' => [
                    ['kode' => '807', 'ket' => 'Tagihan telepon kantor', 'jumlah' => 450000],
                    ['kode' => '807', 'ket' => 'Internet kantor', 'jumlah' => 750000],
                ],
            ],
            [
                'tanggal' => $bulan . '-18',
                'pihak' => 'Fotocopy Jaya',
                'keterangan_utama' => 'Fotocopy Dokumen',
                'items' => [
                    ['kode' => '809', 'ket' => 'Fotocopy surat jalan 500 lembar', 'jumlah' => 125000],
                    ['kode' => '809', 'ket' => 'Print brosur produk', 'jumlah' => 200000],
                ],
            ],
            [
                'tanggal' => $bulan . '-20',
                'pihak' => 'Pos Indonesia',
                'keterangan_utama' => 'Materai dan Pengiriman Surat',
                'items' => [
                    ['kode' => '806', 'ket' => 'Materai 10000 x 20 lembar', 'jumlah' => 200000],
                    ['kode' => '806', 'ket' => 'Ongkos kirim dokumen', 'jumlah' => 50000],
                ],
            ],
            [
                'tanggal' => $bulan . '-22',
                'pihak' => 'Kasir BBM',
                'keterangan_utama' => 'BBM Kendaraan',
                'items' => [
                    ['kode' => '819', 'ket' => 'BBM Solar Truk B 1234 AB', 'jumlah' => 500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-25',
                'pihak' => 'Bengkel Jaya Motor',
                'keterangan_utama' => 'Maintenance Kendaraan',
                'items' => [
                    ['kode' => '827', 'ket' => 'Service berkala mobil operasional', 'jumlah' => 850000],
                    ['kode' => '827', 'ket' => 'Ganti oli dan filter', 'jumlah' => 350000],
                ],
            ],
        ];

        $bkkCount = 0;
        foreach ($bkkData as $data) {
            $bkkCount++;
            $total = collect($data['items'])->sum('jumlah');
            $bukti = BuktiKas::create([
                'jenis' => 'BKK',
                'no_bukti' => 'BKK-202606-' . str_pad($bkkCount, 4, '0', STR_PAD_LEFT),
                'tanggal' => $data['tanggal'],
                'pihak' => $data['pihak'],
                'keterangan_utama' => $data['keterangan_utama'],
                'total' => $total,
                'terbilang' => \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah',
                'status' => $bkkCount <= 3 ? 'konfirmasi' : 'draft',
                'created_by' => $userId,
            ]);

            $urutan = 0;
            foreach ($data['items'] as $item) {
                $urutan++;
                $perkiraanId = $perkiraans[$item['kode']] ?? null;
                $perkiraan = $perkiraanId ? NoPerkiraan::find($perkiraanId) : null;
                $bukti->details()->create([
                    'no_perkiraan_id' => $perkiraanId,
                    'no_account' => $perkiraan?->kode_perkiraan,
                    'nama_perkiraan' => $perkiraan?->nama_perkiraan,
                    'keterangan' => $item['ket'],
                    'jumlah' => $item['jumlah'],
                    'urutan' => $urutan,
                ]);
            }
        }

        // =========================================
        // BKM (Bukti Kas Masuk) - Penerimaan Kas
        // =========================================
        $bkmData = [
            [
                'tanggal' => $bulan . '-01',
                'pihak' => 'KAS BESAR MCA - RATNA',
                'keterangan_utama' => 'Pencairan Kas Harian',
                'items' => [
                    ['kode' => '100', 'ket' => 'Pencairan kas harian operasional', 'jumlah' => 10000000],
                ],
            ],
            [
                'tanggal' => $bulan . '-08',
                'pihak' => 'PT Abadi Sentosa',
                'keterangan_utama' => 'Pembayaran Tunai dari Customer',
                'items' => [
                    ['kode' => '120', 'ket' => 'Pelunasan Invoice INV-2026-001', 'jumlah' => 5500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-15',
                'pihak' => 'KAS BESAR MCA',
                'keterangan_utama' => 'Pencairan Kas Harian',
                'items' => [
                    ['kode' => '100', 'ket' => 'Pencairan kas harian minggu ke-3', 'jumlah' => 8000000],
                ],
            ],
            [
                'tanggal' => $bulan . '-20',
                'pihak' => 'CV Mitra Jaya',
                'keterangan_utama' => 'Pembayaran Tunai',
                'items' => [
                    ['kode' => '120', 'ket' => 'DP Invoice INV-2026-005', 'jumlah' => 3000000],
                ],
            ],
        ];

        $bkmCount = 0;
        foreach ($bkmData as $data) {
            $bkmCount++;
            $total = collect($data['items'])->sum('jumlah');
            $bukti = BuktiKas::create([
                'jenis' => 'BKM',
                'no_bukti' => 'BKM-202606-' . str_pad($bkmCount, 4, '0', STR_PAD_LEFT),
                'tanggal' => $data['tanggal'],
                'pihak' => $data['pihak'],
                'keterangan_utama' => $data['keterangan_utama'],
                'total' => $total,
                'terbilang' => \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah',
                'status' => $bkmCount <= 2 ? 'konfirmasi' : 'draft',
                'created_by' => $userId,
            ]);

            $urutan = 0;
            foreach ($data['items'] as $item) {
                $urutan++;
                $perkiraanId = $perkiraans[$item['kode']] ?? null;
                $perkiraan = $perkiraanId ? NoPerkiraan::find($perkiraanId) : null;
                $bukti->details()->create([
                    'no_perkiraan_id' => $perkiraanId,
                    'no_account' => $perkiraan?->kode_perkiraan,
                    'nama_perkiraan' => $perkiraan?->nama_perkiraan,
                    'keterangan' => $item['ket'],
                    'jumlah' => $item['jumlah'],
                    'urutan' => $urutan,
                ]);
            }
        }

        // =========================================
        // BBK (Bukti Bank Keluar) - Pembayaran via Bank
        // =========================================
        $bankId = $perkiraans['110'] ?? null; // Bank Mandiri

        $bbkData = [
            [
                'tanggal' => $bulan . '-05',
                'pihak' => 'PT Supplier Material',
                'keterangan_utama' => 'Pembayaran PO Supplier',
                'items' => [
                    ['kode' => '300', 'ket' => 'Pelunasan PO-2026-001 Material', 'jumlah' => 15000000],
                ],
            ],
            [
                'tanggal' => $bulan . '-12',
                'pihak' => 'CV Baja Prima',
                'keterangan_utama' => 'Pembayaran Supplier Besi',
                'items' => [
                    ['kode' => '300', 'ket' => 'DP PO-2026-003 Besi Beton', 'jumlah' => 8500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-20',
                'pihak' => 'PT Sejahtera Bersama',
                'keterangan_utama' => 'Pembayaran Supplier',
                'items' => [
                    ['kode' => '300', 'ket' => 'Pelunasan PO-2026-005', 'jumlah' => 12000000],
                ],
            ],
        ];

        $bbkCount = 0;
        foreach ($bbkData as $data) {
            $bbkCount++;
            $total = collect($data['items'])->sum('jumlah');
            $bukti = BuktiBank::create([
                'jenis' => 'BBK',
                'no_bukti' => 'BBK-202606-' . str_pad($bbkCount, 4, '0', STR_PAD_LEFT),
                'tanggal' => $data['tanggal'],
                'pihak' => $data['pihak'],
                'bank_account_id' => $bankId,
                'keterangan_utama' => $data['keterangan_utama'],
                'total' => $total,
                'terbilang' => \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah',
                'status' => 'konfirmasi',
                'created_by' => $userId,
            ]);

            $urutan = 0;
            foreach ($data['items'] as $item) {
                $urutan++;
                $perkiraanId = $perkiraans[$item['kode']] ?? null;
                $perkiraan = $perkiraanId ? NoPerkiraan::find($perkiraanId) : null;
                $bukti->details()->create([
                    'no_perkiraan_id' => $perkiraanId,
                    'kode_perkiraan' => $perkiraan?->kode_perkiraan,
                    'nama_perkiraan' => $perkiraan?->nama_perkiraan,
                    'keterangan' => $item['ket'],
                    'jumlah' => $item['jumlah'],
                    'urutan' => $urutan,
                ]);
            }
        }

        // =========================================
        // BBM (Bukti Bank Masuk) - Penerimaan via Bank
        // =========================================
        $bbmData = [
            [
                'tanggal' => $bulan . '-10',
                'pihak' => 'PT Abadi Sentosa',
                'keterangan_utama' => 'Transfer masuk dari Customer',
                'items' => [
                    ['kode' => '120', 'ket' => 'Pembayaran Invoice INV-2026-002', 'jumlah' => 22000000],
                ],
            ],
            [
                'tanggal' => $bulan . '-18',
                'pihak' => 'CV Makmur Jaya',
                'keterangan_utama' => 'Pembayaran Customer',
                'items' => [
                    ['kode' => '120', 'ket' => 'Pelunasan Invoice INV-2026-004', 'jumlah' => 18500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-25',
                'pihak' => 'PT Indo Megah',
                'keterangan_utama' => 'DP Customer',
                'items' => [
                    ['kode' => '120', 'ket' => 'DP Invoice INV-2026-007', 'jumlah' => 10000000],
                ],
            ],
        ];

        $bbmCount = 0;
        foreach ($bbmData as $data) {
            $bbmCount++;
            $total = collect($data['items'])->sum('jumlah');
            $bukti = BuktiBank::create([
                'jenis' => 'BBM',
                'no_bukti' => 'BBM-202606-' . str_pad($bbmCount, 4, '0', STR_PAD_LEFT),
                'tanggal' => $data['tanggal'],
                'pihak' => $data['pihak'],
                'bank_account_id' => $bankId,
                'keterangan_utama' => $data['keterangan_utama'],
                'total' => $total,
                'terbilang' => \App\Http\Controllers\BuktiKasController::angkaKeTerbilang($total) . ' Rupiah',
                'status' => 'konfirmasi',
                'created_by' => $userId,
            ]);

            $urutan = 0;
            foreach ($data['items'] as $item) {
                $urutan++;
                $perkiraanId = $perkiraans[$item['kode']] ?? null;
                $perkiraan = $perkiraanId ? NoPerkiraan::find($perkiraanId) : null;
                $bukti->details()->create([
                    'no_perkiraan_id' => $perkiraanId,
                    'kode_perkiraan' => $perkiraan?->kode_perkiraan,
                    'nama_perkiraan' => $perkiraan?->nama_perkiraan,
                    'keterangan' => $item['ket'],
                    'jumlah' => $item['jumlah'],
                    'urutan' => $urutan,
                ]);
            }
        }

        // =========================================
        // Jurnal Koreksi
        // =========================================
        $jkData = [
            [
                'tanggal' => $bulan . '-28',
                'keterangan' => 'Beban Gaji Karyawan Bulan Juni 2026',
                'items' => [
                    ['kode' => '801', 'ket' => 'Gaji pokok karyawan', 'debit' => 25000000, 'kredit' => 0],
                    ['kode' => '802', 'ket' => 'Tunjangan karyawan', 'debit' => 5000000, 'kredit' => 0],
                    ['kode' => '344', 'ket' => 'Hutang gaji karyawan', 'debit' => 0, 'kredit' => 30000000],
                ],
            ],
            [
                'tanggal' => $bulan . '-28',
                'keterangan' => 'Administrasi Bank Mandiri Juni 2026',
                'items' => [
                    ['kode' => '920', 'ket' => 'Biaya admin bank Mandiri', 'debit' => 150000, 'kredit' => 0],
                    ['kode' => '110', 'ket' => 'Potongan rekening Bank Mandiri', 'debit' => 0, 'kredit' => 150000],
                ],
            ],
            [
                'tanggal' => $bulan . '-30',
                'keterangan' => 'Penyesuaian Akhir Bulan - Penyusutan',
                'items' => [
                    ['kode' => '817', 'ket' => 'Beban penyusutan aset tetap', 'debit' => 2500000, 'kredit' => 0],
                    ['kode' => '210', 'ket' => 'Akumulasi penyusutan', 'debit' => 0, 'kredit' => 2500000],
                ],
            ],
            [
                'tanggal' => $bulan . '-30',
                'keterangan' => 'Pajak PPh Pasal 21 Juni 2026',
                'items' => [
                    ['kode' => '814', 'ket' => 'Beban pajak PPh 21', 'debit' => 1500000, 'kredit' => 0],
                    ['kode' => '341', 'ket' => 'Hutang pajak PPh 21', 'debit' => 0, 'kredit' => 1500000],
                ],
            ],
        ];

        $jkCount = 0;
        foreach ($jkData as $data) {
            $jkCount++;
            $jurnal = JurnalKoreksi::create([
                'no_jurnal' => 'JK-202606-' . str_pad($jkCount, 4, '0', STR_PAD_LEFT),
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'status' => $jkCount <= 2 ? 'konfirmasi' : 'draft',
                'created_by' => $userId,
            ]);

            $urutan = 0;
            foreach ($data['items'] as $item) {
                $urutan++;
                $perkiraanId = $perkiraans[$item['kode']] ?? null;
                $perkiraan = $perkiraanId ? NoPerkiraan::find($perkiraanId) : null;
                $jurnal->details()->create([
                    'no_perkiraan_id' => $perkiraanId,
                    'kode_perkiraan' => $perkiraan?->kode_perkiraan,
                    'nama_perkiraan' => $perkiraan?->nama_perkiraan,
                    'keterangan' => $item['ket'],
                    'debit' => $item['debit'],
                    'kredit' => $item['kredit'],
                    'urutan' => $urutan,
                ]);
            }
        }

        $this->command->info('Accounting dummy data seeded successfully!');
        $this->command->info("- BKK: {$bkkCount} transaksi");
        $this->command->info("- BKM: {$bkmCount} transaksi");
        $this->command->info("- BBK: {$bbkCount} transaksi");
        $this->command->info("- BBM: {$bbmCount} transaksi");
        $this->command->info("- Jurnal Koreksi: {$jkCount} transaksi");
    }
}
