<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'unsubscribe_token', 'verified_at'];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::random(60);
        });
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
}