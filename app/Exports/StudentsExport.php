<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $classId;

    public function __construct($classId = null)
    {
        $this->classId = $classId;
    }

    public function collection()
    {
        $query = User::where('role', 'student')->with('classes');
        
        if ($this->classId) {
            $query->whereHas('classes', function($q) {
                $q->where('class_id', $this->classId);
            });
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Họ và tên',
            'Email',
            'Lớp học phần',
            'Trạng thái nhóm',
            'Ngày tạo',
        ];
    }

    public function map($student): array
    {
        static $index = 0;
        $index++;
        
        return [
            $index,
            $student->name,
            $student->email,
            $student->classes->pluck('class_name')->implode(', '),
            $student->isHaveGroup ? 'Đã có nhóm' : 'Chưa có nhóm',
            $student->created_at->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'E2EFDA']]],
        ];
    }
}