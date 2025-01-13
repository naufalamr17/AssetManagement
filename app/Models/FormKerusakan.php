<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormKerusakan extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'nama',
        'nik',
        'jabatan',
        'kode_asset',
        'kerusakan',
        'penyebab',
        'tindakan',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}
