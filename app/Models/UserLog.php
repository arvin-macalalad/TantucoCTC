<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'logged_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
