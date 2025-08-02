/**
 * Stock Notification System - Frontend Integration
 * Easy-to-use component for product pages
 */

class StockNotificationSystem {
    constructor(options = {}) {
        this.options = {
            notifyButtonClass: '.notify-when-available',
            outOfStockClass: '.out-of-stock',
            modalId: '#stockNotificationModal',
            toastContainer: '#toast-container',
            subscribeUrl: '/stock-notifications/subscribe',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            ...options
        };
        
        this.init();
    }

    init() {
        this.createToastContainer();
        this.bindEvents();
        this.checkProductStockStatus();
    }

    bindEvents() {
        // Handle notify button clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches(this.options.notifyButtonClass) || 
                e.target.closest(this.options.notifyButtonClass)) {
                e.preventDefault();
                const button = e.target.closest(this.options.notifyButtonClass);
                const productId = button.dataset.productId;
                const productName = button.dataset.productName;
                
                this.showNotificationModal(productId, productName);
            }
        });

        // Handle form submission
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'stockNotificationForm') {
                e.preventDefault();
                this.handleFormSubmission(e.target);
            }
        });
    }

    async showNotificationModal(productId, productName) {
        try {
            // Check if modal already exists
            let modal = document.querySelector(this.options.modalId);
            
            if (!modal) {
                // Create modal dynamically
                modal = await this.createModal(productId, productName);
                document.body.appendChild(modal);
            } else {
                // Update existing modal with product data
                this.updateModalProduct(modal, productId, productName);
            }
            
            // Show modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
        } catch (error) {
            console.error('Error showing notification modal:', error);
            this.showToast('Error loading notification form', 'error');
        }
    }

    async createModal(productId, productName) {
        const modalHtml = `
            <div class="modal fade" id="stockNotificationModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-bell me-2"></i>Notify Me When Available
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <div class="out-of-stock-icon mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-muted product-name">${productName} is currently out of stock</h6>
                                <p class="small text-muted mb-0">Get notified instantly when it's available again!</p>
                            </div>

                            <form id="stockNotificationForm" data-product-id="${productId}">
                                <input type="hidden" name="product_id" value="${productId}">
                                
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
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
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
        `;

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = modalHtml;
        return tempDiv.firstElementChild;
    }

    updateModalProduct(modal, productId, productName) {
        modal.querySelector('.product-name').textContent = `${productName} is currently out of stock`;
        modal.querySelector('input[name="product_id"]').value = productId;
        modal.querySelector('#stockNotificationForm').dataset.productId = productId;
        
        // Reset form
        modal.querySelector('#stockNotificationForm').reset();
        this.clearValidationErrors(modal);
    }

    async handleFormSubmission(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Clear previous validation
        this.clearValidationErrors(form.closest('.modal'));
        
        // Validate form
        if (!this.validateForm(data, form)) {
            return;
        }
        
        // Show loading state
        this.setLoadingState(form, true);
        
        try {
            const response = await fetch(this.options.subscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showToast(result.message, 'success');
                form.reset();
                
                // Close modal after brief delay
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    modal?.hide();
                }, 2000);
                
                // Update UI to show subscription status
                this.updateProductUI(data.product_id, 'subscribed');
                
            } else {
                this.showToast(result.message, 'error');
                if (result.errors) {
                    this.displayValidationErrors(result.errors, form.closest('.modal'));
                }
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            this.showToast('Something went wrong. Please try again.', 'error');
        } finally {
            this.setLoadingState(form, false);
        }
    }

    validateForm(data, form) {
        let isValid = true;
        const modal = form.closest('.modal');
        
        // Check if at least one contact method is provided
        if (!data.customer_email && !data.customer_mobile) {
            this.showContactRequirement(modal);
            isValid = false;
        }
        
        // Validate email format if provided
        if (data.customer_email && !this.isValidEmail(data.customer_email)) {
            this.showFieldError('customer_email', 'Please enter a valid email address', modal);
            isValid = false;
        }
        
        // Validate mobile number if provided
        if (data.customer_mobile && !this.isValidMobile(data.customer_mobile)) {
            this.showFieldError('customer_mobile', 'Please enter a valid 10-digit mobile number', modal);
            isValid = false;
        }
        
        return isValid;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidMobile(mobile) {
        return /^[0-9]{10}$/.test(mobile);
    }

    setLoadingState(form, loading) {
        const btn = form.querySelector('#subscribeBtn');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        
        if (loading) {
            btn.disabled = true;
            btnText.textContent = 'Setting up notification...';
            spinner.classList.remove('d-none');
        } else {
            btn.disabled = false;
            btnText.textContent = 'Notify Me When Available';
            spinner.classList.add('d-none');
        }
    }

    showContactRequirement(modal) {
        modal.querySelector('#contactRequirement').classList.remove('d-none');
    }

    showFieldError(fieldName, message, modal) {
        const field = modal.querySelector(`#${fieldName}`);
        const feedback = field.closest('.col-md-6, .col-12').querySelector('.invalid-feedback');
        
        field.classList.add('is-invalid');
        if (feedback) feedback.textContent = message;
    }

    clearValidationErrors(modal) {
        modal.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        modal.querySelector('#contactRequirement')?.classList.add('d-none');
    }

    displayValidationErrors(errors, modal) {
        Object.keys(errors).forEach(fieldName => {
            if (errors[fieldName].length > 0) {
                this.showFieldError(fieldName, errors[fieldName][0], modal);
            }
        });
    }

    updateProductUI(productId, status) {
        // Update notify buttons to show subscription status
        const notifyButtons = document.querySelectorAll(`[data-product-id="${productId}"]`);
        notifyButtons.forEach(button => {
            if (status === 'subscribed') {
                button.innerHTML = '<i class="fas fa-check-circle me-2"></i>Notification Set';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                button.disabled = true;
            }
        });
    }

    checkProductStockStatus() {
        // Add visual indicators for out-of-stock products
        document.querySelectorAll(this.options.outOfStockClass).forEach(element => {
            const productCard = element.closest('.product-card, .card');
            if (productCard) {
                productCard.classList.add('out-of-stock-product');
            }
        });
    }

    createToastContainer() {
        if (!document.querySelector(this.options.toastContainer)) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
    }

    showToast(message, type = 'info') {
        const toastContainer = document.querySelector(this.options.toastContainer);
        if (!toastContainer) return;

        const iconMap = {
            success: 'fas fa-check-circle text-success',
            error: 'fas fa-exclamation-circle text-danger',
            info: 'fas fa-info-circle text-info',
            warning: 'fas fa-exclamation-triangle text-warning'
        };

        const bgMap = {
            success: 'bg-success-subtle',
            error: 'bg-danger-subtle', 
            info: 'bg-info-subtle',
            warning: 'bg-warning-subtle'
        };

        const toastId = `toast-${Date.now()}`;
        const toastHtml = `
            <div id="${toastId}" class="toast ${bgMap[type] || bgMap.info}" role="alert">
                <div class="toast-header">
                    <i class="${iconMap[type] || iconMap.info} me-2"></i>
                    <strong class="me-auto">Stock Notification</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'success' ? 5000 : 8000
        });
        
        toast.show();
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Utility method for manual integration
    static createNotifyButton(productId, productName, isOutOfStock = true) {
        if (!isOutOfStock) return '';
        
        return `
            <button type="button" 
                    class="btn btn-outline-primary notify-when-available" 
                    data-product-id="${productId}" 
                    data-product-name="${productName}">
                <i class="fas fa-bell me-2"></i>
                Notify When Available
            </button>
        `;
    }

    // Utility method to replace add to cart buttons for out of stock products
    static replaceAddToCartButtons() {
        document.querySelectorAll('[data-out-of-stock="true"]').forEach(container => {
            const addToCartBtn = container.querySelector('.add-to-cart, .btn-add-cart');
            const productId = container.dataset.productId;
            const productName = container.dataset.productName;
            
            if (addToCartBtn && productId) {
                addToCartBtn.outerHTML = StockNotificationSystem.createNotifyButton(productId, productName);
            }
        });
    }
}

// Auto-initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the stock notification system
    window.stockNotificationSystem = new StockNotificationSystem();
    
    // Replace add to cart buttons for out of stock products
    StockNotificationSystem.replaceAddToCartButtons();
});

// Export for manual usage
window.StockNotificationSystem = StockNotificationSystem;
