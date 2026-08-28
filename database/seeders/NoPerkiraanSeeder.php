<?php

namespace Database\Seeders;

use App\Models\NoPerkiraan;
use Illuminate\Database\Seeder;

class NoPerkiraanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_perkiraan' => '100', 'nama_perkiraan' => 'Kas'],
            ['kode_perkiraan' => '110', 'nama_perkiraan' => 'Bank Mandiri'],
            ['kode_perkiraan' => '111', 'nama_perkiraan' => 'Bank xxxx'],
            ['kode_perkiraan' => '112', 'nama_perkiraan' => 'Bank xxxx'],
            ['kode_perkiraan' => '120', 'nama_perkiraan' => 'Piutang Usaha'],
            ['kode_perkiraan' => '121', 'nama_perkiraan' => 'BG / CH Mundur Diterima'],
            ['kode_perkiraan' => '130', 'nama_perkiraan' => 'Piutang Pihak III'],
            ['kode_perkiraan' => '131', 'nama_perkiraan' => 'Bon Sementara'],
            ['kode_perkiraan' => '132', 'nama_perkiraan' => 'BG Tolak Yang Diterima'],
            ['kode_perkiraan' => '140', 'nama_perkiraan' => 'Persediaan Barang Dagangan'],
            ['kode_perkiraan' => '150', 'nama_perkiraan' => 'Uang Muka Pembelian'],
            ['kode_perkiraan' => '160', 'nama_perkiraan' => 'Biaya Dibayar Dimuka'],
            ['kode_perkiraan' => '170', 'nama_perkiraan' => 'Ayat Silang'],
            ['kode_perkiraan' => '190', 'nama_perkiraan' => 'Investasi'],
            ['kode_perkiraan' => '200', 'nama_perkiraan' => 'Aset Tetap'],
            ['kode_perkiraan' => '210', 'nama_perkiraan' => 'Akumulasi Penyusutan'],
            ['kode_perkiraan' => '300', 'nama_perkiraan' => 'Hutang Usaha'],
            ['kode_perkiraan' => '310', 'nama_perkiraan' => 'Hutang Bank'],
            ['kode_perkiraan' => '320', 'nama_perkiraan' => 'Hutang Bank'],
            ['kode_perkiraan' => '330', 'nama_perkiraan' => 'Hutang Pemegang Saham'],
            ['kode_perkiraan' => '340', 'nama_perkiraan' => 'Hutang Lain - lain'],
            ['kode_perkiraan' => '341', 'nama_perkiraan' => 'Hutang Pajak'],
            ['kode_perkiraan' => '342', 'nama_perkiraan' => 'Hutang Pihak III'],
            ['kode_perkiraan' => '343', 'nama_perkiraan' => 'Hutang Karyawan'],
            ['kode_perkiraan' => '344', 'nama_perkiraan' => 'Hutang Gaji Karyawan'],
            ['kode_perkiraan' => '345', 'nama_perkiraan' => 'Hutang Direksi'],
            ['kode_perkiraan' => '350', 'nama_perkiraan' => 'Uang Muka Penjualan'],
            ['kode_perkiraan' => '400', 'nama_perkiraan' => 'Modal'],
            ['kode_perkiraan' => '500', 'nama_perkiraan' => 'Saldo Laba'],
            ['kode_perkiraan' => '501', 'nama_perkiraan' => 'Prive'],
            ['kode_perkiraan' => '600', 'nama_perkiraan' => 'Penjualan'],
            ['kode_perkiraan' => '601', 'nama_perkiraan' => 'Retur Penjualan'],
            ['kode_perkiraan' => '700', 'nama_perkiraan' => 'Pembelian'],
            ['kode_perkiraan' => '701', 'nama_perkiraan' => 'Retur Pembelian'],
            ['kode_perkiraan' => '710', 'nama_perkiraan' => 'HPP'],
            ['kode_perkiraan' => '720', 'nama_perkiraan' => 'Deviden'],
            ['kode_perkiraan' => '801', 'nama_perkiraan' => 'Beban Gaji Karyawan dan THR'],
            ['kode_perkiraan' => '802', 'nama_perkiraan' => 'Beban Tunjangan Karyawan'],
            ['kode_perkiraan' => '803', 'nama_perkiraan' => 'Beban Transportasi dan Perjalanan'],
            ['kode_perkiraan' => '804', 'nama_perkiraan' => 'Beban Listrik / PLN'],
            ['kode_perkiraan' => '805', 'nama_perkiraan' => 'Beban Air & PDAM'],
            ['kode_perkiraan' => '806', 'nama_perkiraan' => 'Beban Pos dan Materai'],
            ['kode_perkiraan' => '807', 'nama_perkiraan' => 'Beban Telekomunikasi'],
            ['kode_perkiraan' => '808', 'nama_perkiraan' => 'Beban Supplies Kantor'],
            ['kode_perkiraan' => '809', 'nama_perkiraan' => 'Beban Foto Copy dan Cetak'],
            ['kode_perkiraan' => '810', 'nama_perkiraan' => 'Beban Iuran & Langganan'],
            ['kode_perkiraan' => '811', 'nama_perkiraan' => 'Beban Ijin dan Surat'],
            ['kode_perkiraan' => '812', 'nama_perkiraan' => 'Beban Konsumsi'],
            ['kode_perkiraan' => '813', 'nama_perkiraan' => 'Beban Pengobatan'],
            ['kode_perkiraan' => '814', 'nama_perkiraan' => 'Beban Pajak'],
            ['kode_perkiraan' => '815', 'nama_perkiraan' => 'Beban PBB'],
            ['kode_perkiraan' => '816', 'nama_perkiraan' => 'Beban Pemeliharaan Aset Tetap'],
            ['kode_perkiraan' => '817', 'nama_perkiraan' => 'Beban Penyusutan Aset Tetap'],
            ['kode_perkiraan' => '818', 'nama_perkiraan' => 'Beban Bahan Baku Penolong'],
            ['kode_perkiraan' => '819', 'nama_perkiraan' => 'Beban Bahan Bakar'],
            ['kode_perkiraan' => '820', 'nama_perkiraan' => 'Beban Pemeliharaan Mesin Pabrik'],
            ['kode_perkiraan' => '821', 'nama_perkiraan' => 'Beban Jasa Pemotongan, Betel, dll'],
            ['kode_perkiraan' => '822', 'nama_perkiraan' => 'Beban Sumbangan & Entertainment'],
            ['kode_perkiraan' => '823', 'nama_perkiraan' => 'Beban Kebersihan'],
            ['kode_perkiraan' => '824', 'nama_perkiraan' => 'Beban Pajak PPN'],
            ['kode_perkiraan' => '825', 'nama_perkiraan' => 'Beban Rapat'],
            ['kode_perkiraan' => '826', 'nama_perkiraan' => 'Beban Perlengkapan & Peralatan'],
            ['kode_perkiraan' => '827', 'nama_perkiraan' => 'Beban Pemeliharaan Kendaraan'],
            ['kode_perkiraan' => '830', 'nama_perkiraan' => 'Beban Operasional Lainnya'],
            ['kode_perkiraan' => '900', 'nama_perkiraan' => 'Pendapatan Jasa Giro'],
            ['kode_perkiraan' => '910', 'nama_perkiraan' => 'Pendapatan Lain-lain'],
            ['kode_perkiraan' => '920', 'nama_perkiraan' => 'Beban Administrasi Bank'],
            ['kode_perkiraan' => '930', 'nama_perkiraan' => 'Beban Bunga Bank'],
            ['kode_perkiraan' => '931', 'nama_perkiraan' => 'Beban Bunga Pihak III'],
            ['kode_perkiraan' => '950', 'nama_perkiraan' => 'Beban Lain-lain'],
        ];

        foreach ($data as $item) {
            NoPerkiraan::updateOrCreate(
                ['kode_perkiraan' => $item['kode_perkiraan']],
                ['nama_perkiraan' => $item['nama_perkiraan']]
            );
        }
    }
}
