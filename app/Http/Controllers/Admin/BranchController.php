<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchController extends BaseAdminController
{
    public function index(Request $request)
    {
        $companyId = $this->getCurrentCompanyId();
        
        $query = Branch::where('company_id', $companyId)
            ->with(['users' => function($q) {
                $q->whereIn('role', ['admin', 'manager']);
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('manager_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'code', 'status', 'city', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $branches = $query->paginate(10)->appends($request->query());

        // Statistics
        $stats = [
            'total' => Branch::where('company_id', $companyId)->count(),
            'active' => Branch::where('company_id', $companyId)->where('status', 'active')->count(),
            'inactive' => Branch::where('company_id', $companyId)->where('status', 'inactive')->count(),
        ];

        return view('admin.branches.index', compact('branches', 'stats'));
    }

    public function show(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        $branch->load([
            'users',
            'orders' => function($q) {
                $q->latest()->limit(10);
            },
            'customers',
            'products' => function($q) {
                $q->active();
            }
        ]);

        // Branch statistics
        $stats = [
            'total_orders' => $branch->orders()->count(),
            'monthly_orders' => $branch->orders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_customers' => $branch->customers()->count(),
            'total_products' => $branch->products()->count(),
            'total_revenue' => $branch->orders()->sum('total'),
            'monthly_revenue' => $branch->orders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'total_users' => $branch->users()->count(),
        ];

        return view('admin.branches.show', compact('branch', 'stats'));
    }

    public function create()
    {
        $companyId = $this->getCurrentCompanyId();
        $nextCode = Branch::generateBranchCode($companyId);
        
        return view('admin.branches.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $companyId = $this->getCurrentCompanyId();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string', 
                'max:50',
                Rule::unique('branches')->where('company_id', $companyId)
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $branchData = $validator->validated();
        $branchData['company_id'] = $companyId;

        $branch = Branch::create($branchData);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);
        
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorize('update', $branch);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string', 
                'max:50',
                Rule::unique('branches')->where('company_id', $branch->company_id)->ignore($branch->id)
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'manager_name' => 'nullable|string|max:255',
            'manager_email' => 'nullable|email|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $branch->update($validator->validated());

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);
        
        // Check if branch has any related data
        $hasUsers = $branch->users()->exists();
        $hasOrders = $branch->orders()->exists();
        $hasProducts = $branch->products()->exists();

        if ($hasUsers || $hasOrders || $hasProducts) {
            return redirect()->back()
                ->with('error', 'Cannot delete branch as it has associated users, orders, or products. Please transfer or remove them first.');
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    public function toggleStatus(Branch $branch)
    {
        $this->authorize('update', $branch);
        
        $branch->update([
            'status' => $branch->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $branch->status,
            'message' => 'Branch status updated successfully.'
        ]);
    }

    public function assignUsers(Request $request, Branch $branch)
    {
        $this->authorize('update', $branch);
        
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        // Get users that belong to the same company
        $users = User::whereIn('id', $request->user_ids)
            ->where('company_id', $branch->company_id)
            ->get();

        // Update users to assign them to this branch
        $users->each(function($user) use ($branch) {
            $user->update(['branch_id' => $branch->id]);
        });

        return redirect()->back()
            ->with('success', 'Users assigned to branch successfully.');
    }

    public function removeUser(Request $request, Branch $branch, User $user)
    {
        $this->authorize('update', $branch);
        
        if ($user->company_id !== $branch->company_id) {
            return redirect()->back()
                ->with('error', 'User does not belong to this company.');
        }

        $user->update(['branch_id' => null]);

        return redirect()->back()
            ->with('success', 'User removed from branch successfully.');
    }

    public function getAvailableUsers(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        // Get users from the same company that are not assigned to any branch
        $availableUsers = User::where('company_id', $branch->company_id)
            ->whereNull('branch_id')
            ->select('id', 'name', 'email', 'role')
            ->get();

        return response()->json($availableUsers);
    }

    public function getBranchStats(Branch $branch)
    {
        $this->authorize('view', $branch);
        
        $stats = [
            'orders_count' => $branch->orders()->count(),
            'customers_count' => $branch->customers()->count(),
            'products_count' => $branch->products()->count(),
            'users_count' => $branch->users()->count(),
            'monthly_orders' => $branch->orders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'monthly_revenue' => $branch->orders()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'total_revenue' => $branch->orders()->sum('total'),
            'top_products' => $branch->products()
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'order_items_count']),
        ];

        return response()->json($stats);
    }

    // Helper method to check authorization
    private function authorize($action, $branch = null)
    {
        if ($branch && $branch->company_id !== $this->getCurrentCompanyId()) {
            abort(403, 'Unauthorized access to branch.');
        }
    }
}
