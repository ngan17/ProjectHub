<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Join_Requests extends Model
{
    use HasFactory;

    protected $table = 'join_requests';
    protected $fillable = [
        'group_id',
        'member_id',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id', 'group_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id', 'user_id');
    }
}