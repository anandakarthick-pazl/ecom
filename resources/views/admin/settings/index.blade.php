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
                                        Control how many records are displayed per page in both admin panel and frontend.
                                    </div>

                                    <!-- Frontend Pagination Settings -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-storefront"></i> Frontend Settings</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="frontend_pagination_enabled" name="frontend_pagination_enabled"
                                                    value="1" checked>
                                                <label class="form-check-label" for="frontend_pagination_enabled">
                                                    <strong>Enable Frontend Pagination</strong>
                                                    <br><small class="text-muted">Enable pagination on products and offers
                                                        pages. If disabled, all items will load at once.</small>
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
                                                            <option value="6">6 Products</option>
                                                            <option value="9">9 Products</option>
                                                            <option value="12" selected>12 Products</option>
                                                            <option value="15">15 Products</option>
                                                            <option value="18">18 Products</option>
                                                            <option value="24">24 Products</option>
                                                            <option value="30">30 Products</option>
                                                        </select>
                                                        <small class="text-muted">Number of products to show per page on
                                                            frontend</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Admin Pagination Settings -->
                                    <div class="card bg-light mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-cog"></i> Admin Panel Settings</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="admin_pagination_enabled" name="admin_pagination_enabled"
                                                    value="1" checked>
                                                <label class="form-check-label" for="admin_pagination_enabled">
                                                    <strong>Enable Admin Pagination</strong>
                                                    <br><small class="text-muted">Enable pagination in admin listings. If
                                                        disabled, all records will load at once.</small>
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
                                                            <option value="10">10 Records</option>
                                                            <option value="15">15 Records</option>
                                                            <option value="20" selected>20 Records</option>
                                                            <option value="25">25 Records</option>
                                                            <option value="30">30 Records</option>
                                                            <option value="50">50 Records</option>
                                                            <option value="100">100 Records</option>
                                                        </select>
                                                        <small class="text-muted">Number of records to show per page in
                                                            admin listings</small>
                                                    </div>
                                                </div>
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
                                        <h6><i class="fas fa-storefront"></i> Frontend:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Status:</strong>
                                                <span class="badge bg-success">
                                                    Enabled
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Products per page:</strong> 12
                                            </div>
                                            <div>
                                                <strong>Behavior:</strong>
                                                <span class="text-primary">
                                                    Paginated with AJAX filtering
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6><i class="fas fa-cog"></i> Admin Panel:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <strong>Status:</strong>
                                                <span class="badge bg-success">
                                                    Enabled
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Records per page:</strong> 20
                                            </div>
                                            <div>
                                                <strong>Behavior:</strong>
                                                <span class="text-primary">
                                                    Paginated listings
                                                </span>
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

                                                    <div class="row">
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

                                                    <div class="form-group">
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
                                            <div>
                                                <strong>Default Format:</strong> 
                                                {{ ucfirst(str_replace('_', ' ', $billFormatSettings['default_bill_format'] ?? 'a4_sheet')) }}
                                            </div>
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
                toggleDeliverySettings();
                toggleFreeDelivery();
                toggleMinOrderValidation();
                toggleAnimationSettings();
                // convertTemplatePlaceholders(); // Disabled to prevent template corruption
            });

            // Convert placeholders before form submission - DISABLED
            // document.querySelector('form[action*="whatsapp-templates"]')?.addEventListener('submit', function() {
            //     convertTemplatePlaceholders();
            // });
        </script>
    @endpush
@endsection
