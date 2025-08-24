@extends('layouts.app')

@section('title', 'Processing Payment - ' . ($globalCompany->company_name ?? 'Store'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-primary"></i>
                    </div>
                    <h2 class="mb-3">Secure Payment Processing</h2>
                    <p class="text-muted mb-4">
                        You will be redirected to Razorpay's secure payment gateway
                    </p>
                    
                    <div class="alert alert-info">
                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                        <strong>Amount to Pay:</strong> ₹{{ number_format($order->total, 2) }}
                    </div>
                    
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    
                    <p class="text-muted small">
                        <i class="fas fa-shield-alt"></i> 100% Secure Payment
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
</div>
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
    document.querySelector('.card-body').innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-success mb-3" role="status">
                <span class="visually-hidden">Verifying...</span>
            </div>
            <h3>Verifying Payment...</h3>
            <p class="text-muted">Please wait while we confirm your payment</p>
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
