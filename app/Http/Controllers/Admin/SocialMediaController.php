<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of social media links
     */
    public function index()
    {
        try {
            $socialMediaLinks = SocialMediaLink::currentTenant()
                ->ordered()
                ->paginate(15);

            $predefinedPlatforms = SocialMediaLink::getPredefinedPlatforms();

            return view('admin.social-media.index', compact('socialMediaLinks', 'predefinedPlatforms'));
        } catch (\Exception $e) {
            Log::error('Social Media Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error loading social media links: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new social media link
     */
    public function create()
    {
        $predefinedPlatforms = SocialMediaLink::getPredefinedPlatforms();
        return view('admin.social-media.create', compact('predefinedPlatforms'));
    }

    /**
     * Store a newly created social media link
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon_class' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Social media name is required.',
            'icon_class.required' => 'Icon class is required.',
            'url.required' => 'URL is required.',
            'color.regex' => 'Color must be a valid hex color code.',
            'sort_order.integer' => 'Sort order must be a number.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get the next sort order if not provided
        $sortOrder = $request->sort_order;
        if (is_null($sortOrder)) {
            $sortOrder = SocialMediaLink::currentTenant()->max('sort_order') + 1;
        }

        // Create the social media link
        SocialMediaLink::create([
            'company_id' => session('selected_company_id'),
            'name' => $request->name,
            'icon_class' => $request->icon_class,
            'url' => $request->url,
            'color' => $request->color,
            'sort_order' => $sortOrder,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media link added successfully!');
    }

    /**
     * Show the form for editing a social media link
     */
    public function edit(SocialMediaLink $social_medium)
    {
        $predefinedPlatforms = SocialMediaLink::getPredefinedPlatforms();
        return view('admin.social-media.edit', compact('social_medium', 'predefinedPlatforms'));
    }

    /**
     * Update the specified social media link
     */
    public function update(Request $request, SocialMediaLink $social_medium)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon_class' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $social_medium->update([
            'name' => $request->name,
            'icon_class' => $request->icon_class,
            'url' => $request->url,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? $social_medium->sort_order,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media link updated successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(SocialMediaLink $social_medium)
    {
        $social_medium->update([
            'is_active' => !$social_medium->is_active
        ]);

        $status = $social_medium->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Social media link {$status} successfully!",
            'is_active' => $social_medium->is_active
        ]);
    }

    /**
     * Update sort order via AJAX
     */
    public function updateSortOrder(Request $request)
    {
        $items = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:social_media_links,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($items['items'] as $item) {
            SocialMediaLink::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sort order updated successfully!'
        ]);
    }

    /**
     * Remove the specified social media link
     */
    public function destroy(SocialMediaLink $social_medium)
    {
        $social_medium->delete();

        return redirect()->route('admin.social-media.index')
            ->with('success', 'Social media link deleted successfully!');
    }

    /**
     * Get social media data for frontend (API endpoint)
     */
    public function getActiveLinks()
    {
        try {
            // Get current tenant from multiple sources
            $tenant = app('current_tenant') ?? null;
            $companyId = session('selected_company_id') ?? ($tenant ? $tenant->id : null);
            
            // If no company context, try to get from domain
            if (!$companyId) {
                $host = request()->getHost();
                $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
                $companyId = $company ? $company->id : null;
            }
            
            // If still no company, return empty result
            if (!$companyId) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No company context found'
                ]);
            }
            
            $links = SocialMediaLink::where('company_id', $companyId)
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get(['name', 'icon_class', 'url', 'color']);

            return response()->json([
                'success' => true,
                'data' => $links->map(function ($link) {
                    return [
                        'name' => $link->name,
                        'icon_class' => $link->icon_class,
                        'url' => $link->formatted_url ?? $link->url,
                        'color' => $link->brand_color ?? $link->color ?? '#333'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Social Media API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'host' => request()->getHost(),
                'company_id' => session('selected_company_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching social media links',
                'data' => []
            ], 500);
        }
    }

    /**
     * Quick add predefined platform
     */
    public function quickAdd(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('Social Media Quick Add Request', [
                'data' => $request->all(),
                'user' => auth()->user()->email ?? 'Unknown',
                'company_id' => session('selected_company_id')
            ]);

            // Check if user is authenticated and has company context
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            if (!session('selected_company_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company context not found. Please refresh the page and try again.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'platform' => 'required|string',
                'url' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data provided.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $predefinedPlatforms = SocialMediaLink::getPredefinedPlatforms();
            
            if (!isset($predefinedPlatforms[$request->platform])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid platform selected.'
                ], 422);
            }

            $platform = $predefinedPlatforms[$request->platform];

            // Check if this platform already exists
            $existing = SocialMediaLink::currentTenant()
                ->where('name', $platform['name'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => $platform['name'] . ' link already exists. Please edit it instead.'
                ], 422);
            }

            // Get next sort order
            $sortOrder = SocialMediaLink::currentTenant()->max('sort_order') + 1;

            // Create the link
            $socialMediaLink = SocialMediaLink::create([
                'company_id' => session('selected_company_id'),
                'name' => $platform['name'],
                'icon_class' => $platform['icon_class'],
                'url' => $request->url,
                'color' => $platform['color'],
                'sort_order' => $sortOrder,
                'is_active' => true
            ]);

            Log::info('Social Media Link Created Successfully', [
                'link_id' => $socialMediaLink->id,
                'name' => $socialMediaLink->name,
                'company_id' => $socialMediaLink->company_id
            ]);

            return response()->json([
                'success' => true,
                'message' => $platform['name'] . ' link added successfully!',
                'data' => [
                    'id' => $socialMediaLink->id,
                    'name' => $socialMediaLink->name,
                    'url' => $socialMediaLink->formatted_url
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Social Media Quick Add Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the social media link. Please try again.',
                'debug_error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
