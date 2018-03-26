<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    protected $dates = ['created_at', 'read_at'];

    protected $fillable = [
        'content', 'email', 'from_id', 'to_id', 'read_at', 'created_at'
    ];

    public function from()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('c');
    }

}
