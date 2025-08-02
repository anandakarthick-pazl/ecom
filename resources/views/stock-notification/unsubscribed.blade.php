<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successfully Unsubscribed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .unsubscribe-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }
        .unsubscribe-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="unsubscribe-container">
            <div class="unsubscribe-card">
                <div class="success-icon">
                    ✅
                </div>
                
                <h2 class="text-success mb-3">Successfully Unsubscribed!</h2>
                
                <p class="text-muted mb-4">
                    You have been unsubscribed from 
                    @if($product_specific)
                        stock notifications for the specific product you selected.
                    @else
                        all stock notifications.
                    @endif
                </p>
                
                <div class="alert alert-info">
                    <strong>Email:</strong> {{ $email }}<br>
                    <strong>Notifications removed:</strong> {{ $count }}
                </div>
                
                <p class="small text-muted mb-4">
                    You will no longer receive email notifications when products come back in stock. 
                    You can always subscribe again by visiting our product pages.
                </p>
                
                <a href="{{ config('app.url') }}" class="btn btn-primary">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</body>
</html>
