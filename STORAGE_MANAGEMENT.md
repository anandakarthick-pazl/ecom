# Storage Management System

This comprehensive storage management system provides seamless switching between local file storage and AWS S3 cloud storage for your e-commerce application.

## ✨ Features

- **Dual Storage Support**: Switch between local and S3 storage
- **Dynamic URL Generation**: Automatic URL generation based on current storage type
- **File Category Management**: Organize files by categories (products, banners, categories, general)
- **Sync Operations**: Sync files between local and S3 storage
- **Backup & Cleanup**: Automated backup and cleanup operations
- **Admin Interface**: Complete admin panel for storage management
- **Fallback Images**: Automatic fallback to placeholder images
- **Command Line Tools**: Artisan commands for storage operations

## 🚀 Quick Setup

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Install AWS SDK (if using S3)
```bash
composer require aws/aws-sdk-php
```

### 3. Configure Environment
Update your `.env` file:
```env
# Storage Configuration
STORAGE_TYPE=local  # or 's3'

# AWS S3 Configuration (if using S3)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name
AWS_URL=https://your-bucket.s3.amazonaws.com
```

### 4. Create Storage Link (for local storage)
```bash
php artisan storage:link
```

### 5. Update Composer Autoload
```bash
composer dump-autoload
```

## 📁 File Organization

Files are organized in the following structure:
```
storage/app/public/
├── products/
│   ├── 2024/
│   │   └── january/
│   └── featured/
├── banners/
├── categories/
└── general/
```

## 🎛️ Admin Interface

Access the storage management interface through the Super Admin panel:

1. **Storage Dashboard**: Overview of storage usage and configuration
2. **Local Storage**: Manage local files and directories  
3. **S3 Storage**: Manage AWS S3 files and buckets
4. **File Upload**: Upload files to either storage type
5. **Sync Operations**: Sync files between storage types

### Available Actions:
- View storage statistics
- Upload files with category organization
- Create directories
- Delete files
- Sync between local and S3
- Test storage connections
- Backup storage
- Cleanup old files

## 🔧 Helper Functions

The system provides global helper functions for easy file handling:

### Basic Usage
```php
// Get file URL based on current storage type
$url = storage_url('products/image.jpg');

// Get image URL with fallback
$imageUrl = image_url('products/image.jpg', 'products');

// Upload file to current storage
$result = upload_to_storage($uploadedFile, 'products', '2024/january');

// Delete file from storage
delete_from_storage('products/image.jpg');

// Check if file exists
$exists = file_exists_in_storage('products/image.jpg');
```

### Advanced Usage
```php
// Get fallback image URL
$placeholder = fallback_image_url('products');

// Format file size
$formattedSize = format_file_size(1024); // Returns "1 KB"

// Check current storage type
$storageType = get_storage_type(); // Returns 'local' or 's3'

// Check if S3 is enabled
$s3Enabled = is_s3_enabled();
```

## 🏗️ Model Integration

Use the `DynamicStorageUrl` trait in your models:

```php
use App\Traits\DynamicStorageUrl;

class Product extends Model
{
    use DynamicStorageUrl;
    
    // Get product image URL with automatic storage detection
    public function getImageUrlAttribute()
    {
        return $this->getImageUrlWithFallback($this->featured_image, 'products');
    }
}
```

### Product Model Enhancements

The Product model now includes:
- `featured_image_url`: Get featured image URL with fallback
- `image_urls`: Get all product image URLs
- `image_url`: Get first available image URL
- `addImage()`: Add image to product
- `removeImage()`: Remove image from product
- `getAllImages()`: Get all product images
- `image_count`: Get total image count

## ⚡ Command Line Operations

Use the storage management command for bulk operations:

### Sync Files
```bash
# Sync from local to S3
php artisan storage:manage sync --source=local --target=s3

# Sync specific category
php artisan storage:manage sync --source=local --target=s3 --category=products
```

