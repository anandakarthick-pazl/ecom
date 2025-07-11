<?php

namespace App\Traits;

use App\Models\AppSetting;
use Illuminate\Http\Request;

trait HasPagination
{
    /**
     * Get the pagination settings for admin panel
     *
     * @param Request $request
     * @param string $defaultPerPage
     * @return array
     */
    protected function getAdminPaginationSettings(Request $request, $defaultPerPage = '20')
    {
        // Check if admin pagination is enabled
        $paginationEnabled = AppSetting::get('admin_pagination_enabled', true);
        
        if (!$paginationEnabled) {
            return ['enabled' => false, 'per_page' => null];
        }
        
        // Get configured records per page
        $configuredPerPage = AppSetting::get('admin_records_per_page', $defaultPerPage);
        
        // Allow user to override per page from request (with validation)
        $requestedPerPage = $request->get('per_page');
        $allowedValues = [10, 15, 20, 25, 30, 50, 100, 200];
        
        if ($requestedPerPage && in_array((int)$requestedPerPage, $allowedValues)) {
            $perPage = (int)$requestedPerPage;
        } else {
            $perPage = (int)$configuredPerPage;
        }
        
        return [
            'enabled' => true,
            'per_page' => $perPage,
            'allowed_values' => $allowedValues,
            'default' => (int)$configuredPerPage
        ];
    }
    
    /**
     * Get the pagination settings for frontend
     *
     * @param Request $request
     * @param string $defaultPerPage
     * @return array
     */
    protected function getFrontendPaginationSettings(Request $request, $defaultPerPage = '12')
    {
        // Check if frontend pagination is enabled
        $paginationEnabled = AppSetting::get('frontend_pagination_enabled', true);
        
        if (!$paginationEnabled) {
            return ['enabled' => false, 'per_page' => null];
        }
        
        // Get configured records per page
        $configuredPerPage = AppSetting::get('frontend_records_per_page', $defaultPerPage);
        
        // Allow user to override per page from request (with validation)
        $requestedPerPage = $request->get('per_page');
        $allowedValues = [6, 9, 12, 15, 18, 24, 30];
        
        if ($requestedPerPage && in_array((int)$requestedPerPage, $allowedValues)) {
            $perPage = (int)$requestedPerPage;
        } else {
            $perPage = (int)$configuredPerPage;
        }
        
        return [
            'enabled' => true,
            'per_page' => $perPage,
            'allowed_values' => $allowedValues,
            'default' => (int)$configuredPerPage
        ];
    }
    
    /**
     * Apply pagination to a query based on admin settings
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param string $defaultPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    protected function applyAdminPagination($query, Request $request, $defaultPerPage = '20')
    {
        $paginationSettings = $this->getAdminPaginationSettings($request, $defaultPerPage);
        
        if (!$paginationSettings['enabled']) {
            // Return all records when pagination is disabled
            return $query->get();
        }
        
        $paginated = $query->paginate($paginationSettings['per_page']);
        $paginated->withQueryString();
        
        return $paginated;
    }
    
    /**
     * Apply pagination to a query based on frontend settings
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param string $defaultPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    protected function applyFrontendPagination($query, Request $request, $defaultPerPage = '12')
    {
        $paginationSettings = $this->getFrontendPaginationSettings($request, $defaultPerPage);
        
        if (!$paginationSettings['enabled']) {
            // Return all records when pagination is disabled
            return $query->get();
        }
        
        $paginated = $query->paginate($paginationSettings['per_page']);
        $paginated->withQueryString();
        
        return $paginated;
    }
    
    /**
     * Get pagination controls data for views
     *
     * @param Request $request
     * @param string $type ('admin' or 'frontend')
     * @return array
     */
    protected function getPaginationControlsData(Request $request, $type = 'admin')
    {
        $method = $type === 'admin' ? 'getAdminPaginationSettings' : 'getFrontendPaginationSettings';
        $defaultPerPage = $type === 'admin' ? '20' : '12';
        
        $settings = $this->$method($request, $defaultPerPage);
        
        // Handle cases where pagination is disabled and default key might not exist
        $defaultValue = isset($settings['default']) ? $settings['default'] : (int)$defaultPerPage;
        $currentPerPage = $settings['per_page'] ?? $defaultValue;
        
        return [
            'enabled' => $settings['enabled'],
            'current_per_page' => $currentPerPage,
            'allowed_values' => $settings['allowed_values'] ?? [],
            'default_per_page' => $defaultValue,
            'request_per_page' => $request->get('per_page')
        ];
    }
}
