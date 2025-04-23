<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'nama',
        'nik',
        'jabatan',
        'nama_2',
        'nik_2',
        'jabatan_2',
        'place',
        'tanggal',
        'barang',
        'kodeprod',
        'qty',
        'satuan',
        'deskripsi',
        'alasan'
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
