@extends('super-admin.layouts.app')

@section('title', 'Create Billing Record')
@section('page-title', 'Create Billing Record')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Billing Record
                </h5>
                <a href="{{ route('super-admin.billing.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Billing
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.billing.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Billing Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                                            <select class="form-select @error('company_id') is-invalid @enderror" 
                                                    id="company_id" name="company_id" required>
                                                <option value="">Select Company</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" 
                                                            data-package-id="{{ $company->package_id }}"
                                                            data-package-price="{{ $company->package->price ?? 0 }}"
                                                            data-billing-cycle="{{ $company->package->billing_cycle ?? 'monthly' }}"
                                                            {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }} ({{ $company->domain }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="package_id" class="form-label">Package <span class="text-danger">*</span></label>
                                            <select class="form-select @error('package_id') is-invalid @enderror" 
                                                    id="package_id" name="package_id" required>
                                                <option value="">Select Package</option>
                                                @foreach($packages as $package)
                                                    <option value="{{ $package->id }}" 
                                                            data-price="{{ $package->price }}"
                                                            data-billing-cycle="{{ $package->billing_cycle }}"
                                                            {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                                        {{ $package->name }} - ${{ number_format($package->price, 2) }}/{{ $package->billing_cycle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('package_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" value="{{ old('amount') }}" 
                                                       min="0" step="0.01" required>
                                                @error('amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                            <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                                                    id="billing_cycle" name="billing_cycle" required>
                                                <option value="">Select Cycle</option>
                                                <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                <option value="lifetime" {{ old('billing_cycle') == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                            </select>
                                            @error('billing_cycle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" name="payment_method">
                                                <option value="">Select Method</option>
                                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Billing Dates -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Dates</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_date" class="form-label">Billing Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('billing_date') is-invalid @enderror" 
                                                   id="billing_date" name="billing_date" value="{{ old('billing_date', date('Y-m-d')) }}" required>
                                            @error('billing_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Date when the billing period starts.</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                                   id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Payment due date.</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Quick Actions:</strong>
                                                <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="setDueDate(7)">+7 days</button>
                                                <button type="button" class="btn btn-sm btn-outline-info ms-1" onclick="setDueDate(15)">+15 days</button>
                                                <button type="button" class="btn btn-sm btn-outline-info ms-1" onclick="setDueDate(30)">+30 days</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Any additional notes or comments about this billing record.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Billing Preview -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="billing-preview" id="billing-preview">
                                        <div class="text-center mb-3">
                                            <h5 class="preview-company">Select Company</h5>
                                            <p class="text-muted preview-package">Select Package</p>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Amount:</span>
                                            <strong class="preview-amount">$0.00</strong>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Billing Cycle:</span>
                                            <span class="preview-cycle">-</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Payment Method:</span>
                                            <span class="preview-method">Not specified</span>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Billing Date:</span>
                                            <span class="preview-billing-date">{{ date('M d, Y') }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>Due Date:</span>
                                            <span class="preview-due-date">Not set</span>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                Invoice number will be auto-generated after creation.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Templates -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Templates</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyTemplate('monthly')">
                                            <i class="fas fa-calendar me-2"></i>Monthly Billing
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="applyTemplate('yearly')">
                                            <i class="fas fa-calendar-alt me-2"></i>Yearly Billing
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="applyTemplate('overdue')">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Overdue Notice
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super-admin.billing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Billing Record
                                </button>
                            </div>
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
    // Auto-fill package when company is selected
    $('#company_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const packageId = selectedOption.data('package-id');
        const packagePrice = selectedOption.data('package-price');
        const billingCycle = selectedOption.data('billing-cycle');
        
        if (packageId) {
            $('#package_id').val(packageId);
            $('#amount').val(packagePrice);
            $('#billing_cycle').val(billingCycle);
        }
        
        updatePreview();
    });
    
    // Update amount when package is changed
    $('#package_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        const billingCycle = selectedOption.data('billing-cycle');
        
        if (price !== undefined) {
            $('#amount').val(price);
            $('#billing_cycle').val(billingCycle);
        }
        
        updatePreview();
    });
    
    // Update preview on input changes
    $('#amount, #billing_cycle, #payment_method, #billing_date, #due_date').on('change input', updatePreview);
    
    // Set due date helper
    window.setDueDate = function(days) {
        const billingDate = new Date($('#billing_date').val());
        const dueDate = new Date(billingDate.getTime() + (days * 24 * 60 * 60 * 1000));
        $('#due_date').val(dueDate.toISOString().split('T')[0]);
        updatePreview();
    };
    
    // Apply template
    window.applyTemplate = function(type) {
        const today = new Date();
        const billingDate = today.toISOString().split('T')[0];
        
        $('#billing_date').val(billingDate);
        
        switch(type) {
            case 'monthly':
                $('#billing_cycle').val('monthly');
                setDueDate(30);
                break;
            case 'yearly':
                $('#billing_cycle').val('yearly');
                setDueDate(15);
                break;
            case 'overdue':
                const pastDate = new Date(today.getTime() - (7 * 24 * 60 * 60 * 1000));
                $('#due_date').val(pastDate.toISOString().split('T')[0]);
                break;
        }
        updatePreview();
    };
    
    // Update preview function
    function updatePreview() {
        const companyName = $('#company_id option:selected').text().split(' (')[0] || 'Select Company';
        const packageName = $('#package_id option:selected').text().split(' - ')[0] || 'Select Package';
        const amount = $('#amount').val() || '0';
        const billingCycle = $('#billing_cycle option:selected').text() || '-';
        const paymentMethod = $('#payment_method option:selected').text() || 'Not specified';
        const billingDate = $('#billing_date').val();
        const dueDate = $('#due_date').val();
        
        $('.preview-company').text(companyName);
        $('.preview-package').text(packageName);
        $('.preview-amount').text('$' + parseFloat(amount).toFixed(2));
        $('.preview-cycle').text(billingCycle);
        $('.preview-method').text(paymentMethod);
        
        if (billingDate) {
            const formattedBillingDate = new Date(billingDate).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            $('.preview-billing-date').text(formattedBillingDate);
        }
        
        if (dueDate) {
            const formattedDueDate = new Date(dueDate).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            $('.preview-due-date').text(formattedDueDate);
        } else {
            $('.preview-due-date').text('Not set');
        }
    }
    
    // Initialize preview
    updatePreview();
    
    // Auto-set due date to 15 days from billing date on load
    if (!$('#due_date').val() && $('#billing_date').val()) {
        setDueDate(15);
    }
});
</script>
@endpush
