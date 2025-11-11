<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSection extends Model
{
    use HasFactory;

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

    // User tham gia lớp học - many to many qua bảng user_classes
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_classes',      // Tên bảng pivot (phải giống với User model)
            'class_id',          // Foreign key của ClassSection trên bảng pivot
            'user_id'            // Foreign key của User trên bảng pivot
        );
    }
      public function topics()
    {
        return $this->hasMany(Topics::class, 'class_id');
    }
}