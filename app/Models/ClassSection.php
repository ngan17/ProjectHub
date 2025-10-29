<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ClassSection extends Model
{
    protected $primaryKey = 'class_id';
    protected $fillable = ['subject_id', 'class_name'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function groups()
    {
        return $this->hasMany(Groups::class, 'class_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_class',
            'class_id',
            'user_id'
        );
    }
}
