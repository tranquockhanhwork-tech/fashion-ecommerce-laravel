<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $categories = Category::query()
            ->with('parent')
            ->withCount(['children', 'products'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $totalCategories = Category::count();
        $rootCategories = Category::whereNull('parent_id')->count();
        $categoriesWithProducts = Category::has('products')->count();

        return view('admin.categories.index', compact(
            'categories',
            'search',
            'totalCategories',
            'rootCategories',
            'categoriesWithProducts'
        ));
    }

    public function create()
    {
        $parentCategories = Category::query()
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCategory($request);
        $data['slug'] = $this->makeUniqueSlug($data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục mới.');
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::query()
            ->whereKeyNot($category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $this->validateCategory($request, $category);

        if ($category->name !== $data['name']) {
            $data['slug'] = $this->makeUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(string $id)
    {
        $category = Category::withCount(['children', 'products'])->findOrFail($id);

        if ($category->children_count > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục đang có danh mục con.');
        }

        if ($category->products_count > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục đang chứa sản phẩm.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục.');
    }

    protected function validateCategory(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                Rule::notIn([$category?->id]),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    protected function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (
            Category::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
