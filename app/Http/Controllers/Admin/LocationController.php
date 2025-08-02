<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('company')
            ->ordered()
            ->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'working_hours' => 'nullable|array',
            'working_hours.*.is_open' => 'boolean',
            'working_hours.*.open' => 'nullable|date_format:H:i',
            'working_hours.*.close' => 'nullable|date_format:H:i',
        ]);

        $data = $request->except(['image']);
        $data['company_id'] = auth()->user()->company_id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('locations', $filename, 'public');
            $data['image'] = $path;
        }

        Location::create($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        return view('admin.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'working_hours' => 'nullable|array',
            'working_hours.*.is_open' => 'boolean',
            'working_hours.*.open' => 'nullable|date_format:H:i',
            'working_hours.*.close' => 'nullable|date_format:H:i',
        ]);

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('locations', $filename, 'public');
            $data['image'] = $path;
        }

        $location->update($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        // Delete image
        if ($location->image) {
            Storage::disk('public')->delete($location->image);
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    public function toggle(Location $location)
    {
        $location->update(['is_active' => !$location->is_active]);

        $status = $location->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Location {$status} successfully.");
    }

    // API endpoint for frontend map
    public function apiLocations(Request $request)
    {
        $locations = Location::active()->ordered()->get();

        // If user provides coordinates, calculate distances
        if ($request->has(['lat', 'lng'])) {
            $userLat = $request->lat;
            $userLng = $request->lng;

            foreach ($locations as $location) {
                $location->distance = $location->getDistanceFrom($userLat, $userLng);
            }

            // Sort by distance
            $locations = $locations->sortBy('distance')->values();
        }

        return response()->json([
            'success' => true,
            'locations' => $locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'phone' => $location->phone,
                    'email' => $location->email,
                    'description' => $location->description,
                    'working_hours' => $location->working_hours,
                    'formatted_working_hours' => $location->formatted_working_hours,
                    'is_open_now' => $location->is_open_now,
                    'image_url' => $location->image_url,
                    'distance' => $location->distance ?? null,
                ];
            })
        ]);
    }
}
