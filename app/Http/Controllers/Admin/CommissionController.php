<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Traits\HasPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    use HasPagination;

    /**
     * Display a listing of commissions
     */
    public function index(Request $request)
    {
        $query = Commission::with(['posSale', 'order', 'paidBy'])
            ->currentTenant();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reference_type) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->reference_name) {
            $query->where('reference_name', 'like', '%' . $request->reference_name . '%');
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference_name', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        // Get paginated results
        $commissions = $this->applyAdminPagination($query->latest(), $request, '20');
        
        // Get pagination controls data
        $paginationControls = $this->getPaginationControlsData($request, 'admin');

        // Get summary statistics
        $stats = Commission::getSummaryStats();

        return view('admin.commissions.index', compact('commissions', 'paginationControls', 'stats'));
    }

    /**
     * Show commission details
     */
    public function show(Commission $commission)
    {
        $commission->load(['posSale', 'order', 'paidBy']);
        
        return view('admin.commissions.show', compact('commission'));
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(Request $request, Commission $commission)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        if (!$commission->canBePaid()) {
            return redirect()->back()->with('error', 'Commission cannot be marked as paid in its current status.');
        }

        try {
            DB::beginTransaction();

            $commission->markAsPaid();

            // Add payment notes if provided
            if ($request->notes) {
                $currentNotes = $commission->notes;
                $newNote = "Payment Note: {$request->notes}";
                $updatedNotes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
                $commission->update(['notes' => $updatedNotes]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Commission marked as paid successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to mark commission as paid: ' . $e->getMessage());
        }
    }

    /**
     * Mark commission as cancelled
     */
    public function markAsCancelled(Request $request, Commission $commission)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if (!$commission->canBeCancelled()) {
            return redirect()->back()->with('error', 'Commission cannot be cancelled in its current status.');
        }

        try {
            DB::beginTransaction();

            $commission->markAsCancelled();

            // Add cancellation reason
            $currentNotes = $commission->notes;
            $newNote = "Cancellation Reason: {$request->reason}";
            $updatedNotes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
            $commission->update(['notes' => $updatedNotes]);

            DB::commit();

            return redirect()->back()->with('success', 'Commission cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to cancel commission: ' . $e->getMessage());
        }
    }

    /**
     * Revert commission back to pending status
     */
    public function revertToPending(Commission $commission)
    {
        if ($commission->status !== 'paid') {
            return response()->json(['error' => 'Only paid commissions can be reverted to pending'], 422);
        }

        try {
            DB::beginTransaction();

            $commission->update([
                'status' => 'pending',
                'paid_at' => null,
                'paid_by' => null
            ]);

            // Add revert note
            $currentNotes = $commission->notes;
            $newNote = "Reverted to pending by: " . auth()->user()->name . " on " . now()->format('Y-m-d H:i:s');
            $updatedNotes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
            $commission->update(['notes' => $updatedNotes]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Commission reverted to pending status']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to revert commission: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get commission details for modal
     */
    public function getDetails(Commission $commission)
    {
        $commission->load(['posSale', 'order', 'paidBy']);
        
        return view('admin.commissions.details-modal', compact('commission'))->render();
    }

    /**
     * Bulk mark commissions as paid
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array|min:1',
            'commission_ids.*' => 'exists:commissions,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $commissions = Commission::whereIn('id', $request->commission_ids)
                ->where('status', 'pending')
                ->get();

            $updatedCount = 0;
            
            foreach ($commissions as $commission) {
                if ($commission->canBePaid()) {
                    $commission->markAsPaid();
                    
                    // Add bulk payment notes if provided
                    if ($request->notes) {
                        $currentNotes = $commission->notes;
                        $newNote = "Bulk Payment Note: {$request->notes}";
                        $updatedNotes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
                        $commission->update(['notes' => $updatedNotes]);
                    }
                    
                    $updatedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully marked {$updatedCount} commission(s) as paid",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to process bulk payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get commission analytics data
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month'); // month, quarter, year
        
        $stats = [
            'total_pending' => Commission::currentTenant()->pending()->sum('commission_amount'),
            'total_paid' => Commission::currentTenant()->paid()->sum('commission_amount'),
            'total_cancelled' => Commission::currentTenant()->cancelled()->sum('commission_amount'),
            'count_pending' => Commission::currentTenant()->pending()->count(),
            'count_paid' => Commission::currentTenant()->paid()->count(),
            'count_cancelled' => Commission::currentTenant()->cancelled()->count(),
        ];

        // Top performers
        $topPerformers = Commission::getTopPerformers(null, 10);

        // Monthly trend data
        $monthlyTrend = Commission::currentTenant()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(commission_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'stats' => $stats,
            'top_performers' => $topPerformers,
            'monthly_trend' => $monthlyTrend
        ]);
    }

    /**
     * Export commissions data
     */
    public function export(Request $request)
    {
        $query = Commission::with(['posSale', 'order'])
            ->currentTenant();

        // Apply same filters as index
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->reference_type) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->reference_name) {
            $query->where('reference_name', 'like', '%' . $request->reference_name . '%');
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->get();

        $filename = 'commissions_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($commissions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Reference Name',
                'Reference Type',
                'Commission %',
                'Base Amount',
                'Commission Amount',
                'Status',
                'Created Date',
                'Paid Date',
                'Notes'
            ]);

            // CSV data
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->reference_name,
                    $commission->reference_type,
                    $commission->commission_percentage . '%',
                    '₹' . number_format($commission->base_amount, 2),
                    '₹' . number_format($commission->commission_amount, 2),
                    ucfirst($commission->status),
                    $commission->created_at->format('Y-m-d H:i:s'),
                    $commission->paid_at ? $commission->paid_at->format('Y-m-d H:i:s') : 'Not Paid',
                    $commission->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
