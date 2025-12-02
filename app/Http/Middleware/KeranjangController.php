<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    // ✅ Menampilkan semua keranjang (hanya untuk admin)
    public function all()
    {
        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        $items = Keranjang::with(['produk', 'user'])->get();
        return response()->json($items);
    }

    // ✅ Menampilkan keranjang user yang sedang login
    public function index()
    {
        $items = Keranjang::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($items);
    }

    // ✅ Menampilkan keranjang berdasarkan ID
    public function show($id)
    {
        $item = Keranjang::with(['produk', 'user'])->find($id);

        if (!$item) {
            return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
        }

        // Admin boleh akses semua, pelanggan hanya miliknya
        if (Auth::user()->peran !== 'admin' && $item->user_id !== Auth::id()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        return response()->json($item);
    }

    // ✅ Tambah produk ke keranjang
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $produk = Produk::find($request->produk_id);

        if ($request->jumlah > $produk->stok) {
            return response()->json(['error' => 'Jumlah stok barang tidak cukup'], 422);
        }

        $item = Keranjang::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'produk_id' => $request->produk_id,
            ],
            [
                'jumlah' => $request->jumlah,
            ]
        );

        return response()->json([
            'message' => 'Produk ditambahkan ke keranjang',
            'keranjang' => $item
        ], 201);
    }

    // ✅ Update jumlah produk di keranjang
    // ✅ Update jumlah produk di keranjang
public function update(Request $request, $id)
{
    $item = Keranjang::with('produk')->find($id);

    if (!$item) {
        return response()->json(['error' => 'Item tidak ditemukan'], 404);
    }

    // Admin hanya boleh lihat, tidak boleh update milik user lain
    if (Auth::user()->peran !== 'admin' && $item->user_id !== Auth::id()) {
        return response()->json(['error' => 'Tidak memiliki izin untuk update keranjang ini'], 403);
    }

    // Pelanggan hanya boleh update miliknya sendiri
    if (Auth::user()->peran === 'admin') {
        return response()->json(['error' => 'Admin tidak diizinkan mengubah keranjang pelanggan'], 403);
    }

    $request->validate([
        'jumlah' => 'required|integer|min:1',
    ]);

    $produk = Produk::find($item->produk_id);

    if ($request->jumlah > $produk->stok) {
        return response()->json(['error' => 'Jumlah stok barang tidak cukup'], 422);
    }

    $item->jumlah = $request->jumlah;
    $item->save();

    return response()->json(['message' => 'Jumlah produk diperbarui', 'keranjang' => $item]);
}


    // ✅ Hapus item dari keranjang
    public function destroy($id)
    {
        $item = Keranjang::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang']);
    }
}
