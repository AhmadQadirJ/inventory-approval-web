<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryUnit extends Model
{
    protected $fillable = ['inventory_id', 'serial_number', 'condition', 'gambar', 'location'];
    
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
