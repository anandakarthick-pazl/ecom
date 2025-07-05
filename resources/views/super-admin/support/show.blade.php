@extends('super-admin.layouts.app')

@section('title', 'Support Ticket Details')
@section('page-title', 'Support Ticket #' . ($support->ticket_number ?? $support->id))

@section('content')
<div class="row">
    <!-- Ticket Details -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $support->subject ?? $support->title ?? 'Support Ticket' }}</h5>
                <div>
                    <span class="badge {{ $support->priority === 'urgent' ? 'bg-danger' : ($support->priority === 'high' ? 'bg-warning text-dark' : ($support->priority === 'medium' ? 'bg-info' : 'bg-secondary')) }}">
                        {{ ucfirst($support->priority ?? 'medium') }} Priority
                    </span>
                    <span class="badge {{ $support->status === 'open' ? 'bg-success' : ($support->status === 'in_progress' ? 'bg-warning text-dark' : ($support->status === 'pending' ? 'bg-info' : 'bg-secondary')) }}">
                        {{ ucfirst(str_replace('_', ' ', $support->status ?? 'open')) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Original Message -->
                <div class="mb-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($support->company->name ?? 'CO', 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $support->company->name ?? 'Company Name' }}</h6>
                                <small class="text-muted">{{ $support->created_at->format('M d, Y - g:i A') }}</small>
                            </div>
                            <div class="text-muted small mb-2">
                                <i class="fas fa-tag me-1"></i>{{ ucfirst($support->category ?? 'general') }}
                            </div>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($support->description ?? 'No description provided.')) !!}
                            </div>
                            
                            <!-- Attachments -->
                            @if(isset($support->attachments) && is_array($support->attachments) && count($support->attachments) > 0)
                                <div class="mt-3">
                                    <small class="text-muted">Attachments:</small>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @foreach($support->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paperclip me-1"></i>{{ basename($attachment) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Responses -->
                @if(isset($support->responses) && is_array($support->responses) && count($support->responses) > 0)
                    <div class="border-top pt-4">
                        <h6 class="mb-3">Responses ({{ count($support->responses) }})</h6>
                        @foreach($support->responses as $response)
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    @if($response['type'] === 'admin')
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                    @else
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($support->company->name ?? 'CO', 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">
                                            {{ $response['type'] === 'admin' ? 'Support Team' : ($support->company->name ?? 'Company') }}
                                        </h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($response['created_at'])->format('M d, Y - g:i A') }}</small>
                                    </div>
                                    <div class="bg-light p-3 rounded">
                                        {!! nl2br(e($response['message'])) !!}
                                    </div>
                                    
                                    @if(isset($response['attachments']) && count($response['attachments']) > 0)
                                        <div class="mt-2">
                                            <small class="text-muted">Attachments:</small>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @foreach($response['attachments'] as $attachment)
                                                    <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-paperclip me-1"></i>{{ basename($attachment) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Add Response Form -->
                @if($support->status !== 'closed')
                    <div class="border-top pt-4">
                        <h6 class="mb-3">Add Response</h6>
                        <form action="{{ route('super-admin.support.respond', $support) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          name="message" rows="4" placeholder="Type your response here..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="response_attachments" class="form-label">Attachments (Optional)</label>
                                <input type="file" class="form-control" name="attachments[]" multiple 
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                <small class="form-text text-muted">Max 5 files, 5MB each</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-reply me-2"></i>Send Response
                                </button>
                                <button type="submit" name="close_ticket" value="1" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Respond & Close
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Ticket Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Ticket Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Ticket #:</strong></div>
                    <div class="col-sm-6">{{ $support->ticket_number ?? $support->id }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Status:</strong></div>
                    <div class="col-sm-6">
                        <span class="badge {{ $support->status === 'open' ? 'bg-success' : ($support->status === 'in_progress' ? 'bg-warning text-dark' : ($support->status === 'pending' ? 'bg-info' : 'bg-secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $support->status ?? 'open')) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Priority:</strong></div>
                    <div class="col-sm-6">
                        <span class="badge {{ $support->priority === 'urgent' ? 'bg-danger' : ($support->priority === 'high' ? 'bg-warning text-dark' : ($support->priority === 'medium' ? 'bg-info' : 'bg-secondary')) }}">
                            {{ ucfirst($support->priority ?? 'medium') }}
                        </span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Category:</strong></div>
                    <div class="col-sm-6">{{ ucfirst($support->category ?? 'general') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Created:</strong></div>
                    <div class="col-sm-6">{{ $support->created_at->format('M d, Y') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6"><strong>Last Updated:</strong></div>
                    <div class="col-sm-6">{{ $support->updated_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Company Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Company Details</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    @if(isset($support->company->logo) && $support->company->logo)
                        <img src="{{ asset('storage/' . $support->company->logo) }}" 
                             class="rounded me-3" width="50" height="50" style="object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            {{ strtoupper(substr($support->company->name ?? 'CO', 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-1">{{ $support->company->name ?? 'Company Name' }}</h6>
                        <small class="text-muted">{{ $support->company->email ?? 'No email' }}</small>
                    </div>
                </div>
                
                @if(isset($support->company))
                    <div class="row mb-2">
                        <div class="col-sm-5"><strong>Domain:</strong></div>
                        <div class="col-sm-7">
                            @if(isset($support->company->domain))
                                <a href="http://{{ $support->company->domain }}" target="_blank" class="text-decoration-none">
                                    {{ $support->company->domain }}
                                </a>
                            @else
                                <span class="text-muted">No domain</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5"><strong>Package:</strong></div>
                        <div class="col-sm-7">
                            @if(isset($support->company->package))
                                <span class="badge bg-info">{{ $support->company->package->name }}</span>
                            @else
                                <span class="text-muted">No Package</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5"><strong>Status:</strong></div>
                        <div class="col-sm-7">
                            <span class="badge {{ ($support->company->status ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($support->company->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('super-admin.companies.show', $support->company) }}" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-building me-2"></i>View Company Details
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.support.update-status', $support) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <div class="mb-2">
                        <label for="status" class="form-label">Change Status:</label>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="open" {{ $support->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $support->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending" {{ $support->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="resolved" {{ $support->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $support->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </form>
                
                <form action="{{ route('super-admin.support.update-priority', $support) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <div class="mb-2">
                        <label for="priority" class="form-label">Change Priority:</label>
                        <select class="form-select" name="priority" onchange="this.form.submit()">
                            <option value="low" {{ $support->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $support->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $support->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $support->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                </form>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.support.edit', $support) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Ticket
                    </a>
                    <a href="{{ route('super-admin.support.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
