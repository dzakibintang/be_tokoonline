<?php

// app/Http/Controllers/TransaksiController.php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\RiwayatTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    // ✅ Checkout keranjang
    public function checkout(Request $request)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $keranjang = Keranjang::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        if ($keranjang->isEmpty()) {
            return response()->json(['error' => 'Keranjang kosong'], 400);
        }

        // Cek stok
        foreach ($keranjang as $item) {
            if ($item->jumlah > $item->produk->stok) {
                return response()->json(['error' => 'Stok tidak mencukupi untuk produk: ' . $item->produk->nama], 422);
            }
        }

        // Simpan bukti pembayaran
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Simpan transaksi
        $transaksi = Transaksi::create([
            'user_id' => Auth::id(),
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_pembayaran' => $path,
            'status' => 'menunggu',
            'detail_keranjang' => $keranjang->toArray(),
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat, menunggu verifikasi',
            'transaksi' => $transaksi
        ], 201);
    }

    // ✅ Admin update status
    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $transaksi->status = $request->status;
        $transaksi->save();

        // Jika diterima, kurangi stok & reset keranjang
        if ($request->status === 'diterima') {
            foreach ($transaksi->detail_keranjang as $item) {
                $produk = Produk::find($item['produk_id']);
                if ($produk) {
                    $produk->stok -= $item['jumlah'];
                    $produk->save();
                }
            }

            Keranjang::where('user_id', $transaksi->user_id)->delete();
        }

        if ($request->status === 'diterima') {
        RiwayatTransaksi::create([
        'user_id' => $transaksi->user_id,
        'metode_pembayaran' => $transaksi->metode_pembayaran,
        'bukti_pembayaran' => $transaksi->bukti_pembayaran,
        'detail_keranjang' => $transaksi->detail_keranjang,
        'tanggal_transaksi' => now(),
        ]);
        }

        return response()->json(['message' => 'Status transaksi diperbarui']);
    }

    // ✅ List transaksi pelanggan (sendiri)
    public function myTransactions()
    {
        $data = Transaksi::where('user_id', Auth::id())->get();
        return response()->json($data);
    }

    // ✅ List semua transaksi (admin)
    public function allTransactions()
    {
        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        $data = Transaksi::with('user')->get();
        return response()->json($data);
    }
}

