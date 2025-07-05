@props(['title' => '', 'company' => null])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' - ' : '' }}{{ $company->name ?? config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link href="{{ asset('css/themes/modern-ecom-themes.css') }}" rel="stylesheet">
    
    <!-- Theme Variables -->
    @hasTheme
        <style>
            :root {
                @foreach($themeVariables as $property => $value)
                    {{ $property }}: {{ $value }};
                @endforeach
            }
        </style>
    @endhasTheme
    
    <!-- Custom Theme Styles -->
    @hasTheme
        <style>
            {!! $themeCSS !!}
        </style>
    @endhasTheme
    
    @stack('styles')
</head>
<body class="@themeClass">
    <div id="app">
        {{ $slot }}
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>
