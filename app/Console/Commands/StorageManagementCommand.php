<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StorageManagementService;
use Illuminate\Support\Facades\Storage;

class StorageManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:manage 
                           {action : Action to perform (sync, backup, cleanup, test, stats, migrate)}
                           {--source= : Source storage type (local, s3)}
                           {--target= : Target storage type (local, s3)}
                           {--category= : File category (products, banners, categories, general)}
                           {--days= : Number of days for cleanup (default: 30)}
                           {--dry-run : Preview actions without executing}
                           {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage storage operations: sync files, backup, cleanup, and more';

    protected $storageService;

    /**
     * Create a new command instance.
     */
    public function __construct(StorageManagementService $storageService)
    {
        parent::__construct();
        $this->storageService = $storageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'sync':
                $this->syncFiles();
                break;
            case 'backup':
                $this->backupStorage();
                break;
            case 'cleanup':
                $this->cleanupFiles();
                break;
            case 'test':
                $this->testConnection();
                break;
            case 'stats':
                $this->showStats();
                break;
            case 'migrate':
                $this->migrateStorage();
                break;
            default:
                $this->error("Unknown action: $action");
                $this->showHelp();
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Sync files between storage types
     */
    protected function syncFiles()
    {
        $source = $this->option('source');
        $target = $this->option('target');
        $category = $this->option('category');

        if (!$source || !$target) {
            $this->error('Source and target storage types are required for sync operation.');
            $this->line('Example: php artisan storage:manage sync --source=local --target=s3');
            return;
        }

        if ($source === $target) {
            $this->error('Source and target storage types cannot be the same.');
            return;
        }

        $direction = $source . '_to_' . $target;

        $this->info("Syncing files from $source to $target...");
        if ($category) {
            $this->line("Category filter: $category");
        }

        if (!$this->option('force') && !$this->confirm("Continue with sync operation?")) {
            $this->info('Sync operation cancelled.');
            return;
        }

        try {
            $result = $this->storageService->syncFiles($direction, $category);
            
            $this->info("Sync completed successfully!");
            $this->line("Files synced: {$result['synced_count']}");
            
            if ($result['error_count'] > 0) {
                $this->warn("Errors encountered: {$result['error_count']}");
                foreach ($result['errors'] as $error) {
                    $this->line("  - $error");
                }
            }
        } catch (\Exception $e) {
            $this->error("Sync operation failed: " . $e->getMessage());
        }
    }

    /**
     * Backup storage
     */
    protected function backupStorage()
    {
        $storageType = $this->option('source') ?? config('app.storage_type', 'local');
        $backupType = 'full'; // Could be extended to support incremental

        $this->info("Creating backup of $storageType storage...");

        if (!$this->option('force') && !$this->confirm("Continue with backup operation?")) {
            $this->info('Backup operation cancelled.');
            return;
        }

        try {
            $result = $this->storageService->backupStorage($storageType, $backupType);
            
            $this->info("Backup completed successfully!");
            $this->line("Backup path: {$result['backup_path']}");
            $this->line("Files backed up: {$result['file_count']}");
        } catch (\Exception $e) {
            $this->error("Backup operation failed: " . $e->getMessage());
        }
    }

    /**
     * Cleanup old files
     */
    protected function cleanupFiles()
    {
        $storageType = $this->option('source') ?? config('app.storage_type', 'local');
        $daysOld = $this->option('days') ?? 30;
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up files older than $daysOld days in $storageType storage...");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No files will be deleted");
        }

        if (!$dryRun && !$this->option('force') && !$this->confirm("Continue with cleanup operation?")) {
            $this->info('Cleanup operation cancelled.');
            return;
        }

        try {
            $result = $this->storageService->cleanupOldFiles($storageType, $daysOld, $dryRun);
            
            if ($dryRun) {
                $this->info("Cleanup preview completed!");
                $this->line("Files that would be deleted: {$result['file_count']}");
            } else {
                $this->info("Cleanup completed successfully!");
                $this->line("Files deleted: {$result['file_count']}");
            }
            
            $this->line("Cutoff date: {$result['cutoff_date']->format('Y-m-d H:i:s')}");
        } catch (\Exception $e) {
            $this->error("Cleanup operation failed: " . $e->getMessage());
        }
    }

    /**
     * Test storage connection
     */
    protected function testConnection()
    {
        $storageType = $this->option('source') ?? 'both';

        if ($storageType === 'both') {
            $this->info("Testing both local and S3 storage connections...");
            
            // Test local storage
            $this->line("\nTesting local storage:");
            try {
                $result = $this->storageService->testConnection('local');
                $this->info("✓ Local storage: " . $result['message']);
            } catch (\Exception $e) {
                $this->error("✗ Local storage: " . $e->getMessage());
            }

            // Test S3 storage
            $this->line("\nTesting S3 storage:");
            try {
                $result = $this->storageService->testConnection('s3');
                $this->info("✓ S3 storage: " . $result['message']);
            } catch (\Exception $e) {
                $this->error("✗ S3 storage: " . $e->getMessage());
            }
        } else {
            $this->info("Testing $storageType storage connection...");
            try {
                $result = $this->storageService->testConnection($storageType);
                $this->info("✓ Connection successful: " . $result['message']);
            } catch (\Exception $e) {
                $this->error("✗ Connection failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Show storage statistics
     */
    protected function showStats()
    {
        $this->info("Storage Statistics:");
        $this->line(str_repeat('-', 50));

        try {
            $stats = $this->storageService->getStorageStats();
            $config = $this->storageService->getStorageConfig();

            // Current configuration
            $this->line("Current Storage: " . ucfirst($config['current_storage']));
            $this->line("Total Files: " . number_format($stats['total_files']));
            $this->line("");

            // Local storage stats
            $this->line("Local Storage:");
            $this->line("  Files: " . number_format($stats['local']['file_count']));
            $this->line("  Size: " . $stats['local']['total_size_formatted']);
            $this->line("  Available: " . $stats['local']['available_space_formatted']);
            $this->line("");

            // S3 storage stats
            $this->line("S3 Storage:");
            if ($stats['s3']['available']) {
                $this->line("  Files: " . number_format($stats['s3']['file_count']));
                $this->line("  Size: " . $stats['s3']['total_size_formatted']);
            } else {
                $this->line("  Status: Not configured");
            }
            $this->line("");

            // Files by category
            if (!empty($stats['categories'])) {
                $this->line("Files by Category:");
                foreach ($stats['categories'] as $category => $count) {
                    $this->line("  " . ucfirst($category) . ": " . number_format($count));
                }
            }
        } catch (\Exception $e) {
            $this->error("Failed to retrieve statistics: " . $e->getMessage());
        }
    }

    /**
     * Migrate storage from one type to another
     */
    protected function migrateStorage()
    {
        $source = $this->option('source');
        $target = $this->option('target');

        if (!$source || !$target) {
            $this->error('Source and target storage types are required for migration.');
            $this->line('Example: php artisan storage:manage migrate --source=local --target=s3');
            return;
        }

        if ($source === $target) {
            $this->error('Source and target storage types cannot be the same.');
            return;
        }

        $this->warn("MIGRATION WARNING: This will copy all files and update the default storage configuration.");
        
        if (!$this->option('force') && !$this->confirm("Are you sure you want to migrate from $source to $target?")) {
            $this->info('Migration cancelled.');
            return;
        }

        try {
            // First sync all files
            $this->info("Step 1: Syncing files from $source to $target...");
            $direction = $source . '_to_' . $target;
            $syncResult = $this->storageService->syncFiles($direction);
            
            $this->info("Files synced: {$syncResult['synced_count']}");

            // Update storage configuration
            $this->info("Step 2: Updating storage configuration...");
            $this->storageService->updateStorageConfig(['storage_type' => $target]);
            
            $this->info("Migration completed successfully!");
            $this->line("Default storage is now: $target");
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Show help information
     */
    protected function showHelp()
    {
        $this->line("");
        $this->line("Available actions:");
        $this->line("  sync     - Sync files between storage types");
        $this->line("  backup   - Create a backup of storage");
        $this->line("  cleanup  - Remove old files");
        $this->line("  test     - Test storage connections");
        $this->line("  stats    - Show storage statistics");
        $this->line("  migrate  - Migrate from one storage to another");
        $this->line("");
        $this->line("Examples:");
        $this->line("  php artisan storage:manage sync --source=local --target=s3");
        $this->line("  php artisan storage:manage backup --source=local");
        $this->line("  php artisan storage:manage cleanup --source=s3 --days=60 --dry-run");
        $this->line("  php artisan storage:manage test");
        $this->line("  php artisan storage:manage stats");
        $this->line("  php artisan storage:manage migrate --source=local --target=s3");
    }
}
