@extends('super-admin.layouts.app')

@section('title', 'Cache Management')
@section('page-title', 'Cache Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Cache Management
                </h5>
            </div>
            <div class="card-body">
                <!-- Cache Status Overview -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-database fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Application Cache</h5>
                                <span class="badge bg-light text-dark" id="app-cache-status">
                                    {{ $cacheStatus['application'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-code fa-2x text-white mb-2"></i>
                                <h5 class="text-white">View Cache</h5>
                                <span class="badge bg-light text-dark" id="view-cache-status">
                                    {{ $cacheStatus['views'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card warning">
                            <div class="card-body text-center">
                                <i class="fas fa-route fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Route Cache</h5>
                                <span class="badge bg-light text-dark" id="route-cache-status">
                                    {{ $cacheStatus['routes'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card danger">
                            <div class="card-body text-center">
                                <i class="fas fa-cogs fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Config Cache</h5>
                                <span class="badge bg-light text-dark" id="config-cache-status">
                                    {{ $cacheStatus['config'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cache Actions -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Cache Operations</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-warning" onclick="performCacheAction('clear-all')">
                                        <i class="fas fa-broom me-2"></i>Clear All Caches
                                    </button>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-primary w-100" onclick="performCacheAction('clear-application')">
                                                <i class="fas fa-database me-2"></i>Clear App Cache
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-success w-100" onclick="performCacheAction('clear-views')">
                                                <i class="fas fa-code me-2"></i>Clear Views
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-warning w-100" onclick="performCacheAction('clear-routes')">
                                                <i class="fas fa-route me-2"></i>Clear Routes
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-danger w-100" onclick="performCacheAction('clear-config')">
                                                <i class="fas fa-cogs me-2"></i>Clear Config
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <button type="button" class="btn btn-success" onclick="performCacheAction('optimize')">
                                        <i class="fas fa-rocket me-2"></i>Optimize Application
                                    </button>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-info w-100" onclick="performCacheAction('cache-config')">
                                                <i class="fas fa-cogs me-2"></i>Cache Config
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-info w-100" onclick="performCacheAction('cache-routes')">
                                                <i class="fas fa-route me-2"></i>Cache Routes
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-info w-100" onclick="performCacheAction('cache-views')">
                                                <i class="fas fa-code me-2"></i>Cache Views
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-secondary w-100" onclick="performCacheAction('queue-restart')">
                                                <i class="fas fa-redo me-2"></i>Restart Queue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Cache Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td><strong>Cache Driver:</strong></td>
                                                <td>{{ config('cache.default') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Session Driver:</strong></td>
                                                <td>{{ config('session.driver') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Queue Driver:</strong></td>
                                                <td>{{ config('queue.default') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cache Size:</strong></td>
                                                <td id="cache-size">{{ $cacheInfo['size'] ?? 'Unknown' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Cleared:</strong></td>
                                                <td id="last-cleared">{{ $cacheInfo['last_cleared'] ?? 'Unknown' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <h6>Environment Information</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Environment:</small>
                                            <div><span class="badge bg-{{ app()->environment('production') ? 'danger' : 'warning' }}">{{ app()->environment() }}</span></div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Debug Mode:</small>
                                            <div><span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">{{ config('app.debug') ? 'On' : 'Off' }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cache Settings -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Cache Settings</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('super-admin.settings.cache.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-3">
                                        <label for="cache_ttl" class="form-label">Default Cache TTL (seconds)</label>
                                        <input type="number" class="form-control" id="cache_ttl" name="cache_ttl" 
                                               value="{{ old('cache_ttl', $settings['cache_ttl'] ?? 3600) }}" min="60" max="86400">
                                        <small class="form-text text-muted">Time to live for cached data (1 hour = 3600 seconds)</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="auto_cache_clear" name="auto_cache_clear" value="1" 
                                                   {{ old('auto_cache_clear', $settings['auto_cache_clear'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_cache_clear">
                                                Auto-clear cache on updates
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_query_cache" name="enable_query_cache" value="1" 
                                                   {{ old('enable_query_cache', $settings['enable_query_cache'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_query_cache">
                                                Enable database query caching
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="cache_prefix" class="form-label">Cache Prefix</label>
                                        <input type="text" class="form-control" id="cache_prefix" name="cache_prefix" 
                                               value="{{ old('cache_prefix', $settings['cache_prefix'] ?? 'herbal_ecom') }}" 
                                               pattern="[a-zA-Z0-9_]+" maxlength="20">
                                        <small class="form-text text-muted">Used to avoid cache conflicts</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Operation Log -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Operation Log</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearLog()">
                                    <i class="fas fa-trash me-2"></i>Clear Log
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="operation-log" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 14px;">
                                    <div class="text-muted">Ready to perform cache operations...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let operationInProgress = false;

function addLogEntry(message, type = 'info') {
    const log = document.getElementById('operation-log');
    const timestamp = new Date().toLocaleTimeString();
    const entry = document.createElement('div');
    
    let color = '#6c757d'; // default gray
    let icon = 'fas fa-info-circle';
    
    switch(type) {
        case 'success':
            color = '#28a745';
            icon = 'fas fa-check-circle';
            break;
        case 'error':
            color = '#dc3545';
            icon = 'fas fa-exclamation-circle';
            break;
        case 'warning':
            color = '#ffc107';
            icon = 'fas fa-exclamation-triangle';
            break;
        case 'info':
            color = '#17a2b8';
            icon = 'fas fa-info-circle';
            break;
    }
    
    entry.innerHTML = `
        <div style="color: ${color}; margin-bottom: 5px;">
            <i class="${icon}" style="margin-right: 5px;"></i>
            [${timestamp}] ${message}
        </div>
    `;
    
    log.appendChild(entry);
    log.scrollTop = log.scrollHeight;
}

function clearLog() {
    document.getElementById('operation-log').innerHTML = '<div class="text-muted">Log cleared...</div>';
}

function updateCacheStatus() {
    addLogEntry('Checking cache status...', 'info');
    
    fetch('{{ route("super-admin.settings.cache.status") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badges
            document.getElementById('app-cache-status').textContent = data.status.application ? 'Active' : 'Inactive';
            document.getElementById('view-cache-status').textContent = data.status.views ? 'Active' : 'Inactive';
            document.getElementById('route-cache-status').textContent = data.status.routes ? 'Active' : 'Inactive';
            document.getElementById('config-cache-status').textContent = data.status.config ? 'Active' : 'Inactive';
            
            if (data.info) {
                document.getElementById('cache-size').textContent = data.info.size || 'Unknown';
                document.getElementById('last-cleared').textContent = data.info.last_cleared || 'Unknown';
            }
            
            addLogEntry('Cache status updated successfully', 'success');
        } else {
            addLogEntry('Failed to update cache status', 'error');
        }
    })
    .catch(error => {
        addLogEntry('Error checking cache status: ' + error.message, 'error');
    });
}

function performCacheAction(action) {
    if (operationInProgress) {
        addLogEntry('Another operation is in progress. Please wait...', 'warning');
        return;
    }
    
    operationInProgress = true;
    
    const actionNames = {
        'clear-all': 'Clearing all caches',
        'clear-application': 'Clearing application cache',
        'clear-views': 'Clearing view cache',
        'clear-routes': 'Clearing route cache',
        'clear-config': 'Clearing config cache',
        'optimize': 'Optimizing application',
        'cache-config': 'Caching configuration',
        'cache-routes': 'Caching routes',
        'cache-views': 'Caching views',
        'queue-restart': 'Restarting queue workers'
    };
    
    addLogEntry(actionNames[action] + '...', 'info');
    
    // Disable all buttons
    document.querySelectorAll('button[onclick^="performCacheAction"]').forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>' + btn.textContent.trim();
    });
    
    fetch('{{ route("super-admin.settings.cache.action") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addLogEntry(data.message || actionNames[action] + ' completed successfully', 'success');
            
            if (data.output) {
                addLogEntry('Output: ' + data.output, 'info');
            }
            
            // Update cache status after the operation
            setTimeout(updateCacheStatus, 1000);
        } else {
            addLogEntry('Error: ' + (data.message || 'Operation failed'), 'error');
        }
    })
    .catch(error => {
        addLogEntry('Network error: ' + error.message, 'error');
    })
    .finally(() => {
        operationInProgress = false;
        
        // Re-enable all buttons
        document.querySelectorAll('button[onclick^="performCacheAction"]').forEach(btn => {
            btn.disabled = false;
            // Restore original button text/icon
            const originalText = btn.textContent.replace('...', '').trim();
            const iconClass = btn.querySelector('i').className.replace('fa-spinner fa-spin', btn.getAttribute('data-original-icon') || 'fas fa-cog');
            btn.innerHTML = `<i class="${iconClass} me-2"></i>${originalText}`;
        });
    });
}

// Store original icons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button[onclick^="performCacheAction"] i').forEach(icon => {
        icon.parentElement.setAttribute('data-original-icon', icon.className);
    });
    
    // Initial cache status check
    updateCacheStatus();
    
    // Auto-refresh cache status every 30 seconds
    setInterval(updateCacheStatus, 30000);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 'r':
                if (e.shiftKey) {
                    e.preventDefault();
                    performCacheAction('clear-all');
                }
                break;
            case 'l':
                if (e.shiftKey) {
                    e.preventDefault();
                    clearLog();
                }
                break;
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.stat-card {
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

#operation-log::-webkit-scrollbar {
    width: 8px;
}

#operation-log::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#operation-log::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

#operation-log::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.btn:disabled {
    opacity: 0.7;
}
</style>
@endpush
