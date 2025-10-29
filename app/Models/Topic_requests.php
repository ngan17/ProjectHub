<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic_requests extends Model
{
    protected $table = 'topic_requests';
    protected $primaryKey = 'request_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Vì bảng chỉ có created_at
    
    protected $fillable = [
        'topic_id',
        'group_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Định nghĩa quan hệ với Topic
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topics::class, 'topic_id', 'topic_id');
    }

    /**
     * Định nghĩa quan hệ với Group
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Groups::class, 'group_id', 'group_id');
    }

    /**
     * Định nghĩa quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Route key name cho implicit model binding
     */
    public function getRouteKeyName()
    {
        return 'request_id';
    }
}