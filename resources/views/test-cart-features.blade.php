<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cart Features</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .feature-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .status-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .status-success {
            background: #10b981;
        }
        .status-warning {
            background: #f59e0b;
        }
        .status-error {
            background: #ef4444;
        }
        
        /* Test Floating Cart */
        .test-floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .test-floating-cart:hover {
            background: #10b981;
            transform: scale(1.1);
        }
        .test-cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
        
        /* Test Product Card */
        .test-product-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            background: white;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
        }
        .qty-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #e5e7eb;
            background: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .qty-btn:hover {
            background: #2563eb;
            color: white;
        }
        .qty-input {
            width: 60px;
            height: 35px;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        .add-to-cart-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .add-to-cart-btn:hover {
            background: #10b981;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🛒 Cart Features Test Page</h1>
        
        <!-- Status Check Section -->
        <div class="test-section">
            <h2>📊 Feature Status Check</h2>
            
            <div class="feature-status">
                <div class="status-icon status-success">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>Floating Cart Button:</strong>
                    <span id="floating-cart-status">Look at bottom-right corner</span>
                </div>
            </div>
            
            <div class="feature-status">
                <div class="status-icon status-success">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>Quantity Selector:</strong>
                    <span>See demo below</span>
                </div>
            </div>
            
            <div class="feature-status">
                <div class="status-icon status-success">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>Add to Cart Function:</strong>
                    <span id="cart-function-status">Ready</span>
                </div>
            </div>
            
            <div class="feature-status">
                <div class="status-icon status-success">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong>Cart Count Update:</strong>
                    <span id="count-update-status">Dynamic</span>
                </div>
            </div>
        </div>
        
        <!-- Demo Product Card -->
        <div class="test-section">
            <h2>🎯 Demo Product Card</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="test-product-card">
                        <img src="https://via.placeholder.com/200" class="img-fluid mb-3" alt="Product">
                        <h4>Sample Product</h4>
                        <p class="text-muted">This is a test product to demonstrate cart features</p>
                        <p class="h5 text-primary">₹299.00</p>
                        
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="decrementQty()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="qty-input" id="test-qty" value="1" min="1" max="10">
                            <button class="qty-btn" onclick="incrementQty()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <button class="add-to-cart-btn" onclick="testAddToCart()">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test Results -->
        <div class="test-section">
            <h2>🔍 Test Results</h2>
            <div id="test-results">
                <p>Click the "Add to Cart" button above to test functionality...</p>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="test-section">
            <h2>📝 Instructions</h2>
            <ol>
                <li>Check if you can see the floating cart button at the bottom-right corner</li>
                <li>Try adjusting the quantity using + and - buttons</li>
                <li>Click "Add to Cart" to test the add to cart functionality</li>
                <li>Watch the cart count update in the floating button</li>
                <li>Navigate to <a href="/shop">Shop Page</a> to see it in action</li>
            </ol>
        </div>
    </div>
    
    <!-- Floating Cart Button -->
    <div class="test-floating-cart" onclick="goToCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="test-cart-count" id="test-cart-count">0</span>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        let cartCount = 0;
        
        function incrementQty() {
            const input = document.getElementById('test-qty');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.getAttribute('max'));
            
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        }
        
        function decrementQty() {
            const input = document.getElementById('test-qty');
            const currentValue = parseInt(input.value);
            const minValue = parseInt(input.getAttribute('min')) || 1;
            
            if (currentValue > minValue) {
                input.value = currentValue - 1;
            }
        }
        
        function testAddToCart() {
            const quantity = document.getElementById('test-qty').value;
            cartCount += parseInt(quantity);
            
            // Update cart count
            document.getElementById('test-cart-count').textContent = cartCount;
            
            // Show success message
            const resultsDiv = document.getElementById('test-results');
            resultsDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    Successfully added ${quantity} item(s) to cart! 
                    Total items in cart: ${cartCount}
                </div>
            `;
            
            // Reset quantity to 1
            document.getElementById('test-qty').value = 1;
            
            // Animate floating cart
            const floatingCart = document.querySelector('.test-floating-cart');
            floatingCart.style.transform = 'scale(1.3)';
            setTimeout(() => {
                floatingCart.style.transform = 'scale(1)';
            }, 300);
        }
        
        function goToCart() {
            window.location.href = '/cart';
        }
        
        // Check if on actual shop page
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            if (currentUrl.includes('greenvalleyherbs.local')) {
                document.getElementById('floating-cart-status').innerHTML = 
                    '<span class="text-success">✅ Active on greenvalleyherbs.local</span>';
            }
        });
    </script>
</body>
</html>
