<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('sort_order')->paginate(10);
        return view('super-admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('super-admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'trial_days' => 'required|integer|min:0|max:365',
            'features' => 'array',
            'limits' => 'array',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive'
        ]);

        Package::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'trial_days' => $request->trial_days,
            'features' => $request->features ?? [],
            'limits' => $request->limits ?? [],
            'is_popular' => $request->boolean('is_popular'),
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status
        ]);

        return redirect()->route('super-admin.packages.index')
                        ->with('success', 'Package created successfully!');
    }

    public function show(Package $package)
    {
        $package->load('companies');
        return view('super-admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        return view('super-admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'trial_days' => 'required|integer|min:0|max:365',
            'features' => 'array',
            'limits' => 'array',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive'
        ]);

        $package->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'billing_cycle' => $request->billing_cycle,
            'trial_days' => $request->trial_days,
            'features' => $request->features ?? [],
            'limits' => $request->limits ?? [],
            'is_popular' => $request->boolean('is_popular'),
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status
        ]);

        return redirect()->route('super-admin.packages.index')
                        ->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        if ($package->companies()->count() > 0) {
            return redirect()->route('super-admin.packages.index')
                            ->with('error', 'Cannot delete package that has active companies.');
        }

        $package->delete();

        return redirect()->route('super-admin.packages.index')
                        ->with('success', 'Package deleted successfully!');
    }

    public function toggleStatus(Package $package)
    {
        $package->update([
            'status' => $package->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json(['success' => true]);
    }
}
