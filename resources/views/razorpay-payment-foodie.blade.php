@extends('layouts.app-foodie')

@section('title', 'Processing Payment - ' . ($globalCompany->company_name ?? 'Store'))

@section('content')
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div style="background: white; border-radius: 16px; padding: 3rem; box-shadow: var(--shadow-sm); text-align: center;">
                    <!-- Lock Icon -->
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                        <i class="fas fa-lock" style="font-size: 3rem; color: white;"></i>
                    </div>
                    
                    <h2 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Secure Payment Processing</h2>
                    <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">
                        You will be redirected to Razorpay's secure payment gateway
                    </p>
                    
                    <!-- Order Info -->
                    <div style="background: var(--background); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                        <div class="mb-2">
                            <strong style="color: var(--text-secondary);">Order Number:</strong>
                            <span style="font-weight: 700; color: var(--primary-color);">{{ $order->order_number }}</span>
                        </div>
                        <div>
                            <strong style="color: var(--text-secondary);">Amount to Pay:</strong>
                            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">₹{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    
                    <!-- Loading Spinner -->
                    <div class="spinner-border" style="color: var(--primary-color); width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 2rem;">
                        <i class="fas fa-shield-alt" style="color: var(--success-color);"></i> 100% Secure Payment
                    </p>
                    
                    <noscript>
                        <div class="alert alert-warning mt-3">
                            JavaScript is required for payment processing. 
                            Please enable JavaScript and refresh the page.
                        </div>
                    </noscript>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
// Auto-initiate payment on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initiating Razorpay payment for order {{ $order->id }}');
    
    // Create Razorpay order via AJAX
    fetch('{{ route("razorpay.create-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_id: {{ $order->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Configure Razorpay options
            const options = {
                key: data.key_id,
                amount: data.amount,
                currency: data.currency,
                name: '{{ $globalCompany->company_name ?? "Your Store" }}',
                description: 'Order #' + data.order_number,
                order_id: data.razorpay_order_id,
                prefill: {
                    name: data.name,
                    email: data.email || '',
                    contact: data.contact
                },
                theme: {
                    color: '{{ $globalCompany->primary_color ?? "#2c3e50" }}'
                },
                modal: {
                    ondismiss: function() {
                        // If user cancels, redirect back to checkout
                        alert('Payment cancelled. Redirecting back to checkout...');
                        window.location.href = '{{ route("checkout") }}';
                    }
                },
                handler: function(response) {
                    // Payment successful, verify it
                    verifyPayment(response);
                }
            };
            
            // Open Razorpay checkout
            const rzp = new Razorpay(options);
            rzp.open();
            
        } else {
            alert('Failed to create payment order: ' + (data.message || 'Unknown error'));
            window.location.href = '{{ route("checkout") }}';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
        window.location.href = '{{ route("checkout") }}';
    });
});

function verifyPayment(paymentResponse) {
    // Show loading state
    document.querySelector('[style*="background: white"]').innerHTML = `
        <div style="text-align: center; padding: 3rem;">
            <div class="spinner-border" style="color: var(--success-color); width: 3rem; height: 3rem; margin-bottom: 2rem;" role="status">
                <span class="visually-hidden">Verifying...</span>
            </div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Verifying Payment...</h3>
            <p style="color: var(--text-secondary);">Please wait while we confirm your payment</p>
        </div>
    `;
    
    // Verify payment
    fetch('{{ route("razorpay.verify-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            razorpay_payment_id: paymentResponse.razorpay_payment_id,
            razorpay_order_id: paymentResponse.razorpay_order_id,
            razorpay_signature: paymentResponse.razorpay_signature,
            order_id: {{ $order->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Payment verified, redirect to success page
            window.location.href = data.redirect;
        } else {
            alert('Payment verification failed: ' + (data.message || 'Unknown error'));
            window.location.href = '{{ route("checkout") }}';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Payment verification failed. Please contact support.');
        window.location.href = '{{ route("checkout") }}';
    });
}
</script>
@endpush
