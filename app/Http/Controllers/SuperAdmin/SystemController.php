<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class SystemController extends Controller
{
    /**
     * System health dashboard
     */
    public function health()
    {
        $healthChecks = $this->runHealthChecks();
        $systemInfo = $this->getSystemInfo();
        $services = $this->checkServices();

        return view('super-admin.system.health', compact('healthChecks', 'systemInfo', 'services'));
    }

    /**
     * Performance monitoring
     */
    public function performance()
    {
        $performanceMetrics = $this->getPerformanceMetrics();
        $memoryUsage = $this->getMemoryUsage();
        $diskUsage = $this->getDiskUsage();
        $queryPerformance = $this->getQueryPerformance();

        return view('super-admin.system.performance', compact(
            'performanceMetrics', 
            'memoryUsage', 
            'diskUsage', 
            'queryPerformance'
        ));
    }

    /**
     * System logs viewer
     */
    public function logs(Request $request)
    {
        $logType = $request->get('type', 'laravel');
        $date = $request->get('date', date('Y-m-d'));
        $level = $request->get('level', 'all');

        $logs = $this->getLogEntries($logType, $date, $level);
        $logFiles = $this->getAvailableLogFiles();
        $logStats = $this->getLogStats($date);

        return view('super-admin.system.logs', compact('logs', 'logFiles', 'logStats', 'logType', 'date', 'level'));
    }

    /**
     * Error logs
     */
    public function errorLogs(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $severity = $request->get('severity', 'all');

        $errorLogs = $this->getErrorLogs($date, $severity);
        $errorStats = $this->getErrorStats();
        $topErrors = $this->getTopErrors();

        return view('super-admin.system.error-logs', compact('errorLogs', 'errorStats', 'topErrors', 'date', 'severity'));
    }

    /**
     * Security logs
     */
    public function securityLogs(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $type = $request->get('type', 'all');

        $securityLogs = $this->getSecurityLogs($date, $type);
        $securityStats = $this->getSecurityStats();
        $threats = $this->getRecentThreats();

        return view('super-admin.system.security-logs', compact('securityLogs', 'securityStats', 'threats', 'date', 'type'));
    }

    /**
     * Activity logs
     */
    public function activityLogs(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $user = $request->get('user');
        $action = $request->get('action');

        $activityLogs = $this->getActivityLogs($date, $user, $action);
        $activityStats = $this->getActivityStats();

        return view('super-admin.system.activity-logs', compact('activityLogs', 'activityStats', 'date', 'user', 'action'));
    }

    /**
     * Queue monitor
     */
    public function queue()
    {
        $queueStats = $this->getQueueStats();
        $recentJobs = $this->getRecentJobs();
        $failedJobs = $this->getFailedJobs();
        $queueConnections = $this->getQueueConnections();

        return view('super-admin.system.queue', compact('queueStats', 'recentJobs', 'failedJobs', 'queueConnections'));
    }

    /**
     * Task scheduler
     */
    public function scheduler()
    {
        $scheduledTasks = $this->getScheduledTasks();
        $taskHistory = $this->getTaskHistory();
        $cronJobs = $this->getCronJobs();

        return view('super-admin.system.scheduler', compact('scheduledTasks', 'taskHistory', 'cronJobs'));
    }

    /**
     * Clear logs
     */
    public function clearLogs(Request $request)
    {
        $request->validate([
            'log_type' => 'required|in:all,laravel,error,security,activity',
            'older_than' => 'required|integer|min:1|max:365',
        ]);

        $logType = $request->log_type;
        $olderThan = $request->older_than;
        $cutoffDate = Carbon::now()->subDays($olderThan);

        try {
            $clearedCount = 0;

            if ($logType === 'all' || $logType === 'laravel') {
                $clearedCount += $this->clearLaravelLogs($cutoffDate);
            }

            if ($logType === 'all' || $logType === 'error') {
                $clearedCount += $this->clearErrorLogs($cutoffDate);
            }

            if ($logType === 'all' || $logType === 'security') {
                $clearedCount += $this->clearSecurityLogs($cutoffDate);
            }

            if ($logType === 'all' || $logType === 'activity') {
                $clearedCount += $this->clearActivityLogs($cutoffDate);
            }

            Log::info('System logs cleared', [
                'type' => $logType,
                'older_than_days' => $olderThan,
                'cleared_count' => $clearedCount,
                'performed_by' => auth()->user()->email,
            ]);

            return back()->with('success', "Successfully cleared {$clearedCount} log entries older than {$olderThan} days.");

        } catch (\Exception $e) {
            Log::error('Failed to clear logs', [
                'error' => $e->getMessage(),
                'type' => $logType,
                'performed_by' => auth()->user()->email,
            ]);

            return back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    // Private Helper Methods

    private function runHealthChecks()
    {
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'details' => 'Connected to ' . config('database.default'),
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed',
                'details' => $e->getMessage(),
            ];
        }

        // Cache check
        try {
            Cache::put('health_check', 'test', 10);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            
            $checks['cache'] = [
                'status' => $value === 'test' ? 'healthy' : 'warning',
                'message' => $value === 'test' ? 'Cache working properly' : 'Cache read/write issue',
                'details' => 'Using ' . config('cache.default') . ' driver',
            ];
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'error',
                'message' => 'Cache system error',
                'details' => $e->getMessage(),
            ];
        }

        // Queue check
        try {
            $queueSize = DB::table('jobs')->count();
            $checks['queue'] = [
                'status' => $queueSize < 1000 ? 'healthy' : 'warning',
                'message' => $queueSize < 1000 ? 'Queue size normal' : 'Queue size high',
                'details' => "{$queueSize} jobs in queue",
            ];
        } catch (\Exception $e) {
            $checks['queue'] = [
                'status' => 'error',
                'message' => 'Queue check failed',
                'details' => $e->getMessage(),
            ];
        }

        // Storage check
        $storagePath = storage_path();
        $freeBytes = disk_free_space($storagePath);
        $totalBytes = disk_total_space($storagePath);
        $usedPercent = (($totalBytes - $freeBytes) / $totalBytes) * 100;

        $checks['storage'] = [
            'status' => $usedPercent < 80 ? 'healthy' : ($usedPercent < 90 ? 'warning' : 'error'),
            'message' => $usedPercent < 80 ? 'Storage usage normal' : 'Storage usage high',
            'details' => sprintf('%.1f%% used (%s free)', $usedPercent, $this->formatBytes($freeBytes)),
        ];

        // Memory check
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseSize(ini_get('memory_limit'));
        $memoryPercent = ($memoryUsage / $memoryLimit) * 100;

        $checks['memory'] = [
            'status' => $memoryPercent < 70 ? 'healthy' : ($memoryPercent < 85 ? 'warning' : 'error'),
            'message' => $memoryPercent < 70 ? 'Memory usage normal' : 'Memory usage high',
            'details' => sprintf('%.1f%% used (%s of %s)', $memoryPercent, $this->formatBytes($memoryUsage), $this->formatBytes($memoryLimit)),
        ];

        return $checks;
    }

    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'operating_system' => PHP_OS,
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'maintenance_mode' => app()->isDownForMaintenance() ? 'Active' : 'Inactive',
            'uptime' => $this->getServerUptime(),
        ];
    }

    private function checkServices()
    {
        $services = [];

        // Web server
        $services['web_server'] = [
            'name' => 'Web Server',
            'status' => 'running',
            'uptime' => $this->getServerUptime(),
        ];

        // Queue worker
        try {
            $queueWorkers = $this->getRunningQueueWorkers();
            $services['queue_worker'] = [
                'name' => 'Queue Worker',
                'status' => count($queueWorkers) > 0 ? 'running' : 'stopped',
                'workers' => count($queueWorkers),
            ];
        } catch (\Exception $e) {
            $services['queue_worker'] = [
                'name' => 'Queue Worker',
                'status' => 'unknown',
                'error' => $e->getMessage(),
            ];
        }

        // Scheduler
        $services['scheduler'] = [
            'name' => 'Task Scheduler',
            'status' => $this->isSchedulerRunning() ? 'running' : 'stopped',
            'last_run' => $this->getLastSchedulerRun(),
        ];

        return $services;
    }

    private function getPerformanceMetrics()
    {
        return [
            'response_time' => $this->getAverageResponseTime(),
            'requests_per_minute' => $this->getRequestsPerMinute(),
            'cpu_usage' => $this->getCpuUsage(),
            'load_average' => $this->getLoadAverage(),
            'active_connections' => $this->getActiveConnections(),
        ];
    }

    private function getMemoryUsage()
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => $this->parseSize(ini_get('memory_limit')),
            'percentage' => (memory_get_usage(true) / $this->parseSize(ini_get('memory_limit'))) * 100,
        ];
    }

    private function getDiskUsage()
    {
        $paths = [
            'root' => '/',
            'storage' => storage_path(),
            'public' => public_path(),
        ];

        $usage = [];
        foreach ($paths as $name => $path) {
            if (is_dir($path)) {
                $freeBytes = disk_free_space($path);
                $totalBytes = disk_total_space($path);
                $usage[$name] = [
                    'path' => $path,
                    'free' => $freeBytes,
                    'total' => $totalBytes,
                    'used' => $totalBytes - $freeBytes,
                    'percentage' => (($totalBytes - $freeBytes) / $totalBytes) * 100,
                ];
            }
        }

        return $usage;
    }

    private function getQueryPerformance()
    {
        // Mock implementation - you'd need to implement actual query monitoring
        return [
            'slow_queries' => 5,
            'average_query_time' => 0.125,
            'total_queries' => 1250,
            'cache_hit_ratio' => 85.5,
        ];
    }

    private function getLogEntries($logType, $date, $level)
    {
        $logFile = $this->getLogFile($logType, $date);
        
        if (!File::exists($logFile)) {
            return [];
        }

        $content = File::get($logFile);
        $entries = $this->parseLogContent($content, $level);

        return array_slice($entries, 0, 100); // Limit to 100 entries
    }

    private function getAvailableLogFiles()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        
        return array_map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => Carbon::createFromTimestamp($file->getMTime()),
            ];
        }, $files);
    }

    private function getLogStats($date)
    {
        // Mock implementation - implement actual log statistics
        return [
            'total_entries' => 1250,
            'error_count' => 15,
            'warning_count' => 45,
            'info_count' => 890,
            'debug_count' => 300,
        ];
    }

    private function getErrorLogs($date, $severity)
    {
        // Mock implementation - implement actual error log parsing
        return [
            [
                'timestamp' => Carbon::now()->subHours(2),
                'level' => 'ERROR',
                'message' => 'Database connection timeout',
                'file' => 'app/Http/Controllers/ProductController.php',
                'line' => 125,
                'context' => 'User ID: 1234, Product ID: 5678',
            ],
            [
                'timestamp' => Carbon::now()->subHours(5),
                'level' => 'CRITICAL',
                'message' => 'Payment gateway authentication failed',
                'file' => 'app/Services/PaymentService.php',
                'line' => 89,
                'context' => 'Gateway: Razorpay, Amount: 2500',
            ],
        ];
    }

    private function getErrorStats()
    {
        return [
            'total_errors_today' => 25,
            'critical_errors' => 3,
            'error_rate' => 0.02, // 2%
            'most_common_error' => 'Database timeout',
        ];
    }

    private function getTopErrors()
    {
        return [
            ['error' => 'Database connection timeout', 'count' => 15],
            ['error' => 'File not found', 'count' => 8],
            ['error' => 'Permission denied', 'count' => 5],
        ];
    }

    private function getSecurityLogs($date, $type)
    {
        // Mock implementation
        return [
            [
                'timestamp' => Carbon::now()->subHours(1),
                'type' => 'Failed Login',
                'ip' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0...',
                'details' => 'Multiple failed login attempts',
                'severity' => 'Medium',
            ],
            [
                'timestamp' => Carbon::now()->subHours(3),
                'type' => 'Suspicious Activity',
                'ip' => '10.0.0.5',
                'user_agent' => 'Bot/1.0',
                'details' => 'Rapid API requests detected',
                'severity' => 'High',
            ],
        ];
    }

    private function getSecurityStats()
    {
        return [
            'failed_logins_today' => 15,
            'blocked_ips' => 5,
            'suspicious_activities' => 8,
            'security_incidents' => 2,
        ];
    }

    private function getRecentThreats()
    {
        return [
            ['type' => 'Brute Force', 'ip' => '192.168.1.100', 'attempts' => 25],
            ['type' => 'SQL Injection', 'ip' => '10.0.0.5', 'attempts' => 3],
        ];
    }

    private function getActivityLogs($date, $user, $action)
    {
        // This would integrate with your activity logging system
        return [
            [
                'timestamp' => Carbon::now()->subMinutes(30),
                'user' => 'admin@example.com',
                'action' => 'User Created',
                'resource' => 'User #1234',
                'ip' => '192.168.1.10',
                'details' => 'Created new user account',
            ],
            [
                'timestamp' => Carbon::now()->subHours(1),
                'user' => 'super@admin.com',
                'action' => 'Company Suspended',
                'resource' => 'Company #56',
                'ip' => '192.168.1.1',
                'details' => 'Suspended company for policy violation',
            ],
        ];
    }

    private function getActivityStats()
    {
        return [
            'total_activities_today' => 150,
            'user_activities' => 120,
            'admin_activities' => 30,
            'system_activities' => 25,
        ];
    }

    private function getQueueStats()
    {
        try {
            return [
                'pending_jobs' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
                'processed_today' => DB::table('jobs')->whereDate('created_at', today())->count(),
                'average_processing_time' => 2.5, // seconds
            ];
        } catch (\Exception $e) {
            return [
                'pending_jobs' => 0,
                'failed_jobs' => 0,
                'processed_today' => 0,
                'average_processing_time' => 0,
            ];
        }
    }

    private function getRecentJobs()
    {
        try {
            return DB::table('jobs')
                ->select('queue', 'payload', 'created_at')
                ->latest('created_at')
                ->limit(10)
                ->get()
                ->map(function ($job) {
                    $payload = json_decode($job->payload, true);
                    return [
                        'queue' => $job->queue,
                        'job' => $payload['displayName'] ?? 'Unknown',
                        'created_at' => Carbon::parse($job->created_at),
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getFailedJobs()
    {
        try {
            return DB::table('failed_jobs')
                ->select('queue', 'payload', 'exception', 'failed_at')
                ->latest('failed_at')
                ->limit(10)
                ->get()
                ->map(function ($job) {
                    $payload = json_decode($job->payload, true);
                    return [
                        'queue' => $job->queue,
                        'job' => $payload['displayName'] ?? 'Unknown',
                        'exception' => substr($job->exception, 0, 100) . '...',
                        'failed_at' => Carbon::parse($job->failed_at),
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getQueueConnections()
    {
        return array_keys(config('queue.connections', []));
    }

    private function getScheduledTasks()
    {
        // Mock implementation - you'd need to implement actual task discovery
        return [
            ['name' => 'Send Email Notifications', 'schedule' => '0 9 * * *', 'last_run' => Carbon::now()->subHours(3)],
            ['name' => 'Clean Temporary Files', 'schedule' => '0 2 * * *', 'last_run' => Carbon::now()->subHours(8)],
            ['name' => 'Generate Reports', 'schedule' => '0 0 * * 0', 'last_run' => Carbon::now()->subDays(2)],
        ];
    }

    private function getTaskHistory()
    {
        // Mock implementation
        return [
            ['task' => 'Send Email Notifications', 'status' => 'Success', 'duration' => '2.5s', 'completed_at' => Carbon::now()->subHours(3)],
            ['task' => 'Clean Temporary Files', 'status' => 'Success', 'duration' => '15.2s', 'completed_at' => Carbon::now()->subHours(8)],
            ['task' => 'Generate Reports', 'status' => 'Failed', 'duration' => '45.1s', 'completed_at' => Carbon::now()->subDays(2)],
        ];
    }

    private function getCronJobs()
    {
        // This would read from crontab or system scheduler
        return [
            ['schedule' => '* * * * *', 'command' => 'php artisan schedule:run'],
            ['schedule' => '0 2 * * *', 'command' => 'php artisan backup:run'],
        ];
    }

    // Utility Methods

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($size, 1024);
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
    }

    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }

    private function getServerUptime()
    {
        // Mock implementation - this would vary by system
        return '5 days, 12 hours';
    }

    private function getRunningQueueWorkers()
    {
        // Mock implementation
        return ['worker_1', 'worker_2'];
    }

    private function isSchedulerRunning()
    {
        // Mock implementation
        return true;
    }

    private function getLastSchedulerRun()
    {
        // Mock implementation
        return Carbon::now()->subMinutes(5);
    }

    private function getAverageResponseTime()
    {
        // Mock implementation
        return 145; // milliseconds
    }

    private function getRequestsPerMinute()
    {
        // Mock implementation
        return 25;
    }

    private function getCpuUsage()
    {
        // Mock implementation
        return 35.5; // percentage
    }

    private function getLoadAverage()
    {
        // Mock implementation
        return [1.2, 1.5, 1.8];
    }

    private function getActiveConnections()
    {
        // Mock implementation
        return 150;
    }

    private function getLogFile($type, $date)
    {
        $logPath = storage_path('logs');
        
        switch ($type) {
            case 'laravel':
                return $logPath . '/laravel-' . $date . '.log';
            case 'error':
                return $logPath . '/error-' . $date . '.log';
            case 'security':
                return $logPath . '/security-' . $date . '.log';
            default:
                return $logPath . '/laravel.log';
        }
    }

    private function parseLogContent($content, $level)
    {
        // Simple log parsing - implement more sophisticated parsing as needed
        $lines = explode("\n", $content);
        $entries = [];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            if ($level !== 'all' && !str_contains(strtolower($line), strtolower($level))) {
                continue;
            }
            
            $entries[] = [
                'content' => $line,
                'timestamp' => $this->extractTimestamp($line),
                'level' => $this->extractLogLevel($line),
            ];
        }
        
        return array_reverse($entries); // Latest first
    }

    private function extractTimestamp($line)
    {
        // Extract timestamp from log line
        preg_match('/\[(.*?)\]/', $line, $matches);
        return isset($matches[1]) ? Carbon::parse($matches[1]) : null;
    }

    private function extractLogLevel($line)
    {
        // Extract log level from log line
        if (preg_match('/\]\s+(\w+)\./', $line, $matches)) {
            return strtoupper($matches[1]);
        }
        return 'INFO';
    }

    private function clearLaravelLogs($cutoffDate)
    {
        $logPath = storage_path('logs');
        $files = File::glob($logPath . '/laravel-*.log');
        $cleared = 0;
        
        foreach ($files as $file) {
            $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
            if ($fileDate->lt($cutoffDate)) {
                File::delete($file);
                $cleared++;
            }
        }
        
        return $cleared;
    }

    private function clearErrorLogs($cutoffDate)
    {
        // Implement error log clearing
        return 0;
    }

    private function clearSecurityLogs($cutoffDate)
    {
        // Implement security log clearing
        return 0;
    }

    private function clearActivityLogs($cutoffDate)
    {
        // Implement activity log clearing from database
        return 0;
    }
}
