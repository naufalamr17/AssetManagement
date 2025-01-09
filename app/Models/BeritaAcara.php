<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'nama',
        'nik',
        'dept',
        'jabatan',
        'alamat',
        'no_asset',
        'tanggal',
        'alasan',
        'kronologi',
        'nama_2',
        'nik_2',
        'jabatan_2',
        'tujuan',
    ];

    public function letter()
    {
        return $this->belongsTo(Letter::class);
    }
}