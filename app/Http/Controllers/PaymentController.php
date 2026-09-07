<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Order;
use App\Models\PurchaseOrder;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required|in:purchase,sale',
            'transaction_id' => 'required|integer',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        $type = $request->transaction_type;
        $txId = $request->transaction_id;
        $amount = $request->amount;

        return DB::transaction(function () use ($type, $txId, $amount, $request) {
            if ($type === 'purchase') {
                $tx = PurchaseOrder::findOrFail($txId);
                $partyType = 'supplier';
                $partyName = $tx->supplier_name;
            } else {
                $tx = Order::findOrFail($txId);
                $partyType = 'customer';
                $partyName = $tx->customer_name;
            }

            $remaining = $tx->remaining;
            if ($amount > $remaining) {
                return redirect()->back()->withErrors([
                    'amount' => 'Nominal pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($remaining, 0, '.', ',') . ')'
                ]);
            }

            Payment::create([
                'transaction_type' => $type,
                'transaction_id' => $txId,
                'party_type' => $partyType,
                'party_name' => $partyName,
                'payment_date' => $request->payment_date,
                'amount' => $amount,
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            // Update status pembayaran di transaksi induk agar sinkron
            $newRemaining = max(0, $remaining - $amount);
            if ($type === 'purchase') {
                $tx->status = $newRemaining <= 0 ? 'lunas' : 'belum lunas';
                $tx->save();
            } else {
                $tx->status_pembayaran = $newRemaining <= 0 ? 'lunas' : 'belum dibayar';
                $tx->save();
            }

            return redirect()->back()->with('success', 'Pembayaran berhasil disimpan.');
        });
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $payment = Payment::findOrFail($id);
            $type = $payment->transaction_type;
            $txId = $payment->transaction_id;
            $oldAmount = $payment->amount;
            $newAmount = $request->amount;

            if ($type === 'purchase') {
                $tx = PurchaseOrder::findOrFail($txId);
            } else {
                $tx = Order::findOrFail($txId);
            }

            // Hitung ulang tagihan maksimal yang bisa dibayar
            // (sisa saat ini + nominal yang sebelumnya sudah dibayarkan di pembayaran ini)
            $maxPayable = $tx->remaining + $oldAmount;
            
            if ($newAmount > $maxPayable) {
                return redirect()->back()->withErrors([
                    'amount' => 'Nominal pembayaran tidak boleh melebihi total tagihan yang belum terbayar (Rp ' . number_format($maxPayable, 0, '.', ',') . ')'
                ]);
            }

            // Update payment
            $payment->update([
                'payment_date' => $request->payment_date,
                'amount' => $newAmount,
                'note' => $request->note,
            ]);

            // Update tx status
            $newRemaining = max(0, $maxPayable - $newAmount);
            if ($type === 'purchase') {
                $tx->status = $newRemaining <= 0 ? 'lunas' : 'belum lunas';
                $tx->save();
            } else {
                $tx->status_pembayaran = $newRemaining <= 0 ? 'lunas' : 'belum dibayar';
                $tx->save();
            }

            return redirect()->back()->with('success', 'Pembayaran berhasil diperbarui.');
        });
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $payment = Payment::findOrFail($id);
            $type = $payment->transaction_type;
            $txId = $payment->transaction_id;

            $payment->delete();

            // Ambil transaksi induk dan hitung ulang sisa tagihan untuk mengupdate status
            if ($type === 'purchase') {
                $tx = PurchaseOrder::findOrFail($txId);
                $tx->status = $tx->remaining <= 0 ? 'lunas' : 'belum lunas';
                $tx->save();
            } else {
                $tx = Order::findOrFail($txId);
                $tx->status_pembayaran = $tx->remaining <= 0 ? 'lunas' : 'belum dibayar';
                $tx->save();
            }

            return redirect()->back()->with('success', 'Pembayaran berhasil dihapus.');
        });
    }
}
