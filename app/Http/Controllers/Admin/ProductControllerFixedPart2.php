    public function toggleStatus(Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $newStatus = !$product->is_active;
            $product->update(['is_active' => $newStatus]);
            
            $status = $newStatus ? 'activated' : 'deactivated';
            $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
            
            $message = "Product {$status} successfully!";
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $product->is_active
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product status'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update product status!');
        }
    }

    public function toggleFeatured(Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $newStatus = !$product->is_featured;
            $product->update(['is_featured' => $newStatus]);
            
            $status = $newStatus ? 'marked as featured' : 'removed from featured';
            $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
            
            $message = "Product {$status} successfully!";
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_featured' => $product->is_featured
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to toggle product featured status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update featured status'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update featured status!');
        }
    }

    public function removeImage(Request $request, Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $imageIndex = $request->input('image_index');
            $images = $product->images ?? [];
            
            if (isset($images[$imageIndex])) {
                $this->deleteFileDynamically($images[$imageIndex]);
                
                unset($images[$imageIndex]);
                $images = array_values($images);
                
                $product->update(['images' => $images]);
                
                $this->logActivity('Product image removed', $product, [
                    'name' => $product->name,
                    'image_index' => $imageIndex
                ]);
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Image removed successfully!',
                        'remaining_images' => count($images)
                    ]);
                }
                
                return redirect()->back()->with('success', 'Image removed successfully!');
            }
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found!'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Image not found!');
            
        } catch (\Exception $e) {
            Log::error('Failed to remove product image', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'image_index' => $request->input('image_index')
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove image: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to remove image!');
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'products' => 'required|array',
            'products.*' => 'exists:products,id'
        ]);

        try {
            DB::beginTransaction();
            
            $products = Product::whereIn('id', $request->products)->get();
            
            foreach ($products as $product) {
                $this->validateTenantOwnership($product);
            }

            $count = $products->count();

            switch ($request->action) {
                case 'activate':
                    Product::whereIn('id', $request->products)->update(['is_active' => true]);
                    $message = "{$count} products activated successfully!";
                    break;
                    
                case 'deactivate':
                    Product::whereIn('id', $request->products)->update(['is_active' => false]);
                    $message = "{$count} products deactivated successfully!";
                    break;
                    
                case 'delete':
                    $hasOrders = $products->filter(function ($product) {
                        return $product->orderItems()->count() > 0;
                    });

                    if ($hasOrders->count() > 0) {
                        throw new \Exception('Cannot delete products with order history!');
                    }

                    foreach ($products as $product) {
                        if ($product->featured_image) {
                            $this->deleteFileDynamically($product->featured_image);
                        }
                        if ($product->images) {
                            foreach ($product->images as $image) {
                                $this->deleteFileDynamically($image);
                            }
                        }
                    }

                    Product::whereIn('id', $request->products)->delete();
                    $message = "{$count} products deleted successfully!";
                    break;
            }

            DB::commit();
            
            $this->logActivity("Bulk action: {$request->action}", null, [
                'action' => $request->action,
                'count' => $count,
                'product_ids' => $request->products
            ]);

            return $this->handleSuccess($message);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk action failed', [
                'action' => $request->action,
                'products' => $request->products,
                'error' => $e->getMessage()
            ]);
            return $this->handleError($e->getMessage());
        }
    }

    /**
     * FIXED BULK UPLOAD - Now handles Excel and CSV properly
     */
    public function showBulkUpload()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.bulk-upload', compact('categories'));
    }

    public function downloadTemplate()
    {
        $filename = 'product_upload_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'name', 'description', 'short_description', 'price', 'discount_price', 'cost_price',
                'stock', 'sku', 'barcode', 'code', 'category_name', 'weight', 'weight_unit',
                'tax_percentage', 'low_stock_threshold', 'is_active', 'is_featured', 'sort_order',
                'meta_title', 'meta_description', 'meta_keywords'
            ]);
            
            fputcsv($file, [
                'Sample Product Name',
                'This is a detailed description of the product with all features and benefits.',
                'Short product description for listings.',
                '99.99', '79.99', '50.00', '100', 'SKU001', '1234567890123', 'PROD001',
                'Electronics', '1.5', 'kg', '18', '10', '1', '1', '1',
                'Sample Product - Buy Online',
                'Buy sample product online at best price.',
                'sample, product, online, buy'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * COMPLETELY REWRITTEN BULK UPLOAD PROCESSING
     */
    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:csv,xlsx,xls',
            'update_existing' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $file = $request->file('file');
            $updateExisting = $request->boolean('update_existing');
            
            Log::info('Starting bulk upload', [
                'original_filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'update_existing' => $updateExisting
            ]);
            
            // Parse the file
            $data = $this->parseUploadFile($file);
            
            if (empty($data)) {
                throw new \Exception('No valid data found in the uploaded file.');
            }
            
            Log::info('File parsed successfully', [
                'rows_found' => count($data),
                'first_row_keys' => !empty($data) ? array_keys($data[0]) : []
            ]);
            
            // Process the data
            $result = $this->processProductDataBulk($data, $updateExisting);
            
            DB::commit();
            
            // Log the upload
            $this->logBulkUpload($file, $result);
            
            $message = "Bulk upload completed! ";
            $message .= "Created: {$result['created']}, ";
            $message .= "Updated: {$result['updated']}, ";
            $message .= "Errors: {$result['errors']}";
            
            if ($result['errors'] > 0) {
                session()->flash('upload_errors', $result['error_details']);
                return redirect()->back()->with('warning', $message);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Bulk upload failed', [
                'error' => $e->getMessage(),
                'file' => isset($file) ? $file->getClientOriginalName() : 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * FIXED FILE PARSING - Handles both CSV and Excel properly
     */
    private function parseUploadFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension === 'csv') {
            return $this->parseCsvFile($file);
        } else {
            return $this->parseExcelFile($file);
        }
    }

    /**
     * Parse CSV file directly from uploaded file
     */
    private function parseCsvFile($file)
    {
        $data = [];
        $headers = [];
        
        try {
            $handle = fopen($file->getPathname(), 'r');
            
            if ($handle === false) {
                throw new \Exception('Could not open CSV file for reading.');
            }
            
            // Read header row
            $headers = fgetcsv($handle);
            
            if ($headers === false || empty($headers)) {
                fclose($handle);
                throw new \Exception('Could not read CSV headers or file is empty.');
            }
            
            // Clean headers
            $headers = array_map('trim', $headers);
            
            // Read data rows
            $rowCount = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $rowCount++;
                
                if (count($row) === count($headers)) {
                    $rowData = array_combine($headers, array_map('trim', $row));
                    
                    // Skip empty rows
                    if (!empty(array_filter($rowData))) {
                        $data[] = $rowData;
                    }
                } else {
                    Log::warning("CSV row {$rowCount} has mismatched column count", [
                        'expected' => count($headers),
                        'actual' => count($row)
                    ]);
                }
            }
            
            fclose($handle);
            
            Log::info('CSV file parsed successfully', [
                'headers' => count($headers),
                'rows' => count($data)
            ]);
            
        } catch (\Exception $e) {
            if (isset($handle)) {
                fclose($handle);
            }
            throw new \Exception('Failed to parse CSV file: ' . $e->getMessage());
        }
        
        return $data;
    }

    /**
     * Parse Excel file using Laravel Excel (if available)
     */
    private function parseExcelFile($file)
    {
        try {
            // Check if Laravel Excel is available
            if (!class_exists('Maatwebsite\Excel\Facades\Excel')) {
                throw new \Exception('Laravel Excel package is required for .xlsx/.xls files. Please install maatwebsite/excel or use CSV format.');
            }
            
            $collection = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
            
            if (empty($collection) || empty($collection[0])) {
                return [];
            }
            
            $rows = $collection[0]; // Get first sheet
            
            if (empty($rows)) {
                return [];
            }
            
            $headers = array_shift($rows); // Remove and get header row
            $headers = array_map('trim', $headers); // Clean headers
            
            $data = [];
            foreach ($rows as $row) {
                if (count($row) === count($headers)) {
                    $rowData = array_combine($headers, array_map('trim', $row));
                    
                    // Skip empty rows
                    if (!empty(array_filter($rowData))) {
                        $data[] = $rowData;
                    }
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to parse Excel file: ' . $e->getMessage());
        }
    }

    /**
     * FIXED BULK DATA PROCESSING
     */
    private function processProductDataBulk($data, $updateExisting = false)
    {
        $result = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'error_details' => []
        ];
        
        // Cache categories for performance
        $categories = Category::active()->pluck('id', 'name')->toArray();
        
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because array is 0-indexed and we skip header
            
            try {
                // Validate required fields
                if (empty($row['name']) || empty($row['description']) || empty($row['price']) || empty($row['category_name'])) {
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Missing required fields (name, description, price, category_name)";
                    continue;
                }
                
                // Find category
                $categoryId = $categories[$row['category_name']] ?? null;
                if (!$categoryId) {
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Category '{$row['category_name']}' not found";
                    continue;
                }
                
                // Prepare product data
                $productData = [
                    'name' => trim($row['name']),
                    'description' => trim($row['description']),
                    'short_description' => trim($row['short_description'] ?? ''),
                    'price' => (float) $row['price'],
                    'discount_price' => !empty($row['discount_price']) ? (float) $row['discount_price'] : null,
                    'cost_price' => !empty($row['cost_price']) ? (float) $row['cost_price'] : null,
                    'stock' => (int) ($row['stock'] ?? 0),
                    'sku' => trim($row['sku'] ?? ''),
                    'barcode' => trim($row['barcode'] ?? ''),
                    'code' => trim($row['code'] ?? ''),
                    'category_id' => $categoryId,
                    'weight' => !empty($row['weight']) ? (float) $row['weight'] : null,
                    'weight_unit' => trim($row['weight_unit'] ?? 'kg'),
                    'tax_percentage' => (float) ($row['tax_percentage'] ?? 0),
                    'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 5),
                    'is_active' => !empty($row['is_active']) ? (bool) $row['is_active'] : true,
                    'is_featured' => !empty($row['is_featured']) ? (bool) $row['is_featured'] : false,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'meta_title' => trim($row['meta_title'] ?? ''),
                    'meta_description' => trim($row['meta_description'] ?? ''),
                    'meta_keywords' => trim($row['meta_keywords'] ?? ''),
                    'company_id' => $this->getCurrentCompanyId(),
                    'branch_id' => session('selected_branch_id')
                ];
                
                // Check for existing product
                $existingProduct = null;
                if (!empty($productData['sku'])) {
                    $existingProduct = Product::where('sku', $productData['sku'])
                        ->where('company_id', $productData['company_id'])
                        ->first();
                }
                
                if (!$existingProduct) {
                    $existingProduct = Product::where('name', $productData['name'])
                        ->where('company_id', $productData['company_id'])
                        ->first();
                }
                
                if ($existingProduct && $updateExisting) {
                    // Update existing product
                    $existingProduct->update($productData);
                    $result['updated']++;
                    
                    Log::info("Product updated from bulk upload", [
                        'row' => $rowNumber,
                        'product_id' => $existingProduct->id,
                        'name' => $productData['name']
                    ]);
                    
                } elseif (!$existingProduct) {
                    // Create new product
                    $product = Product::create($productData);
                    $result['created']++;
                    
                    Log::info("Product created from bulk upload", [
                        'row' => $rowNumber,
                        'product_id' => $product->id,
                        'name' => $productData['name']
                    ]);
                    
                } else {
                    // Product exists but update is not enabled
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Product '{$productData['name']}' already exists (enable update to modify)";
                }
                
            } catch (\Exception $e) {
                $result['errors']++;
                $result['error_details'][] = "Row $rowNumber: {$e->getMessage()}";
                
                Log::error("Bulk upload row processing failed", [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                    'data' => $row
                ]);
            }
        }
        
        return $result;
    }

    /**
     * Store file content to a path
     */
    protected function storeFile($content, $path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            $directory = dirname($fullPath);
            
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            return file_put_contents($fullPath, $content) !== false;
        } catch (\Exception $e) {
            Log::error('Failed to store file', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function logBulkUpload($file, $result)
    {
        try {
            if (class_exists('App\Models\UploadLog')) {
                \App\Models\UploadLog::create([
                    'file_name' => 'bulk_upload_' . time() . '.' . $file->getClientOriginalExtension(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => '',
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'storage_type' => 'local',
                    'upload_type' => 'product_bulk',
                    'uploaded_by' => auth()->id(),
                    'meta_data' => $result,
                    'company_id' => session('selected_company_id')
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log bulk upload: ' . $e->getMessage());
        }
    }

    private function exportProducts($products)
    {
        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'SKU', 'Category', 'Price', 'Discount Price', 'Stock',
                'Weight', 'Weight Unit', 'Tax Percentage', 'Status', 'Featured',
                'Description', 'Short Description', 'Meta Title', 'Meta Description',
                'Created At', 'Updated At'
            ]);
            
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku ?? '',
                    $product->category->name ?? '',
                    $product->price,
                    $product->discount_price ?? '',
                    $product->stock,
                    $product->weight ?? '',
                    $product->weight_unit ?? '',
                    $product->tax_percentage ?? '',
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->is_featured ? 'Yes' : 'No',
                    strip_tags($product->description ?? ''),
                    strip_tags($product->short_description ?? ''),
                    $product->meta_title ?? '',
                    $product->meta_description ?? '',
                    $product->created_at->format('Y-m-d H:i:s'),
                    $product->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadHistory()
    {
        try {
            $uploads = \App\Models\UploadLog::where('upload_type', 'product_bulk')
                ->with('uploader')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            Log::warning('Upload history unavailable: ' . $e->getMessage());
            $uploads = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }
            
        return view('admin.products.upload-history', compact('uploads'));
    }
}
