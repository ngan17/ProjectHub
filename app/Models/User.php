<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
        'name',
        
        'isFirstLogin',
        'isHaveGroup',
    ];

    protected $hidden = [
        'password',
    ];

    protected $primaryKey = 'user_id';

    // Lớp học của user
    public function classes()
    {
        return $this->belongsToMany(
            ClassSection::class, 
            'user_classes',      
            'user_id',       
            'class_id'         
        );
    }

    // Nhóm mà user lãnh đạo
    public function groupsLed()
    {
        return $this->hasMany(Groups::class, 'leader_id', 'user_id');
    }

    // Nhóm mà user tham gia
    public function groupsJoined()
    {
        return $this->belongsToMany(Groups::class, 'group_members', 'user_id', 'group_id');
    }

    // Lời mời nhận được
    public function invites()
    {
        return $this->hasMany(Invites::class, 'member_id', 'user_id');
    }

    // Yêu cầu tham gia gửi
    public function joinRequests()
    {
        return $this->hasMany(Join_Requests::class, 'member_id', 'user_id');
    }

    // Lời mời mà user gửi
    public function sentInvites()
    {
        return $this->hasMany(Invites::class, 'invitedBy', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!str_starts_with($model->password, '$2y$')) {
                $model->password = bcrypt($model->password);
            }
        });
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        return in_array($this->role, $roles);
    }
}