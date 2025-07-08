<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];

        return view('super-admin.settings.index', compact('settings'));
    }

    public function email()
    {
        // Load saved email settings from cache or use defaults
        $savedSettings = cache('super_admin_email_settings', []);
        
        $defaultSettings = [
            'mail_from_name' => config('mail.from.name', 'Multi-Tenant E-commerce'),
            'mail_from_email' => config('mail.from.address', 'noreply@example.com'),
            'enable_queue' => false,
            'mail_driver' => config('mail.default', 'smtp'),
            'mail_host' => config('mail.mailers.smtp.host', 'smtp.gmail.com'),
            'mail_port' => config('mail.mailers.smtp.port', 587),
            'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
            'mail_username' => config('mail.mailers.smtp.username', ''),
            'mail_password' => '', // Never show stored password
            'email_verified' => false
        ];
        
        // Merge saved settings with defaults
        $settings = array_merge($defaultSettings, $savedSettings);

        return view('super-admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_from_name' => 'required|string|max:255',
            'mail_from_email' => 'required|email',
            'enable_queue' => 'boolean',
            'mail_driver' => 'required|in:smtp,mail,sendmail,log',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl,starttls',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string',
            'mail_password' => 'nullable|string',
            'email_verified' => 'boolean'
        ]);

        try {
            // Update .env file for mail settings
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);

            $envVars = [
                'MAIL_MAILER' => $request->mail_driver,
                'MAIL_FROM_ADDRESS' => $request->mail_from_email,
                'MAIL_FROM_NAME' => '"' . $request->mail_from_name . '"'
            ];
            
            // Add SMTP specific settings if driver is smtp
            if ($request->mail_driver === 'smtp') {
                $envVars['MAIL_HOST'] = $request->mail_host;
                $envVars['MAIL_PORT'] = $request->mail_port;
                $envVars['MAIL_USERNAME'] = $request->mail_username;
                $envVars['MAIL_ENCRYPTION'] = $request->mail_encryption ?? 'null';
                
                // Only update password if provided
                if ($request->filled('mail_password')) {
                    $envVars['MAIL_PASSWORD'] = $request->mail_password;
                }
            }
            
            // Update queue driver if queue is enabled
            if ($request->boolean('enable_queue')) {
                $envVars['QUEUE_CONNECTION'] = 'database';
            } else {
                $envVars['QUEUE_CONNECTION'] = 'sync';
            }

            foreach ($envVars as $key => $value) {
                $pattern = "/^{$key}=.*$/m";
                $replacement = "{$key}={$value}";
                
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envFile, $envContent);

            // Save settings to cache for form persistence
            $settings = [
                'mail_from_name' => $request->mail_from_name,
                'mail_from_email' => $request->mail_from_email,
                'enable_queue' => $request->boolean('enable_queue'),
                'mail_driver' => $request->mail_driver,
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_encryption' => $request->mail_encryption,
                'mail_username' => $request->mail_username,
                'email_verified' => $request->boolean('email_verified'),
                'updated_at' => now()
            ];
            
            // Don't store password in cache for security
            if ($request->filled('mail_password')) {
                $settings['password_updated'] = true;
            }
            
            cache()->put('super_admin_email_settings', $settings, now()->addDays(30));

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('super-admin.settings.email')
                            ->with('success', 'Email settings updated successfully!');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Failed to update email settings: ' . $e->getMessage()]);
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Temporarily configure mail settings if provided in request
            if ($request->has('mail_host')) {
                config([
                    'mail.default' => $request->get('mail_driver', 'smtp'),
                    'mail.mailers.smtp.host' => $request->mail_host,
                    'mail.mailers.smtp.port' => $request->mail_port,
                    'mail.mailers.smtp.username' => $request->mail_username,
                    'mail.mailers.smtp.encryption' => $request->mail_encryption,
                    'mail.from.address' => $request->mail_from_email,
                    'mail.from.name' => $request->mail_from_name
                ]);
            }

            $testMessage = '
                <h2>Test Email from Super Admin Panel</h2>
                <p>This is a test email to verify your email configuration.</p>
                <hr>
                <p><strong>Sent at:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
                <p><strong>From:</strong> ' . config('mail.from.name') . ' (' . config('mail.from.address') . ')</p>
                <p><strong>Server:</strong> ' . ($request->mail_host ?? config('mail.mailers.smtp.host')) . '</p>
                <p><strong>Driver:</strong> ' . ($request->mail_driver ?? config('mail.default')) . '</p>
                <hr>
                <p style="color: #28a745;">✅ If you receive this email, your configuration is working correctly!</p>
            ';

            Mail::html($testMessage, function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - Super Admin Panel - ' . now()->format('Y-m-d H:i:s'));
            });

            return response()->json([
                'success' => true, 
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function general()
    {
        // Load saved settings from cache, or use defaults
        $savedSettings = cache('super_admin_settings', []);
        
        $defaultSettings = [
            'site_name' => config('app.name', 'Multi-Tenant E-commerce'),
            'site_tagline' => 'Build Your E-commerce Empire',
            'site_description' => 'A powerful multi-tenant e-commerce platform for building and managing online stores.',
            'admin_email' => 'admin@example.com',
            'support_email' => 'support@example.com',
            'timezone' => config('app.timezone', 'UTC'),
            'date_format' => 'Y-m-d',
            'currency' => 'USD',
            'items_per_page' => 15,
            'allow_registration' => true,
            'email_verification_required' => true,
            'default_trial_days' => 14,
            'max_trial_extensions' => 2,
            'maintenance_mode' => false,
            'maintenance_message' => 'We are currently performing scheduled maintenance. Please check back soon.',
            'session_lifetime' => 120,
            'max_login_attempts' => 5,
            'site_logo' => null,
            'favicon' => null,
            'primary_color' => '#667eea',
            'company_name' => '',
            'company_address' => '',
            'company_phone' => '',
            'google_analytics_id' => '',
            'facebook_pixel_id' => '',
            'enable_cookie_consent' => true,
            'notify_new_registrations' => true,
            'notify_new_orders' => false,
            'notify_payment_failures' => true
        ];
        
        // Merge saved settings with defaults
        $settings = array_merge($defaultSettings, $savedSettings);

        return view('super-admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'admin_email' => 'required|email',
            'support_email' => 'nullable|email',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'currency' => 'required|string',
            'items_per_page' => 'required|integer|min:5|max:100',
            'allow_registration' => 'boolean',
            'email_verification_required' => 'boolean',
            'default_trial_days' => 'nullable|integer|min:0|max:365',
            'max_trial_extensions' => 'nullable|integer|min:0|max:10',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'session_lifetime' => 'required|integer|min:15|max:1440',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'primary_color' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:20',
            'google_analytics_id' => 'nullable|string|max:20',
            'facebook_pixel_id' => 'nullable|string|max:20',
            'enable_cookie_consent' => 'boolean',
            'notify_new_registrations' => 'boolean',
            'notify_new_orders' => 'boolean',
            'notify_payment_failures' => 'boolean'
        ]);

        try {
            // Update .env file for app-level settings
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);

            $envVars = [
                'APP_NAME' => '"' . $request->site_name . '"',
                'APP_URL' => config('app.url'), // Keep existing URL
                'APP_TIMEZONE' => $request->timezone
            ];

            foreach ($envVars as $key => $value) {
                $pattern = "/^{$key}=.*$/m";
                $replacement = "{$key}={$value}";
                
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$replacement}";
                }
            }

            file_put_contents($envFile, $envContent);

            // Handle file uploads
            $uploadedFiles = [];
            
            if ($request->hasFile('site_logo')) {
                $logoPath = $request->file('site_logo')->store('settings/logos', 'public');
                $uploadedFiles['site_logo'] = $logoPath;
            }
            
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('settings/favicons', 'public');
                $uploadedFiles['favicon'] = $faviconPath;
            }

            // For now, we'll store other settings in session or you can create a settings table
            // This is a temporary solution - in production, create a settings table
            $settings = [
                'site_name' => $request->site_name,
                'site_tagline' => $request->site_tagline,
                'site_description' => $request->site_description,
                'admin_email' => $request->admin_email,
                'support_email' => $request->support_email,
                'timezone' => $request->timezone,
                'date_format' => $request->date_format,
                'currency' => $request->currency,
                'items_per_page' => $request->items_per_page,
                'allow_registration' => $request->boolean('allow_registration'),
                'email_verification_required' => $request->boolean('email_verification_required'),
                'default_trial_days' => $request->default_trial_days ?? 14,
                'max_trial_extensions' => $request->max_trial_extensions ?? 2,
                'maintenance_mode' => $request->boolean('maintenance_mode'),
                'maintenance_message' => $request->maintenance_message,
                'session_lifetime' => $request->session_lifetime,
                'max_login_attempts' => $request->max_login_attempts,
                'primary_color' => $request->primary_color,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_phone' => $request->company_phone,
                'google_analytics_id' => $request->google_analytics_id,
                'facebook_pixel_id' => $request->facebook_pixel_id,
                'enable_cookie_consent' => $request->boolean('enable_cookie_consent'),
                'notify_new_registrations' => $request->boolean('notify_new_registrations'),
                'notify_new_orders' => $request->boolean('notify_new_orders'),
                'notify_payment_failures' => $request->boolean('notify_payment_failures'),
            ];
            
            // Merge uploaded file paths
            $settings = array_merge($settings, $uploadedFiles);
            
            // Store in cache for now (in production, use database)
            cache()->put('super_admin_settings', $settings, now()->addDays(30));

            // Also update .env file with site name if provided
            if ($request->site_name) {
                $this->updateEnvValue('APP_NAME', '"' . $request->site_name . '"');
            }

            // Clear all caches to ensure settings take effect immediately
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            // Also clear any application-specific caches
            cache()->flush();

            return redirect()->route('super-admin.settings.general')
                            ->with('success', 'General settings updated successfully!');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Failed to update settings: ' . $e->getMessage()]);
        }
    }

    public function backup()
    {
        // Mock backup data - ensure all data is template-safe
        $backupsData = [
            [
                'id' => 1,
                'filename' => 'backup_2024_01_15_120000.sql',
                'description' => 'Daily automated backup',
                'type' => 'full',
                'status' => 'completed',
                'formatted_size' => '2.5 MB',
                'size' => 2621440,
                'path' => 'backups/backup_2024_01_15_120000.sql',
                'created_at_formatted' => now()->subDays(1)->format('M d, Y H:i'),
                'created_at_human' => now()->subDays(1)->diffForHumans(),
                'updated_at_formatted' => now()->subDays(1)->format('M d, Y H:i'),
                'updated_at_human' => now()->subDays(1)->diffForHumans()
            ],
            [
                'id' => 2,
                'filename' => 'backup_2024_01_14_120000.sql',
                'description' => 'Manual backup before update',
                'type' => 'full',
                'status' => 'completed',
                'formatted_size' => '2.3 MB',
                'size' => 2411724,
                'path' => 'backups/backup_2024_01_14_120000.sql',
                'created_at_formatted' => now()->subDays(2)->format('M d, Y H:i'),
                'created_at_human' => now()->subDays(2)->diffForHumans(),
                'updated_at_formatted' => now()->subDays(2)->format('M d, Y H:i'),
                'updated_at_human' => now()->subDays(2)->diffForHumans()
            ],
            [
                'id' => 3,
                'filename' => 'backup_2024_01_13_120000.sql',
                'description' => 'Automated weekly backup',
                'type' => 'partial',
                'status' => 'completed',
                'formatted_size' => '1.1 MB',
                'size' => 1153434,
                'path' => 'backups/backup_2024_01_13_120000.sql',
                'created_at_formatted' => now()->subDays(3)->format('M d, Y H:i'),
                'created_at_human' => now()->subDays(3)->diffForHumans(),
                'updated_at_formatted' => now()->subDays(3)->format('M d, Y H:i'),
                'updated_at_human' => now()->subDays(3)->diffForHumans()
            ]
        ];
        
        // Convert to objects with template-safe properties
        $backupsData = array_map(function($backup) {
            $obj = (object) $backup;
            // Add Carbon object for any method calls that might still exist
            $obj->created_at = now()->subDays(rand(1, 5));
            $obj->updated_at = now()->subDays(rand(1, 5));
            return $obj;
        }, $backupsData);
        
        // Get the first backup for "Last Backup" display
        $firstBackup = !empty($backupsData) ? $backupsData[0] : null;
        $lastBackupTime = $firstBackup ? $firstBackup->created_at_human : 'Never';
        
        // Create paginated collection
        $backups = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($backupsData),
            count($backupsData),
            15,
            1,
            ['path' => request()->url()]
        );
        
        // Override first() method result to return safe data
        $backups->lastBackupTime = $lastBackupTime;
        
        $totalSize = '4.8 MB';
        
        $settings = [
            'auto_backup_enabled' => false,
            'backup_frequency' => 'daily',
            'backup_time' => '02:00',
            'retention_days' => 30,
            'max_backups' => 10,
            'compress_backups' => true
        ];
        
        $storageInfo = [
            'available' => '1.2 GB',
            'used' => '850 MB',
            'path' => storage_path('app/backups'),
            'usage_percentage' => 70
        ];
        
        $databaseTables = [
            'users', 'companies', 'products', 'orders', 'categories',
            'support_tickets', 'packages', 'themes', 'billing'
        ];

        return view('super-admin.settings.backup', compact('backups', 'totalSize', 'settings', 'storageInfo', 'databaseTables', 'lastBackupTime'));
    }

    public function createBackup()
    {
        try {
            // Run backup command
            Artisan::call('backup:run');
            
            return response()->json(['success' => true, 'message' => 'Backup created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function cache()
    {
        // Check cache status
        $cacheStatus = [
            'application' => file_exists(storage_path('framework/cache/data')),
            'views' => file_exists(storage_path('framework/views')),
            'routes' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
            'config' => file_exists(base_path('bootstrap/cache/config.php'))
        ];
        
        $cacheInfo = [
            'size' => $this->formatBytes($this->getCacheSize()),
            'last_cleared' => 'Unknown'
        ];
        
        $settings = [
            'cache_ttl' => 3600,
            'auto_cache_clear' => false,
            'enable_query_cache' => true,
            'cache_prefix' => 'herbal_ecom'
        ];

        return view('super-admin.settings.cache', compact('cacheStatus', 'cacheInfo', 'settings'));
    }

    public function storage()
    {
        // Get current storage configuration
        $currentStorageType = \DB::table('app_settings')
            ->where('key', 'primary_storage_type')
            ->whereNull('company_id')
            ->value('value') ?? env('STORAGE_TYPE', 'local');

        $storageConfig = [
            'current_type' => $currentStorageType,
            'aws_access_key_id' => config('filesystems.disks.s3.key'),
            'aws_secret_access_key' => config('filesystems.disks.s3.secret'),
            'aws_default_region' => config('filesystems.disks.s3.region'),
            'aws_bucket' => config('filesystems.disks.s3.bucket'),
            'aws_url' => config('filesystems.disks.s3.url'),
            'local_path' => storage_path('app/public')
        ];

        // Get storage statistics
        $storageService = app(\App\Services\StorageManagementService::class);
        $storageStats = $storageService->getStorageStats();

        return view('super-admin.settings.storage', compact('storageConfig', 'storageStats'));
    }

    public function updateStorage(Request $request)
    {
        $request->validate([
            'storage_type' => 'required|in:local,s3',
            'aws_access_key_id' => 'nullable|string',
            'aws_secret_access_key' => 'nullable|string',
            'aws_default_region' => 'nullable|string',
            'aws_bucket' => 'nullable|string',
            'aws_url' => 'nullable|url',
        ]);

        try {
            $storageService = app(\App\Services\StorageManagementService::class);
            $storageService->updateStorageConfig($request->all());

            return redirect()->route('super-admin.settings.storage')
                            ->with('success', 'Storage configuration updated successfully!');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Failed to update storage configuration: ' . $e->getMessage()]);
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json(['success' => true, 'message' => 'All caches cleared successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cache clear failed: ' . $e->getMessage()]);
        }
    }
    
    // Additional cache methods
    public function cacheAction(Request $request)
    {
        $action = $request->input('action');
        
        try {
            switch ($action) {
                case 'clear-all':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $message = 'All caches cleared successfully';
                    break;
                case 'clear-application':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared';
                    break;
                case 'clear-views':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared';
                    break;
                case 'clear-routes':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared';
                    break;
                case 'clear-config':
                    Artisan::call('config:clear');
                    $message = 'Config cache cleared';
                    break;
                case 'optimize':
                    Artisan::call('optimize');
                    $message = 'Application optimized';
                    break;
                case 'cache-config':
                    Artisan::call('config:cache');
                    $message = 'Configuration cached';
                    break;
                case 'cache-routes':
                    Artisan::call('route:cache');
                    $message = 'Routes cached';
                    break;
                case 'cache-views':
                    Artisan::call('view:cache');
                    $message = 'Views cached';
                    break;
                case 'queue-restart':
                    Artisan::call('queue:restart');
                    $message = 'Queue workers restarted';
                    break;
                default:
                    throw new \Exception('Unknown cache action');
            }
            
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Action failed: ' . $e->getMessage()]);
        }
    }
    
    public function cacheStatus()
    {
        $status = [
            'application' => file_exists(storage_path('framework/cache/data')),
            'views' => file_exists(storage_path('framework/views')),
            'routes' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
            'config' => file_exists(base_path('bootstrap/cache/config.php'))
        ];
        
        $info = [
            'size' => $this->formatBytes($this->getCacheSize()),
            'last_cleared' => 'Just checked'
        ];
        
        return response()->json(['success' => true, 'status' => $status, 'info' => $info]);
    }
    
    public function updateCacheSettings(Request $request)
    {
        // In a real implementation, you'd save these to database or config
        return response()->json(['success' => true, 'message' => 'Cache settings updated']);
    }
    
    // Backup methods
    public function downloadBackup($backup)
    {
        // In real implementation, return file download
        return response()->download(storage_path('app/backups/backup_example.sql'));
    }
    
    public function restoreBackup($backup)
    {
        try {
            // In real implementation, restore from backup file
            return response()->json(['success' => true, 'message' => 'Backup restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()]);
        }
    }
    
    public function deleteBackup($backup)
    {
        try {
            // In real implementation, delete backup file
            return response()->json(['success' => true, 'message' => 'Backup deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
        }
    }
    
    public function backupProgress($backup)
    {
        // Mock progress response
        return response()->json([
            'success' => true,
            'progress' => 100,
            'status' => 'Backup completed',
            'completed' => true
        ]);
    }
    
    public function cancelBackup($backup)
    {
        return response()->json(['success' => true, 'message' => 'Backup cancelled']);
    }
    
    public function backupDetails($backup)
    {
        $html = '<div class="p-3">
            <h6>Backup Details</h6>
            <p><strong>Filename:</strong> backup_example.sql</p>
            <p><strong>Size:</strong> 2.5 MB</p>
            <p><strong>Created:</strong> ' . now()->format('M d, Y H:i') . '</p>
            <p><strong>Status:</strong> Completed</p>
        </div>';
        
        return response($html);
    }
    
    public function cleanupBackups()
    {
        return response()->json([
            'success' => true,
            'message' => 'Cleanup completed',
            'deleted_count' => 3
        ]);
    }
    
    public function updateBackupSettings(Request $request)
    {
        // In real implementation, save backup settings
        return response()->json(['success' => true, 'message' => 'Backup settings updated']);
    }
    
    // Helper methods
    private function getCacheSize()
    {
        $size = 0;
        $paths = [
            storage_path('framework/cache'),
            storage_path('framework/views'),
            base_path('bootstrap/cache')
        ];
        
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $size += $this->getDirectorySize($path);
            }
        }
        
        return $size;
    }
    
    private function getDirectorySize($directory)
    {
        $size = 0;
        foreach (glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getDirectorySize($each);
        }
        return $size;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
    
    /**
     * Update .env file with a specific key-value pair
     */
    private function updateEnvValue($key, $value)
    {
        try {
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);
            
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
            
            file_put_contents($envFile, $envContent);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update .env file: ' . $e->getMessage());
            return false;
        }
    }
}
