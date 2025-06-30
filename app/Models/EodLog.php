<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class EodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'schedule', 'tasks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

