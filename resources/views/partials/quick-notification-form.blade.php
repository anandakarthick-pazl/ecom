<!-- Quick Stock Notification Form -->
<div id="stockNotificationModal" class="modal fade" tabindex="-1" aria-labelledby="stockNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="stockNotificationModalLabel">
                    <i class="fas fa-bell me-2"></i>Notify Me When Available
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="out-of-stock-icon mb-2">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <h6 class="text-muted">{{ $product->name ?? 'This product' }} is currently out of stock</h6>
                    <p class="small text-muted mb-0">Get notified instantly when it's available again!</p>
                </div>

                <form id="stockNotificationForm" data-product-id="{{ $product->id ?? '' }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="customer_name" class="form-label">Your Name (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter your name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="your@email.com">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_mobile" class="form-label">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="customer_mobile" name="customer_mobile" placeholder="9876543210" pattern="[0-9]{10}">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">How would you like to be notified?</label>
                        <div class="notification-options">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="notification_type" id="notify_both" value="both" checked>
                                <label class="form-check-label" for="notify_both">
                                    <i class="fas fa-envelope me-1"></i>
                                    <i class="fas fa-mobile-alt me-1"></i>
                                    Both Email & WhatsApp (Recommended)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="notification_type" id="notify_email" value="email">
                                <label class="form-check-label" for="notify_email">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email Only
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="notification_type" id="notify_whatsapp" value="whatsapp">
                                <label class="form-check-label" for="notify_whatsapp">
                                    <i class="fab fa-whatsapp me-1"></i>
                                    WhatsApp Only
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-none" id="contactRequirement">
                        <small><i class="fas fa-info-circle me-1"></i>Please provide at least one contact method to receive notifications.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="subscribeBtn">
                            <i class="fas fa-bell me-2"></i>
                            <span class="btn-text">Notify Me When Available</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        We respect your privacy. Unsubscribe anytime.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-options .form-check {
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
}

.notification-options .form-check:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
}

.notification-options .form-check-input:checked + .form-check-label {
    color: #0d6efd;
    font-weight: 500;
}

.out-of-stock-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
    transform: translateY(-1px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('stockNotificationForm');
    const submitBtn = document.getElementById('subscribeBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const spinner = submitBtn.querySelector('.spinner-border');
    const modal = new bootstrap.Modal(document.getElementById('stockNotificationModal'));

    // Form validation and submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous validation states
        clearValidationErrors();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Client-side validation
        if (!validateForm(data)) {
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            const response = await fetch('{{ route("stock-notification.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessMessage(result.message);
                form.reset();
                setTimeout(() => modal.hide(), 2000);
            } else {
                showErrorMessage(result.message);
                if (result.errors) {
                    displayValidationErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorMessage('Something went wrong. Please try again.');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time notification type updates
    document.querySelectorAll('input[name="notification_type"]').forEach(radio => {
        radio.addEventListener('change', updateContactRequirement);
    });

    document.getElementById('customer_email').addEventListener('input', updateContactRequirement);
    document.getElementById('customer_mobile').addEventListener('input', updateContactRequirement);

    function validateForm(data) {
        let isValid = true;
        
        // Check if at least one contact method is provided
        if (!data.customer_email && !data.customer_mobile) {
            showContactRequirement();
            isValid = false;
        }
        
        // Validate email format if provided
        if (data.customer_email && !isValidEmail(data.customer_email)) {
            showFieldError('customer_email', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate mobile number if provided
        if (data.customer_mobile && !isValidMobile(data.customer_mobile)) {
            showFieldError('customer_mobile', 'Please enter a valid 10-digit mobile number');
            isValid = false;
        }
        
        return isValid;
    }

    function updateContactRequirement() {
        const email = document.getElementById('customer_email').value;
        const mobile = document.getElementById('customer_mobile').value;
        const notificationType = document.querySelector('input[name="notification_type"]:checked').value;
        
        const requirementAlert = document.getElementById('contactRequirement');
        
        if (!email && !mobile) {
            requirementAlert.classList.remove('d-none');
        } else {
            requirementAlert.classList.add('d-none');
        }
        
        // Auto-adjust notification type based on available contact info
        if (email && !mobile && notificationType !== 'email') {
            document.getElementById('notify_email').checked = true;
        } else if (!email && mobile && notificationType !== 'whatsapp') {
            document.getElementById('notify_whatsapp').checked = true;
        } else if (email && mobile && (notificationType === 'email' || notificationType === 'whatsapp')) {
            document.getElementById('notify_both').checked = true;
        }
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidMobile(mobile) {
        return /^[0-9]{10}$/.test(mobile);
    }

    function setLoadingState(loading) {
        if (loading) {
            submitBtn.disabled = true;
            btnText.textContent = 'Setting up notification...';
            spinner.classList.remove('d-none');
        } else {
            submitBtn.disabled = false;
            btnText.textContent = 'Notify Me When Available';
            spinner.classList.add('d-none');
        }
    }

    function showSuccessMessage(message) {
        // You can integrate with your existing toast/notification system
        alert(message); // Replace with better notification
    }

    function showErrorMessage(message) {
        // You can integrate with your existing toast/notification system
        alert(message); // Replace with better notification
    }

    function showContactRequirement() {
        document.getElementById('contactRequirement').classList.remove('d-none');
    }

    function showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const feedback = field.parentNode.parentNode.querySelector('.invalid-feedback');
        
        field.classList.add('is-invalid');
        feedback.textContent = message;
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        document.getElementById('contactRequirement').classList.add('d-none');
    }

    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            if (errors[fieldName].length > 0) {
                showFieldError(fieldName, errors[fieldName][0]);
            }
        });
    }
});
</script>
