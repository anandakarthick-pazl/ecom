# 🚀 Adaptive Navbar System

An intelligent, responsive navbar system that automatically adjusts its size and layout based on company information and content length. Perfect for e-commerce sites where company names and branding requirements may vary.

## ✨ Features

- **🎯 Automatic Size Detection**: Dynamically adjusts navbar size based on company name length
- **📱 Mobile Responsive**: Optimized layouts for all screen sizes
- **🔄 Smooth Transitions**: Fluid animations between different states
- **🖼️ Logo Aware**: Considers logo presence in size calculations
- **📊 Performance Optimized**: Minimal performance impact with smart rendering
- **🛠️ Developer Friendly**: Easy to implement and customize

## 📏 Size Classes

The system automatically applies one of three size classes:

### 🟢 Normal (0-15 characters)
- **Height**: 80px
- **Logo Size**: 40px
- **Font Size**: 1.5rem (brand), 1rem (nav)
- **Search Width**: 300px

### 🟡 Compact (16-25 characters)
- **Height**: 60px
- **Logo Size**: 30px
- **Font Size**: 1.2rem (brand), 0.9rem (nav)
- **Search Width**: 250px

### 🔴 Extra Compact (26+ characters)
- **Height**: 50px
- **Logo Size**: 25px
- **Font Size**: 1rem (brand), 0.85rem (nav)
- **Search Width**: 200px

## 🚀 Quick Start

### Method 1: Blade Component (Recommended)

```blade
{{-- In your layout file --}}
<x-adaptive-navbar :company="$globalCompany" />
```

### Method 2: Partial Include

```blade
{{-- In your layout file --}}
@include('partials.adaptive-navbar')
```

### Method 3: Manual Implementation

```blade
{{-- Copy the navbar HTML from the component file --}}
<nav class="navbar navbar-expand-lg fixed-top navbar-modern" id="adaptive-navbar">
    <!-- Navbar content -->
</nav>
```

## ⚙️ Advanced Configuration

### Force Specific Size

```blade
<x-adaptive-navbar 
    :company="$globalCompany" 
    force-size="compact" />
```

### Custom Categories

```blade
<x-adaptive-navbar 
    :company="$globalCompany" 
    :categories="$customCategories" />
```

### Additional Attributes

```blade
<x-adaptive-navbar 
    :company="$globalCompany" 
    class="custom-navbar"
    data-theme="dark" />
```

## 🔧 Service Usage

### Analyze Company Requirements

```php
use App\Services\AdaptiveNavbarService;

$company = Company::first();
$analysis = AdaptiveNavbarService::determineNavbarSize($company);

// Returns:
// [
//     'size_class' => 'compact',
//     'company_name' => 'Your Amazing Store',
//     'name_length' => 19,
//     'word_count' => 3,
//     'has_logo' => true,
//     'reasons' => ['Company name is moderately long'],
//     'recommendations' => ['Consider adding a logo for better branding']
// ]
```

### Generate CSS Variables

```php
$variables = AdaptiveNavbarService::getCSSVariables('compact');
// Returns CSS custom properties for the specified size
```

### Check if Update Needed

```php
$needsUpdate = AdaptiveNavbarService::needsUpdate($oldCompany, $newCompany);
if ($needsUpdate) {
    // Refresh navbar configuration
}
```

## 🖥️ Command Line Tools

### Analyze Current Navbar

```bash
php artisan navbar:analyze
```

### Analyze Specific Company

```bash
php artisan navbar:analyze --company-id=1
```

This will provide:
- Company information analysis
- Size recommendations
- Performance suggestions
- Sample implementation code

## 🎨 Customization

### CSS Variables

The system uses CSS custom properties for easy customization:

```css
:root {
    --navbar-height: 80px;
    --navbar-padding: 1rem 0;
    --logo-size: 40px;
    --font-size-brand: 1.5rem;
    --font-size-nav: 1rem;
    --search-width: 300px;
}
```

### Override Specific Sizes

```css
.navbar-modern.compact {
    --navbar-height: 65px; /* Custom height */
    --logo-size: 35px;     /* Custom logo size */
}
```

### Custom Breakpoints

```javascript
window.adaptiveNavbarConfig = {
    breakpoints: {
        extraCompact: 30,  // Characters
        compact: 20,       // Characters
        normal: 0          // Characters
    }
};
```

## 📱 Mobile Optimizations

The navbar automatically adjusts for mobile devices:

- Collapsible menu for screens < 992px
- Touch-friendly button sizes (minimum 44px)
- Optimized text truncation
- Reduced spacing and padding
- Auto-hide on scroll (optional)

## 🧪 Testing

Open `test-adaptive-navbar.html` in your browser to test:

1. Different company name lengths
2. Logo presence/absence
3. Scroll behavior
4. Mobile responsiveness
5. State transitions

### Test Functions

```javascript
// Test different company names
testCompanyName('Your New Store Name');

// Toggle logo visibility
toggleLogo();

// Simulate scroll effect
simulateScroll();

// Reset to default state
resetNavbar();
```

## 🐛 Debugging

### Enable Debug Mode

Add `?debug-navbar=1` to your URL to see debug information:

```
https://yoursite.com?debug-navbar=1
```

This displays:
- Current size class
- Company name and length
- Logo status
- Categories count
- Analysis reasons

### Console Logging

The system logs configuration changes to the browser console:

```javascript
// Check current configuration
console.log(window.adaptiveNavbar.config);

// Force recalculation
window.adaptiveNavbar.recalculate();
```

## 📊 Performance Considerations

### Automatic Optimizations

- **Low FPS Detection**: Reduces animations when frame rate drops below 30fps
- **Debounced Events**: Scroll and resize events are throttled
- **CSS Containment**: Uses GPU acceleration for transforms
- **Minimal Reflows**: Changes use CSS custom properties

### Manual Optimizations

```css
/* Disable animations for low-end devices */
@media (prefers-reduced-motion: reduce) {
    .navbar-modern * {
        transition: none !important;
        animation: none !important;
    }
}
```

## 🔄 Migration Guide

### From Static Navbar

1. Replace your existing navbar with the adaptive component:

```blade
{{-- Before --}}
<nav class="navbar navbar-expand-lg">
    <!-- Static content -->
</nav>

{{-- After --}}
<x-adaptive-navbar :company="$globalCompany" />
```

2. Update your CSS to use the new classes:

```css
/* Remove old navbar styles */
/* Add adaptive navbar styles from component */
```

3. Test with different company names to ensure proper sizing.

### From Bootstrap Navbar

The adaptive navbar is fully compatible with Bootstrap 5. Simply replace your navbar component and the system will handle the rest.

## 🆘 Troubleshooting

### Common Issues

**Navbar height not updating:**
```javascript
// Force recalculation
window.adaptiveNavbar.recalculate();
```

**Text overflow not working:**
```css
/* Ensure parent container has defined width */
.navbar-brand-modern {
    max-width: 300px;
}
```

**Mobile menu not collapsing:**
```javascript
// Check Bootstrap JS is loaded
if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap JS is required');
}
```

**Scroll effects not working:**
```javascript
// Check scroll event listener
window.addEventListener('scroll', function() {
    console.log('Scroll detected');
});
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Update documentation
5. Submit a pull request

## 📄 License

This adaptive navbar system is part of your e-commerce platform and follows the same licensing terms.

## 📞 Support

For issues and questions:

1. Check the troubleshooting section
2. Use the debug mode
3. Run the analyze command
4. Check browser console for errors

---

**Made with ❤️ for adaptive, responsive web design**
