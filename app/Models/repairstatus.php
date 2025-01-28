<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class repairstatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_id',
        'status',
        'tanggal_kerusakan',
        'tanggal_pengembalian',
        'note',
        'dokumen_breakdown',
    ];

    public function inventory()
    {
        return $this->belongsTo(inventory::class);
    }
}
