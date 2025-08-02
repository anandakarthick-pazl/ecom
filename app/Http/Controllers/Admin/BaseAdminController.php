<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class BaseAdminController extends Controller
{
    public function __construct()
    {
        // Remove problematic middleware call - middleware is applied via routes
        // $this->middleware(['auth', 'company.context']);
        
        // Share common data with all admin views
        View::composer('admin.*', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'currentUser' => auth()->user(),
                    'currentCompany' => $this->getCurrentCompany(),
                ]);
            }
        });
    }

    /**
     * Get the current company
     */
    protected function getCurrentCompany()
    {
        return session('selected_company') ?? auth()->user()->company;
    }

    /**
     * Get the current company ID
     */
    protected function getCurrentCompanyId()
    {
        return session('selected_company_id') ?? auth()->user()->company_id;
    }

    /**
     * Check if user has permission
     */
    protected function checkPermission($permission)
    {
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Check if user has any of the given permissions
     */
    protected function checkAnyPermission($permissions)
    {
        if (!auth()->user()->hasAnyPermission($permissions)) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Check if user has all of the given permissions
     */
    protected function checkAllPermissions($permissions)
    {
        if (!auth()->user()->hasAllPermissions($permissions)) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Apply tenant scope to query
     */
    protected function applyTenantScope($query)
    {
        if (method_exists($query->getModel(), 'scopeCurrentTenant')) {
            return $query->currentTenant();
        }
        
        if ($query->getModel()->getFillable() && in_array('company_id', $query->getModel()->getFillable())) {
            return $query->where('company_id', $this->getCurrentCompanyId());
        }
        
        return $query;
    }

    /**
     * Get paginated results with tenant scope
     */
    protected function getPaginatedResults($query, $perPage = 15)
    {
        return $this->applyTenantScope($query)->latest()->paginate($perPage);
    }

    /**
     * Get success message for CRUD operations
     */
    protected function getSuccessMessage($action, $resource)
    {
        $messages = [
            'created' => "{$resource} created successfully!",
            'updated' => "{$resource} updated successfully!",
            'deleted' => "{$resource} deleted successfully!",
            'restored' => "{$resource} restored successfully!",
            'archived' => "{$resource} archived successfully!",
        ];

        return $messages[$action] ?? "{$resource} {$action} successfully!";
    }

    /**
     * Get error message for CRUD operations
     */
    protected function getErrorMessage($action, $resource, $error = null)
    {
        $baseMessage = "Error {$action} {$resource}";
        return $error ? "{$baseMessage}: {$error}" : $baseMessage;
    }

    /**
     * Handle successful response
     */
    protected function successResponse($message, $redirectRoute = null, $data = [])
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        }

        $response = redirect($redirectRoute ?? back())->with('success', $message);
        
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $response->with($key, $value);
            }
        }
        
        return $response;
    }

    /**
     * Handle error response
     */
    protected function errorResponse($message, $redirectRoute = null, $errors = [])
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], 422);
        }

        $response = redirect($redirectRoute ?? back())->with('error', $message);
        
        if (!empty($errors)) {
            $response->withErrors($errors);
        }
        
        return $response;
    }

    /**
     * Validate unique field within company scope
     */
    protected function validateUniqueInCompany($request, $field, $table, $excludeId = null, $column = null)
    {
        $column = $column ?? $field;
        $companyId = $this->getCurrentCompanyId();
        
        $rules = [
            $field => [
                'required',
                function ($attribute, $value, $fail) use ($table, $column, $companyId, $excludeId) {
                    $query = \DB::table($table)
                              ->where($column, $value)
                              ->where('company_id', $companyId);
                    
                    if ($excludeId) {
                        $query->where('id', '!=', $excludeId);
                    }
                    
                    if ($query->exists()) {
                        $fail("The {$attribute} has already been taken within your company.");
                    }
                }
            ]
        ];

        return $request->validate($rules);
    }

    /**
     * Log admin activity - Enhanced version supporting both old and new signatures
     */
    protected function logActivity($action, $resourceOrModel = null, $resourceIdOrDetails = null, $details = [])
    {
        try {
            $logData = [
                'user_id' => auth()->id(),
                'company_id' => $this->getCurrentCompanyId(),
                'action' => $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Handle different method signatures
            if (is_object($resourceOrModel)) {
                // New signature: logActivity($action, $model, $details)
                $logData['resource_type'] = get_class($resourceOrModel);
                $logData['resource_id'] = $resourceOrModel->id ?? null;
                $logData['resource'] = class_basename($resourceOrModel);
                
                if (is_array($resourceIdOrDetails)) {
                    $logData['details'] = json_encode($resourceIdOrDetails);
                }
            } else {
                // Old signature: logActivity($action, $resource, $resourceId, $details)
                $logData['resource'] = $resourceOrModel;
                $logData['resource_id'] = $resourceIdOrDetails;
                
                if (!empty($details)) {
                    $logData['details'] = json_encode($details);
                }
            }

            // Check if admin_activity_logs table exists
            if (\Schema::hasTable('admin_activity_logs')) {
                \DB::table('admin_activity_logs')->insert($logData);
            } else {
                // Fallback to Laravel's default log
                \Log::info("Admin Activity: {$action}", $logData);
            }
        } catch (\Exception $e) {
            // Log the error but don't break the main functionality
            \Log::error('Failed to log admin activity: ' . $e->getMessage());
        }
    }

    /**
     * Check rate limiting for sensitive operations
     */
    protected function checkRateLimit($key, $maxAttempts = 5, $decayMinutes = 60)
    {
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            abort(429, 'Too many attempts. Please try again later.');
        }
        
        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
    }

    /**
     * Clear rate limiting
     */
    protected function clearRateLimit($key)
    {
        cache()->forget($key);
    }

    /**
     * Get common validation rules
     */
    protected function getCommonValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get pagination settings
     */
    protected function getPaginationLimit()
    {
        return request('per_page', 15);
    }

    /**
     * Apply search filters
     */
    protected function applySearchFilters($query, $searchableFields, $search = null)
    {
        $search = $search ?? request('search');
        
        if ($search && !empty($searchableFields)) {
            $query->where(function ($q) use ($searchableFields, $search) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }
        
        return $query;
    }

    /**
     * Export data to CSV
     */
    protected function exportToCsv($data, $filename, $headers = [])
    {
        $csvData = [];
        
        // Add headers if provided
        if (!empty($headers)) {
            $csvData[] = $headers;
        }
        
        // Add data rows
        foreach ($data as $row) {
            if (is_object($row)) {
                $csvData[] = $row->toArray();
            } else {
                $csvData[] = $row;
            }
        }
        
        $output = fopen('php://temp', 'w');
        
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Generate unique code for resources
     */
    protected function generateUniqueCode($table, $prefix = '', $length = 6, $column = 'code')
    {
        do {
            $code = $prefix . strtoupper(\Str::random($length));
            $exists = \DB::table($table)
                        ->where($column, $code)
                        ->where('company_id', $this->getCurrentCompanyId())
                        ->exists();
        } while ($exists);
        
        return $code;
    }

    /**
     * Handle file upload
     */
    protected function handleFileUpload($file, $directory = 'uploads', $disk = 'public')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }

    /**
     * Delete file
     */
    protected function deleteFile($path, $disk = 'public')
    {
        if ($path && \Storage::disk($disk)->exists($path)) {
            \Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Validate that a model belongs to the current tenant
     */
    protected function validateTenantOwnership($model)
    {
        $currentCompanyId = $this->getCurrentCompanyId();
        
        if (!$currentCompanyId) {
            abort(403, 'Company context not found');
        }
        
        if (!$model || $model->company_id != $currentCompanyId) {
            abort(404, 'Resource not found or access denied');
        }
        
        return true;
    }

    /**
     * Get tenant-scoped unique validation rule
     */
    protected function getTenantUniqueRule($table, $column, $ignoreId = null)
    {
        $companyId = $this->getCurrentCompanyId();
        
        // Build the unique rule properly
        if ($ignoreId) {
            // For updates: unique:table,column,ignore_id,ignore_column,where_column,where_value
            $rule = "unique:{$table},{$column},{$ignoreId},id,company_id,{$companyId}";
        } else {
            // For creates: unique:table,column,NULL,id,where_column,where_value
            $rule = "unique:{$table},{$column},NULL,id,company_id,{$companyId}";
        }
        
        return $rule;
    }

    /**
     * Get tenant-scoped exists validation rule
     */
    protected function getTenantExistsRule($table, $column = 'id')
    {
        $companyId = $this->getCurrentCompanyId();
        
        return function ($attribute, $value, $fail) use ($table, $column, $companyId) {
            if ($value) {
                $exists = \DB::table($table)
                           ->where($column, $value)
                           ->where('company_id', $companyId)
                           ->exists();
                
                if (!$exists) {
                    $fail("The selected {$attribute} is invalid for your company.");
                }
            }
        };
    }

    /**
     * Handle successful response with redirect
     */
    protected function handleSuccess($message, $redirectRoute = null, $data = [])
    {
        // Clean message to prevent header issues
        $message = trim(preg_replace('/[\r\n]+/', ' ', $message));
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        }

        $redirect = $redirectRoute ? route($redirectRoute) : back();
        return redirect($redirect)->with('success', $message);
    }

    /**
     * Handle error response with redirect
     */
    protected function handleError($message, $redirectRoute = null, $errors = [])
    {
        // Clean message to prevent header issues
        $message = trim(preg_replace('/[\r\n]+/', ' ', $message));
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], 422);
        }

        $redirect = $redirectRoute ? route($redirectRoute) : back();
        return redirect($redirect)->with('error', $message);
    }
    /**
     * Ensure result is always a paginator to prevent links() method errors
     * 
     * @param mixed $result
     * @param \Illuminate\Http\Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function ensurePaginator($result, $request, $perPage = 20)
    {
        // If it's already a paginator, return as is
        if (method_exists($result, 'links')) {
            return $result;
        }
        
        // If it's a Collection, convert to manual paginator
        if ($result instanceof \Illuminate\Support\Collection) {
            return $this->createManualPaginator($result, $request, $perPage);
        }
        
        // If it's something else, throw error
        throw new \Exception('Invalid result type for pagination');
    }
    
    /**
     * Create a manual paginator from a collection
     * 
     * @param \Illuminate\Support\Collection $items
     * @param \Illuminate\Http\Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function createManualPaginator($items, $request, $perPage = 20)
    {
        $page = (int) $request->get('page', 1);
        $total = $items->count();
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
    }

}
