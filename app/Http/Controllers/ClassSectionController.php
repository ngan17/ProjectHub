<?php

namespace App\Http\Controllers;


use App\Models\Groups;
use App\Models\Topics;
use App\Models\Invites;
use App\Models\Join_Requests;
use App\Models\Topic_requests;
use App\Models\ClassSection;
use App\Models\Group_Members;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
class ClassSectionController extends Controller
{
    /**
     * Display a listing of classes
     */
    public function index(Request $request)
    {
        $query = ClassSection::with(['subject', 'users', 'groups', 'topics']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('subject_name', 'like', "%{$search}%")
                               ->orWhere('subject_code', 'like', "%{$search}%");
                  });
            });
        }

        // Subject filter
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // QUAN TRỌNG: Phải paginate để trả về Paginator object
        $classes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Append query parameters to pagination links
        $classes->appends($request->query());

        // Get all subjects for filter dropdown
        $subjects = Subject::orderBy('subject_name')->get();

        return view('classes.index', compact('classes', 'subjects'));
    }

    /**
     * Show the form for creating a new class
     */
    public function create()
    {
        $subjects = Subject::orderBy('subject_name')->get();
        return view('classes.create', compact('subjects'));
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255|unique:class_sections,class_name',
            'subject_id' => 'required|exists:subjects,subject_id',
        ], [
            'class_name.required' => 'Tên lớp là bắt buộc',
            'class_name.unique' => 'Tên lớp đã tồn tại',
            'subject_id.required' => 'Vui lòng chọn môn học',
            'subject_id.exists' => 'Môn học không tồn tại',
        ]);

        try {
            ClassSection::create($validated);
            return redirect()->route('classes.index')
                ->with('success', 'Tạo lớp học thành công!');
        } catch (\Exception $e) {
            Log::error('Error creating class: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo lớp học!');
        }
    }

    /**
     * Display the specified class
     */
    public function show($id)
    {
        $class = ClassSection::with([
            'subject.lecturer',
            'users',
            'groups.leader',
            'groups.members',
            'groups.topic',
            'topics'
        ])->findOrFail($id);

        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified class
     */
    public function edit($id)
    {
        $class = ClassSection::with(['users', 'groups', 'topics'])->findOrFail($id);
        $subjects = Subject::orderBy('subject_name')->get();
        return view('classes.edit', compact('class', 'subjects'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, $id)
    {
        $class = ClassSection::findOrFail($id);

        $validated = $request->validate([
            'class_name' => 'required|string|max:255|unique:class_sections,class_name,' . $id . ',class_id',
            'subject_id' => 'required|exists:subjects,subject_id',
        ], [
            'class_name.required' => 'Tên lớp là bắt buộc',
            'class_name.unique' => 'Tên lớp đã tồn tại',
            'subject_id.required' => 'Vui lòng chọn môn học',
            'subject_id.exists' => 'Môn học không tồn tại',
        ]);

        try {
            $class->update($validated);
            return redirect()->route('classes.index')
                ->with('success', 'Cập nhật lớp học thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật lớp học!');
        }
    }

    /**
     * Remove the specified class
     */
    public function destroy($id)
    {
        $class = ClassSection::findOrFail($id);

        // Check if class has groups
        if ($class->groups()->count() > 0) {
            return back()->with('error', 'Không thể xóa lớp có nhóm!');
        }

        // Check if class has students
        if ($class->users()->count() > 0) {
            return back()->with('error', 'Không thể xóa lớp có sinh viên!');
        }

        try {
            $class->delete();
            return redirect()->route('classes.index')
                ->with('success', 'Xóa lớp học thành công!');
        } catch (\Exception $e) {
            Log::error('Error deleting class: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa lớp học!');
        }
    }
}
