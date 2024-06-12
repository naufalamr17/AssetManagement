<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class repairstatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_id',
        'tanggal_kerusakan',
        'tanggal_pengembalian',
        'note',
    ];

    public function inventory()
    {
        return $this->belongsTo(inventory::class);
    }
}
