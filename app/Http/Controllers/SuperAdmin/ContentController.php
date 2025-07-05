<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContentController extends Controller
{
    /**
     * Blog management
     */
    public function blog(Request $request)
    {
        $query = DB::table('blog_posts')
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->category, function ($q) use ($request) {
                $q->where('category', $request->category);
            });

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $blogStats = [
            'total_posts' => DB::table('blog_posts')->count(),
            'published_posts' => DB::table('blog_posts')->where('status', 'published')->count(),
            'draft_posts' => DB::table('blog_posts')->where('status', 'draft')->count(),
            'scheduled_posts' => DB::table('blog_posts')->where('status', 'scheduled')->count(),
            'total_views' => DB::table('blog_posts')->sum('views_count'),
        ];

        $categories = $this->getBlogCategories();

        return view('super-admin.content.blog', compact('posts', 'blogStats', 'categories'));
    }

    /**
     * Create blog post
     */
    public function createBlogPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,scheduled',
            'publish_at' => 'nullable|required_if:status,scheduled|date|after:now',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:160',
            'allow_comments' => 'boolean',
        ]);

        $slug = $request->slug ?: Str::slug($request->title);
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (DB::table('blog_posts')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('blog/images', 'public');
        }

        $postId = DB::table('blog_posts')->insertGetId([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'category' => $request->category,
            'tags' => $request->tags,
            'featured_image' => $featuredImagePath,
            'status' => $request->status,
            'publish_at' => $request->publish_at,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'allow_comments' => $request->boolean('allow_comments'),
            'author_id' => auth()->id(),
            'views_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'post_id' => $postId,
                'title' => $request->title,
                'status' => $request->status,
            ])
            ->log('Blog post created');

        return back()->with('success', 'Blog post created successfully.');
    }

    /**
     * Media library
     */
    public function media(Request $request)
    {
        $query = DB::table('media_files')
            ->when($request->search, function ($q) use ($request) {
                $q->where('filename', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('mime_type', 'like', $request->type . '%');
            });

        $mediaFiles = $query->orderBy('created_at', 'desc')->paginate(24);

        $mediaStats = [
            'total_files' => DB::table('media_files')->count(),
            'total_size' => $this->formatBytes(DB::table('media_files')->sum('file_size')),
            'images' => DB::table('media_files')->where('mime_type', 'like', 'image/%')->count(),
            'documents' => DB::table('media_files')->where('mime_type', 'like', 'application/%')->count(),
            'videos' => DB::table('media_files')->where('mime_type', 'like', 'video/%')->count(),
        ];

        $storageInfo = $this->getStorageInfo();

        return view('super-admin.content.media', compact('mediaFiles', 'mediaStats', 'storageInfo'));
    }

    /**
     * Upload media file
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // 10MB max
            'folder' => 'nullable|string|max:100',
        ]);

        $uploadedFiles = [];
        $folder = $request->folder ?? 'uploads';

        foreach ($request->file('files') as $file) {
            try {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($folder, $filename, 'public');
                
                $mediaId = DB::table('media_files')->insertGetId([
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'folder' => $folder,
                    'uploaded_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $uploadedFiles[] = [
                    'id' => $mediaId,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'url' => Storage::url($path),
                    'size' => $this->formatBytes($file->getSize()),
                    'type' => $file->getMimeType(),
                ];

            } catch (\Exception $e) {
                return back()->with('error', 'Failed to upload ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
            }
        }

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'uploaded_files' => count($uploadedFiles),
                'folder' => $folder,
            ])
            ->log('Media files uploaded');

        return back()->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    /**
     * Delete media file
     */
    public function deleteMedia($mediaId)
    {
        $media = DB::table('media_files')->where('id', $mediaId)->first();

        if (!$media) {
            return back()->with('error', 'Media file not found.');
        }

        try {
            // Delete from storage
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            // Delete from database
            DB::table('media_files')->where('id', $mediaId)->delete();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'media_id' => $mediaId,
                    'filename' => $media->filename,
                    'original_name' => $media->original_name,
                ])
                ->log('Media file deleted');

            return back()->with('success', 'Media file deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete media file: ' . $e->getMessage());
        }
    }

    /**
     * Content templates management
     */
    public function templates()
    {
        $templates = DB::table('content_templates')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $templateStats = [
            'total_templates' => DB::table('content_templates')->count(),
            'email_templates' => DB::table('content_templates')->where('category', 'email')->count(),
            'page_templates' => DB::table('content_templates')->where('category', 'page')->count(),
            'notification_templates' => DB::table('content_templates')->where('category', 'notification')->count(),
        ];

        return view('super-admin.content.templates', compact('templates', 'templateStats'));
    }

    /**
     * Email templates management
     */
    public function emailTemplates()
    {
        $templates = DB::table('email_templates')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $templateStats = [
            'total_templates' => DB::table('email_templates')->count(),
            'user_templates' => DB::table('email_templates')->where('category', 'user')->count(),
            'order_templates' => DB::table('email_templates')->where('category', 'order')->count(),
            'system_templates' => DB::table('email_templates')->where('category', 'system')->count(),
            'marketing_templates' => DB::table('email_templates')->where('category', 'marketing')->count(),
        ];

        $availableVariables = $this->getEmailTemplateVariables();

        return view('super-admin.content.email-templates', compact('templates', 'templateStats', 'availableVariables'));
    }

    /**
     * Update email template
     */
    public function updateEmailTemplate(Request $request, $templateId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = DB::table('email_templates')->where('id', $templateId)->first();

        if (!$template) {
            return back()->with('error', 'Email template not found.');
        }

        DB::table('email_templates')
            ->where('id', $templateId)
            ->update([
                'name' => $request->name,
                'subject' => $request->subject,
                'content' => $request->content,
                'variables' => json_encode($request->variables ?? []),
                'is_active' => $request->boolean('is_active'),
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'template_id' => $templateId,
                'template_name' => $request->name,
                'category' => $template->category,
            ])
            ->log('Email template updated');

        return back()->with('success', 'Email template updated successfully.');
    }

    /**
     * Preview email template
     */
    public function previewEmailTemplate($templateId)
    {
        $template = DB::table('email_templates')->where('id', $templateId)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // Replace variables with sample data
        $sampleData = $this->getSampleEmailData();
        $content = $this->replaceTemplateVariables($template->content, $sampleData);
        $subject = $this->replaceTemplateVariables($template->subject, $sampleData);

        return response()->json([
            'subject' => $subject,
            'content' => $content,
            'template' => $template,
        ]);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request, $templateId)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $template = DB::table('email_templates')->where('id', $templateId)->first();

        if (!$template) {
            return back()->with('error', 'Email template not found.');
        }

        try {
            // Replace variables with sample data
            $sampleData = $this->getSampleEmailData();
            $content = $this->replaceTemplateVariables($template->content, $sampleData);
            $subject = $this->replaceTemplateVariables($template->subject, $sampleData);

            // Send email (implement your email sending logic here)
            // Mail::to($request->email)->send(new TestEmail($subject, $content));

            // For now, just log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'template_id' => $templateId,
                    'test_email' => $request->email,
                ])
                ->log('Test email sent');

            return back()->with('success', 'Test email sent successfully to ' . $request->email);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    // Private Helper Methods

    private function getBlogCategories()
    {
        return [
            'health' => 'Health & Wellness',
            'herbs' => 'Herbal Medicine',
            'nutrition' => 'Nutrition',
            'lifestyle' => 'Lifestyle',
            'recipes' => 'Recipes',
            'research' => 'Research & Studies',
            'news' => 'Company News',
            'guides' => 'How-to Guides',
        ];
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }

    private function getStorageInfo()
    {
        $publicPath = storage_path('app/public');
        
        return [
            'used_space' => $this->formatBytes($this->getDirectorySize($publicPath)),
            'available_space' => $this->formatBytes(disk_free_space($publicPath)),
            'total_space' => $this->formatBytes(disk_total_space($publicPath)),
            'usage_percentage' => round((($this->getDirectorySize($publicPath) / disk_total_space($publicPath)) * 100), 2),
        ];
    }

    private function getDirectorySize($path)
    {
        $size = 0;
        if (is_dir($path)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        return $size;
    }

    private function getEmailTemplateVariables()
    {
        return [
            'user' => [
                '{{user.name}}' => 'User full name',
                '{{user.email}}' => 'User email address',
                '{{user.first_name}}' => 'User first name',
                '{{user.last_name}}' => 'User last name',
                '{{user.company}}' => 'User company name',
            ],
            'order' => [
                '{{order.id}}' => 'Order ID',
                '{{order.number}}' => 'Order number',
                '{{order.total}}' => 'Order total amount',
                '{{order.status}}' => 'Order status',
                '{{order.date}}' => 'Order date',
                '{{order.items}}' => 'Order items list',
                '{{order.shipping_address}}' => 'Shipping address',
            ],
            'company' => [
                '{{company.name}}' => 'Company name',
                '{{company.email}}' => 'Company email',
                '{{company.phone}}' => 'Company phone',
                '{{company.address}}' => 'Company address',
                '{{company.website}}' => 'Company website',
            ],
            'system' => [
                '{{site.name}}' => 'Site name',
                '{{site.url}}' => 'Site URL',
                '{{date}}' => 'Current date',
                '{{year}}' => 'Current year',
                '{{support.email}}' => 'Support email',
                '{{support.phone}}' => 'Support phone',
            ],
        ];
    }

    private function getSampleEmailData()
    {
        return [
            'user.name' => 'John Doe',
            'user.email' => 'john.doe@example.com',
            'user.first_name' => 'John',
            'user.last_name' => 'Doe',
            'user.company' => 'Green Health Solutions',
            'order.id' => '12345',
            'order.number' => 'ORD-2024-001',
            'order.total' => '$125.50',
            'order.status' => 'Completed',
            'order.date' => Carbon::now()->format('M d, Y'),
            'order.items' => 'Herbal Tea Blend (x2), Organic Turmeric (x1)',
            'order.shipping_address' => '123 Main St, Anytown, ST 12345',
            'company.name' => 'Herbal E-commerce Platform',
            'company.email' => 'info@herbal-ecom.com',
            'company.phone' => '+1-555-0123',
            'company.address' => '456 Business Ave, Commerce City, ST 67890',
            'company.website' => 'https://herbal-ecom.com',
            'site.name' => 'Herbal E-commerce',
            'site.url' => url('/'),
            'date' => Carbon::now()->format('M d, Y'),
            'year' => Carbon::now()->format('Y'),
            'support.email' => 'support@herbal-ecom.com',
            'support.phone' => '+1-555-0456',
        ];
    }

    private function replaceTemplateVariables($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    /**
     * Bulk actions for media files
     */
    public function bulkMediaAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,move',
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media_files,id',
            'folder' => 'required_if:action,move|string|max:100',
        ]);

        $mediaIds = $request->media_ids;
        $action = $request->action;

        try {
            if ($action === 'delete') {
                $mediaFiles = DB::table('media_files')->whereIn('id', $mediaIds)->get();
                
                foreach ($mediaFiles as $media) {
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                }
                
                DB::table('media_files')->whereIn('id', $mediaIds)->delete();
                
                $message = count($mediaIds) . ' file(s) deleted successfully.';
                
            } elseif ($action === 'move') {
                $folder = $request->folder;
                $moved = 0;
                
                $mediaFiles = DB::table('media_files')->whereIn('id', $mediaIds)->get();
                
                foreach ($mediaFiles as $media) {
                    $oldPath = $media->file_path;
                    $newPath = $folder . '/' . $media->filename;
                    
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->move($oldPath, $newPath);
                        
                        DB::table('media_files')
                            ->where('id', $media->id)
                            ->update([
                                'file_path' => $newPath,
                                'folder' => $folder,
                                'updated_at' => now(),
                            ]);
                        
                        $moved++;
                    }
                }
                
                $message = "{$moved} file(s) moved to {$folder} successfully.";
            }

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => $action,
                    'file_count' => count($mediaIds),
                    'folder' => $request->folder ?? null,
                ])
                ->log("Media bulk action: {$action}");

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Create new content folder
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:100|regex:/^[a-zA-Z0-9_-]+$/',
            'parent_folder' => 'nullable|string|max:100',
        ]);

        $folderPath = $request->parent_folder 
            ? $request->parent_folder . '/' . $request->folder_name
            : $request->folder_name;

        try {
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
                
                // Log the action
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'folder_path' => $folderPath,
                    ])
                    ->log('Content folder created');

                return back()->with('success', 'Folder created successfully.');
            } else {
                return back()->with('error', 'Folder already exists.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create folder: ' . $e->getMessage());
        }
    }
}