### Backup Storage
```bash
# Backup local storage
php artisan storage:manage backup --source=local

# Backup S3 storage
php artisan storage:manage backup --source=s3
```

### Cleanup Old Files
```bash
# Preview cleanup (dry run)
php artisan storage:manage cleanup --source=local --days=30 --dry-run

# Actually delete old files
php artisan storage:manage cleanup --source=local --days=30 --force
```

### Test Connections
```bash
# Test both storage types
php artisan storage:manage test

# Test specific storage
php artisan storage:manage test --source=s3
```

### View Statistics
```bash
php artisan storage:manage stats
```

### Migrate Storage
```bash
# Migrate from local to S3 (copies files and updates config)
php artisan storage:manage migrate --source=local --target=s3 --force
```

## 🗄️ Database Integration

The system tracks all uploaded files in the `storage_files` table:

```php
use App\Models\StorageFile;

// Get files by category
$productImages = StorageFile::category('products')->get();

// Get files by storage type
$s3Files = StorageFile::storageType('s3')->get();

// Get only images
$images = StorageFile::images()->get();

// Get category statistics
$stats = StorageFile::getCategoryStats();

// Get storage statistics
$storageStats = StorageFile::getStorageStats();
```

## 🔄 Switching Storage Types

### Via Admin Interface
1. Go to Super Admin → Storage Management
2. Update storage configuration
3. Test connection
4. Optionally sync existing files

### Via Environment
1. Update `STORAGE_TYPE` in `.env`
2. Configure AWS credentials (if switching to S3)
3. Run: `php artisan config:clear`
4. Optionally sync files: `php artisan storage:manage sync --source=local --target=s3`

### Via Command Line
```bash
# Full migration with file sync and config update
php artisan storage:manage migrate --source=local --target=s3 --force
```

## 📸 Image Categories

The system supports four main categories:

1. **Products**: Product images and galleries
2. **Banners**: Homepage and promotional banners
3. **Categories**: Category thumbnails and images
4. **General**: Miscellaneous files and documents

Each category has its own fallback placeholder image.

## 🛡️ Security & Best Practices

### File Validation
- File type validation based on MIME type
- File size limits (configurable)
- Malicious file detection
- Sanitized file names

### S3 Security
- Use IAM roles with minimal permissions
- Enable bucket versioning
- Configure CORS policies
- Use private buckets with signed URLs for sensitive files

### Local Storage
- Files stored outside web root
- Symlink to public directory
- Proper file permissions
- Regular cleanup of temporary files

## 🐛 Troubleshooting

### Common Issues

1. **S3 Connection Failed**
   - Check AWS credentials
   - Verify bucket exists and is accessible
   - Check region configuration
   - Test with: `php artisan storage:manage test --source=s3`

2. **Local Storage Issues**
   - Run: `php artisan storage:link`
   - Check file permissions
   - Verify storage directory exists

3. **Images Not Displaying**
   - Check storage configuration
   - Verify file paths in database
   - Test file existence: `file_exists_in_storage()`

4. **Sync Issues**
   - Check source and target configurations
   - Verify permissions on both storage types
   - Use dry-run mode first

### Debug Commands
```bash
# Check storage statistics
php artisan storage:manage stats

# Test all connections
php artisan storage:manage test

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📊 Monitoring & Analytics

### Storage Statistics
- Total files and storage usage
- Files by category breakdown
- Storage type distribution
- Growth trends over time

### Performance Monitoring
- File access patterns
- Upload/download speeds
- Error rates and types
- Storage costs (for S3)

## 🔮 Future Enhancements

- **CDN Integration**: CloudFront/CloudFlare support
- **Image Optimization**: Automatic compression and resizing
- **Multiple S3 Buckets**: Region-specific storage
- **File Versioning**: Track file changes and history
- **Advanced Analytics**: Detailed usage reporting
- **API Integration**: RESTful API for external applications

## 📄 License

This storage management system is part of the main application and follows the same licensing terms.
