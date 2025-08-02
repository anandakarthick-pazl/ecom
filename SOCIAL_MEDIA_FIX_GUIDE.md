# SOCIAL MEDIA ROUTES FIX GUIDE

## Issue
**Route [admin.social-media.index] not defined**

## Root Cause
The social media routes were defined in a separate file (`routes/social_media_direct.php`) but were not being included in the main route registration in `routes/web.php`.

## Fixes Applied

### 1. Added Social Media Routes to Main Admin Routes Group
**File**: `routes/web.php`

**Added**:
```php
// Social Media Management
Route::resource('social-media', SocialMediaController::class);
Route::patch('social-media/{socialMediaLink}/toggle-status', [SocialMediaController::class, 'toggleStatus'])->name('social-media.toggle-status');
Route::post('social-media/update-sort-order', [SocialMediaController::class, 'updateSortOrder'])->name('social-media.update-sort-order');
Route::post('social-media/quick-add', [SocialMediaController::class, 'quickAdd'])->name('social-media.quick-add');
```

### 2. Added Controller Import
**File**: `routes/web.php`

**Added**:
```php
use App\Http\Controllers\Admin\SocialMediaController;
```

### 3. Added Frontend API Route
**File**: `routes/web.php`

**Added**:
```php
// Social Media Links API (for frontend display)
Route::get('/api/social-media-links', [SocialMediaController::class, 'getActiveLinks'])->name('api.social-media-links');
```

## Verification Steps

### Step 1: Clear All Caches
Run the provided cache clearing script:
```bash
# Windows
clear_caches.bat

# Linux/Mac
bash clear_caches.sh

# Or manually:
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear
```

### Step 2: Verify Routes Are Registered
```bash
php artisan route:list --name=social-media
```

You should see routes like:
- `admin.social-media.index`
- `admin.social-media.create`
- `admin.social-media.store`
- `admin.social-media.show`
- `admin.social-media.edit`
- `admin.social-media.update`
- `admin.social-media.destroy`

### Step 3: Check Database Table
Run the debug script:
```bash
php artisan tinker
```
Then paste the contents of `debug_social_media.php`

### Step 4: Test the Route
Visit: `http://greenvalleyherbs.local:8000/admin/social-media`

## Available Routes

After the fix, these routes are now available:

### Admin Routes (Protected)
- `GET /admin/social-media` - List all social media links
- `GET /admin/social-media/create` - Show create form
- `POST /admin/social-media` - Store new social media link
- `GET /admin/social-media/{id}` - Show specific link
- `GET /admin/social-media/{id}/edit` - Show edit form
- `PUT/PATCH /admin/social-media/{id}` - Update social media link
- `DELETE /admin/social-media/{id}` - Delete social media link

### Additional Routes
- `PATCH /admin/social-media/{id}/toggle-status` - Toggle active status
- `POST /admin/social-media/update-sort-order` - Update sort order
- `POST /admin/social-media/quick-add` - Quick add social media link

### Frontend API Route
- `GET /api/social-media-links` - Get active social media links for frontend display

## Files Involved

### ✅ Existing Files (Already Present)
- `app/Http/Controllers/Admin/SocialMediaController.php` - Controller
- `app/Models/SocialMediaLink.php` - Model
- `database/migrations/2025_01_22_120000_create_social_media_links_table.php` - Migration
- `resources/views/admin/social-media/index.blade.php` - Index view
- `resources/views/admin/social-media/create.blade.php` - Create view
- `resources/views/admin/social-media/edit.blade.php` - Edit view
- `app/Traits/BelongsToTenantEnhanced.php` - Multi-tenant trait

### ✅ Updated Files
- `routes/web.php` - Added route registrations and imports

## Features Available

### Admin Panel Features
- ✅ List all social media links
- ✅ Add new social media platforms
- ✅ Edit existing links
- ✅ Delete social media links
- ✅ Toggle active/inactive status
- ✅ Reorder social media links
- ✅ Predefined platform templates (Facebook, Twitter, Instagram, etc.)
- ✅ Custom colors and icons
- ✅ Multi-tenant support (company-specific)

### Predefined Platforms
- Facebook
- Twitter/X
- Instagram
- LinkedIn
- YouTube
- WhatsApp
- Pinterest
- TikTok
- Snapchat
- Discord
- Telegram
- And more...

## Troubleshooting

### If Routes Still Not Working
1. **Check route cache**: `php artisan route:list | grep social-media`
2. **Check imports**: Ensure `SocialMediaController` is imported in `routes/web.php`
3. **Check middleware**: Ensure you're logged in as admin with proper permissions
4. **Check company context**: Ensure company context middleware is working

### If Database Issues
1. **Run migrations**: `php artisan migrate`
2. **Check table exists**: `php artisan tinker` then `Schema::hasTable('social_media_links')`
3. **Check company exists**: Ensure you have a company record for your domain

### If Controller Issues
1. **Check namespace**: `App\Http\Controllers\Admin\SocialMediaController`
2. **Check class exists**: `php artisan tinker` then `class_exists('App\\Http\\Controllers\\Admin\\SocialMediaController')`

## Success Indicators

✅ **Route works**: Visiting `/admin/social-media` shows the social media management page  
✅ **No errors**: No "Route not defined" or "Class not found" errors  
✅ **CRUD operations**: Can create, read, update, and delete social media links  
✅ **Multi-tenant**: Links are company-specific  
✅ **Frontend API**: `/api/social-media-links` returns JSON data  

## Next Steps

Once routes are working:
1. **Add Social Media Links**: Use the admin panel to add your company's social media profiles
2. **Customize Display**: Update frontend templates to show social media icons
3. **Test Frontend**: Verify social media links display correctly on the store frontend
4. **Configure Permissions**: Set up user roles and permissions for social media management

The social media management system is now fully integrated and ready to use!
