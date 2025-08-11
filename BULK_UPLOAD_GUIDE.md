# Bulk Upload Products Guide

## Overview
The bulk upload functionality allows you to add or update multiple products at once using CSV or Excel files. This feature is designed to save time when managing large product catalogs.

## How to Access

1. **Navigate to Products Page**
   - Go to Admin Dashboard
   - Click on "Products" in the sidebar

2. **Access Bulk Upload**
   - On the products page, locate the "Bulk Upload" dropdown button in the top right
   - Click on it to see the options:
     - **Upload Products** - Upload your product file
     - **Download Template** - Get the CSV template with all required columns
     - **Upload History** - View previous upload logs

## Step-by-Step Guide

### Step 1: Download Template
1. Click "Bulk Upload" → "Download Template"
2. This downloads a CSV file with all the required and optional columns
3. The template includes a sample row to guide you

### Step 2: Prepare Your Data
1. Open the downloaded template in Excel or Google Sheets
2. Fill in your product data following the template format
3. **Required Fields:**
   - `name` - Product name
   - `description` - Full product description  
   - `price` - Product price (numeric)
   - `stock` - Available quantity (numeric)
   - `category_name` - Must match existing category names exactly
   - `tax_percentage` - Tax rate (numeric, e.g., 18 for 18% GST)

4. **Optional Fields:**
   - `short_description` - Brief product description
   - `discount_price` - Discounted price (must be less than price)
   - `cost_price` - Cost price for profit calculations
   - `sku` - Unique product code (recommended for tracking)
   - `barcode` - Product barcode
   - `weight` & `weight_unit` - Product weight (kg, g, lb, oz)
   - `is_active` - 1 for active, 0 for inactive
   - `is_featured` - 1 for featured, 0 for normal
   - `meta_title`, `meta_description`, `meta_keywords` - SEO fields
   - `featured_image_url` - URL to main product image
   - `additional_images` - Comma-separated URLs for gallery images

### Step 3: Upload Your File
1. Click "Bulk Upload" → "Upload Products"
2. Select your prepared CSV or Excel file
3. Choose whether to "Update Existing Products" (if checked, products with matching SKU/name will be updated)
4. Click "Upload Products"
5. Wait for processing (may take several minutes for large files)

### Step 4: Review Results
After upload completion, you'll see:
- Number of products created
- Number of products updated  
- Number of errors (if any)
- Detailed error messages for troubleshooting

## Supported File Formats
- **CSV** (.csv) - Comma-separated values
- **Excel** (.xlsx, .xls) - Microsoft Excel files
- **Maximum file size:** 10MB

## Important Notes

### Categories
- Category names in your CSV must exactly match existing categories
- Available categories are shown on the bulk upload page
- Create categories first if they don't exist

### Product Matching
- Products are matched by SKU (if provided) or name
- Duplicate products will be skipped unless "Update Existing" is checked
- Use unique SKUs to avoid conflicts

### Image Handling
- Product images can be uploaded via URLs in the CSV
- System will automatically download and store images from provided URLs
- Images should be accessible public URLs
- Supported formats: JPG, PNG, WebP

### Best Practices
1. **Start Small:** Test with a few products first
2. **Backup Data:** Always backup existing products before bulk operations
3. **Validate Data:** Check your CSV for formatting errors before upload
4. **Use SKUs:** Always include unique SKUs for better product management
5. **Check Categories:** Ensure all category names match exactly

## Troubleshooting

### Common Errors
1. **"Category not found"** - The category name doesn't exist
2. **"Missing required fields"** - Required columns are empty
3. **"Product already exists"** - Duplicate detected (enable update to modify)
4. **"Invalid price"** - Price must be numeric and greater than 0
5. **"Image download failed"** - Image URL is invalid or inaccessible

### File Format Issues
- Ensure CSV uses UTF-8 encoding
- Remove any special characters that might cause parsing errors
- Check that numbers are formatted correctly (no currency symbols)

### Performance Tips
- Keep files under 1000 products for optimal performance
- Large files may take several minutes to process
- Don't close the browser tab during upload

## Upload History
- View all previous uploads in "Upload History"
- See statistics for each upload (created/updated/errors)
- Review error details for failed uploads

## Technical Details
- Supports CSV and Excel file parsing
- Automatic image downloading and storage
- Tenant-aware (multi-company support)
- Transaction-safe processing
- Detailed error logging

## Support
If you encounter issues:
1. Check the upload history for error details
2. Verify your CSV format matches the template
3. Ensure all required fields are filled
4. Contact support with specific error messages

---

**Pro Tip:** Use the "Download Template" feature regularly to ensure you have the latest column structure, as new fields may be added over time.
