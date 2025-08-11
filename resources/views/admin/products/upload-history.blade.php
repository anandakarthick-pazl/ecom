@extends('admin.layouts.app')

@section('title', 'Product Upload History')
@section('page_title', 'Product Upload History')

@section('page_actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.products.bulk-upload') }}" class="btn btn-primary">
        <i class="fas fa-upload"></i> New Upload
    </a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Upload History
            </h6>
        </div>
        <div class="card-body">
            @if($uploads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="200">File Details</th>
                                <th>Results</th>
                                <th>Uploaded By</th>
                                <th>Upload Time</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploads as $upload)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-primary">{{ $upload->original_name }}</strong>
                                            <small class="text-muted">
                                                <i class="fas fa-file"></i> {{ $upload->formatted_size }} • 
                                                <i class="fas fa-clock"></i> {{ $upload->created_at->format('M d, Y H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($upload->meta_data)
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <div class="h5 text-success mb-0">{{ $upload->meta_data['created'] ?? 0 }}</div>
                                                        <small class="text-muted">Created</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <div class="h5 text-info mb-0">{{ $upload->meta_data['updated'] ?? 0 }}</div>
                                                        <small class="text-muted">Updated</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <div class="h5 text-danger mb-0">{{ $upload->meta_data['errors'] ?? 0 }}</div>
                                                        <small class="text-muted">Errors</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if(isset($upload->meta_data['errors']) && $upload->meta_data['errors'] > 0)
                                                <div class="mt-2">
                                                    <button class="btn btn-outline-warning btn-sm" type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#errors-{{ $upload->id }}" 
                                                            aria-expanded="false">
                                                        <i class="fas fa-exclamation-triangle"></i> View Errors
                                                    </button>
                                                    <div class="collapse mt-2" id="errors-{{ $upload->id }}">
                                                        <div class="card card-body bg-light">
                                                            @if(isset($upload->meta_data['error_details']))
                                                                <div style="max-height: 200px; overflow-y: auto;">
                                                                    @foreach($upload->meta_data['error_details'] as $error)
                                                                        <div class="text-sm text-danger">• {{ $error }}</div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">No data available</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($upload->uploader)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($upload->uploader->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $upload->uploader->name }}</strong>
                                                    @if($upload->uploader->email)
                                                        <br><small class="text-muted">{{ $upload->uploader->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $upload->created_at->format('M d, Y') }}</strong>
                                            <small class="text-muted">{{ $upload->created_at->format('H:i A') }}</small>
                                            <small class="text-muted">{{ $upload->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm">
                                            @if(isset($upload->meta_data['created']) && $upload->meta_data['created'] > 0)
                                                <a href="{{ route('admin.products.index') }}?search={{ urlencode($upload->original_name) }}" 
                                                   class="btn btn-outline-primary btn-sm" title="View Created Products">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $uploads->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-upload fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Upload History</h5>
                    <p class="text-muted">You haven't uploaded any products yet.</p>
                    <a href="{{ route('admin.products.bulk-upload') }}" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Your First Batch
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        display: inline-block;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
</style>
@endpush
@endsection