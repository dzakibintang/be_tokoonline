<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatTransaksi extends Model
{
    protected $table = 'riwayat_transaksi';

    protected $fillable = [
        'user_id',
        'metode_pembayaran',
        'bukti_pembayaran',
        'detail_keranjang',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'detail_keranjang' => 'array',
        'tanggal_transaksi' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
