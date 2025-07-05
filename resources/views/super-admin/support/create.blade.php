@extends('super-admin.layouts.app')

@section('title', 'Create Support Ticket')
@section('page-title', 'Create Support Ticket')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-headset me-2"></i>Create New Support Ticket
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.support.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_id" class="form-label">Company</label>
                                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                                    <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Support</option>
                                    <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Billing Issue</option>
                                    <option value="feature" {{ old('category') == 'feature' ? 'selected' : '' }}>Feature Request</option>
                                    <option value="bug" {{ old('category') == 'bug' ? 'selected' : '' }}>Bug Report</option>
                                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General Inquiry</option>
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
                                    <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="resolved" {{ old('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
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
                               id="subject" name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments (Optional)</label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                               id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                        <small class="form-text text-muted">
                            Maximum 5 files. Supported formats: JPG, PNG, PDF, DOC, DOCX, TXT. Max size: 5MB per file.
                        </small>
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" {{ old('send_notification') ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_notification">
                                Send email notification to company
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.support.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Ticket
                        </button>
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
    // Auto-suggest based on category
    $('#category').on('change', function() {
        const category = $(this).val();
        const subjectField = $('#subject');
        
        if (category && !subjectField.val()) {
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
            subjectField.val(suggestion);
            subjectField.focus();
        }
    });
});
</script>
@endpush
