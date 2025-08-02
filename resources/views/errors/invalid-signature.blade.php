<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            text-align: center;
        }
        .error-icon {
            color: #dc3545;
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
        <div class="error-container">
            <div class="error-card">
                <div class="error-icon">
                    ❌
                </div>
                
                <h2 class="text-danger mb-3">Invalid or Expired Link</h2>
                
                <p class="text-muted mb-4">
                    {{ $message ?? 'The link you clicked is invalid or has expired.' }}
                </p>
                
                <div class="alert alert-warning">
                    <strong>What can you do?</strong><br>
                    • Check if you copied the complete URL<br>
                    • The link may have expired (valid for 24 hours)<br>
                    • Contact support if you continue having issues
                </div>
                
                <a href="{{ config('app.url') }}" class="btn btn-primary">
                    Go to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>
