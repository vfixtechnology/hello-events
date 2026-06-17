<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:category list')->only(['index']);
        $this->middleware('can:category create')->only(['create', 'store']);
        $this->middleware('can:category edit')->only(['edit', 'update']);
        $this->middleware('can:category delete')->only(['destroy']);
    }

    public function index()
    {
        $categories = Category::latest()->get();
        return view('backend.category.index',compact('categories'));
    }


    public function create()
    {
        return view('backend.category.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:categories,slug',
            'image' =>  'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string'
        ]);

        unset($data['image'], $data['seo_title'], $data['seo_description']);

        $category = Category::create($data);

        // handle image
        if($request->hasFile('image'))
        {
            $category->addMediaFromRequest('image')
                     ->toMediaCollection('image');
        }

        // handle seo
        $category->seo->update([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'image'  => $category->getFirstMediaUrl('image')
        ]);

        return back()->withSuccess('Category has been created successfully!');
    }


    public function show(Category $category)
    {
        //
    }


    public function edit(Category $category)
    {
        return view('backend.category.edit',compact('category'));
    }


    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:categories,slug,'.$category->id,
            'image' =>  'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string'
        ]);

        unset($data['image'], $data['seo_title'], $data['seo_description']);

        $category->update($data);

        // handle image
        if($request->hasFile('image'))
        {
            // remove old file if exists
            $category->clearMediaCollection('image');

            $category->addMediaFromRequest('image')
                     ->toMediaCollection('image');
        }

        // handle seo
        $category->seo->update([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'image'  => $category->getFirstMediaUrl('image')
        ]);

        return redirect()->route('category.index')->withSuccess('Category has been updated successfully!');
    }


    public function destroy(Category $category)
    {
        if ($category->events()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete category with active events. Remove or reassign events first.']);
        }

        $category->delete();

        return back()->withSuccess('Category has been deleted successfully!');
    }
}
