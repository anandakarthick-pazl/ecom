@extends('admin.layouts.app')

@section('title', 'Edit Offer')
@section('page_title', 'Edit Offer: ' . $offer->name)

@section('page_actions')
<a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Offers
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Offer Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $offer->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $offer->code) }}" 
                                   style="text-transform: uppercase;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Offer Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="percentage" {{ old('type', $offer->type) == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                <option value="fixed" {{ old('type', $offer->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                <option value="category" {{ old('type', $offer->type) == 'category' ? 'selected' : '' }}>Category Specific</option>
                                <option value="product" {{ old('type', $offer->type) == 'product' ? 'selected' : '' }}>Product Specific</option>
                                <option value="flash" {{ old('type', $offer->type) == 'flash' ? 'selected' : '' }}>Flash Offer</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="discount-type-section" style="display: {{ in_array(old('type', $offer->type), ['category', 'product', 'flash']) ? 'block' : 'none' }};">
                            <label for="discount_type" class="form-label">Discount Type *</label>
                            <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type">
                                <option value="">Select Discount Type</option>
                                <option value="percentage" {{ old('discount_type', $offer->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="flat" {{ old('discount_type', $offer->discount_type) == 'flat' ? 'selected' : '' }}>Flat Amount (₹)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">Discount Value *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="value-prefix">
                                    @php
                                        $currentType = old('type', $offer->type);
                                        $currentDiscountType = old('discount_type', $offer->discount_type ?? 'percentage');
                                        $showPercentage = $currentType === 'percentage' || $currentDiscountType === 'percentage';
                                    @endphp
                                    {{ $showPercentage ? '%' : '₹' }}
                                </span>
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value', $offer->value) }}" 
                                       step="0.01" min="0" required>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="minimum_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                   id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $offer->minimum_amount) }}" 
                                   step="0.01" min="0" placeholder="0.00">
                            @error('minimum_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Minimum cart value required</small>
                        </div>
                    </div>
                    
                    <div class="row" id="target-selection" style="display: {{ in_array(old('type', $offer->type), ['category', 'product']) ? 'block' : 'none' }};">
                        <div class="col-md-6 mb-3" id="category-selection" style="display: {{ old('type', $offer->type) === 'category' ? 'block' : 'none' }};">
                            <label for="category_id" class="form-label">Target Category *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $offer->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="product-selection" style="display: {{ old('type', $offer->type) === 'product' ? 'block' : 'none' }};">
                            <label for="product_id" class="form-label">Target Product *</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $offer->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $offer->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', $offer->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $offer->usage_limit) }}" 
                                   min="1" placeholder="Unlimited">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Maximum number of times this offer can be used</small>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Flash Offer Section -->
                    <div id="flash-offer-section" style="display: {{ old('type', $offer->type) == 'flash' || $offer->is_flash_offer ? 'block' : 'none' }};">
                        <hr class="my-4">
                        <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> Flash Offer Settings</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_flash_offer" name="is_flash_offer" value="1"
                                           {{ old('is_flash_offer', $offer->is_flash_offer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_flash_offer">
                                        <strong>Enable Flash Offer</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Enable this to show as a flash offer with popup banner</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_popup" name="show_popup" value="1"
                                           {{ old('show_popup', $offer->show_popup ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_popup">
                                        Show Popup Banner
                                    </label>
                                </div>
                                <small class="text-muted">Display popup when users visit the site</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="popup_frequency" class="form-label">Popup Frequency</label>
                                <select class="form-select @error('popup_frequency') is-invalid @enderror" id="popup_frequency" name="popup_frequency">
                                    <option value="always" {{ old('popup_frequency', $offer->popup_frequency ?? 'always') == 'always' ? 'selected' : '' }}>Every Page Visit</option>
                                    <option value="once_per_session" {{ old('popup_frequency', $offer->popup_frequency) == 'once_per_session' ? 'selected' : '' }}>Once Per Session</option>
                                    <option value="once_per_day" {{ old('popup_frequency', $offer->popup_frequency) == 'once_per_day' ? 'selected' : '' }}>Once Per Day</option>
                                    <option value="once_per_week" {{ old('popup_frequency', $offer->popup_frequency) == 'once_per_week' ? 'selected' : '' }}>Once Per Week</option>
                                </select>
                                @error('popup_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How often to show the popup to users</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <!-- Empty column for spacing -->
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="banner_image" class="form-label">Banner Image</label>
                                @if($offer->banner_image)
                                    <div class="mb-2">
                                        <img src="{{ $offer->banner_image_url }}" alt="Current Banner" style="max-width: 200px; height: auto;" class="img-thumbnail">
                                        <small class="d-block text-muted">Current banner image</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('banner_image') is-invalid @enderror" 
                                       id="banner_image" name="banner_image" accept="image/*">
                                @error('banner_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload new banner image for flash offer popup (Recommended: 800x400px)</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="banner_title" class="form-label">Banner Title</label>
                                <input type="text" class="form-control @error('banner_title') is-invalid @enderror" 
                                       id="banner_title" name="banner_title" value="{{ old('banner_title', $offer->banner_title) }}" 
                                       placeholder="Limited Time Flash Sale!">
                                @error('banner_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="banner_button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control @error('banner_button_text') is-invalid @enderror" 
                                       id="banner_button_text" name="banner_button_text" value="{{ old('banner_button_text', $offer->banner_button_text ?: 'Shop Now') }}" 
                                       placeholder="Shop Now">
                                @error('banner_button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="banner_description" class="form-label">Banner Description</label>
                                <textarea class="form-control @error('banner_description') is-invalid @enderror" 
                                          id="banner_description" name="banner_description" rows="3" 
                                          placeholder="Get amazing discounts on all herbal products. Limited time offer!">{{ old('banner_description', $offer->banner_description) }}</textarea>
                                @error('banner_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="banner_button_url" class="form-label">Button URL</label>
                                <input type="url" class="form-control @error('banner_button_url') is-invalid @enderror" 
                                       id="banner_button_url" name="banner_button_url" value="{{ old('banner_button_url', $offer->banner_button_url) }}" 
                                       placeholder="https://example.com/shop">
                                @error('banner_button_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Optional: Leave empty to use default shop page</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="popup_delay" class="form-label">Popup Delay (seconds)</label>
                                <input type="number" class="form-control @error('popup_delay') is-invalid @enderror" 
                                       id="popup_delay" name="popup_delay" value="{{ old('popup_delay', $offer->popup_delay ? intval($offer->popup_delay / 1000) : 3) }}" 
                                       min="0" max="60">
                                @error('popup_delay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Delay before showing popup (0-60 seconds)</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_countdown" name="show_countdown" value="1"
                                           {{ old('show_countdown', $offer->show_countdown ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_countdown">
                                        Show Countdown Timer
                                    </label>
                                </div>
                                <small class="text-muted">Display countdown timer in popup</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="countdown_text" class="form-label">Countdown Text</label>
                                <input type="text" class="form-control @error('countdown_text') is-invalid @enderror" 
                                       id="countdown_text" name="countdown_text" value="{{ old('countdown_text', $offer->countdown_text ?: 'Hurry! Limited time offer') }}" 
                                       placeholder="Hurry! Limited time offer">
                                @error('countdown_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Offer
                        </button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Offer Statistics</h6>
                <p><strong>Used:</strong> {{ $offer->used_count }} times</p>
                @if($offer->usage_limit)
                    <p><strong>Remaining:</strong> {{ $offer->usage_limit - $offer->used_count }}</p>
                @endif
                <p><strong>Created:</strong> {{ $offer->created_at->format('M d, Y') }}</p>
                
                @if($offer->isValid())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> This offer is currently active
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This offer is not currently active
                    </div>
                @endif
                
                <hr>
                
                <h6>Current Settings</h6>
                <ul class="list-unstyled small">
                    <li><strong>Type:</strong> {{ ucfirst($offer->type) }}</li>
                    @if($offer->discount_type)
                        <li><strong>Discount Type:</strong> {{ ucfirst($offer->discount_type) }}</li>
                    @endif
                    <li><strong>Value:</strong> {{ $offer->discount_value_display }}</li>
                    @if($offer->category)
                        <li><strong>Category:</strong> {{ $offer->category->name }}</li>
                    @endif
                    @if($offer->product)
                        <li><strong>Product:</strong> {{ $offer->product->name }}</li>
                    @endif
                    @if($offer->is_flash_offer)
                        <li><strong>Flash Offer:</strong> <span class="text-warning"><i class="fas fa-bolt"></i> Enabled</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const discountTypeSelect = document.getElementById('discount_type');
    const discountTypeSection = document.getElementById('discount-type-section');
    const valuePrefix = document.getElementById('value-prefix');
    const targetSelection = document.getElementById('target-selection');
    const categorySelection = document.getElementById('category-selection');
    const productSelection = document.getElementById('product-selection');
    const flashOfferSection = document.getElementById('flash-offer-section');
    const codeInput = document.getElementById('code');
    
    // Auto-uppercase coupon code
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Update value prefix based on discount type
    function updateValuePrefix() {
        const offerType = typeSelect.value;
        const discountType = discountTypeSelect.value;
        
        if (offerType === 'percentage' || discountType === 'percentage') {
            valuePrefix.textContent = '%';
        } else {
            valuePrefix.textContent = '₹';
        }
    }
    
    // Handle offer type changes
    typeSelect.addEventListener('change', function() {
        const value = this.value;
        
        // Show/hide sections based on offer type
        if (value === 'flash') {
            // Flash offer selected
            discountTypeSection.style.display = 'block';
            targetSelection.style.display = 'none';
            flashOfferSection.style.display = 'block';
            
            // Auto-check flash offer checkbox
            document.getElementById('is_flash_offer').checked = true;
            
            // Remove required attributes from category/product
            document.getElementById('category_id').removeAttribute('required');
            document.getElementById('product_id').removeAttribute('required');
            
        } else if (value === 'category' || value === 'product') {
            discountTypeSection.style.display = 'block';
            targetSelection.style.display = 'block';
            flashOfferSection.style.display = 'none';
            
            if (value === 'category') {
                categorySelection.style.display = 'block';
                productSelection.style.display = 'none';
                // Make category required
                document.getElementById('category_id').setAttribute('required', 'required');
                document.getElementById('product_id').removeAttribute('required');
            } else {
                categorySelection.style.display = 'none';
                productSelection.style.display = 'block';
                // Make product required
                document.getElementById('product_id').setAttribute('required', 'required');
                document.getElementById('category_id').removeAttribute('required');
            }
        } else {
            discountTypeSection.style.display = 'none';
            targetSelection.style.display = 'none';
            flashOfferSection.style.display = 'none';
            // Remove required attributes
            document.getElementById('category_id').removeAttribute('required');
            document.getElementById('product_id').removeAttribute('required');
        }
        
        updateValuePrefix();
    });
    
    // Handle discount type changes
    discountTypeSelect.addEventListener('change', function() {
        updateValuePrefix();
    });
    
    // Trigger change event for initial state
    typeSelect.dispatchEvent(new Event('change'));
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const offerType = typeSelect.value;
        
        if (offerType === 'category') {
            const categoryId = document.getElementById('category_id').value;
            if (!categoryId) {
                e.preventDefault();
                alert('Please select a category for category-specific offers.');
                return;
            }
        }
        
        if (offerType === 'product') {
            const productId = document.getElementById('product_id').value;
            if (!productId) {
                e.preventDefault();
                alert('Please select a product for product-specific offers.');
                return;
            }
        }
        
        if ((offerType === 'category' || offerType === 'product' || offerType === 'flash')) {
            const discountType = discountTypeSelect.value;
            if (!discountType) {
                e.preventDefault();
                alert('Please select a discount type (percentage or flat amount).');
                return;
            }
        }
    });
});
</script>
@endpush
@endsection
