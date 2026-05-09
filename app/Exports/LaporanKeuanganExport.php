<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;

class LaporanKeuanganExport implements FromCollection
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $query = Order::query();

        // Jika bulan dan tahun tersedia, filter datanya
        if ($this->bulan && $this->tahun) {
            $query->whereMonth('ordered_at', $this->bulan)
                  ->whereYear('ordered_at', $this->tahun);
        }

        return $query->get();
    }
}

