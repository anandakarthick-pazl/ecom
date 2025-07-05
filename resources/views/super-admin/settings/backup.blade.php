@extends('super-admin.layouts.app')

@section('title', 'Backup Management')
@section('page-title', 'Backup Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-database me-2"></i>Database Backup Management
                </h5>
                <button type="button" class="btn btn-primary" onclick="createBackup()">
                    <i class="fas fa-plus me-2"></i>Create New Backup
                </button>
            </div>
            <div class="card-body">
                <!-- Backup Status Overview -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-database fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Total Backups</h5>
                                <h3 class="text-white mb-0" id="total-backups">{{ $backups->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card success">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Last Backup</h5>
                                <div class="text-white" id="last-backup">
                                    {{ $lastBackupTime ?? 'Never' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card warning">
                            <div class="card-body text-center">
                                <i class="fas fa-hdd fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Total Size</h5>
                                <div class="text-white" id="total-size">{{ $totalSize }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card danger">
                            <div class="card-body text-center">
                                <i class="fas fa-cog fa-2x text-white mb-2"></i>
                                <h5 class="text-white">Auto Backup</h5>
                                <span class="badge bg-light text-dark">
                                    {{ $settings['auto_backup_enabled'] ?? false ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Backup List -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Available Backups</h6>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="cleanupOldBackups()">
                                    <i class="fas fa-broom me-2"></i>Cleanup Old Backups
                                </button>
                            </div>
                            <div class="card-body">
                                @if($backups->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Backup Name</th>
                                                    <th>Type</th>
                                                    <th>Size</th>
                                                    <th>Created</th>
                                                    <th>Status</th>
                                                    <th class="actions-column">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="backups-table">
                                                @foreach($backups as $backup)
                                                    <tr id="backup-row-{{ $backup->id }}">
                                                        <td>
                                                        <strong>{{ $backup->filename }}</strong>
                                                        @if($backup->description)
                                                        <br><small class="text-muted">{{ $backup->description }}</small>
                                                        @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $backup->type === 'full' ? 'bg-success' : ($backup->type === 'partial' ? 'bg-warning text-dark' : 'bg-info') }}">
                                                                {{ ucfirst($backup->type) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $backup->formatted_size }}</td>
                                                        <td>
                                                            {{ $backup->created_at_formatted }}
                                                            <br><small class="text-muted">{{ $backup->created_at_human }}</small>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $backup->status === 'completed' ? 'bg-success' : ($backup->status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                                {{ ucfirst($backup->status) }}
                                                            </span>
                                                        </td>
                                                        <td class="actions-column">
                                                            <div class="btn-group" role="group">
                                                                @if($backup->status === 'completed')
                                                                    <a href="{{ route('super-admin.settings.backup.download', $backup) }}" 
                                                                       class="btn btn-sm btn-outline-primary" title="Download">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-outline-success restore-backup-btn" 
                                                                            data-backup-id="{{ $backup->id }}" title="Restore">
                                                                        <i class="fas fa-undo"></i>
                                                                    </button>
                                                                @endif
                                                                <button type="button" class="btn btn-sm btn-outline-info view-details-btn" 
                                                                        data-backup-id="{{ $backup->id }}" title="View Details">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger delete-backup-btn" 
                                                                        data-backup-id="{{ $backup->id }}" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $backups->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Backups Found</h5>
                                        <p class="text-muted">Create your first backup to secure your data.</p>
                                        <button type="button" class="btn btn-primary" onclick="createBackup()">
                                            <i class="fas fa-plus me-2"></i>Create First Backup
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Backup Settings -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Backup Settings</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('super-admin.settings.backup.settings') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="auto_backup_enabled" 
                                                   name="auto_backup_enabled" value="1" 
                                                   {{ old('auto_backup_enabled', $settings['auto_backup_enabled'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="auto_backup_enabled">
                                                Enable Automatic Backups
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                        <select class="form-select" id="backup_frequency" name="backup_frequency">
                                            <option value="daily" {{ old('backup_frequency', $settings['backup_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ old('backup_frequency', $settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="backup_time" class="form-label">Backup Time</label>
                                        <input type="time" class="form-control" id="backup_time" name="backup_time" 
                                               value="{{ old('backup_time', $settings['backup_time'] ?? '02:00') }}">
                                        <small class="form-text text-muted">Time when automatic backups should run</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="retention_days" class="form-label">Retention Period (days)</label>
                                        <input type="number" class="form-control" id="retention_days" name="retention_days" 
                                               value="{{ old('retention_days', $settings['retention_days'] ?? 30) }}" min="1" max="365">
                                        <small class="form-text text-muted">How long to keep backups before auto-deletion</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="max_backups" class="form-label">Maximum Backups</label>
                                        <input type="number" class="form-control" id="max_backups" name="max_backups" 
                                               value="{{ old('max_backups', $settings['max_backups'] ?? 10) }}" min="1" max="50">
                                        <small class="form-text text-muted">Maximum number of backups to keep</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="compress_backups" 
                                                   name="compress_backups" value="1" 
                                                   {{ old('compress_backups', $settings['compress_backups'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="compress_backups">
                                                Compress Backup Files
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Backup Progress -->
                        <div class="card" id="backup-progress-card" style="display: none;">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Backup Progress</h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar" style="width: 0%" id="backup-progress-bar">0%</div>
                                </div>
                                <div id="backup-status-text">Preparing backup...</div>
                                <button type="button" class="btn btn-outline-danger btn-sm mt-3 w-100" onclick="cancelBackup()">
                                    <i class="fas fa-times me-2"></i>Cancel Backup
                                </button>
                            </div>
                        </div>
                        
                        <!-- Storage Info -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Storage Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-7"><strong>Available Space:</strong></div>
                                    <div class="col-5">{{ $storageInfo['available'] ?? 'Unknown' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7"><strong>Used Space:</strong></div>
                                    <div class="col-5">{{ $storageInfo['used'] ?? 'Unknown' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7"><strong>Backup Location:</strong></div>
                                    <div class="col-5"><small class="text-muted">{{ $storageInfo['path'] ?? '/storage/backups' }}</small></div>
                                </div>
                                
                                @if(isset($storageInfo['usage_percentage']))
                                    <div class="progress mt-3">
                                        <div class="progress-bar {{ $storageInfo['usage_percentage'] > 80 ? 'bg-danger' : ($storageInfo['usage_percentage'] > 60 ? 'bg-warning' : 'bg-success') }}" 
                                             style="width: {{ $storageInfo['usage_percentage'] }}%">
                                            {{ $storageInfo['usage_percentage'] }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Backup Modal -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBackupForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="backup_name" class="form-label">Backup Name</label>
                        <input type="text" class="form-control" id="backup_name" name="backup_name" 
                               value="backup_{{ date('Y_m_d_H_i_s') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="backup_type" class="form-label">Backup Type</label>
                        <select class="form-select" id="backup_type" name="backup_type" required>
                            <option value="full">Full Backup (All Data)</option>
                            <option value="partial">Partial Backup (Structure Only)</option>
                            <option value="custom">Custom Backup</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="custom-tables" style="display: none;">
                        <label class="form-label">Select Tables</label>
                        <div class="form-check-list" style="max-height: 200px; overflow-y: auto;">
                            @foreach($databaseTables ?? [] as $table)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tables[]" value="{{ $table }}" id="table_{{ $table }}">
                                    <label class="form-check-label" for="table_{{ $table }}">{{ $table }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Add a description for this backup..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="compress_backup" name="compress_backup" value="1" checked>
                            <label class="form-check-label" for="compress_backup">
                                Compress backup file
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play me-2"></i>Start Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Backup Details Modal -->
<div class="modal fade" id="backupDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Backup Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="backup-details-content">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Restore</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> Restoring a backup will overwrite your current database. This action cannot be undone.
                </div>
                <p>Are you sure you want to restore from backup <strong id="restore-backup-name"></strong>?</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirm_restore" required>
                    <label class="form-check-label" for="confirm_restore">
                        I understand that this will overwrite my current data
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-restore-btn">
                    <i class="fas fa-undo me-2"></i>Restore Backup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete backup <strong id="delete-backup-name"></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                    <i class="fas fa-trash me-2"></i>Delete Backup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentBackupId = null;
let backupInProgress = false;

// Create backup
function createBackup() {
    $('#createBackupModal').modal('show');
}

// Handle backup type change
$('#backup_type').on('change', function() {
    if ($(this).val() === 'custom') {
        $('#custom-tables').show();
    } else {
        $('#custom-tables').hide();
    }
});

// Handle create backup form submission
$('#createBackupForm').on('submit', function(e) {
    e.preventDefault();
    
    if (backupInProgress) {
        alert('Another backup is already in progress.');
        return;
    }
    
    const formData = new FormData(this);
    
    $.ajax({
        url: '{{ route("super-admin.settings.backup.create") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#createBackupModal').modal('hide');
                startBackupProgress(response.backup_id);
            } else {
                alert('Error creating backup: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error creating backup. Please try again.');
        }
    });
});

// Start backup progress tracking
function startBackupProgress(backupId) {
    backupInProgress = true;
    currentBackupId = backupId;
    $('#backup-progress-card').show();
    
    const progressInterval = setInterval(() => {
        checkBackupProgress(backupId, progressInterval);
    }, 2000);
}

// Check backup progress
function checkBackupProgress(backupId, interval) {
    $.ajax({
        url: `{{ route("super-admin.settings.backup.progress", "") }}/${backupId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                const progress = response.progress;
                $('#backup-progress-bar').css('width', progress + '%').text(progress + '%');
                $('#backup-status-text').text(response.status);
                
                if (progress >= 100 || response.completed) {
                    clearInterval(interval);
                    backupInProgress = false;
                    $('#backup-progress-card').hide();
                    
                    if (response.success) {
                        alert('Backup completed successfully!');
                        location.reload();
                    } else {
                        alert('Backup failed: ' + response.message);
                    }
                }
            }
        },
        error: function() {
            clearInterval(interval);
            backupInProgress = false;
            $('#backup-progress-card').hide();
            alert('Error checking backup progress.');
        }
    });
}

// Cancel backup
function cancelBackup() {
    if (currentBackupId && confirm('Are you sure you want to cancel the backup?')) {
        $.ajax({
            url: `{{ route("super-admin.settings.backup.cancel", "") }}/${currentBackupId}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                backupInProgress = false;
                $('#backup-progress-card').hide();
                alert('Backup cancelled.');
            }
        });
    }
}

// View backup details
$(document).on('click', '.view-details-btn', function() {
    const backupId = $(this).data('backup-id');
    
    $.ajax({
        url: `{{ route("super-admin.settings.backup.details", "") }}/${backupId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#backup-details-content').html(response);
            $('#backupDetailsModal').modal('show');
        },
        error: function() {
            alert('Error loading backup details.');
        }
    });
});

// Restore backup
$(document).on('click', '.restore-backup-btn', function() {
    const backupId = $(this).data('backup-id');
    const backupName = $(this).closest('tr').find('strong').text();
    
    currentBackupId = backupId;
    $('#restore-backup-name').text(backupName);
    $('#restoreModal').modal('show');
});

$('#confirm-restore-btn').on('click', function() {
    if (!$('#confirm_restore').is(':checked')) {
        alert('Please confirm that you understand the risks.');
        return;
    }
    
    if (currentBackupId) {
        $.ajax({
            url: `{{ route("super-admin.settings.backup.restore", "") }}/${currentBackupId}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#restoreModal').modal('hide');
                    alert('Database restored successfully!');
                    location.reload();
                } else {
                    alert('Error restoring backup: ' + response.message);
                }
            },
            error: function() {
                alert('Error restoring backup. Please try again.');
            }
        });
    }
});

// Delete backup
$(document).on('click', '.delete-backup-btn', function() {
    const backupId = $(this).data('backup-id');
    const backupName = $(this).closest('tr').find('strong').text();
    
    currentBackupId = backupId;
    $('#delete-backup-name').text(backupName);
    $('#deleteBackupModal').modal('show');
});

$('#confirm-delete-btn').on('click', function() {
    if (currentBackupId) {
        $.ajax({
            url: `{{ route("super-admin.settings.backup.delete", "") }}/${currentBackupId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteBackupModal').modal('hide');
                    $(`#backup-row-${currentBackupId}`).remove();
                    alert('Backup deleted successfully!');
                } else {
                    alert('Error deleting backup: ' + response.message);
                }
            },
            error: function() {
                alert('Error deleting backup. Please try again.');
            }
        });
    }
});

// Cleanup old backups
function cleanupOldBackups() {
    if (confirm('This will delete all backups older than the retention period. Continue?')) {
        $.ajax({
            url: '{{ route("super-admin.settings.backup.cleanup") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(`Cleanup completed. ${response.deleted_count} backups deleted.`);
                    location.reload();
                } else {
                    alert('Error during cleanup: ' + response.message);
                }
            },
            error: function() {
                alert('Error during cleanup. Please try again.');
            }
        });
    }
}
</script>
@endpush
