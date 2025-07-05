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
     * Log admin activity
     */
    protected function logActivity($action, $resource, $resourceId = null, $details = [])
    {
        try {
            \DB::table('admin_activity_logs')->insert([
                'user_id' => auth()->id(),
                'company_id' => $this->getCurrentCompanyId(),
                'action' => $action,
                'resource' => $resource,
                'resource_id' => $resourceId,
                'details' => json_encode($details),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
}
