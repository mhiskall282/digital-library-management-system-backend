<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $level = $request->input('level');
        $semester = $request->input('semester');
        $search = $request->input('search');

        $query = Category::withCount('resources')->latest();

        if ($level) {
            $query->where('level', $level);
        }
        if ($semester) {
            $query->where('semester', $semester);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(12)->withQueryString();

        return view('admin.categories.index', compact('categories', 'level', 'semester', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20'],
            'course_name' => ['required', 'string', 'max:255'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'semester' => ['required', Rule::in(['FIRST', 'SECOND'])],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create([
            'name' => $validated['course_name'],
            'course_code' => strtoupper(trim($validated['course_code'])),
            'course_name' => trim($validated['course_name']),
            'level' => $validated['level'],
            'semester' => $validated['semester'],
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLog::record('CATEGORY_CREATE', $request->user(), $category);

        return back()->with('success', 'Course category created successfully!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20'],
            'course_name' => ['required', 'string', 'max:255'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'semester' => ['required', Rule::in(['FIRST', 'SECOND'])],
            'description' => ['nullable', 'string'],
        ]);

        $category->update([
            'name' => $validated['course_name'],
            'course_code' => strtoupper(trim($validated['course_code'])),
            'course_name' => trim($validated['course_name']),
            'level' => $validated['level'],
            'semester' => $validated['semester'],
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLog::record('CATEGORY_UPDATE', $request->user(), $category);

        return back()->with('success', 'Course category updated successfully.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->resources()->exists()) {
            return back()->with('error', 'Cannot delete this category because it contains associated lecture slides or past questions.');
        }

        ActivityLog::record('CATEGORY_DELETE', $request->user(), null, ['course_code' => $category->course_code]);

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
