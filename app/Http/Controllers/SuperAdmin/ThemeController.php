<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::latest()->paginate(12);
        return view('super-admin.themes.index', compact('themes'));
    }

    public function create()
    {
        return view('super-admin.themes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'layout_type' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
            'demo_url' => 'nullable|url',
            'preview_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features' => 'nullable|array',
            'tags' => 'nullable|array',
            'color_scheme' => 'nullable|array',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'author' => 'nullable|string|max:255',
            'responsive' => 'boolean',
            'rtl_support' => 'boolean',
            'dark_mode' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        // Handle preview image upload
        $previewImagePath = null;
        if ($request->hasFile('preview_image')) {
            $previewImagePath = $request->file('preview_image')->store('themes/previews', 'public');
        }

        // Handle screenshots upload
        $screenshotPaths = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $screenshot) {
                $screenshotPaths[] = $screenshot->store('themes/screenshots', 'public');
            }
        }

        // Filter out empty features and tags
        $features = array_filter($request->input('features', []), function($item) {
            return !empty(trim($item));
        });
        
        $tags = array_filter($request->input('tags', []), function($item) {
            return !empty(trim($item));
        });

        // Create the theme
        $theme = Theme::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'category' => $request->category,
            'layout_type' => $request->layout_type,
            'price' => $request->price,
            'is_free' => $request->boolean('is_free'),
            'demo_url' => $request->demo_url,
            'preview_image' => $previewImagePath,
            'screenshots' => $screenshotPaths,
            'features' => array_values($features),
            'tags' => array_values($tags),
            'color_scheme' => $request->color_scheme,
            'difficulty_level' => $request->difficulty_level ?: 'beginner',
            'author' => $request->author,
            'responsive' => $request->boolean('responsive', true),
            'rtl_support' => $request->boolean('rtl_support'),
            'dark_mode' => $request->boolean('dark_mode'),
            'status' => $request->status,
            'rating' => 0,
            'downloads_count' => 0
        ]);

        $message = 'Theme created successfully!';
        
        if ($request->input('action') === 'save_and_continue') {
            return redirect()->route('super-admin.themes.edit', $theme)
                            ->with('success', $message);
        }

        return redirect()->route('super-admin.themes.index')
                        ->with('success', $message);
    }

    public function show(Theme $theme)
    {
        return view('super-admin.themes.show', compact('theme'));
    }

    public function edit(Theme $theme)
    {
        return view('super-admin.themes.edit', compact('theme'));
    }

    public function update(Request $request, Theme $theme)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'layout_type' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
            'demo_url' => 'nullable|url',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'features' => 'nullable|array',
            'tags' => 'nullable|array',
            'color_scheme' => 'nullable|array',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'author' => 'nullable|string|max:255',
            'responsive' => 'boolean',
            'rtl_support' => 'boolean',
            'dark_mode' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->except(['preview_image', 'screenshots']);
        $data['slug'] = Str::slug($request->name);
        $data['is_free'] = $request->boolean('is_free');
        $data['responsive'] = $request->boolean('responsive', true);
        $data['rtl_support'] = $request->boolean('rtl_support');
        $data['dark_mode'] = $request->boolean('dark_mode');
        $data['difficulty_level'] = $request->difficulty_level ?: 'beginner';

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            // Delete old preview image if exists
            if ($theme->preview_image && Storage::disk('public')->exists($theme->preview_image)) {
                Storage::disk('public')->delete($theme->preview_image);
            }
            $data['preview_image'] = $request->file('preview_image')->store('themes/previews', 'public');
        }

        // Handle screenshots upload
        if ($request->hasFile('screenshots')) {
            // Delete old screenshots if exists
            if ($theme->screenshots && is_array($theme->screenshots)) {
                foreach ($theme->screenshots as $screenshot) {
                    if (Storage::disk('public')->exists($screenshot)) {
                        Storage::disk('public')->delete($screenshot);
                    }
                }
            }
            
            $screenshotPaths = [];
            foreach ($request->file('screenshots') as $screenshot) {
                $screenshotPaths[] = $screenshot->store('themes/screenshots', 'public');
            }
            $data['screenshots'] = $screenshotPaths;
        }

        // Filter out empty features and tags
        if ($request->has('features')) {
            $data['features'] = array_values(array_filter($request->input('features', []), function($item) {
                return !empty(trim($item));
            }));
        }
        
        if ($request->has('tags')) {
            $data['tags'] = array_values(array_filter($request->input('tags', []), function($item) {
                return !empty(trim($item));
            }));
        }

        $theme->update($data);

        $message = 'Theme updated successfully!';
        
        if ($request->input('action') === 'save_and_continue') {
            return redirect()->route('super-admin.themes.edit', $theme)
                            ->with('success', $message);
        }

        return redirect()->route('super-admin.themes.index')
                        ->with('success', $message);
    }

    public function destroy(Theme $theme)
    {
        $theme->delete();

        return redirect()->route('super-admin.themes.index')
                        ->with('success', 'Theme deleted successfully!');
    }

    public function toggleStatus(Theme $theme)
    {
        $theme->update([
            'status' => $theme->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json(['success' => true]);
    }

    public function loadSampleThemes()
    {
        try {
            // Run the enhanced theme seeder
            Artisan::call('db:seed', ['--class' => 'ModernEcomThemeSeeder']);
            
            return response()->json([
                'success' => true,
                'message' => 'Sample themes loaded successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading sample themes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadThemePackage(Request $request)
    {
        $request->validate([
            'theme_package' => 'required|file|mimes:zip|max:10240' // 10MB max
        ]);

        try {
            $file = $request->file('theme_package');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('theme-packages', $fileName, 'local');
            
            // Extract and process the theme package
            $extractPath = storage_path('app/theme-packages/extracted/' . pathinfo($fileName, PATHINFO_FILENAME));
            
            $zip = new ZipArchive;
            if ($zip->open(storage_path('app/' . $filePath)) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
                
                // Look for theme.json configuration file
                $configFile = $extractPath . '/theme.json';
                
                if (file_exists($configFile)) {
                    $themeConfig = json_decode(file_get_contents($configFile), true);
                    
                    // Create theme from configuration
                    $theme = Theme::create([
                        'name' => $themeConfig['name'] ?? 'Imported Theme',
                        'slug' => Str::slug($themeConfig['name'] ?? 'imported-theme'),
                        'description' => $themeConfig['description'] ?? 'Imported theme package',
                        'category' => $themeConfig['category'] ?? 'general',
                        'layout_type' => $themeConfig['layout_type'] ?? 'grid',
                        'price' => $themeConfig['price'] ?? 0,
                        'is_free' => $themeConfig['is_free'] ?? true,
                        'color_scheme' => $themeConfig['color_scheme'] ?? [],
                        'features' => $themeConfig['features'] ?? [],
                        'components' => $themeConfig['components'] ?? [],
                        'tags' => $themeConfig['tags'] ?? [],
                        'difficulty_level' => $themeConfig['difficulty_level'] ?? 'beginner',
                        'responsive' => $themeConfig['responsive'] ?? true,
                        'rtl_support' => $themeConfig['rtl_support'] ?? false,
                        'dark_mode' => $themeConfig['dark_mode'] ?? false,
                        'author' => $themeConfig['author'] ?? 'Unknown',
                        'rating' => $themeConfig['rating'] ?? 0,
                        'status' => 'active'
                    ]);
                    
                    // Handle preview image if exists
                    $previewPath = $extractPath . '/preview.jpg';
                    if (!file_exists($previewPath)) {
                        $previewPath = $extractPath . '/preview.png';
                    }
                    
                    if (file_exists($previewPath)) {
                        $previewFileName = 'themes/previews/' . $theme->slug . '_preview.' . pathinfo($previewPath, PATHINFO_EXTENSION);
                        Storage::disk('public')->put($previewFileName, file_get_contents($previewPath));
                        $theme->update(['preview_image' => $previewFileName]);
                    }
                    
                    // Copy theme assets to public directory if needed
                    $assetsPath = $extractPath . '/assets';
                    if (is_dir($assetsPath)) {
                        $publicAssetsPath = public_path('themes/' . $theme->slug);
                        if (!is_dir($publicAssetsPath)) {
                            mkdir($publicAssetsPath, 0755, true);
                        }
                        $this->copyDirectory($assetsPath, $publicAssetsPath);
                    }
                    
                    // Clean up extracted files
                    $this->deleteDirectory($extractPath);
                    Storage::disk('local')->delete($filePath);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Theme package uploaded and installed successfully!',
                        'theme' => $theme
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid theme package: theme.json configuration file not found'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to extract theme package'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading theme package: ' . $e->getMessage()
            ], 500);
        }
    }

    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName(), 0755, true);
            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }
        
        return rmdir($dir);
    }
}
