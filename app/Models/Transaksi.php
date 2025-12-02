<?php

// app/Models/Transaksi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi'; // 👉 Menentukan nama tabel secara eksplisit
    
    protected $fillable = [
        'user_id',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status',
        'detail_keranjang',
    ];

    protected $casts = [
        'detail_keranjang' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

