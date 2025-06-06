<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'old_asset_code',
        'pic_dept',
        'location',
        'asset_category',
        'asset_position_dept',
        'asset_type',
        'merk',
        'description',
        'serial_number',
        'acquisition_date',
        'disposal_date',
        'useful_life',
        'acquisition_value',
        'status',
        'hand_over_date',
        'user',
        'dept',
        'barcode_availability',
    ];

    public function userhist()
    {
        return $this->hasMany(userhist::class);
    }

    public function repairStatuses()
    {
        return $this->hasMany(repairstatus::class);
    }

    public function dispose()
    {
        return $this->hasMany(dispose::class);
    }
}
