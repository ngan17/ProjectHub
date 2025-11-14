<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function model(array $row)
    {
        // Kiểm tra xem email đã tồn tại chưa
        $existingUser = User::where('email', $row['email'])->first();
        
        if ($existingUser) {
            // Nếu đã tồn tại, chỉ thêm vào lớp mới
            if (!$existingUser->classes()->where('class_id', $this->classId)->exists()) {
                $existingUser->classes()->attach($this->classId);
            }
            return null;
        }

        // Tạo user mới
        $student = User::create([
            'name' => $row['ho_va_ten'],
            'email' => $row['email'],
            'password' => Hash::make($row['mat_khau'] ?? '123456'), // Mật khẩu mặc định nếu không có
            'role' => 'student',
            'isFirstLogin' => true,
            'isHaveGroup' => false,
        ]);

        // Gán vào lớp
        $student->classes()->attach($this->classId);

        return $student;
    }

    public function rules(): array
    {
        return [
            'ho_va_ten' => 'required|string|max:255',
            'email' => 'required|email',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ho_va_ten.required' => 'Họ và tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
        ];
    }
}