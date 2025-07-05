<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ThemeAssignmentController extends Controller
{
    /**
     * Display the theme assignment interface
     */
    public function index()
    {
        $companies = Company::with('theme')->latest()->paginate(20);
        $themes = Theme::active()->get();
        
        return view('super-admin.theme-assignment.index', compact('companies', 'themes'));
    }

    /**
     * Assign a theme to a company
     */
    public function assign(Request $request, Company $company)
    {
        $request->validate([
            'theme_id' => 'required|exists:themes,id'
        ]);

        $theme = Theme::findOrFail($request->theme_id);

        // Check if theme is active
        if ($theme->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign inactive theme'
            ], 400);
        }

        // Update company theme
        $company->update([
            'theme_id' => $theme->id
        ]);

        // Update theme download count
        $theme->increment('downloads_count');

        // Clear company cache
        Cache::forget("company_theme_{$company->id}");
        Cache::forget("company_settings_{$company->id}");

        return response()->json([
            'success' => true,
            'message' => 'Theme assigned successfully',
            'theme' => $theme
        ]);
    }

    /**
     * Remove theme assignment from company
     */
    public function unassign(Company $company)
    {
        $company->update([
            'theme_id' => null
        ]);

        // Clear company cache
        Cache::forget("company_theme_{$company->id}");
        Cache::forget("company_settings_{$company->id}");

        return response()->json([
            'success' => true,
            'message' => 'Theme unassigned successfully'
        ]);
    }

    /**
     * Get theme preview for company
     */
    public function preview(Company $company, Theme $theme)
    {
        return view('super-admin.theme-assignment.preview', compact('company', 'theme'));
    }

    /**
     * Bulk assign themes to companies
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'companies' => 'required|array',
            'companies.*' => 'exists:companies,id',
            'theme_id' => 'required|exists:themes,id'
        ]);

        $theme = Theme::findOrFail($request->theme_id);

        if ($theme->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign inactive theme'
            ], 400);
        }

        $companies = Company::whereIn('id', $request->companies)->get();

        foreach ($companies as $company) {
            $company->update(['theme_id' => $theme->id]);
            Cache::forget("company_theme_{$company->id}");
            Cache::forget("company_settings_{$company->id}");
        }

        // Update theme download count
        $theme->increment('downloads_count', count($companies));

        return response()->json([
            'success' => true,
            'message' => "Theme assigned to " . count($companies) . " companies successfully"
        ]);
    }

    /**
     * Get companies using a specific theme
     */
    public function getCompaniesByTheme(Theme $theme)
    {
        $companies = Company::where('theme_id', $theme->id)
            ->with('theme')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'companies' => $companies,
            'theme' => $theme
        ]);
    }

    /**
     * Get theme statistics
     */
    public function getThemeStats()
    {
        $stats = [
            'total_themes' => Theme::count(),
            'active_themes' => Theme::active()->count(),
            'free_themes' => Theme::where('is_free', true)->count(),
            'premium_themes' => Theme::where('is_free', false)->count(),
            'companies_with_themes' => Company::whereNotNull('theme_id')->count(),
            'companies_without_themes' => Company::whereNull('theme_id')->count(),
            'most_popular_themes' => Theme::orderBy('downloads_count', 'desc')
                ->take(5)
                ->get(),
            'recent_assignments' => Company::whereNotNull('theme_id')
                ->with('theme')
                ->latest('updated_at')
                ->take(10)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Generate theme usage report
     */
    public function generateReport()
    {
        $themes = Theme::with(['companies' => function($query) {
            $query->select('id', 'name', 'domain', 'status', 'theme_id');
        }])->get();

        $report = [];
        foreach ($themes as $theme) {
            $report[] = [
                'theme_name' => $theme->name,
                'theme_category' => $theme->category,
                'total_companies' => $theme->companies->count(),
                'active_companies' => $theme->companies->where('status', 'active')->count(),
                'inactive_companies' => $theme->companies->where('status', 'inactive')->count(),
                'download_count' => $theme->downloads_count,
                'rating' => $theme->rating,
                'is_free' => $theme->is_free,
                'companies' => $theme->companies->map(function($company) {
                    return [
                        'name' => $company->name,
                        'domain' => $company->domain,
                        'status' => $company->status
                    ];
                })
            ];
        }

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * Clone theme settings from one company to another
     */
    public function cloneTheme(Request $request, Company $sourceCompany, Company $targetCompany)
    {
        if (!$sourceCompany->theme_id) {
            return response()->json([
                'success' => false,
                'message' => 'Source company has no theme assigned'
            ], 400);
        }

        $theme = $sourceCompany->theme;

        if ($theme->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot clone inactive theme'
            ], 400);
        }

        $targetCompany->update([
            'theme_id' => $theme->id
        ]);

        // Update theme download count
        $theme->increment('downloads_count');

        // Clear target company cache
        Cache::forget("company_theme_{$targetCompany->id}");
        Cache::forget("company_settings_{$targetCompany->id}");

        return response()->json([
            'success' => true,
            'message' => 'Theme cloned successfully',
            'theme' => $theme
        ]);
    }

    /**
     * Get theme customization options for a company
     */
    public function getCustomizationOptions(Company $company)
    {
        if (!$company->theme_id) {
            return response()->json([
                'success' => false,
                'message' => 'Company has no theme assigned'
            ], 400);
        }

        $theme = $company->theme;
        $customizations = $company->settings['theme_customizations'] ?? [];

        return response()->json([
            'success' => true,
            'theme' => $theme,
            'customizations' => $customizations,
            'available_options' => [
                'color_scheme' => $theme->color_scheme,
                'components' => $theme->components,
                'layout_options' => [
                    'header_style' => ['fixed', 'sticky', 'static'],
                    'footer_style' => ['minimal', 'detailed', 'contact'],
                    'sidebar_position' => ['left', 'right', 'none'],
                    'layout_width' => ['full', 'boxed', 'fluid']
                ]
            ]
        ]);
    }

    /**
     * Save theme customizations for a company
     */
    public function saveCustomizations(Request $request, Company $company)
    {
        if (!$company->theme_id) {
            return response()->json([
                'success' => false,
                'message' => 'Company has no theme assigned'
            ], 400);
        }

        $request->validate([
            'customizations' => 'required|array'
        ]);

        $settings = $company->settings ?? [];
        $settings['theme_customizations'] = $request->customizations;

        $company->update(['settings' => $settings]);

        // Clear company cache
        Cache::forget("company_theme_{$company->id}");
        Cache::forget("company_settings_{$company->id}");

        return response()->json([
            'success' => true,
            'message' => 'Theme customizations saved successfully'
        ]);
    }
}
