<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'kode', 'nama', 'brand', 'kategori', 'harga', 'branch',
        'tahun_beli', 'nama_vendor', 'vendor_link', 'qty', 'deskripsi', 'gambar'
    ];
}
