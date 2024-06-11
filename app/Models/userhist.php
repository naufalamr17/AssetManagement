<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userhist extends Model
{
    use HasFactory;

    protected $fillable = [
        'inv_id',
        'hand_over_date',
        'user',
        'dept',
        'note',
    ];

    public function inv()
    {
        return $this->belongsTo(inventory::class);
    }
}
