<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dispose extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_id',
        'tanggal_penghapusan',
        'note',
    ];

    public function inventory()
    {
        return $this->belongsTo(inventory::class);
    }
}
