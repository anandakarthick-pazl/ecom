@extends('super-admin.layouts.app')

@section('title', 'Edit Support Ticket')
@section('page-title', 'Edit Support Ticket #' . $support->ticket_number)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Support Ticket
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.support.update', $support) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ (old('company_id', $support->company_id) == $company->id) ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority', $support->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $support->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $support->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $support->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="technical" {{ old('category', $support->category) == 'technical' ? 'selected' : '' }}>Technical Support</option>
                                    <option value="billing" {{ old('category', $support->category) == 'billing' ? 'selected' : '' }}>Billing Issue</option>
                                    <option value="feature" {{ old('category', $support->category) == 'feature' ? 'selected' : '' }}>Feature Request</option>
                                    <option value="bug" {{ old('category', $support->category) == 'bug' ? 'selected' : '' }}>Bug Report</option>
                                    <option value="general" {{ old('category', $support->category) == 'general' ? 'selected' : '' }}>General Inquiry</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="open" {{ old('status', $support->status) == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ old('status', $support->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="pending" {{ old('status', $support->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="resolved" {{ old('status', $support->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ old('status', $support->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                               id="subject" name="subject" value="{{ old('subject', $support->subject) }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="6" required>{{ old('description', $support->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Current Attachments -->
                    @if($support->attachments && count($support->attachments) > 0)
                        <div class="mb-3">
                            <label class="form-label">Current Attachments</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($support->attachments as $index => $attachment)
                                    <div class="d-flex align-items-center bg-light p-2 rounded">
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="text-decoration-none me-2">
                                            <i class="fas fa-paperclip me-1"></i>{{ basename($attachment) }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-attachment" 
                                                data-attachment-index="{{ $index }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="hidden" name="keep_attachments[]" value="{{ $index }}" id="keep_attachment_{{ $index }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="new_attachments" class="form-label">Add New Attachments (Optional)</label>
                        <input type="file" class="form-control @error('new_attachments.*') is-invalid @enderror" 
                               id="new_attachments" name="new_attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                        <small class="form-text text-muted">
                            Maximum 5 files. Supported formats: JPG, PNG, PDF, DOC, DOCX, TXT. Max size: 5MB per file.
                        </small>
                        @error('new_attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" {{ old('send_notification') ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_notification">
                                Send email notification to company about changes
                            </label>
                        </div>
                    </div>
                    
                    <!-- Ticket History -->
                    @if($support->responses && count($support->responses) > 0)
                        <div class="mb-4">
                            <h6>Recent Activity</h6>
                            <div class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                @foreach(array_reverse($support->responses) as $response)
                                    <div class="mb-2 pb-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $response['type'] === 'admin' ? 'Support Team' : $support->company->name }}</strong>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($response['created_at'])->format('M d, Y g:i A') }}</small>
                                        </div>
                                        <div class="mt-1">{{ Str::limit($response['message'], 100) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('super-admin.support.show', $support) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Ticket
                            </a>
                            <a href="{{ route('super-admin.support.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>All Tickets
                            </a>
                        </div>
                        
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Ticket
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Remove attachment functionality
    $('.remove-attachment').on('click', function() {
        const attachmentIndex = $(this).data('attachment-index');
        const $container = $(this).closest('.d-flex');
        
        if (confirm('Are you sure you want to remove this attachment?')) {
            $container.hide();
            $('#keep_attachment_' + attachmentIndex).remove();
        }
    });
    
    // Auto-suggest based on category
    $('#category').on('change', function() {
        const category = $(this).val();
        const subjectField = $('#subject');
        const currentSubject = subjectField.val();
        
        // Only suggest if subject doesn't already have a prefix
        if (category && currentSubject && !currentSubject.includes(':')) {
            let suggestion = '';
            switch(category) {
                case 'technical':
                    suggestion = 'Technical Issue: ';
                    break;
                case 'billing':
                    suggestion = 'Billing Inquiry: ';
                    break;
                case 'feature':
                    suggestion = 'Feature Request: ';
                    break;
                case 'bug':
                    suggestion = 'Bug Report: ';
                    break;
                case 'general':
                    suggestion = 'General Question: ';
                    break;
            }
            
            if (suggestion && !currentSubject.startsWith(suggestion)) {
                subjectField.val(suggestion + currentSubject);
            }
        }
    });
});
</script>
@endpush
