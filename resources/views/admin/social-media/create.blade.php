@extends('admin.layouts.app')

@section('title', 'Add Social Media Link')
@section('page_title', 'Add Social Media Link')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Social Media Link</h5>
                <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.social-media.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Platform Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., Facebook, Twitter, Instagram" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon_class" class="form-label">Icon Class <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('icon_class') is-invalid @enderror" 
                                           id="icon_class" name="icon_class" value="{{ old('icon_class') }}" 
                                           placeholder="e.g., fab fa-facebook-f" required>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                @error('icon_class')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Font Awesome icon class (e.g., fab fa-facebook-f)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Profile URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" 
                               id="url" name="url" value="{{ old('url') }}" 
                               placeholder="https://facebook.com/yourpage" required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the complete URL to your social media profile or page</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Brand Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', '#6c757d') }}" title="Choose brand color">
                                    <input type="text" class="form-control" id="color_text" 
                                           placeholder="#6c757d" value="{{ old('color', '#6c757d') }}">
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Brand color for the social media icon</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order') }}" 
                                       placeholder="Leave blank for auto" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Lower numbers appear first (leave blank for automatic ordering)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Display on website)
                            </label>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-4">
                        <h6>Preview</h6>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon me-3" id="preview-icon" style="background-color: #6c757d;">
                                    <i class="fas fa-share-alt text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" id="preview-name">Platform Name</div>
                                    <small class="text-muted" id="preview-icon-class">Icon Class</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Social Media Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Select from Predefined Platforms -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Select from Popular Platforms</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($predefinedPlatforms as $key => $platform)
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary platform-btn"
                                    data-name="{{ $platform['name'] }}"
                                    data-icon="{{ $platform['icon_class'] }}"
                                    data-color="{{ $platform['color'] }}"
                                    data-placeholder="{{ $platform['placeholder'] }}">
                                <div class="d-flex align-items-center">
                                    <div class="social-icon me-2" style="background-color: {{ $platform['color'] }};">
                                        <i class="{{ $platform['icon_class'] }} text-white"></i>
                                    </div>
                                    {{ $platform['name'] }}
                                </div>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scroll to Bottom Button -->
<button type="button" class="scroll-to-bottom" id="scrollToBottom" title="Scroll to Submit Button">
    <i class="fas fa-arrow-down"></i>
</button>

