@extends('admin.layouts.app')

@section('title', 'Create Product')
@section('page_title', 'Create Product')

@section('page_actions')
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Products
</a>
@endsection

@section('scripts')
<script>
// Toggle discount fields based on selected type
function toggleDiscountFields() {
    const discountType = document.getElementById('discount_type').value;
    const percentageField = document.getElementById('discount_percentage_field');
    const priceField = document.getElementById('discount_price_field');
    const percentageInput = document.getElementById('discount_percentage');
    const priceInput = document.getElementById('discount_price');
    
    // Hide all fields first
    percentageField.style.display = 'none';
    priceField.style.display = 'none';
    
    // Clear values
    percentageInput.value = '';
    priceInput.value = '';
    
    // Show appropriate field
    if (discountType === 'percentage') {
        percentageField.style.display = 'block';
        percentageInput.required = true;
        priceInput.required = false;
    } else if (discountType === 'fixed') {
        priceField.style.display = 'block';
        priceInput.required = true;
        percentageInput.required = false;
    } else {
        percentageInput.required = false;
        priceInput.required = false;
    }
    
    calculateDiscountPrice();
}

// Calculate and display the final discount price
function calculateDiscountPrice() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discountType = document.getElementById('discount_type').value;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    const discountPrice = parseFloat(document.getElementById('discount_price').value) || 0;
    const calculatedPriceElement = document.getElementById('calculated_price');
    
    let finalPrice = price;
    let discountAmount = 0;
    
    if (discountType === 'percentage' && discountPercentage > 0) {
        discountAmount = (price * discountPercentage) / 100;
        finalPrice = price - discountAmount;
    } else if (discountType === 'fixed' && discountPrice > 0) {
        finalPrice = discountPrice;
        discountAmount = price - discountPrice;
    }
    
    if (discountType && (discountPercentage > 0 || discountPrice > 0)) {
        const savingsPercentage = ((discountAmount / price) * 100).toFixed(1);
        calculatedPriceElement.innerHTML = `
            <strong>₹${finalPrice.toFixed(2)}</strong>
            <span class="text-success ms-2">
                <small>(Save ₹${discountAmount.toFixed(2)} - ${savingsPercentage}%)</small>
            </span>
        `;
    } else {
        calculatedPriceElement.innerHTML = '<span class="text-muted">No discount applied</span>';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set up event listeners
    document.getElementById('price').addEventListener('input', calculateDiscountPrice);
    document.getElementById('discount_percentage').addEventListener('input', calculateDiscountPrice);
    document.getElementById('discount_price').addEventListener('input', calculateDiscountPrice);
    
    // Initialize display based on old values (for validation errors)
    toggleDiscountFields();
});
</script>

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                  id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price (₹) *</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="discount_type" class="form-label">Discount Type</label>
                            <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" onchange="toggleDiscountFields()">
                                <option value="" {{ old('discount_type') == '' ? 'selected' : '' }}>No Discount</option>
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div id="discount_percentage_field" style="display: none;">
                                <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                                <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                       id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage') }}" 
                                       step="0.01" min="0" max="100" placeholder="e.g., 10">
                                @error('discount_percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter percentage (0-100)</small>
                            </div>
                            
                            <div id="discount_price_field" style="display: none;">
                                <label for="discount_price" class="form-label">Discount Price (₹)</label>
                                <input type="number" class="form-control @error('discount_price') is-invalid @enderror" 
                                       id="discount_price" name="discount_price" value="{{ old('discount_price') }}" 
                                       step="0.01" min="0" placeholder="e.g., 450">
                                @error('discount_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter discounted price</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Calculated Discount Price</label>
                            <div class="form-control-plaintext bg-light p-2 rounded" id="calculated_price">
                                <span class="text-muted">No discount applied</span>
                            </div>
                            <small class="text-muted">This will be the final selling price</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tax_percentage" class="form-label">GST Tax (%) *</label>
                            <input type="number" class="form-control @error('tax_percentage') is-invalid @enderror" 
                                   id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage', 0) }}" 
                                   step="0.01" min="0" max="100" required>
                            @error('tax_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter GST percentage (will split into CGST/SGST)</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" name="stock" value="{{ old('stock') }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" name="sku" value="{{ old('sku') }}">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" name="weight" value="{{ old('weight') }}" step="0.01" min="0">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="weight_unit" class="form-label">Weight Unit</label>
                            <select class="form-select @error('weight_unit') is-invalid @enderror" id="weight_unit" name="weight_unit">
                                <option value="gm" {{ old('weight_unit') == 'gm' ? 'selected' : '' }}>Grams</option>
                                <option value="kg" {{ old('weight_unit') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                <option value="ml" {{ old('weight_unit') == 'ml' ? 'selected' : '' }}>Milliliters</option>
                                <option value="ltr" {{ old('weight_unit') == 'ltr' ? 'selected' : '' }}>Liters</option>
                                <option value="box" {{ old('weight_unit') == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pack" {{ old('weight_unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                            </select>
                            @error('weight_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                               id="featured_image" name="featured_image" accept="image/*">
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">Additional Images</label>
                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                               id="images" name="images[]" accept="image/*" multiple>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">You can select multiple images</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check me-3">
                                <!-- Hidden field to ensure 0 is sent when checkbox is unchecked -->
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="form-check">
                                <!-- Hidden field to ensure 0 is sent when checkbox is unchecked -->
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" 
                                       value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>SEO Settings</h6>
                    
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                               id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                  id="meta_description" name="meta_description" rows="2" maxlength="500">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                               id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}" 
                               placeholder="keyword1, keyword2, keyword3">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Product Guidelines</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Use clear, descriptive names</li>
                    <li><i class="fas fa-check text-success"></i> Write detailed descriptions</li>
                    <li><i class="fas fa-check text-success"></i> Upload high-quality images</li>
                    <li><i class="fas fa-check text-success"></i> Set accurate pricing</li>
                    <li><i class="fas fa-check text-success"></i> Maintain proper stock levels</li>
                    <li><i class="fas fa-check text-success"></i> Fill SEO meta tags</li>
                </ul>
                
                <hr>
                
                <h6>Image Requirements</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-info text-info"></i> Recommended: 800x800px</li>
                    <li><i class="fas fa-info text-info"></i> Max size: 2MB per image</li>
                    <li><i class="fas fa-info text-info"></i> Formats: JPG, PNG, WebP</li>
                    <li><i class="fas fa-info text-info"></i> Use clear, bright lighting</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
