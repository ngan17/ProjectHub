<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group_Members extends Model
{
    use HasFactory;

    protected $table = 'group_members';
    protected $primaryKey = 'id';

    protected $fillable = [
        'group_id',
        'user_id',
        'role'
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id', 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}