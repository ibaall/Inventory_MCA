<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Jumlah Transaksi</th>
            <th>Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan as $data)
            <tr>
                <td>{{ \Carbon\Carbon::create()->month($data->bulan)->translatedFormat('F') }}</td>
                <td>{{ $data->tahun }}</td>
                <td>{{ $data->jumlah_transaksi }}</td>
                <td>{{ $data->total_pendapatan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
