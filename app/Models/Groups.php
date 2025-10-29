<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    use HasFactory;
    protected $primaryKey = 'group_id';

    protected $fillable = [
        'group_name',
        'leader_id',
        'topic_id',
        'class_id'
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id', 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topics::class, 'topic_id', 'topic_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSection::class, 'class_id', 'class_id');
    }

    public function invites()
    {
        return $this->hasMany(Invites::class, 'group_id', 'group_id');
    }

    public function joinRequests()
    {
        return $this->hasMany(Join_Requests::class, 'group_id', 'group_id');
    }

    public function topicRequests()
    {
        return $this->hasMany(Topic_Requests::class, 'group_id', 'group_id');
    }
}