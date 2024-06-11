<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class access extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'access',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
