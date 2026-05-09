@extends('layouts.app')

@section('title', 'Laporan Keuangan Bulanan')

@section('content')
<div class="container">
    <h2>Laporan Keuangan Per Bulan</h2>

    <a href="{{ route('laporan.export') }}" class="btn btn-success mb-3">Export Semua ke Excel</a>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Jumlah Transaksi</th>
                <th>Total Pendapatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F') }}</td>
                    <td>{{ $data->tahun }}</td>
                    <td>{{ $data->jumlah_transaksi }}</td>
                    <td>Rp {{ number_format($data->total_pendapatan, 0, ',', '.') }}</td>
                    <td>

                  <a href="{{ route('laporan.export', ['bulan' => $data->bulan, 'tahun' => $data->tahun]) }}"
   class="btn btn-primary btn-sm">
   Export Bulan Ini
</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
