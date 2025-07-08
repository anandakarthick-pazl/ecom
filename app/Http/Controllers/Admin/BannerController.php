<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\DynamicStorage;

class BannerController extends Controller
{
    use DynamicStorage;
    public function index()
    {
        $banners = Banner::orderBy('position')
                        ->orderBy('sort_order')
                        ->paginate(20);
        
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';
        $uploadResult = $this->storeFileDynamically($request->file('image'), 'banners', 'banners');
        $data['image'] = $uploadResult['file_path'];

        Banner::create($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner created successfully!');
    }

    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        if ($request->hasFile('image')) {
            if ($banner->image) {
                $this->deleteFileDynamically($banner->image);
            }
            $uploadResult = $this->storeFileDynamically($request->file('image'), 'banners', 'banners');
            $data['image'] = $uploadResult['file_path'];
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            $this->deleteFileDynamically($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner deleted successfully!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        
        $status = $banner->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Banner {$status} successfully!");
    }
}
