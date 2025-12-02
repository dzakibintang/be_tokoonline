<?php

namespace App\Http\Controllers;

use App\Models\RiwayatTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatTransaksiController extends Controller
{
    // ✅ Menampilkan semua riwayat (admin) atau milik sendiri (pelanggan)
    public function index()
    {
        if (Auth::user()->peran === 'admin') {
            $riwayat = RiwayatTransaksi::with('user')->latest()->get();
        } else {
            $riwayat = RiwayatTransaksi::with('user')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return response()->json($riwayat);
    }

    // ✅ Menampilkan satu riwayat transaksi berdasarkan ID
    public function show($id)
    {
        $item = RiwayatTransaksi::with('user')->find($id);

        if (!$item) {
            return response()->json(['error' => 'Riwayat tidak ditemukan'], 404);
        }

        if (Auth::user()->peran !== 'admin' && $item->user_id !== Auth::id()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        return response()->json($item);
    }
}
