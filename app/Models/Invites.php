<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invites extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'invitedBy',
        'member_id',
        'status',
        'created_at',
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id', 'group_id');
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invitedBy', 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id', 'user_id');
    }
}