@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Settings</h1>
        </div>

        <div class="row">
            <div class="col-md-3">
                <!-- Settings Navigation -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-company-tab" data-bs-toggle="pill"
                                href="#v-pills-company" role="tab">
                                <i class="fas fa-building"></i> Company Settings
                            </a>
                            <a class="nav-link" id="v-pills-theme-tab" data-bs-toggle="pill" href="#v-pills-theme"
                                role="tab">
                                <i class="fas fa-palette"></i> Theme & Appearance
                            </a>
                            <a class="nav-link" id="v-pills-delivery-tab" data-bs-toggle="pill" href="#v-pills-delivery"
                                role="tab">
                                <i class="fas fa-truck"></i> Delivery Settings
                            </a>
                            <a class="nav-link" id="v-pills-pagination-tab" data-bs-toggle="pill" href="#v-pills-pagination"
                                role="tab">
                                <i class="fas fa-list"></i> Pagination Settings
                            </a>
                            <a class="nav-link" id="v-pills-bill-format-tab" data-bs-toggle="pill" href="#v-pills-bill-format"
                                role="tab">
                                <i class="fas fa-print"></i> Bill Format Settings
                            </a>
                            <a class="nav-link" id="v-pills-invoice-numbering-tab" data-bs-toggle="pill" href="#v-pills-invoice-numbering"
                                role="tab">
                                <i class="fas fa-hashtag"></i> Invoice Numbering
                            </a>
                            <a class="nav-link" id="v-pills-notifications-tab" data-bs-toggle="pill"
                                href="#v-pills-notifications" role="tab">
                                <i class="fas fa-bell"></i> Notifications
                            </a>
                            <a class="nav-link" id="v-pills-animations-tab" data-bs-toggle="pill" href="#v-pills-animations"
                                role="tab">
                                <i class="fas fa-magic"></i> Frontend Animations
                            </a>
                            <a class="nav-link" id="v-pills-whatsapp-tab" data-bs-toggle="pill" href="#v-pills-whatsapp"
                                role="tab">
                                <i class="fab fa-whatsapp"></i> WhatsApp Templates
                            </a>
                            <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile"
                                role="tab">
                                <i class="fas fa-user"></i> Profile Settings
                            </a>
                            <a class="nav-link" id="v-pills-password-tab" data-bs-toggle="pill" href="#v-pills-password"
                                role="tab">
                                <i class="fas fa-lock"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Company Settings -->
                    <div class="tab-pane fade show active" id="v-pills-company" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Company Information</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.company') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <!-- Hidden field to fix custom_tax_enabled validation error -->
                                    <input type="hidden" name="custom_tax_enabled" value="0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name">Company Name *</label>
                                                <input type="text" class="form-control" id="company_name"
                                                    name="company_name"
                                                    value="{{ $company ? $company->name : 'Herbal Bliss' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_email">Company Email *</label>
                                                <input type="email" class="form-control" id="company_email"
                                                    name="company_email"
                                                    value="{{ $company ? $company->email : 'admin@herbalbliss.com' }}"
                                                    required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_phone">Company Phone</label>
                                                <input type="text" class="form-control" id="company_phone"
                                                    name="company_phone" value="{{ $company ? $company->phone : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_address">Company Address</label>
                                                <input type="text" class="form-control" id="company_address"
                                                    name="company_address"
                                                    value="{{ $company ? $company->address : '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_city">City</label>
                                                <input type="text" class="form-control" id="company_city"
                                                    name="company_city" value="{{ $company ? $company->city : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_state">State</label>
                                                <input type="text" class="form-control" id="company_state"
                                                    name="company_state" value="{{ $company ? $company->state : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_postal_code">Postal Code</label>
                                                <input type="text" class="form-control" id="company_postal_code"
                                                    name="company_postal_code"
                                                    value="{{ $company ? $company->postal_code : '' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- GST Number Section - IMPORTANT FOR INVOICES -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-receipt"></i> GST Configuration for Invoices</h6>
                                                <p class="mb-0">Enter your GST number below to display it on all POS receipts, customer invoices, and email receipts automatically.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="gst_number" class="form-label">
                                                    <i class="fas fa-id-card text-primary"></i> 
                                                    <strong>GST Registration Number</strong> 
                                                    <span class="badge bg-secondary">Optional</span>
                                                </label>
                                                <input type="text" 
                                                       class="form-control form-control-lg" 
                                                       id="gst_number"
                                                       name="gst_number" 
                                                       value="{{ $company ? $company->gst_number : '' }}"
                                                       placeholder="Enter 15-digit GST number (e.g., 33BEXPA7899P1ZA)"
                                                       maxlength="15"
                                                       pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9A-Z]{1}Z[0-9A-Z]{1}"
                                                       title="GST format: 2 digits + 5 letters + 4 digits + 1 letter + 1 alphanumeric + Z + 1 alphanumeric"
                                                       style="border: 2px solid #ddd; font-family: monospace;">
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle text-info"></i> 
                                                    Format: 2 digits + 5 letters + 4 digits + 1 letter + 1 alphanumeric + Z + 1 alphanumeric
                                                    <br>
                                                    <i class="fas fa-check text-success"></i> 
                                                    <strong>Example:</strong> 33BEXPA7899P1ZA (Your format is correct!)
                                                    <br>
                                                    <i class="fas fa-magic text-success"></i> 
                                                    <strong>When entered:</strong> GST number will automatically appear on all invoices, receipts, and emails
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title"><i class="fas fa-check-circle text-success"></i> GST Benefits</h6>
                                                    <ul class="list-unstyled small text-left">
                                                        <li>✅ Shows on POS receipts</li>
                                                        <li>✅ Shows on customer invoices</li>
                                                        <li>✅ Shows on email receipts</li>
                                                        <li>✅ Automatic validation</li>
                                                        <li>✅ Professional appearance</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company_logo">Company Logo</label>
                                                <input type="file" class="form-control" id="company_logo"
                                                    name="company_logo" accept="image/*">
                                                @if ($company && $company->logo)
                                                    <small class="text-muted">Current logo:</small>
                                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo"
                                                        class="img-thumbnail mt-2" style="max-height: 100px;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Company Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Settings -->
                    <div class="tab-pane fade" id="v-pills-theme" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Theme & Appearance</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.theme') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="primary_color">Primary Color</label>
                                                <input type="color" class="form-control" id="primary_color"
                                                    name="primary_color" value="#2d5016">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="secondary_color">Secondary Color</label>
                                                <input type="color" class="form-control" id="secondary_color"
                                                    name="secondary_color" value="#6b8e23">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sidebar_color">Sidebar Color</label>
                                                <input type="color" class="form-control" id="sidebar_color"
                                                    name="sidebar_color" value="#2d5016">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="theme_mode">Theme Mode</label>
                                                <select class="form-control" id="theme_mode" name="theme_mode">
                                                    <option value="light" selected>Light</option>
                                                    <option value="dark">Dark</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Theme Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Settings -->
                    <div class="tab-pane fade" id="v-pills-delivery" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-truck"></i> Delivery Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.delivery') }}" method="POST">
                                    @csrf

                                    <!-- Enable/Disable Delivery -->
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Configure your delivery settings</strong><br>
                                        Set up delivery charges, free delivery thresholds, and delivery options for your
                                        customers.
                                    </div>

                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="delivery_enabled"
                                            name="delivery_enabled" value="1" checked
                                            onchange="toggleDeliverySettings()">
                                        <label class="form-check-label" for="delivery_enabled">
                                            <strong>Enable Delivery Service</strong>
                                            <br><small class="text-muted">Turn on/off delivery service for your
                                                store</small>
                                        </label>
                                    </div>

                                    <div id="delivery-settings-content" style="display: block;">
                                        <!-- Delivery Charge -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="delivery_charge">
                                                        <i class="fas fa-rupee-sign"></i> Delivery Charge (₹) *
                                                    </label>
                                                    <input type="number" class="form-control" id="delivery_charge"
                                                        name="delivery_charge" value="50.00" step="0.01"
                                                        min="0" max="9999.99" required>
                                                    <small class="text-muted">Amount to charge for delivery</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="delivery_time_estimate">
                                                        <i class="fas fa-clock"></i> Delivery Time Estimate
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        id="delivery_time_estimate" name="delivery_time_estimate"
                                                        value="3-5 business days" placeholder="e.g., 2-3 business days">
                                                    <small class="text-muted">Expected delivery time to show
                                                        customers</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Free Delivery -->
                                        <div class="card bg-light mb-3">
                                            <div class="card-body">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="free_delivery_enabled" name="free_delivery_enabled"
                                                        value="1" checked onchange="toggleFreeDelivery()">
                                                    <label class="form-check-label" for="free_delivery_enabled">
                                                        <strong><i class="fas fa-gift text-success"></i> Enable Free
                                                            Delivery</strong>
                                                        <br><small class="text-muted">Offer free delivery for orders above
                                                            a certain amount</small>
                                                    </label>
                                                </div>

                                                <div id="free-delivery-content" style="display: block;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="free_delivery_threshold">
                                                                    <i class="fas fa-tag"></i> Free Delivery Threshold (₹)
                                                                    *
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    id="free_delivery_threshold"
                                                                    name="free_delivery_threshold" value="500.00"
                                                                    step="0.01" min="0.01" max="999999.99">
                                                                <small class="text-muted">Minimum order amount for free
                                                                    delivery</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="delivery_max_amount">
                                                                    <i class="fas fa-arrow-up"></i> Maximum Order for Paid
                                                                    Delivery (₹)
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    id="delivery_max_amount" name="delivery_max_amount"
                                                                    value="" step="0.01" min="0.01"
                                                                    max="999999.99"
                                                                    placeholder="Leave empty for no limit">
                                                                <small class="text-muted">Orders above this amount get free
                                                                    delivery (optional)</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delivery Description -->
                                        <div class="form-group mb-3">
                                            <label for="delivery_description">
                                                <i class="fas fa-info-circle"></i> Delivery Description
                                            </label>
                                            <textarea class="form-control" id="delivery_description" name="delivery_description" rows="3" maxlength="500"
                                                placeholder="Additional delivery information for customers..."></textarea>
                                            <small class="text-muted">Optional message to display to customers about your
                                                delivery service</small>
                                        </div>

                                        <!-- Minimum Order Amount Validation -->
                                        <div class="card bg-warning bg-opacity-10 border-warning mb-3">
                                            <div class="card-body">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="min_order_validation_enabled" name="min_order_validation_enabled"
                                                        value="1" {{ (($deliverySettings['min_order_validation_enabled'] ?? false) ? 'checked' : '') }} onchange="toggleMinOrderValidation()">
                                                    <label class="form-check-label" for="min_order_validation_enabled">
                                                        <strong><i class="fas fa-exclamation-triangle text-warning"></i> Enable Minimum Order Amount Validation</strong>
                                                        <br><small class="text-muted">Require customers to place orders above a minimum amount for online orders</small>
                                                    </label>
                                                </div>

                                                <div id="min-order-validation-content" style="display: {{ (($deliverySettings['min_order_validation_enabled'] ?? false) ? 'block' : 'none') }};">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="min_order_amount">
                                                                    <i class="fas fa-rupee-sign"></i> Minimum Order Amount (₹) *
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                    id="min_order_amount"
                                                                    name="min_order_amount" value="{{ $deliverySettings['min_order_amount'] ?? '1000.00' }}"
                                                                    step="0.01" min="1" max="999999.99" placeholder="1000.00">
                                                                <small class="text-muted">Minimum amount required for online orders</small>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="min_order_message">
                                                                    <i class="fas fa-comment"></i> Validation Message
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="min_order_message" name="min_order_message"
                                                                    value="{{ $deliverySettings['min_order_message'] ?? 'Minimum order amount is ₹1000 for online orders.' }}"
                                                                    placeholder="Minimum order amount is ₹1000 for online orders.">
                                                                <small class="text-muted">Message to show when order amount is below minimum</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fas fa-info-circle"></i>
                                                        <strong>Note:</strong> This validation only applies to online orders. POS sales and admin-created orders are not affected.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Delivery Settings
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-lightbulb"></i>
                                            Changes will apply to new orders immediately
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Delivery Preview -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-eye"></i> Delivery Settings Preview
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-calculator"></i> Example Calculations:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Order ₹300:</strong>
                                                <span class="text-danger">₹50 delivery charge</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Order ₹500:</strong>
                                                <span class="text-success">FREE delivery</span>
                                            </div>
                                            @if (false)
                                                <div>
                                                    <strong>Order ₹1000+:</strong>
                                                    <span class="text-success">FREE delivery</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-info-circle"></i> Current Settings:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-1">
                                                <strong>Status:</strong>
                                                <span class="badge bg-success">
                                                    Enabled
                                                </span>
                                            </div>
                                            <div class="mb-1">
                                                <strong>Delivery Charge:</strong> ₹50
                                            </div>
                                            <div class="mb-1">
                                                <strong>Free Delivery:</strong>
                                                Above ₹500
                                            </div>
                                            <div class="mb-1">
                                                <strong>Delivery Time:</strong> 3-5 business days
                                            </div>
                                            @if($deliverySettings['min_order_validation_enabled'] ?? false)
                                                <div class="mb-1">
                                                    <strong>Min Order:</strong> ₹{{ number_format($deliverySettings['min_order_amount'] ?? 1000, 0) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Settings -->
                    <div class="tab-pane fade" id="v-pills-pagination" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-list"></i> Pagination Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.pagination') }}" method="POST">
                                    @csrf

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Configure pagination settings</strong><br>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-storefront text-primary"></i> Frontend Settings:</strong>
                                                <small class="d-block">Controls pagination on your customer-facing e-commerce website (product listings, category pages, search results that customers see)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-cog text-success"></i> Admin Panel Settings:</strong>
                                                <small class="d-block">Controls pagination in the admin dashboard (where you manage products, orders, customers, etc.)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Frontend Settings (Customer Website) -->
                                    <div class="card bg-primary bg-opacity-10 border-primary mb-4">
                                        <div class="card-header bg-primary bg-opacity-25">
                                            <h6 class="mb-0"><i class="fas fa-storefront"></i> Frontend Settings (Customer Website)</h6>
                                            <small class="text-muted">These settings control how customers see products on your e-commerce website</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="frontend_pagination_enabled" name="frontend_pagination_enabled"
                                                    value="1" {{ ($paginationSettings['frontend_pagination_enabled'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="frontend_pagination_enabled">
                                                    <strong>Enable Customer Website Pagination</strong>
                                                    <br><small class="text-muted">Enable pagination on product listings, category pages, and search results that customers see. If disabled, all products will load at once.</small>
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="frontend_records_per_page">
                                                            <i class="fas fa-list-ol"></i> Products Per Page *
                                                        </label>
                                                        <select class="form-control" id="frontend_records_per_page"
                                                            name="frontend_records_per_page" required>
                                                            <option value="6" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 6 ? 'selected' : '' }}>6 Products</option>
                                                            <option value="9" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 9 ? 'selected' : '' }}>9 Products</option>
                                                            <option value="12" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 12 ? 'selected' : '' }}>12 Products</option>
                                                            <option value="15" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 15 ? 'selected' : '' }}>15 Products</option>
                                                            <option value="18" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 18 ? 'selected' : '' }}>18 Products</option>
                                                            <option value="24" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 24 ? 'selected' : '' }}>24 Products</option>
                                                            <option value="30" {{ ($paginationSettings['frontend_records_per_page'] ?? 12) == 30 ? 'selected' : '' }}>30 Products</option>
                                                        </select>
                                                        <small class="text-muted">Number of products customers see per page on your website</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="frontend_default_sort_order">
                                                            <i class="fas fa-sort"></i> Default Sort Order *
                                                        </label>
                                                        <select class="form-control" id="frontend_default_sort_order"
                                                            name="frontend_default_sort_order" required>
                                                            <option value="desc" {{ ($paginationSettings['frontend_default_sort_order'] ?? 'desc') == 'desc' ? 'selected' : '' }}>Newest First</option>
                                                            <option value="asc" {{ ($paginationSettings['frontend_default_sort_order'] ?? 'desc') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                                        </select>
                                                        <small class="text-muted">Default sorting for customer product listings</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="frontend_load_more_enabled" name="frontend_load_more_enabled"
                                                    value="1" {{ ($paginationSettings['frontend_load_more_enabled'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="frontend_load_more_enabled">
                                                    <strong>Enable "Load More" Button</strong>
                                                    <br><small class="text-muted">Show "Load More" button instead of page numbers for better mobile customer experience</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Admin Panel Settings (Dashboard) -->
                                    <div class="card bg-success bg-opacity-10 border-success mb-4">
                                        <div class="card-header bg-success bg-opacity-25">
                                            <h6 class="mb-0"><i class="fas fa-cog"></i> Admin Panel Settings (Dashboard)</h6>
                                            <small class="text-muted">These settings control how you see data in the admin dashboard</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="admin_pagination_enabled" name="admin_pagination_enabled"
                                                    value="1" {{ ($paginationSettings['admin_pagination_enabled'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="admin_pagination_enabled">
                                                    <strong>Enable Admin Dashboard Pagination</strong>
                                                    <br><small class="text-muted">Enable pagination in admin listings (products, orders, customers). If disabled, all records will load at once in admin.</small>
                                                </label>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_records_per_page">
                                                            <i class="fas fa-table"></i> Records Per Page *
                                                        </label>
                                                        <select class="form-control" id="admin_records_per_page"
                                                            name="admin_records_per_page" required>
                                                            <option value="10" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 10 ? 'selected' : '' }}>10 Records</option>
                                                            <option value="15" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 15 ? 'selected' : '' }}>15 Records</option>
                                                            <option value="20" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 20 ? 'selected' : '' }}>20 Records</option>
                                                            <option value="25" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 25 ? 'selected' : '' }}>25 Records</option>
                                                            <option value="30" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 30 ? 'selected' : '' }}>30 Records</option>
                                                            <option value="50" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 50 ? 'selected' : '' }}>50 Records</option>
                                                            <option value="100" {{ ($paginationSettings['admin_records_per_page'] ?? 20) == 100 ? 'selected' : '' }}>100 Records</option>
                                                        </select>
                                                        <small class="text-muted">Number of records you see per page in admin dashboard</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="admin_default_sort_order">
                                                            <i class="fas fa-sort"></i> Default Sort Order *
                                                        </label>
                                                        <select class="form-control" id="admin_default_sort_order"
                                                            name="admin_default_sort_order" required>
                                                            <option value="desc" {{ ($paginationSettings['admin_default_sort_order'] ?? 'desc') == 'desc' ? 'selected' : '' }}>Newest First</option>
                                                            <option value="asc" {{ ($paginationSettings['admin_default_sort_order'] ?? 'desc') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                                        </select>
                                                        <small class="text-muted">Default sorting for admin dashboard listings</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="admin_show_per_page_selector" name="admin_show_per_page_selector"
                                                    value="1" {{ ($paginationSettings['admin_show_per_page_selector'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="admin_show_per_page_selector">
                                                    <strong>Show Per-Page Selector in Admin</strong>
                                                    <br><small class="text-muted">Allow changing records per page in admin dashboard listings</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Pagination Settings
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-lightbulb"></i>
                                            Changes will apply immediately
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Pagination Preview -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-eye"></i> Current Pagination Settings
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-storefront"></i> Customer Website (Frontend):</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Pagination Status:</strong>
                                                @if($paginationSettings['frontend_pagination_enabled'] ?? true)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Products per page:</strong> {{ $paginationSettings['frontend_records_per_page'] ?? 12 }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Sort order:</strong> {{ ($paginationSettings['frontend_default_sort_order'] ?? 'desc') == 'desc' ? 'Newest First' : 'Oldest First' }}
                                            </div>
                                            <div>
                                                <strong>Load More Button:</strong>
                                                @if($paginationSettings['frontend_load_more_enabled'] ?? false)
                                                    <span class="badge bg-info">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Page Numbers</span>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted"><i class="fas fa-info-circle"></i> Controls what customers see on your website</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-cog"></i> Admin Dashboard (Backend):</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Pagination Status:</strong>
                                                @if($paginationSettings['admin_pagination_enabled'] ?? true)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Records per page:</strong> {{ $paginationSettings['admin_records_per_page'] ?? 20 }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Sort order:</strong> {{ ($paginationSettings['admin_default_sort_order'] ?? 'desc') == 'desc' ? 'Newest First' : 'Oldest First' }}
                                            </div>
                                            <div>
                                                <strong>Per-page selector:</strong>
                                                @if($paginationSettings['admin_show_per_page_selector'] ?? true)
                                                    <span class="badge bg-info">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted"><i class="fas fa-info-circle"></i> Controls what you see in admin dashboard</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bill Format Settings -->
                    <div class="tab-pane fade" id="v-pills-bill-format" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-print"></i> Bill Format Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.bill-format') }}" method="POST">
                                    @csrf

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Configure bill format preferences</strong><br>
                                        Choose between thermal printer format (80mm width) and A4 sheet PDF format for both POS sales and online orders.
                                        At least one format must be enabled.
                                    </div>

                                    <!-- Format Options -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-header">
                                                    <h6 class="mb-0"><i class="fas fa-receipt"></i> Thermal Printer Format</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="thermal_printer_enabled"
                                                            name="thermal_printer_enabled" value="1" 
                                                            {{ ($billFormatSettings['thermal_printer_enabled'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="thermal_printer_enabled">
                                                            <strong>Enable Thermal Printer Format</strong>
                                                            <br><small class="text-muted">80mm width, optimized for thermal printers</small>
                                                        </label>
                                                    </div>

                                                    <div class="row" id="thermal-printer-settings" style="display: {{ ($billFormatSettings['thermal_printer_enabled'] ?? false) ? 'block' : 'none' }};">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="thermal_printer_width">
                                                                    <i class="fas fa-arrows-alt-h"></i> Printer Width (mm)
                                                                </label>
                                                                <select class="form-control" id="thermal_printer_width" name="thermal_printer_width">
                                                                    <option value="58" {{ ($billFormatSettings['thermal_printer_width'] ?? 80) == 58 ? 'selected' : '' }}>58mm</option>
                                                                    <option value="80" {{ ($billFormatSettings['thermal_printer_width'] ?? 80) == 80 ? 'selected' : '' }}>80mm (Standard)</option>
                                                                    <option value="112" {{ ($billFormatSettings['thermal_printer_width'] ?? 80) == 112 ? 'selected' : '' }}>112mm</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check form-switch mt-4">
                                                                <input class="form-check-input" type="checkbox" id="thermal_printer_auto_cut"
                                                                    name="thermal_printer_auto_cut" value="1" 
                                                                    {{ ($billFormatSettings['thermal_printer_auto_cut'] ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="thermal_printer_auto_cut">
                                                                    <strong>Auto Cut</strong>
                                                                    <br><small class="text-muted">Automatically cut paper after printing</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-header">
                                                    <h6 class="mb-0"><i class="fas fa-file-pdf"></i> A4 Sheet PDF Format</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="a4_sheet_enabled"
                                                            name="a4_sheet_enabled" value="1" 
                                                            {{ ($billFormatSettings['a4_sheet_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="a4_sheet_enabled">
                                                            <strong>Enable A4 Sheet PDF Format</strong>
                                                            <br><small class="text-muted">Professional PDF layout for A4 paper</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-group" id="a4-sheet-settings" style="display: {{ ($billFormatSettings['a4_sheet_enabled'] ?? true) ? 'block' : 'none' }};">
                                                        <label for="a4_sheet_orientation">
                                                            <i class="fas fa-orientation"></i> Paper Orientation
                                                        </label>
                                                        <select class="form-control" id="a4_sheet_orientation" name="a4_sheet_orientation">
                                                            <option value="portrait" {{ ($billFormatSettings['a4_sheet_orientation'] ?? 'portrait') == 'portrait' ? 'selected' : '' }}>Portrait</option>
                                                            <option value="landscape" {{ ($billFormatSettings['a4_sheet_orientation'] ?? 'portrait') == 'landscape' ? 'selected' : '' }}>Landscape</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Default Format Selection -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-cog"></i> Default Format Settings</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="default_bill_format">
                                                            <i class="fas fa-star"></i> Default Bill Format *
                                                        </label>
                                                        <select class="form-control" id="default_bill_format" name="default_bill_format" required>
                                                            <option value="thermal" {{ ($billFormatSettings['default_bill_format'] ?? 'a4_sheet') == 'thermal' ? 'selected' : '' }}>Thermal Printer</option>
                                                            <option value="a4_sheet" {{ ($billFormatSettings['default_bill_format'] ?? 'a4_sheet') == 'a4_sheet' ? 'selected' : '' }}>A4 Sheet PDF</option>
                                                        </select>
                                                        <small class="text-muted">Used when both formats are enabled</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Display Options -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-eye"></i> Display Options</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="bill_logo_enabled"
                                                            name="bill_logo_enabled" value="1" 
                                                            {{ ($billFormatSettings['bill_logo_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="bill_logo_enabled">
                                                            <strong>Show Company Logo</strong>
                                                            <br><small class="text-muted">Display company logo on bills and receipts</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="bill_company_info_enabled"
                                                            name="bill_company_info_enabled" value="1" 
                                                            {{ ($billFormatSettings['bill_company_info_enabled'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="bill_company_info_enabled">
                                                            <strong>Show Company Information</strong>
                                                            <br><small class="text-muted">Display company address, phone, email on bills</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Bill Format Settings
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-lightbulb"></i>
                                            Changes will apply to new bills immediately
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Bill Format Preview -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-eye"></i> Current Bill Format Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-receipt"></i> Thermal Printer:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Status:</strong>
                                                @if($billFormatSettings['thermal_printer_enabled'] ?? false)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Width:</strong> {{ $billFormatSettings['thermal_printer_width'] ?? 80 }}mm
                                            </div>
                                            <div>
                                                <strong>Auto Cut:</strong> 
                                                {{ ($billFormatSettings['thermal_printer_auto_cut'] ?? true) ? 'Yes' : 'No' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-file-pdf"></i> A4 Sheet PDF:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Status:</strong>
                                                @if($billFormatSettings['a4_sheet_enabled'] ?? true)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Orientation:</strong> {{ ucfirst($billFormatSettings['a4_sheet_orientation'] ?? 'portrait') }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Saved Default:</strong> 
                                                <span class="badge bg-primary">
                                                    {{ $billFormatSettings['default_bill_format'] == 'thermal' ? 'Thermal Printer' : 'A4 Sheet PDF' }}
                                                </span>
                                            </div>
                                            {{-- <div>
                                                <strong>Current Selection:</strong> 
                                                <span id="current-default-display" class="badge bg-info">Loading...</span>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Numbering Settings -->
                    <div class="tab-pane fade" id="v-pills-invoice-numbering" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-hashtag"></i> Invoice Numbering Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.invoice-numbering') }}" method="POST">
                                    @csrf

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Configure automatic invoice numbering</strong><br>
                                        Set up custom invoice number formats for both online orders and POS sales. Both formats support prefixes, separators, sequence numbers, and date components.
                                    </div>

                                    <!-- Preview Section -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-eye"></i> Live Preview</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Next Online Order Invoice:</h6>
                                                    <div class="bg-white p-3 border rounded">
                                                        <code id="order-invoice-preview" class="text-success fs-5">
                                                            {{ $invoiceNumberingSettings['order_invoice_prefix'] ?? 'ORD' }}-{{ date('Y') }}-{{ str_pad('1', $invoiceNumberingSettings['order_invoice_digits'] ?? 5, '0', STR_PAD_LEFT) }}
                                                        </code>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Next POS Sale Invoice:</h6>
                                                    <div class="bg-white p-3 border rounded">
                                                        <code id="pos-invoice-preview" class="text-success fs-5">
                                                            {{ $invoiceNumberingSettings['pos_invoice_prefix'] ?? 'POS' }}-{{ date('Y') }}-{{ str_pad('1', $invoiceNumberingSettings['pos_invoice_digits'] ?? 4, '0', STR_PAD_LEFT) }}
                                                        </code>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="updatePreviews()">
                                                    <i class="fas fa-sync"></i> Update Preview
                                                </button>
                                                <button type="button" class="btn btn-outline-info btn-sm" onclick="fetchServerPreviews()">
                                                    <i class="fas fa-server"></i> Get Actual Next Numbers
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Online Order Invoice Settings -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Online Order Invoice Format</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="order_invoice_prefix">
                                                            <i class="fas fa-tag"></i> Prefix *
                                                        </label>
                                                        <input type="text" class="form-control" id="order_invoice_prefix"
                                                            name="order_invoice_prefix" 
                                                            value="{{ $invoiceNumberingSettings['order_invoice_prefix'] ?? 'ORD' }}"
                                                            placeholder="ORD" maxlength="10" required
                                                            pattern="[A-Z0-9]+" title="Only uppercase letters and numbers allowed"
                                                            onchange="updatePreviews()">
                                                        <small class="text-muted">Prefix for order invoices (e.g., ORD, ORDER)</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="order_invoice_separator">
                                                            <i class="fas fa-minus"></i> Separator *
                                                        </label>
                                                        <select class="form-control" id="order_invoice_separator" name="order_invoice_separator" onchange="updatePreviews()">
                                                            <option value="-" {{ ($invoiceNumberingSettings['order_invoice_separator'] ?? '-') == '-' ? 'selected' : '' }}>Dash (-)</option>
                                                            <option value="_" {{ ($invoiceNumberingSettings['order_invoice_separator'] ?? '-') == '_' ? 'selected' : '' }}>Underscore (_)</option>
                                                            <option value="/" {{ ($invoiceNumberingSettings['order_invoice_separator'] ?? '-') == '/' ? 'selected' : '' }}>Slash (/)</option>
                                                            <option value="" {{ ($invoiceNumberingSettings['order_invoice_separator'] ?? '-') == '' ? 'selected' : '' }}>None</option>
                                                        </select>
                                                        <small class="text-muted">Character between components</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="order_invoice_digits">
                                                            <i class="fas fa-sort-numeric-up"></i> Number Digits *
                                                        </label>
                                                        <select class="form-control" id="order_invoice_digits" name="order_invoice_digits" onchange="updatePreviews()">
                                                            <option value="3" {{ ($invoiceNumberingSettings['order_invoice_digits'] ?? 5) == 3 ? 'selected' : '' }}>3 digits (001)</option>
                                                            <option value="4" {{ ($invoiceNumberingSettings['order_invoice_digits'] ?? 5) == 4 ? 'selected' : '' }}>4 digits (0001)</option>
                                                            <option value="5" {{ ($invoiceNumberingSettings['order_invoice_digits'] ?? 5) == 5 ? 'selected' : '' }}>5 digits (00001)</option>
                                                            <option value="6" {{ ($invoiceNumberingSettings['order_invoice_digits'] ?? 5) == 6 ? 'selected' : '' }}>6 digits (000001)</option>
                                                            <option value="7" {{ ($invoiceNumberingSettings['order_invoice_digits'] ?? 5) == 7 ? 'selected' : '' }}>7 digits (0000001)</option>
                                                        </select>
                                                        <small class="text-muted">Number of digits for sequence (with zero padding)</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="order_invoice_include_year"
                                                            name="order_invoice_include_year" value="1" 
                                                            {{ ($invoiceNumberingSettings['order_invoice_include_year'] ?? true) ? 'checked' : '' }}
                                                            onchange="updatePreviews()">
                                                        <label class="form-check-label" for="order_invoice_include_year">
                                                            <strong>Include Year</strong>
                                                            <br><small class="text-muted">Add current year to invoice number</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="order_invoice_include_month"
                                                            name="order_invoice_include_month" value="1" 
                                                            {{ ($invoiceNumberingSettings['order_invoice_include_month'] ?? false) ? 'checked' : '' }}
                                                            onchange="updatePreviews()">
                                                        <label class="form-check-label" for="order_invoice_include_month">
                                                            <strong>Include Month</strong>
                                                            <br><small class="text-muted">Add current month to invoice number</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="order_invoice_reset_yearly"
                                                            name="order_invoice_reset_yearly" value="1" 
                                                            {{ ($invoiceNumberingSettings['order_invoice_reset_yearly'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="order_invoice_reset_yearly">
                                                            <strong>Reset Yearly</strong>
                                                            <br><small class="text-muted">Reset sequence to 1 every year</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="order_invoice_reset_monthly"
                                                            name="order_invoice_reset_monthly" value="1" 
                                                            {{ ($invoiceNumberingSettings['order_invoice_reset_monthly'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="order_invoice_reset_monthly">
                                                            <strong>Reset Monthly</strong>
                                                            <br><small class="text-muted">Reset sequence to 1 every month</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- POS Sale Invoice Settings -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-cash-register"></i> POS Sale Invoice Format</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="pos_invoice_prefix">
                                                            <i class="fas fa-tag"></i> Prefix *
                                                        </label>
                                                        <input type="text" class="form-control" id="pos_invoice_prefix"
                                                            name="pos_invoice_prefix" 
                                                            value="{{ $invoiceNumberingSettings['pos_invoice_prefix'] ?? 'POS' }}"
                                                            placeholder="POS" maxlength="10" required
                                                            pattern="[A-Z0-9]+" title="Only uppercase letters and numbers allowed"
                                                            onchange="updatePreviews()">
                                                        <small class="text-muted">Prefix for POS invoices (e.g., POS, SALE)</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="pos_invoice_separator">
                                                            <i class="fas fa-minus"></i> Separator *
                                                        </label>
                                                        <select class="form-control" id="pos_invoice_separator" name="pos_invoice_separator" onchange="updatePreviews()">
                                                            <option value="-" {{ ($invoiceNumberingSettings['pos_invoice_separator'] ?? '-') == '-' ? 'selected' : '' }}>Dash (-)</option>
                                                            <option value="_" {{ ($invoiceNumberingSettings['pos_invoice_separator'] ?? '-') == '_' ? 'selected' : '' }}>Underscore (_)</option>
                                                            <option value="/" {{ ($invoiceNumberingSettings['pos_invoice_separator'] ?? '-') == '/' ? 'selected' : '' }}>Slash (/)</option>
                                                            <option value="" {{ ($invoiceNumberingSettings['pos_invoice_separator'] ?? '-') == '' ? 'selected' : '' }}>None</option>
                                                        </select>
                                                        <small class="text-muted">Character between components</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="pos_invoice_digits">
                                                            <i class="fas fa-sort-numeric-up"></i> Number Digits *
                                                        </label>
                                                        <select class="form-control" id="pos_invoice_digits" name="pos_invoice_digits" onchange="updatePreviews()">
                                                            <option value="3" {{ ($invoiceNumberingSettings['pos_invoice_digits'] ?? 4) == 3 ? 'selected' : '' }}>3 digits (001)</option>
                                                            <option value="4" {{ ($invoiceNumberingSettings['pos_invoice_digits'] ?? 4) == 4 ? 'selected' : '' }}>4 digits (0001)</option>
                                                            <option value="5" {{ ($invoiceNumberingSettings['pos_invoice_digits'] ?? 4) == 5 ? 'selected' : '' }}>5 digits (00001)</option>
                                                            <option value="6" {{ ($invoiceNumberingSettings['pos_invoice_digits'] ?? 4) == 6 ? 'selected' : '' }}>6 digits (000001)</option>
                                                            <option value="7" {{ ($invoiceNumberingSettings['pos_invoice_digits'] ?? 4) == 7 ? 'selected' : '' }}>7 digits (0000001)</option>
                                                        </select>
                                                        <small class="text-muted">Number of digits for sequence (with zero padding)</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="pos_invoice_include_year"
                                                            name="pos_invoice_include_year" value="1" 
                                                            {{ ($invoiceNumberingSettings['pos_invoice_include_year'] ?? true) ? 'checked' : '' }}
                                                            onchange="updatePreviews()">
                                                        <label class="form-check-label" for="pos_invoice_include_year">
                                                            <strong>Include Year</strong>
                                                            <br><small class="text-muted">Add current year to invoice number</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="pos_invoice_include_month"
                                                            name="pos_invoice_include_month" value="1" 
                                                            {{ ($invoiceNumberingSettings['pos_invoice_include_month'] ?? false) ? 'checked' : '' }}
                                                            onchange="updatePreviews()">
                                                        <label class="form-check-label" for="pos_invoice_include_month">
                                                            <strong>Include Month</strong>
                                                            <br><small class="text-muted">Add current month to invoice number</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="pos_invoice_reset_yearly"
                                                            name="pos_invoice_reset_yearly" value="1" 
                                                            {{ ($invoiceNumberingSettings['pos_invoice_reset_yearly'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="pos_invoice_reset_yearly">
                                                            <strong>Reset Yearly</strong>
                                                            <br><small class="text-muted">Reset sequence to 1 every year</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="pos_invoice_reset_monthly"
                                                            name="pos_invoice_reset_monthly" value="1" 
                                                            {{ ($invoiceNumberingSettings['pos_invoice_reset_monthly'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="pos_invoice_reset_monthly">
                                                            <strong>Reset Monthly</strong>
                                                            <br><small class="text-muted">Reset sequence to 1 every month</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Advanced Options -->
                                    <div class="card bg-warning bg-opacity-10 border-warning mb-4">
                                        <div class="card-header bg-warning bg-opacity-25">
                                            <h6 class="mb-0"><i class="fas fa-tools"></i> Advanced Options</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-warning mb-3">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Caution:</strong> These actions will affect existing invoice sequences. Use with care.
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-redo"></i> Reset Sequences</h6>
                                                    <p class="text-muted small">Reset invoice sequences back to 1. This is useful at the beginning of a new year or when reorganizing your numbering system.</p>
                                                    
                                                    <button type="button" class="btn btn-outline-warning btn-sm mb-2" onclick="resetSequences('order')">
                                                        <i class="fas fa-redo"></i> Reset Order Sequences
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm mb-2" onclick="resetSequences('pos')">
                                                        <i class="fas fa-redo"></i> Reset POS Sequences
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm mb-2" onclick="resetSequences('')">
                                                        <i class="fas fa-redo"></i> Reset All Sequences
                                                    </button>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-info-circle"></i> Current Status</h6>
                                                    <div id="sequence-status" class="bg-light p-3 rounded">
                                                        <div class="text-muted">
                                                            <i class="fas fa-spinner fa-spin"></i> Loading current sequence status...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Invoice Numbering Settings
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-lightbulb"></i>
                                            Changes apply to new orders immediately
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Examples Card -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-lightbulb"></i> Format Examples
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-shopping-cart"></i> Online Order Examples:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2"><code>ORD-2025-00001</code> <small class="text-muted">(with year)</small></div>
                                            <div class="mb-2"><code>ORDER_2025_01_0001</code> <small class="text-muted">(with year & month)</small></div>
                                            <div class="mb-2"><code>INV00001</code> <small class="text-muted">(no separator, no year)</small></div>
                                            <div class="mb-2"><code>2025/ORD/001</code> <small class="text-muted">(custom format)</small></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-cash-register"></i> POS Sale Examples:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2"><code>POS-2025-0001</code> <small class="text-muted">(with year)</small></div>
                                            <div class="mb-2"><code>SALE_2025_01_001</code> <small class="text-muted">(with year & month)</small></div>
                                            <div class="mb-2"><code>R0001</code> <small class="text-muted">(no separator, no year)</small></div>
                                            <div class="mb-2"><code>2025/POS/001</code> <small class="text-muted">(custom format)</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Notification Preferences</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.notifications') }}" method="POST">
                                    @csrf
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="email_notifications"
                                            name="email_notifications" value="1" {{ ($notificationSettings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">
                                            <strong>Email Notifications</strong>
                                            <br><small class="text-muted">Receive email notifications for orders and system
                                                events</small>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notifications"
                                            name="whatsapp_notifications" value="1" {{ ($notificationSettings['whatsapp_notifications'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notifications">
                                            <strong><i class="fab fa-whatsapp text-success"></i> WhatsApp
                                                Notifications</strong>
                                            <br><small class="text-muted">Send WhatsApp messages to customers when order
                                                status changes (requires WhatsApp configuration in Super Admin)</small>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="sound_notifications"
                                            name="sound_notifications" value="1" {{ ($notificationSettings['sound_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sound_notifications">
                                            <strong>Sound Notifications</strong>
                                            <br><small class="text-muted">Play sound when new orders are placed</small>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="popup_notifications"
                                            name="popup_notifications" value="1" {{ ($notificationSettings['popup_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="popup_notifications">
                                            <strong>Popup Notifications</strong>
                                            <br><small class="text-muted">Show popup notifications for new orders</small>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="order_notifications"
                                            name="order_notifications" value="1" {{ ($notificationSettings['order_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="order_notifications">
                                            <strong>Order Notifications</strong>
                                            <br><small class="text-muted">Get notified when orders are placed or
                                                updated</small>
                                        </label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="low_stock_alert"
                                            name="low_stock_alert" value="1" {{ ($notificationSettings['low_stock_alert'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="low_stock_alert">
                                            <strong>Low Stock Alerts</strong>
                                            <br><small class="text-muted">Send email alerts when products are running
                                                low</small>
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Notification Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Frontend Animations Settings -->
                    <div class="tab-pane fade" id="v-pills-animations" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-magic"></i> Frontend Animation Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Configure frontend animations and visual effects</strong><br>
                                    Control cracker animations, celebrations, and interactive effects on your website.
                                    These settings affect user experience and page performance.
                                </div>

                                <form action="{{ route('admin.settings.animations') }}" method="POST">
                                    @csrf

                                    <!-- Main Animation Toggle -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-toggle-on"></i> Animation Controls</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="frontend_animations_enabled" name="frontend_animations_enabled"
                                                    value="1" {{ (\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true') ? 'checked' : '' }}
                                                    onchange="toggleAnimationSettings()">
                                                <label class="form-check-label" for="frontend_animations_enabled">
                                                    <strong>Enable Frontend Animations</strong>
                                                    <br><small class="text-muted">Master switch for all animations and visual effects</small>
                                                </label>
                                            </div>

                                            <div id="animation-settings-content" style="display: {{ (\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true') ? 'block' : 'none' }};">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="frontend_animation_intensity">
                                                                <i class="fas fa-sliders-h"></i> Animation Intensity *
                                                            </label>
                                                            <select class="form-control" id="frontend_animation_intensity"
                                                                name="frontend_animation_intensity" required>
                                                                <option value="1" {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') == '1' ? 'selected' : '' }}>Low (1x)</option>
                                                                <option value="2" {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') == '2' ? 'selected' : '' }}>Medium (2x)</option>
                                                                <option value="3" {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') == '3' ? 'selected' : '' }}>High (3x)</option>
                                                                <option value="4" {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') == '4' ? 'selected' : '' }}>Very High (4x)</option>
                                                                <option value="5" {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') == '5' ? 'selected' : '' }}>Maximum (5x)</option>
                                                            </select>
                                                            <small class="text-muted">Controls the number and frequency of animations</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="frontend_animation_style">
                                                                <i class="fas fa-palette"></i> Animation Style
                                                            </label>
                                                            <select class="form-control" id="frontend_animation_style"
                                                                name="frontend_animation_style">
                                                                <option value="modern" {{ \App\Models\AppSetting::get('frontend_animation_style', 'crackers') == 'modern' ? 'selected' : '' }}>Modern & Smooth</option>
                                                                <option value="crackers" {{ \App\Models\AppSetting::get('frontend_animation_style', 'crackers') == 'crackers' ? 'selected' : '' }}>Crackers & Fireworks</option>
                                                                <option value="festive" {{ \App\Models\AppSetting::get('frontend_animation_style', 'crackers') == 'festive' ? 'selected' : '' }}>Festive & Colorful</option>
                                                                <option value="minimal" {{ \App\Models\AppSetting::get('frontend_animation_style', 'crackers') == 'minimal' ? 'selected' : '' }}>Minimal & Subtle</option>
                                                            </select>
                                                            <small class="text-muted">Choose the overall animation theme</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Specific Animation Features -->
                                    <div class="card bg-light mb-4" id="specific-animations" style="display: {{ (\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true') ? 'block' : 'none' }};">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-stars"></i> Animation Features</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_celebration_enabled" name="frontend_celebration_enabled"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_celebration_enabled', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_celebration_enabled">
                                                            <strong><i class="fas fa-party-horn text-warning"></i> Celebration Bursts</strong>
                                                            <br><small class="text-muted">Show celebration animations on successful actions</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_fireworks_enabled" name="frontend_fireworks_enabled"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_fireworks_enabled', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_fireworks_enabled">
                                                            <strong><i class="fas fa-rocket text-danger"></i> Fireworks Effects</strong>
                                                            <br><small class="text-muted">Enable fireworks and cracker-like animations</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_hover_effects_enabled" name="frontend_hover_effects_enabled"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_hover_effects_enabled', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_hover_effects_enabled">
                                                            <strong><i class="fas fa-mouse-pointer text-info"></i> Enhanced Hover Effects</strong>
                                                            <br><small class="text-muted">Add interactive hover animations to products</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_loading_animations" name="frontend_loading_animations"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_loading_animations', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_loading_animations">
                                                            <strong><i class="fas fa-spinner text-primary"></i> Loading Animations</strong>
                                                            <br><small class="text-muted">Show animated loading states</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_page_transitions" name="frontend_page_transitions"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_page_transitions', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_page_transitions">
                                                            <strong><i class="fas fa-exchange-alt text-success"></i> Page Transitions</strong>
                                                            <br><small class="text-muted">Smooth transitions between pages and content</small>
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="frontend_welcome_animation" name="frontend_welcome_animation"
                                                            value="1" {{ (\App\Models\AppSetting::get('frontend_welcome_animation', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="frontend_welcome_animation">
                                                            <strong><i class="fas fa-hand-sparkles text-warning"></i> Welcome Animation</strong>
                                                            <br><small class="text-muted">Show welcome animation when page loads</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance Settings -->
                                    <div class="card bg-warning bg-opacity-10 border-warning mb-4" id="performance-settings" style="display: {{ (\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true') ? 'block' : 'none' }};">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-tachometer-alt"></i> Performance Options</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="animation_duration">
                                                            <i class="fas fa-clock"></i> Animation Duration (ms)
                                                        </label>
                                                        <select class="form-control" id="animation_duration" name="animation_duration">
                                                            <option value="300" {{ \App\Models\AppSetting::get('animation_duration', '600') == '300' ? 'selected' : '' }}>Fast (300ms)</option>
                                                            <option value="600" {{ \App\Models\AppSetting::get('animation_duration', '600') == '600' ? 'selected' : '' }}>Normal (600ms)</option>
                                                            <option value="1000" {{ \App\Models\AppSetting::get('animation_duration', '600') == '1000' ? 'selected' : '' }}>Slow (1000ms)</option>
                                                            <option value="1500" {{ \App\Models\AppSetting::get('animation_duration', '600') == '1500' ? 'selected' : '' }}>Very Slow (1500ms)</option>
                                                        </select>
                                                        <small class="text-muted">How long animations should take to complete</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch mt-4">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="reduce_motion_respect" name="reduce_motion_respect"
                                                            value="1" {{ (\App\Models\AppSetting::get('reduce_motion_respect', 'true') === 'true') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="reduce_motion_respect">
                                                            <strong><i class="fas fa-universal-access text-info"></i> Respect Reduced Motion</strong>
                                                            <br><small class="text-muted">Automatically reduce animations for users who prefer reduced motion</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Animation Settings
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-lightbulb"></i>
                                            Changes apply immediately on frontend
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Animation Preview -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-eye"></i> Current Animation Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-cog"></i> Animation Status:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Animations:</strong>
                                                @if(\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true')
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Intensity:</strong> {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') }}x
                                            </div>
                                            <div class="mb-2">
                                                <strong>Style:</strong> {{ ucfirst(\App\Models\AppSetting::get('frontend_animation_style', 'crackers')) }}
                                            </div>
                                            <div>
                                                <strong>Duration:</strong> {{ \App\Models\AppSetting::get('animation_duration', '600') }}ms
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-magic"></i> Active Features:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-1">
                                                <strong>Celebrations:</strong> 
                                                {{ (\App\Models\AppSetting::get('frontend_celebration_enabled', 'true') === 'true') ? '✅' : '❌' }}
                                            </div>
                                            <div class="mb-1">
                                                <strong>Fireworks:</strong> 
                                                {{ (\App\Models\AppSetting::get('frontend_fireworks_enabled', 'true') === 'true') ? '🎆' : '❌' }}
                                            </div>
                                            <div class="mb-1">
                                                <strong>Hover Effects:</strong> 
                                                {{ (\App\Models\AppSetting::get('frontend_hover_effects_enabled', 'true') === 'true') ? '✅' : '❌' }}
                                            </div>
                                            <div>
                                                <strong>Welcome Animation:</strong> 
                                                {{ (\App\Models\AppSetting::get('frontend_welcome_animation', 'true') === 'true') ? '👋' : '❌' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Templates Settings -->
                    <div class="tab-pane fade" id="v-pills-whatsapp" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fab fa-whatsapp text-success"></i> WhatsApp Message Templates
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Customize WhatsApp message templates</strong><br>
                                    These templates will be used when sending order status updates to customers via
                                    WhatsApp.
                                    You can use placeholders like @verbatim{{ customer_name }}, {{ order_number }},
                                    {{ total }}, {{ company_name }}, {{ order_date }}, {{ status }},
                                    {{ payment_status }}@endverbatim.
                                    <br><small class="text-success"><i class="fas fa-magic"></i> <strong>Note:</strong>
                                        Use double braces @{@{variable@}@} directly in your templates for placeholder
                                        functionality. Single braces @{variable@} will not be automatically
                                        converted.</small>
                                </div>

                                <form action="{{ route('admin.settings.whatsapp-templates') }}" method="POST">
                                    @csrf

                                    <!-- Order Status Templates -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_pending">
                                                    <i class="fas fa-clock text-warning"></i> <strong>Order Pending
                                                        Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_pending" name="whatsapp_template_pending" rows="6"
                                                    maxlength="1000" placeholder="Enter template for pending orders...">@if($whatsappSettings['whatsapp_template_pending'] ?? false){{ $whatsappSettings['whatsapp_template_pending'] }}@elseHello @{{ customer_name }},

Your order #@{{ order_number }} is now PENDING.

We have received your order and it's being processed.

Order Total: ₹@{{ total }}
Order Date: @{{ order_date }}

Thank you for choosing @{{ company_name }}!@endif</textarea>

                                                <small class="text-muted">Message sent when order status is set to
                                                    Pending</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_processing">
                                                    <i class="fas fa-cog text-primary"></i> <strong>Order Processing
                                                        Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_processing" name="whatsapp_template_processing" rows="6"
                                                    maxlength="1000" placeholder="Enter template for processing orders...">@if($whatsappSettings['whatsapp_template_processing'] ?? false){{ $whatsappSettings['whatsapp_template_processing'] }}@elseHello @{{ customer_name }},

Great news! Your order #@{{ order_number }} is now PROCESSING.

We are preparing your items for shipment.

Order Total: ₹@{{ total }}
Expected Processing: 1-2 business days

Thank you for your patience!

@{{ company_name }}@endif</textarea>

                                                <small class="text-muted">Message sent when order status is set to
                                                    Processing</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_shipped">
                                                    <i class="fas fa-truck text-info"></i> <strong>Order Shipped
                                                        Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_shipped" name="whatsapp_template_shipped" rows="6"
                                                    maxlength="1000" placeholder="Enter template for shipped orders...">@if($whatsappSettings['whatsapp_template_shipped'] ?? false){{ $whatsappSettings['whatsapp_template_shipped'] }}@else🚚 Hello @{{ customer_name }},

Exciting news! Your order #@{{ order_number }} has been SHIPPED!

Your package is on its way to you.

Order Total: ₹@{{ total }}
Expected Delivery: 2-5 business days

Track your order for real-time updates.

Thanks for shopping with @{{ company_name }}!@endif</textarea>
                                                <small class="text-muted">Message sent when order status is set to
                                                    Shipped</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_delivered">
                                                    <i class="fas fa-check-circle text-success"></i> <strong>Order
                                                        Delivered Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_delivered" name="whatsapp_template_delivered" rows="6"
                                                    maxlength="1000" placeholder="Enter template for delivered orders...">@if($whatsappSettings['whatsapp_template_delivered'] ?? false){{ $whatsappSettings['whatsapp_template_delivered'] }}@else✅ Hello @{{ customer_name }},

Wonderful! Your order #@{{ order_number }} has been DELIVERED!

We hope you love your purchase.

Order Total: ₹@{{ total }}
Delivered on: @{{ order_date }}

Please let us know if you have any questions or feedback.

Thank you for choosing @{{ company_name }}!@endif</textarea>
                                                <small class="text-muted">Message sent when order status is set to
                                                    Delivered</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_cancelled">
                                                    <i class="fas fa-times-circle text-danger"></i> <strong>Order Cancelled
                                                        Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_cancelled" name="whatsapp_template_cancelled" rows="6"
                                                    maxlength="1000" placeholder="Enter template for cancelled orders...">@if($whatsappSettings['whatsapp_template_cancelled'] ?? false){{ $whatsappSettings['whatsapp_template_cancelled'] }}@else❌ Hello @{{ customer_name }},

We're sorry to inform you that your order #@{{ order_number }} has been CANCELLED.

Order Total: ₹@{{ total }}
Cancellation Date: @{{ order_date }}

If you have any questions about this cancellation, please contact our customer support.

We apologize for any inconvenience.

@{{ company_name }}@endif</textarea>
                                                <small class="text-muted">Message sent when order status is set to
                                                    Cancelled</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="whatsapp_template_payment_confirmed">
                                                    <i class="fas fa-credit-card text-success"></i> <strong>Payment
                                                        Confirmed Template</strong>
                                                </label>
                                                <textarea class="form-control" id="whatsapp_template_payment_confirmed" name="whatsapp_template_payment_confirmed"
                                                    rows="6" maxlength="1000" placeholder="Enter template for payment confirmation...">@if($whatsappSettings['whatsapp_template_payment_confirmed'] ?? false){{ $whatsappSettings['whatsapp_template_payment_confirmed'] }}@else💳 Hello @{{ customer_name }},

Great news! Your payment for order #@{{ order_number }} has been CONFIRMED!

Payment Status: @{{ payment_status }}
Order Total: ₹@{{ total }}
Payment Date: @{{ order_date }}

Your order is now being processed and will be shipped soon.

Thank you for your payment!

@{{ company_name }}@endif</textarea>
                                                <small class="text-muted">Message sent when payment status is
                                                    confirmed</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Available Placeholders Info -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-tags"></i> Available Placeholders</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled">
                                                        <li><code>@verbatim{{ customer_name }}@endverbatim
                                                            </code>
                                                            - Customer's name</li>
                                                        <li><code>@verbatim{{ order_number }}@endverbatim
                                                            </code>
                                                            - Order number</li>
                                                        <li><code>@verbatim{{ total }}@endverbatim
                                                            </code>
                                                            - Order total amount</li>
                                                        <li><code>@verbatim{{ company_name }}@endverbatim
                                                            </code>
                                                            - Your company name</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled">
                                                        <li><code>@verbatim{{ order_date }}@endverbatim
                                                            </code>
                                                            - Order date</li>
                                                        <li><code>@verbatim{{ status }}@endverbatim
                                                            </code>
                                                            - Current order status</li>
                                                        <li><code>@verbatim{{ payment_status }}@endverbatim
                                                            </code>
                                                            - Payment status</li>
                                                        <li><code>@verbatim{{ customer_mobile }}@endverbatim
                                                            </code> - Customer mobile number</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <small class="text-muted"><i class="fas fa-lightbulb"></i>
                                                <strong>Tip:</strong> Use emojis and line breaks (\n) to make your messages
                                                more engaging and readable. Templates use double braces like
                                                @verbatim{{ customer_name }}@endverbatim for placeholders.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save WhatsApp Templates
                                        </button>

                                        <div class="text-muted small">
                                            <i class="fas fa-info-circle"></i>
                                            Templates will be used for automatic WhatsApp notifications
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- WhatsApp Settings Preview -->
                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-eye"></i> WhatsApp Integration Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-cog"></i> Current Configuration:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>WhatsApp Notifications:</strong>
                                                @if($notificationSettings['whatsapp_notifications'] ?? false)
                                                    <span class="badge bg-success">
                                                        <i class="fab fa-whatsapp"></i> Enabled
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-times"></i> Disabled
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <strong>Super Admin Setup:</strong>
                                                <span class="text-muted">Contact super admin to configure Twilio WhatsApp
                                                    integration</span>
                                            </div>
                                            <div>
                                                <strong>Template Count:</strong>
                                                <span class="text-primary">6 templates configured</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-question-circle"></i> How It Works:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <ol class="mb-0 small">
                                                <li>Admin updates order status</li>
                                                <li>System checks if WhatsApp is enabled</li>
                                                <li>Message template is populated with order data</li>
                                                <li>WhatsApp message is sent to customer</li>
                                                <li>Delivery status is logged for tracking</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Settings -->
                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.profile') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Full Name *</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ auth()->user()->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address *</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="{{ auth()->user()->email }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input type="text" class="form-control" id="phone" name="phone"
                                                    value="{{ auth()->user()->phone }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="avatar">Profile Picture</label>
                                                <input type="file" class="form-control" id="avatar" name="avatar"
                                                    accept="image/*">
                                                @if (auth()->user()->avatar)
                                                    <small class="text-muted">Current picture:</small>
                                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                                        alt="Profile Picture" class="img-thumbnail mt-2"
                                                        style="max-height: 100px;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3">{{ auth()->user()->address }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="tab-pane fade" id="v-pills-password" role="tabpanel">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.password') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="current_password">Current Password *</label>
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="password">New Password *</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                        <small class="text-muted">Password must be at least 8 characters long</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm New Password *</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            :root {
                --primary-color: #2d5016;
                --secondary-color: #6b8e23;
                --sidebar-color: #2d5016;
            }
            
            // Manual test function - can be called from browser console
            window.testBillFormatSettings = function() {
                debugLog('=== MANUAL TEST STARTED ===');
                
                // Test element existence
                const elements = {
                    thermal_checkbox: document.getElementById('thermal_printer_enabled'),
                    a4_checkbox: document.getElementById('a4_sheet_enabled'),
                    thermal_settings: document.getElementById('thermal-printer-settings'),
                    a4_settings: document.getElementById('a4-sheet-settings'),
                    default_format: document.getElementById('default_bill_format'),
                    form: document.querySelector('form[action*="bill-format"]')
                };
                
                debugLog('Element existence test:', Object.keys(elements).reduce((acc, key) => {
                    acc[key] = !!elements[key];
                    return acc;
                }, {}));
                
                // Test current states
                if (elements.thermal_checkbox && elements.a4_checkbox) {
                    debugLog('Current checkbox states:', {
                        thermal: elements.thermal_checkbox.checked,
                        a4: elements.a4_checkbox.checked
                    });
                }
                
                // Test toggle functions
                try {
                    debugLog('Testing toggle functions...');
                    toggleThermalPrinterSettings();
                    toggleA4SheetSettings();
                    updateDefaultFormatOptions();
                    debugLog('Toggle functions executed successfully');
                } catch (error) {
                    debugLog('ERROR in toggle functions:', error);
                }
                
                // Test event listeners
                debugLog('Re-setting up event listeners...');
                setupBillFormatEventListeners();
                
                debugLog('=== MANUAL TEST COMPLETED ===');
                debugLog('You can now try toggling the checkboxes to see if they work.');
                
                return 'Test completed. Check console for details.';
            };
            
            // Quick toggle test functions
            window.toggleThermalTest = function() {
                const checkbox = document.getElementById('thermal_printer_enabled');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    handleThermalChange();
                    return 'Thermal toggled to: ' + checkbox.checked;
                }
                return 'Thermal checkbox not found';
            };
            
            window.toggleA4Test = function() {
                const checkbox = document.getElementById('a4_sheet_enabled');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    handleA4Change();
                    return 'A4 toggled to: ' + checkbox.checked;
                }
                return 'A4 checkbox not found';
            };
            
            // Manual test function - can be called from browser console
            window.testBillFormatSettings = function() {
                debugLog('=== MANUAL TEST STARTED ===');
                
                // Test element existence
                const elements = {
                    thermal_checkbox: document.getElementById('thermal_printer_enabled'),
                    a4_checkbox: document.getElementById('a4_sheet_enabled'),
                    thermal_settings: document.getElementById('thermal-printer-settings'),
                    a4_settings: document.getElementById('a4-sheet-settings'),
                    default_format: document.getElementById('default_bill_format'),
                    form: document.querySelector('form[action*="bill-format"]')
                };
                
                debugLog('Element existence test:', Object.keys(elements).reduce((acc, key) => {
                    acc[key] = !!elements[key];
                    return acc;
                }, {}));
                
                // Test current states
                if (elements.thermal_checkbox && elements.a4_checkbox) {
                    debugLog('Current checkbox states:', {
                        thermal: elements.thermal_checkbox.checked,
                        a4: elements.a4_checkbox.checked
                    });
                }
                
                // Test toggle functions
                try {
                    debugLog('Testing toggle functions...');
                    toggleThermalPrinterSettings();
                    toggleA4SheetSettings();
                    updateDefaultFormatOptions();
                    debugLog('Toggle functions executed successfully');
                } catch (error) {
                    debugLog('ERROR in toggle functions:', error);
                }
                
                // Test event listeners
                debugLog('Re-setting up event listeners...');
                setupBillFormatEventListeners();
                
                debugLog('=== MANUAL TEST COMPLETED ===');
                debugLog('You can now try toggling the checkboxes to see if they work.');
                
                return 'Test completed. Check console for details.';
            };
            
            // Quick toggle test functions
            window.toggleThermalTest = function() {
                const checkbox = document.getElementById('thermal_printer_enabled');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    handleThermalChange();
                    return 'Thermal toggled to: ' + checkbox.checked;
                }
                return 'Thermal checkbox not found';
            };
            
            window.toggleA4Test = function() {
                const checkbox = document.getElementById('a4_sheet_enabled');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    handleA4Change();
                    return 'A4 toggled to: ' + checkbox.checked;
                }
                return 'A4 checkbox not found';
            };

            .sidebar {
                background: var(--sidebar-color) !important;
            }

            .btn-primary,
            .bg-primary {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
            }

            .btn-primary:hover,
            .bg-primary:hover {
                background-color: var(--secondary-color) !important;
                border-color: var(--secondary-color) !important;
            }

            .text-primary {
                color: var(--primary-color) !important;
            }

            .bg-primary {
                background-color: var(--primary-color) !important;
            }

            .bg-secondary {
                background-color: var(--secondary-color) !important;
            }

            .form-check-input:checked {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Global debug flag
            const DEBUG_BILL_FORMAT = true;
            
            function debugLog(message, data = null) {
                if (DEBUG_BILL_FORMAT) {
                    console.log('[Bill Format Debug]:', message, data);
                }
            }
            
            function toggleDeliverySettings() {
                const deliveryEnabled = document.getElementById('delivery_enabled').checked;
                const deliveryContent = document.getElementById('delivery-settings-content');

                if (deliveryEnabled) {
                    deliveryContent.style.display = 'block';
                } else {
                    deliveryContent.style.display = 'none';
                }
            }

            function toggleFreeDelivery() {
                const freeDeliveryEnabled = document.getElementById('free_delivery_enabled').checked;
                const freeDeliveryContent = document.getElementById('free-delivery-content');

                if (freeDeliveryEnabled) {
                    freeDeliveryContent.style.display = 'block';
                } else {
                    freeDeliveryContent.style.display = 'none';
                }
            }

            function toggleMinOrderValidation() {
                const minOrderEnabled = document.getElementById('min_order_validation_enabled').checked;
                const minOrderContent = document.getElementById('min-order-validation-content');

                if (minOrderEnabled) {
                    minOrderContent.style.display = 'block';
                } else {
                    minOrderContent.style.display = 'none';
                }
            }

            function toggleAnimationSettings() {
                const animationsEnabled = document.getElementById('frontend_animations_enabled').checked;
                const animationContent = document.getElementById('animation-settings-content');
                const specificAnimations = document.getElementById('specific-animations');
                const performanceSettings = document.getElementById('performance-settings');

                if (animationsEnabled) {
                    animationContent.style.display = 'block';
                    specificAnimations.style.display = 'block';
                    performanceSettings.style.display = 'block';
                } else {
                    animationContent.style.display = 'none';
                    specificAnimations.style.display = 'none';
                    performanceSettings.style.display = 'none';
                }
            }

            function toggleThermalPrinterSettings() {
                debugLog('toggleThermalPrinterSettings called');
                
                const thermalCheckbox = document.getElementById('thermal_printer_enabled');
                const thermalSettings = document.getElementById('thermal-printer-settings');
                
                if (!thermalCheckbox) {
                    debugLog('ERROR: thermal_printer_enabled checkbox not found!');
                    return;
                }
                
                if (!thermalSettings) {
                    debugLog('ERROR: thermal-printer-settings container not found!');
                    return;
                }
                
                const thermalEnabled = thermalCheckbox.checked;
                debugLog('Thermal printer enabled:', thermalEnabled);
                
                if (thermalEnabled) {
                    thermalSettings.style.display = 'block';
                    debugLog('Thermal settings shown');
                } else {
                    thermalSettings.style.display = 'none';
                    debugLog('Thermal settings hidden');
                }
            }

            function toggleA4SheetSettings() {
                debugLog('toggleA4SheetSettings called');
                
                const a4Checkbox = document.getElementById('a4_sheet_enabled');
                const a4Settings = document.getElementById('a4-sheet-settings');
                
                if (!a4Checkbox) {
                    debugLog('ERROR: a4_sheet_enabled checkbox not found!');
                    return;
                }
                
                if (!a4Settings) {
                    debugLog('ERROR: a4-sheet-settings container not found!');
                    return;
                }
                
                const a4Enabled = a4Checkbox.checked;
                debugLog('A4 sheet enabled:', a4Enabled);
                
                if (a4Enabled) {
                    a4Settings.style.display = 'block';
                    debugLog('A4 settings shown');
                } else {
                    a4Settings.style.display = 'none';
                    debugLog('A4 settings hidden');
                }
            }

            function updateDefaultFormatOptions() {
                debugLog('updateDefaultFormatOptions called');
                
                const thermalCheckbox = document.getElementById('thermal_printer_enabled');
                const a4Checkbox = document.getElementById('a4_sheet_enabled');
                const defaultFormatSelect = document.getElementById('default_bill_format');
                
                if (!thermalCheckbox || !a4Checkbox || !defaultFormatSelect) {
                    debugLog('ERROR: Required elements not found:', {
                        thermalCheckbox: !!thermalCheckbox,
                        a4Checkbox: !!a4Checkbox,
                        defaultFormatSelect: !!defaultFormatSelect
                    });
                    return;
                }
                
                const thermalEnabled = thermalCheckbox.checked;
                const a4Enabled = a4Checkbox.checked;
                
                debugLog('Current format states:', {
                    thermalEnabled: thermalEnabled,
                    a4Enabled: a4Enabled
                });
                
                // Store current selection (including server-side value)
                const currentValue = defaultFormatSelect.value;
                debugLog('Current default format value:', currentValue);
                
                // If both formats are enabled, don't rebuild - just ensure the current selection is valid
                if (thermalEnabled && a4Enabled) {
                    debugLog('Both formats enabled - no need to rebuild dropdown');
                    
                    // Just validate that current selection is valid
                    const validOptions = ['thermal', 'a4_sheet'];
                    if (!validOptions.includes(currentValue)) {
                        // If somehow invalid, set to first valid option
                        defaultFormatSelect.value = 'thermal';
                        debugLog('Invalid selection detected, defaulted to thermal');
                    }
                    return;
                }
                
                // Only rebuild if we need to limit options
                debugLog('Rebuilding dropdown options due to format constraints');
                
                // Store the server-side selected value before clearing
                const originalServerValue = getOriginalServerValue();
                debugLog('Original server value detected:', originalServerValue);
                
                // Clear current options
                defaultFormatSelect.innerHTML = '';
                
                // Track if current selection will be available
                let selectedValue = currentValue || originalServerValue || '';
                let selectionMade = false;
                
                // Add available options based on enabled formats
                if (thermalEnabled) {
                    const thermalOption = document.createElement('option');
                    thermalOption.value = 'thermal';
                    thermalOption.textContent = 'Thermal Printer';
                    
                    if (selectedValue === 'thermal') {
                        thermalOption.selected = true;
                        selectionMade = true;
                        debugLog('Selected thermal option');
                    }
                    
                    defaultFormatSelect.appendChild(thermalOption);
                    debugLog('Added thermal option');
                }
                
                if (a4Enabled) {
                    const a4Option = document.createElement('option');
                    a4Option.value = 'a4_sheet';
                    a4Option.textContent = 'A4 Sheet PDF';
                    
                    if (selectedValue === 'a4_sheet') {
                        a4Option.selected = true;
                        selectionMade = true;
                        debugLog('Selected A4 option');
                    }
                    
                    defaultFormatSelect.appendChild(a4Option);
                    debugLog('Added A4 option');
                }
                
                // If no selection was made but we have options, select the first one
                if (!selectionMade && defaultFormatSelect.options.length > 0) {
                    defaultFormatSelect.options[0].selected = true;
                    debugLog('Auto-selected first available option:', defaultFormatSelect.options[0].value);
                    
                    // Show notification if we had to change from a valid previous selection
                    if (selectedValue && selectedValue !== defaultFormatSelect.options[0].value) {
                        showDefaultFormatChangeNotification(selectedValue, defaultFormatSelect.options[0].value);
                    }
                }
                
                // If no options available, add a disabled option
                if (!thermalEnabled && !a4Enabled) {
                    const disabledOption = document.createElement('option');
                    disabledOption.value = '';
                    disabledOption.textContent = 'Please enable at least one format';
                    disabledOption.disabled = true;
                    disabledOption.selected = true;
                    defaultFormatSelect.appendChild(disabledOption);
                    
                    debugLog('Added disabled option - no formats enabled');
                }
                
                // Update the visual state of the dropdown
                if (defaultFormatSelect.options.length === 1 && !thermalEnabled && !a4Enabled) {
                    defaultFormatSelect.style.color = '#6c757d';
                    defaultFormatSelect.style.fontStyle = 'italic';
                } else {
                    defaultFormatSelect.style.color = '';
                    defaultFormatSelect.style.fontStyle = '';
                }
                
                debugLog('Default format options updated successfully:', {
                    thermalEnabled: thermalEnabled,
                    a4Enabled: a4Enabled,
                    originalValue: selectedValue,
                    newValue: defaultFormatSelect.value,
                    optionsCount: defaultFormatSelect.options.length
                });
            }
            
            // Function to detect the original server-side selected value
            function updateCurrentDefaultDisplay() {
                const defaultFormatSelect = document.getElementById('default_bill_format');
                const displayElement = document.getElementById('current-default-display');
                
                if (!defaultFormatSelect || !displayElement) {
                    return;
                }
                
                const currentValue = defaultFormatSelect.value;
                const formatNames = {
                    'thermal': 'Thermal Printer',
                    'a4_sheet': 'A4 Sheet PDF'
                };
                
                const displayText = formatNames[currentValue] || currentValue || 'None';
                displayElement.textContent = displayText;
                
                // Update badge color based on whether it matches the server value
                const originalValue = window.originalDefaultFormat;
                if (originalValue && currentValue === originalValue) {
                    displayElement.className = 'badge bg-success';
                } else {
                    displayElement.className = 'badge bg-warning';
                }
            }
            
            function showDefaultFormatChangeNotification(oldFormat, newFormat) {
                const formatNames = {
                    'thermal': 'Thermal Printer',
                    'a4_sheet': 'A4 Sheet PDF'
                };
                
                debugLog('Showing format change notification:', { oldFormat, newFormat });
                
                // Create a temporary notification element
                const notification = document.createElement('div');
                notification.className = 'alert alert-info alert-dismissible fade show mt-2';
                notification.style.fontSize = '0.875rem';
                notification.innerHTML = `
                    <i class="fas fa-info-circle"></i>
                    Default format automatically changed from "${formatNames[oldFormat] || oldFormat}" to "${formatNames[newFormat] || newFormat}" because the previous option was disabled.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                // Insert after the default format field
                const defaultFormatField = document.getElementById('default_bill_format').closest('.form-group');
                if (defaultFormatField) {
                    defaultFormatField.appendChild(notification);
                    
                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 5000);
                }
            }

            function validateBillFormatSettings() {
                debugLog('validateBillFormatSettings called');
                
                const thermalCheckbox = document.getElementById('thermal_printer_enabled');
                const a4Checkbox = document.getElementById('a4_sheet_enabled');
                
                if (!thermalCheckbox || !a4Checkbox) {
                    debugLog('ERROR: Validation checkboxes not found!');
                    return false;
                }
                
                const thermalEnabled = thermalCheckbox.checked;
                const a4Enabled = a4Checkbox.checked;
                
                debugLog('Validation check:', { thermalEnabled, a4Enabled });
                
                if (!thermalEnabled && !a4Enabled) {
                    alert('Error: At least one bill format must be enabled. Please enable either Thermal Printer or A4 Sheet format.');
                    debugLog('Validation failed - no formats enabled');
                    return false;
                }
                
                debugLog('Validation passed');
                return true;
            }
            
            function setupBillFormatEventListeners() {
                debugLog('Setting up event listeners');
                
                const thermalCheckbox = document.getElementById('thermal_printer_enabled');
                const a4Checkbox = document.getElementById('a4_sheet_enabled');
                
                if (thermalCheckbox) {
                    // Remove any existing listeners
                    thermalCheckbox.removeEventListener('change', handleThermalChange);
                    // Add new listener
                    thermalCheckbox.addEventListener('change', handleThermalChange);
                    debugLog('Thermal checkbox listener added');
                } else {
                    debugLog('ERROR: Could not find thermal checkbox for event listener');
                }
                
                if (a4Checkbox) {
                    // Remove any existing listeners
                    a4Checkbox.removeEventListener('change', handleA4Change);
                    // Add new listener
                    a4Checkbox.addEventListener('change', handleA4Change);
                    debugLog('A4 checkbox listener added');
                } else {
                    debugLog('ERROR: Could not find A4 checkbox for event listener');
                }
            }
            
            function handleThermalChange() {
                debugLog('Thermal checkbox changed');
                toggleThermalPrinterSettings();
                setTimeout(updateDefaultFormatOptions, 50);
            }
            
            function handleA4Change() {
                debugLog('A4 checkbox changed');
                toggleA4SheetSettings();
                setTimeout(updateDefaultFormatOptions, 50);
            }
            
            function initializeBillFormatSettings() {
                debugLog('Initializing bill format settings...');
                
                // Wait for DOM to be fully ready
                setTimeout(() => {
                    try {
                        // FIRST: Capture the original server-side selected value before any modifications
                        const originalValue = getOriginalServerValue();
                        debugLog('Captured original server value on initialization:', originalValue);
                        
                        // Check if all required elements exist
                        const requiredElements = {
                            thermal_checkbox: document.getElementById('thermal_printer_enabled'),
                            a4_checkbox: document.getElementById('a4_sheet_enabled'),
                            thermal_settings: document.getElementById('thermal-printer-settings'),
                            a4_settings: document.getElementById('a4-sheet-settings'),
                            default_format: document.getElementById('default_bill_format')
                        };
                        
                        debugLog('Required elements check:', Object.keys(requiredElements).reduce((acc, key) => {
                            acc[key] = !!requiredElements[key];
                            return acc;
                        }, {}));
                        
                        // Initialize current states (but don't update dropdown yet)
                        toggleThermalPrinterSettings();
                        toggleA4SheetSettings();
                        
                        // Only update dropdown if format constraints require it
                        const thermalEnabled = requiredElements.thermal_checkbox ? requiredElements.thermal_checkbox.checked : false;
                        const a4Enabled = requiredElements.a4_checkbox ? requiredElements.a4_checkbox.checked : true;
                        
                        // If both formats are enabled, preserve the server selection completely
                        if (thermalEnabled && a4Enabled) {
                            debugLog('Both formats enabled - preserving server selection:', originalValue);
                            // Don't call updateDefaultFormatOptions() to preserve server value
                        } else {
                            debugLog('Format constraints detected - updating dropdown');
                            updateDefaultFormatOptions();
                        }
                        
                        // Set up event listeners
                        setupBillFormatEventListeners();
                        
                        debugLog('Bill format settings initialized successfully!');
                        
                    } catch (error) {
                        debugLog('ERROR during initialization:', error);
                    }
                }, 200);
            }

            // Convert single braces to double braces for WhatsApp templates
            function convertTemplatePlaceholders() {
                const templateTextareas = [
                    'whatsapp_template_pending',
                    'whatsapp_template_processing',
                    'whatsapp_template_shipped',
                    'whatsapp_template_delivered',
                    'whatsapp_template_cancelled',
                    'whatsapp_template_payment_confirmed'
                ];

                templateTextareas.forEach(function(id) {
                    const textarea = document.getElementById(id);
                    if (textarea) {
                        // Convert single braces to double braces - DISABLED to prevent corruption
                        // This conversion is now handled server-side to avoid UI issues
                        console.log('Template conversion disabled for better user experience');
                    }
                });
            }

            // Initialize toggles on page load
            document.addEventListener('DOMContentLoaded', function() {
                debugLog('DOM Content Loaded - Starting initialization');
                
                try {
                    // Initialize other settings first
                    toggleDeliverySettings();
                    toggleFreeDelivery();
                    toggleMinOrderValidation();
                    toggleAnimationSettings();
                    
                    // Initialize bill format settings with enhanced debugging
                    initializeBillFormatSettings();
                    
                    // Add enhanced form validation for bill format settings
                    setTimeout(() => {
                        const billFormatForm = document.querySelector('form[action*="bill-format"]');
                        if (billFormatForm) {
                            debugLog('Bill format form found, adding validation');
                            
                            billFormatForm.addEventListener('submit', function(e) {
                                debugLog('Form submission attempted');
                                
                                if (!validateBillFormatSettings()) {
                                    debugLog('Form validation failed, preventing submission');
                                    e.preventDefault();
                                    e.stopPropagation();
                                    return false;
                                }
                                
                                debugLog('Form validation passed, allowing submission');
                                
                                // Show loading state
                                const submitButton = billFormatForm.querySelector('button[type="submit"]');
                                if (submitButton) {
                                    submitButton.disabled = true;
                                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                                }
                            });
                        } else {
                            debugLog('WARNING: Bill format form not found!');
                        }
                    }, 300);
                    
                } catch (error) {
                    debugLog('ERROR in DOMContentLoaded:', error);
                }
                
                // convertTemplatePlaceholders(); // Disabled to prevent template corruption
            });

            // Convert placeholders before form submission - DISABLED
            // document.querySelector('form[action*="whatsapp-templates"]')?.addEventListener('submit', function() {
            //     convertTemplatePlaceholders();
            // });
            
            // Global debugging function - accessible from browser console
            window.debugBillFormatSettings = function() {
                console.log('\n=== BILL FORMAT SETTINGS DEBUG ===');
                
                const thermal = document.getElementById('thermal_printer_enabled');
                const a4 = document.getElementById('a4_sheet_enabled');
                const defaultFormat = document.getElementById('default_bill_format');
                const form = document.querySelector('form[action*="bill-format"]');
                
                console.log('✅ Elements Found:', {
                    'Thermal Checkbox': !!thermal,
                    'A4 Checkbox': !!a4,
                    'Default Format Dropdown': !!defaultFormat,
                    'Form': !!form
                });
                
                if (thermal && a4 && defaultFormat) {
                    console.log('✅ Current States:', {
                        'Thermal Enabled': thermal.checked,
                        'A4 Enabled': a4.checked,
                        'Default Format Value': defaultFormat.value,
                        'Default Format Text': defaultFormat.options[defaultFormat.selectedIndex] ? defaultFormat.options[defaultFormat.selectedIndex].text : 'N/A',
                        'Available Options': Array.from(defaultFormat.options).map(opt => ({ value: opt.value, text: opt.text, selected: opt.selected }))
                    });
                    
                    console.log('🔍 Server Value Info:', {
                        'Original Server Value': window.originalDefaultFormat || 'Not captured',
                        'Current Selection': defaultFormat.value,
                        'Values Match': (window.originalDefaultFormat === defaultFormat.value)
                    });
                }
                
                console.log('\n🔧 Manual Test Commands:');
                console.log('- testBillFormatSettings() - Run full test');
                console.log('- toggleThermalTest() - Toggle thermal setting');
                console.log('- toggleA4Test() - Toggle A4 setting');
                console.log('- fixDefaultFormat() - Reset dropdown to server value');
                console.log('\n================================\n');
                
                return 'Debug info displayed in console above.';
            };
            
            // Function to manually fix the default format dropdown
            window.fixDefaultFormat = function() {
                const defaultFormatSelect = document.getElementById('default_bill_format');
                const originalValue = window.originalDefaultFormat;
                
                if (!defaultFormatSelect) {
                    return 'Default format dropdown not found';
                }
                
                if (!originalValue) {
                    return 'Original server value not captured';
                }
                
                // Find the option with the original value
                for (let option of defaultFormatSelect.options) {
                    if (option.value === originalValue) {
                        option.selected = true;
                        debugLog('Manually restored default format to:', originalValue);
                        return `Default format restored to: ${option.text}`;
                    }
                }
                
                return `Original value "${originalValue}" not found in current options`;
            };
            
            // Invoice Numbering Functions
            function updatePreviews() {
                try {
                    // Get form values
                    const orderPrefix = document.getElementById('order_invoice_prefix')?.value || 'ORD';
                    const orderSeparator = document.getElementById('order_invoice_separator')?.value || '-';
                    const orderDigits = parseInt(document.getElementById('order_invoice_digits')?.value) || 5;
                    const orderIncludeYear = document.getElementById('order_invoice_include_year')?.checked || false;
                    const orderIncludeMonth = document.getElementById('order_invoice_include_month')?.checked || false;
                    
                    const posPrefix = document.getElementById('pos_invoice_prefix')?.value || 'POS';
                    const posSeparator = document.getElementById('pos_invoice_separator')?.value || '-';
                    const posDigits = parseInt(document.getElementById('pos_invoice_digits')?.value) || 4;
                    const posIncludeYear = document.getElementById('pos_invoice_include_year')?.checked || false;
                    const posIncludeMonth = document.getElementById('pos_invoice_include_month')?.checked || false;
                    
                    // Generate preview numbers
                    const currentYear = new Date().getFullYear().toString();
                    const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0');
                    
                    // Build order invoice preview
                    let orderParts = [orderPrefix];
                    if (orderIncludeYear) orderParts.push(currentYear);
                    if (orderIncludeMonth) orderParts.push(currentMonth);
                    orderParts.push('1'.padStart(orderDigits, '0'));
                    const orderPreview = orderParts.join(orderSeparator);
                    
                    // Build POS invoice preview
                    let posParts = [posPrefix];
                    if (posIncludeYear) posParts.push(currentYear);
                    if (posIncludeMonth) posParts.push(currentMonth);
                    posParts.push('1'.padStart(posDigits, '0'));
                    const posPreview = posParts.join(posSeparator);
                    
                    // Update preview displays
                    const orderPreviewEl = document.getElementById('order-invoice-preview');
                    const posPreviewEl = document.getElementById('pos-invoice-preview');
                    
                    if (orderPreviewEl) orderPreviewEl.textContent = orderPreview;
                    if (posPreviewEl) posPreviewEl.textContent = posPreview;
                } catch (error) {
                    console.error('Error updating invoice previews:', error);
                }
            }
            
            // Fetch actual next invoice numbers from server
            function fetchServerPreviews() {
                const button = event?.target;
                const originalContent = button?.innerHTML;
                
                // Show loading state
                if (button) {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    button.disabled = true;
                }
                
                fetch('{{ route("admin.settings.preview-invoice-numbers") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orderPreviewEl = document.getElementById('order-invoice-preview');
                        const posPreviewEl = document.getElementById('pos-invoice-preview');
                        
                        if (orderPreviewEl && data.preview?.order?.preview) {
                            orderPreviewEl.textContent = data.preview.order.preview;
                        }
                        if (posPreviewEl && data.preview?.pos?.preview) {
                            posPreviewEl.textContent = data.preview.pos.preview;
                        }
                        
                        // Update sequence status
                        updateSequenceStatus(data.preview);
                        
                        // Show success message
                        showToast('success', 'Previews updated with actual next numbers!');
                    } else {
                        showToast('error', 'Failed to fetch preview: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error fetching previews:', error);
                    showToast('error', 'Failed to fetch preview. Please try again.');
                })
                .finally(() => {
                    // Restore button
                    if (button && originalContent) {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }
                });
            }
            
            // Update sequence status display
            function updateSequenceStatus(previewData) {
                const statusDiv = document.getElementById('sequence-status');
                if (statusDiv && previewData) {
                    statusDiv.innerHTML = `
                        <div class="mb-2">
                            <strong>Order Sequences:</strong>
                            <br>Next: ${previewData.order?.next_sequence || 'N/A'}
                            <br>Preview: <code>${previewData.order?.preview || 'N/A'}</code>
                        </div>
                        <div>
                            <strong>POS Sequences:</strong>
                            <br>Next: ${previewData.pos?.next_sequence || 'N/A'}
                            <br>Preview: <code>${previewData.pos?.preview || 'N/A'}</code>
                        </div>
                    `;
                }
            }
            
            // Reset invoice sequences
            function resetSequences(type) {
                const typeLabel = type ? (type === 'order' ? 'Order' : 'POS') : 'All';
                
                if (!confirm(`Are you sure you want to reset ${typeLabel.toLowerCase()} invoice sequences? This will restart numbering from 1.`)) {
                    return;
                }
                
                const button = event?.target;
                const originalContent = button?.innerHTML;
                
                // Show loading state
                if (button) {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
                    button.disabled = true;
                }
                
                // Create form data
                const formData = new FormData();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) formData.append('_token', csrfToken);
                formData.append('confirm', '1');
                if (type) {
                    formData.append('type', type);
                }
                
                fetch('{{ route("admin.settings.reset-invoice-sequences") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        // Handle redirect response
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data) {
                        if (data.success) {
                            showToast('success', `${typeLabel} invoice sequences reset successfully!`);
                            // Refresh previews after reset
                            setTimeout(() => {
                                fetchServerPreviews();
                            }, 500);
                        } else {
                            showToast('error', 'Failed to reset sequences: ' + (data.error || 'Unknown error'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error resetting sequences:', error);
                    showToast('error', 'Failed to reset sequences. Please try again.');
                })
                .finally(() => {
                    // Restore button
                    if (button && originalContent) {
                        button.innerHTML = originalContent;
                        button.disabled = false;
                    }
                });
            }
            
            // Show toast notification
            function showToast(type, message) {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
                toast.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(toast);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 5000);
            }
            
            // Add invoice numbering initialization to existing DOMContentLoaded
            const originalDOMContentLoaded = document.addEventListener;
            setTimeout(() => {
                // Update previews on page load if invoice numbering elements exist
                if (document.getElementById('order_invoice_prefix')) {
                    updatePreviews();
                    
                    // Fetch actual server previews after a delay
                    setTimeout(() => {
                        if (document.getElementById('order-invoice-preview')) {
                            fetchServerPreviews();
                        }
                    }, 1000);
                }
                
                // Add form validation for invoice numbering
                const invoiceForm = document.querySelector('form[action*="invoice-numbering"]');
                if (invoiceForm) {
                    invoiceForm.addEventListener('submit', function(e) {
                        // Validate that prefixes are not empty
                        const orderPrefix = document.getElementById('order_invoice_prefix')?.value?.trim();
                        const posPrefix = document.getElementById('pos_invoice_prefix')?.value?.trim();
                        
                        if (!orderPrefix || !posPrefix) {
                            e.preventDefault();
                            showToast('error', 'Both order and POS prefixes are required.');
                            return false;
                        }
                        
                        // Show loading state on submit button
                        const submitButton = invoiceForm.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                            submitButton.disabled = true;
                        }
                    });
                }
            }, 500);
            
            // Make functions global for onclick handlers
            window.updatePreviews = updatePreviews;
            window.fetchServerPreviews = fetchServerPreviews;
            window.resetSequences = resetSequences;
        </script>
    @endpush
@endsection
