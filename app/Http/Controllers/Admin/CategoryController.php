<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Thêm danh mục thất bại. Vui lòng thử lại.')
                ->with('openModal', 'modalAddCategory');
        }
    }


    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cập nhật danh mục thất bại. Vui lòng thử lại.')
                ->with('openModal', 'modalEditCategory')
                ->with('editingId', $id);
        }
    }


    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return back()->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Xóa danh mục thất bại. Vui lòng thử lại.');
        }
    }
}
