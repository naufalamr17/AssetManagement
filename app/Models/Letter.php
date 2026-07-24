<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'kode_surat',
        'tanggal',
        'perihal',
        'jenisBA',
        'creator',
        'location',
        'file',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->status === 'Super Admin' ? $query : $query->where('company', $user->company ?? 'MLP');
    }

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