<!-- Icon Selection Modal -->
<div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconModalLabel">Select Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Search icons...">
                </div>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle"></i> Click on any icon to select it. The modal will close automatically.</small>
                </div>
                <div class="row g-2" id="iconGrid">
                    @php
                    $socialIcons = \App\Helpers\IconClass::getSocialMediaIcons();
                    @endphp
                    
                    @foreach($socialIcons as $iconClass => $iconName)
                    <div class="col-md-4 col-lg-3">
                        <button type="button" class="btn btn-outline-secondary w-100 icon-select-btn" 
                                data-icon="{{ $iconClass }}" 
                                title="{{ $iconName }}">
                            <i class="{{ $iconClass }} me-2"></i>{{ $iconName }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.platform-btn {
    text-align: left;
    padding: 0.75rem;
}

.platform-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.platform-btn:hover .social-icon {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

.icon-select-btn {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    margin-bottom: 0.5rem;
}

.icon-select-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Modal fixes to prevent greyout issues */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

/* Ensure modal content is clickable */
.modal-content {
    position: relative;
    z-index: 1060;
}

/* Fix for modal button focus */
.icon-select-btn:focus {
    outline: 2px solid var(--bs-primary);
    outline-offset: 2px;
}

/* Toast positioning */
.toast {
    z-index: 9999 !important;
}

/* Prevent body scroll issues */
body.modal-open {
    overflow: hidden;
}

/* Force scrollable after modal close */
body:not(.modal-open) {
    overflow: auto !important;
    height: auto !important;
    position: static !important;
}

html:not(.modal-open) {
    overflow: auto !important;
    height: auto !important;
}

/* Ensure form is always accessible */
.card {
    margin-bottom: 100px; /* Extra space at bottom */
}

/* Submit button visibility */
.btn[type="submit"] {
    position: relative;
    z-index: 1;
    margin-bottom: 20px;
}

/* Scroll to bottom button */
.scroll-to-bottom {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    background: var(--bs-primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    display: none;
}

.scroll-to-bottom:hover {
    background: var(--bs-primary);
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.scroll-to-bottom.show {
    display: block;
}

/* Loading state for buttons */
.btn.loading {
    opacity: 0.6;
    pointer-events: none;
}

.btn.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-left: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Color picker synchronization
    $('#color').on('input', function() {
        $('#color_text').val($(this).val());
        updatePreview();
    });
    
    $('#color_text').on('input', function() {
        const color = $(this).val();
        if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
            $('#color').val(color);
            updatePreview();
        }
    });

    // Update preview on input changes
    $('#name, #icon_class').on('input', updatePreview);

    // Platform button clicks
    $('.platform-btn').click(function() {
        const name = $(this).data('name');
        const icon = $(this).data('icon');
        const color = $(this).data('color');
        const placeholder = $(this).data('placeholder');
        
        $('#name').val(name);
        $('#icon_class').val(icon);
        $('#color').val(color);
        $('#color_text').val(color);
        $('#url').attr('placeholder', placeholder);
        
        updatePreview();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#name').offset().top - 100
        }, 500);
    });

    // Icon selection with improved modal handling
    $(document).on('click', '.icon-select-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const iconClass = $(this).data('icon');
        
        // Update the icon field
        $('#icon_class').val(iconClass);
        
        // Update preview
        updatePreview();
        
        // Force close modal and cleanup everything
        closeModalCompletely();
        
        // Show success message
        showToast('Icon selected: ' + iconClass, 'success');
        
        // Ensure page scrollability is restored
        restorePageScrolling();
        
        // Check scroll and show scroll button if needed
        checkScrollAfterIconSelection();
    });
    
    // Function to completely close modal and cleanup
    function closeModalCompletely() {
        // Hide modal using Bootstrap API
        const modal = bootstrap.Modal.getInstance(document.getElementById('iconModal'));
        if (modal) {
            modal.hide();
        }
        
        // Also try jQuery method as fallback
        $('#iconModal').modal('hide');
        
        // Force cleanup after a short delay
        setTimeout(function() {
            // Remove all modal backdrops
            $('.modal-backdrop').remove();
            
            // Reset body classes and styles
            $('body').removeClass('modal-open');
            $('body').css({
                'overflow': '',
                'padding-right': '',
                'margin-right': ''
            });
            
            // Reset html overflow as well
            $('html').css('overflow', '');
            
            // Hide the modal element
            $('#iconModal').removeClass('show').attr('aria-hidden', 'true');
            $('#iconModal').css('display', 'none');
            
            // Restore focus to trigger button
            $('button[data-bs-target="#iconModal"]').focus();
        }, 100);
    }
    
    // Function to restore page scrolling
    function restorePageScrolling() {
        // Force scroll restoration
        setTimeout(function() {
            $('body, html').css({
                'overflow': 'auto',
                'height': 'auto',
                'position': 'static'
            });
            
            // Remove any inline styles that might prevent scrolling
            $('body').removeAttr('style');
            $('html').removeAttr('style');
            
            // Trigger a scroll event to ensure scrollbars appear
            $(window).trigger('resize');
        }, 200);
    }

    // Icon search
    $('#iconSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.icon-select-btn').each(function() {
            const text = $(this).text().toLowerCase();
            const iconClass = $(this).data('icon').toLowerCase();
            
            if (text.includes(searchTerm) || iconClass.includes(searchTerm)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });
    
    // Modal event handling to prevent greyout issues
    $('#iconModal').on('hidden.bs.modal', function() {
        // Comprehensive cleanup
        cleanupModalCompletely();
    });
    
    $('#iconModal').on('hide.bs.modal', function() {
        // Start cleanup process
        cleanupModalCompletely();
    });
    
    $('#iconModal').on('show.bs.modal', function() {
        // Clear any existing search
        $('#iconSearch').val('');
        $('.icon-select-btn').parent().show();
        
        // Ensure body can scroll within modal
        $('body').css('overflow-y', 'auto');
    });
    
    // Comprehensive modal cleanup function
    function cleanupModalCompletely() {
        // Remove modal backdrop
        $('.modal-backdrop').remove();
        
        // Reset body classes and styles completely
        $('body').removeClass('modal-open');
        $('body').css({
            'overflow': '',
            'overflow-y': '',
            'padding-right': '',
            'margin-right': '',
            'height': '',
            'position': ''
        });
        
        // Reset html styles
        $('html').css({
            'overflow': '',
            'overflow-y': '',
            'height': '',
            'position': ''
        });
        
        // Clear search and show all icons
        $('#iconSearch').val('');
        $('.icon-select-btn').parent().show();
        
        // Force page to be scrollable
        setTimeout(function() {
            $('body').removeAttr('style');
            $('html').removeAttr('style');
            $(window).trigger('resize');
        }, 50);
    }

    function updatePreview() {
        const name = $('#name').val() || 'Platform Name';
        const iconClass = $('#icon_class').val() || 'fas fa-share-alt';
        const color = $('#color').val() || '#6c757d';
        
        $('#preview-name').text(name);
        $('#preview-icon-class').text(iconClass);
        $('#preview-icon').css('background-color', color);
        $('#preview-icon i').attr('class', iconClass + ' text-white');
    }
    
    function showToast(message, type = 'info') {
        // Simple toast notification
        const toast = $(`
            <div class="toast position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                <div class="toast-header">
                    <i class="fas fa-check-circle text-${type === 'success' ? 'success' : 'info'} me-2"></i>
                    <strong class="me-auto">Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);
        
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remove after hiding
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    // Initial preview update
    updatePreview();
    
    // Scroll to bottom functionality
    $('#scrollToBottom').on('click', function() {
        $('html, body').animate({
            scrollTop: $(document).height()
        }, 800);
    });
    
    // Show/hide scroll to bottom button
    $(window).on('scroll', function() {
        const submitButton = $('button[type="submit"]');
        if (submitButton.length) {
            const submitButtonTop = submitButton.offset().top;
            const windowBottom = $(window).scrollTop() + $(window).height();
            
            if (submitButtonTop > windowBottom + 50) {
                $('#scrollToBottom').addClass('show');
            } else {
                $('#scrollToBottom').removeClass('show');
            }
        }
    });
    
    // Force scroll check after icon selection
    function checkScrollAfterIconSelection() {
        setTimeout(function() {
            // Ensure page is scrollable
            $('body, html').css('overflow', 'auto');
            
            // Check if submit button is visible
            const submitButton = $('button[type="submit"]');
            if (submitButton.length) {
                const submitButtonTop = submitButton.offset().top;
                const windowBottom = $(window).scrollTop() + $(window).height();
                
                if (submitButtonTop > windowBottom) {
                    // Show scroll button and toast
                    $('#scrollToBottom').addClass('show');
                    showToast('Scroll down to see the submit button', 'info');
                }
            }
            
            // Trigger scroll event to update scroll button visibility
            $(window).trigger('scroll');
        }, 500);
    }
    
    // Prevent any form submission issues
    $('form').on('submit', function() {
        // Ensure no modals are open during form submission
        $('.modal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
});
</script>
@endpush
@endsection
