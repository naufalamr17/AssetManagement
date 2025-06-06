<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_surat',
        'tanggal',
        'perihal',
        'jenisBA',
        'creator',
        'location',
        'file',
    ];

    public function beritaAcara()
    {
        return $this->hasMany(BeritaAcara::class);
    }

    public function formKerusakan()
    {
        return $this->hasMany(FormKerusakan::class);
    }

    public function bast()
    {
        return $this->hasMany(Bast::class);
    }
}
