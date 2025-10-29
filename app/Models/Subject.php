<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Subject extends Model
{
    protected $primaryKey = 'subject_id';
    protected $fillable = ['subject_code', 'subject_name', 'lecturer_id'];

    public function lecturer() {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function classes() {
        return $this->hasMany(ClassSection::class, 'subject_id');
    }

    public function topics() {
        return $this->hasMany(Topics::class, 'subject_id');
    }
}
