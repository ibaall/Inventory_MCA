<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->ordered_at)->format('d-m-Y') }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->total_price }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
