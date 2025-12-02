<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::all();
        return response()->json($produk);
    }

    // ✅ PUT/PATCH: Update produk (hanya admin)
    public function update(Request $request, $id)
    {
        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Hanya admin yang boleh mengubah produk'], 403);
        }

        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'sometimes|required|numeric',
            'stok' => 'sometimes|required|integer',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Jika gambar diupload baru, simpan dan hapus lama
        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $produk->gambar = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($request->only(['nama', 'deskripsi', 'harga', 'stok']));

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'produk' => $produk
        ]);
    }

    // ✅ DELETE: Hapus produk (hanya admin)
    public function destroy($id)
    {
        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Hanya admin yang boleh menghapus produk'], 403);
        }

        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        // Hapus gambar jika ada
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function show($id)
    {
    $produk = Produk::find($id);

    if (!$produk) {
        return response()->json(['error' => 'Produk tidak ditemukan'], 404);
    }

    return response()->json($produk);
    }


    public function store(Request $request)
    {
        // Hanya admin
        if (Auth::user()->peran !== 'admin') {
            return response()->json(['error' => 'Hanya admin yang boleh menambah produk'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan gambar
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('produk', 'public');
        }

        $produk = Produk::create([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => $validated['harga'],
            'stok' => $validated['stok'],
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'produk' => $produk
        ], 201);
    }
}
