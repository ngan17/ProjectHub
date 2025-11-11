<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topics extends Model
{
    use HasFactory;
    protected $primaryKey = 'topic_id';
    protected $fillable = [
        'name',
        'description',
        'lecturer',
        'goal',
        'requirements',
        'assigned_group_id',
        'subject_id'
    ];

    public function assignedGroup()
    {
        return $this->hasOne(Groups::class, 'topic_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function topic_requests()
{
    return $this->hasMany(Topic_requests::class, 'topic_id', 'topic_id');
}
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }


}
