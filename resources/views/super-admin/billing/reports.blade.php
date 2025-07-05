@extends('super-admin.layouts.app')

@section('title', 'Billing Reports')
@section('page-title', 'Billing Reports')

@section('content')
<div class="row">
    <!-- Revenue Summary Cards -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Total Revenue</div>
                                    <div class="h4 mb-0">${{ number_format($totalRevenue, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">This Month</div>
                                    <div class="h4 mb-0">${{ number_format($monthlyRevenue, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Pending</div>
                                    <div class="h4 mb-0">${{ number_format($pendingAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Overdue</div>
                                    <div class="h4 mb-0">${{ number_format($overdueAmount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="col-12 mb-4">
        <div class="row">
            <!-- Monthly Revenue Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Monthly Revenue - {{ now()->year }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyRevenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Revenue Distribution -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Revenue Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Reports -->
    <div class="col-12">
        <div class="row">
            <!-- Top Performing Companies -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>Top Performing Companies
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $topCompanies = App\Models\SuperAdmin\Billing::selectRaw('company_id, SUM(amount) as total_revenue')
                                ->where('status', 'paid')
                                ->groupBy('company_id')
                                ->orderByDesc('total_revenue')
                                ->with('company')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @if($topCompanies->count() > 0)
                            @foreach($topCompanies as $index => $billing)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <strong>{{ $billing->company->name }}</strong>
                                            <br><small class="text-muted">{{ $billing->company->domain }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">${{ number_format($billing->total_revenue, 2) }}</div>
                                        <small class="text-muted">Total Revenue</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No revenue data available yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Recent Payments -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Recent Payments
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $recentPayments = App\Models\SuperAdmin\Billing::where('status', 'paid')
                                ->whereNotNull('paid_at')
                                ->with(['company', 'package'])
                                ->orderByDesc('paid_at')
                                ->take(5)
                                ->get();
                        @endphp
                        
                        @if($recentPayments->count() > 0)
                            @foreach($recentPayments as $payment)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <strong>{{ $payment->company->name }}</strong>
                                        <br><small class="text-muted">{{ $payment->package->name }} - {{ $payment->invoice_number }}</small>
                                        <br><small class="text-success">
                                            <i class="fas fa-check me-1"></i>{{ $payment->paid_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">{{ $payment->formatted_amount }}</div>
                                        @if($payment->payment_method)
                                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No recent payments found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Package Revenue Analysis -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>Package Revenue Analysis
                </h6>
            </div>
            <div class="card-body">
                @php
                    $packageRevenue = App\Models\SuperAdmin\Billing::selectRaw('package_id, SUM(amount) as total_revenue, COUNT(*) as total_billings')
                        ->where('status', 'paid')
                        ->groupBy('package_id')
                        ->with('package')
                        ->orderByDesc('total_revenue')
                        ->get();
                @endphp
                
                @if($packageRevenue->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Package</th>
                                    <th>Price</th>
                                    <th>Total Billings</th>
                                    <th>Total Revenue</th>
                                    <th>Avg. Revenue/Billing</th>
                                    <th>Revenue Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packageRevenue as $revenue)
                                    <tr>
                                        <td>
                                            <strong>{{ $revenue->package->name }}</strong>
                                            <br><small class="text-muted">{{ ucfirst($revenue->package->billing_cycle) }}</small>
                                        </td>
                                        <td>${{ number_format($revenue->package->price, 2) }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $revenue->total_billings }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">${{ number_format($revenue->total_revenue, 2) }}</strong>
                                        </td>
                                        <td>${{ number_format($revenue->total_revenue / $revenue->total_billings, 2) }}</td>
                                        <td>
                                            @php
                                                $percentage = $totalRevenue > 0 ? ($revenue->total_revenue / $totalRevenue) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small>{{ number_format($percentage, 1) }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Package Revenue Data</h5>
                        <p class="text-muted">Revenue data will appear here once payments are processed.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Monthly Revenue Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    
    // Prepare data for all 12 months
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const revenueData = new Array(12).fill(0);
    
    monthlyData.forEach(item => {
        revenueData[item.month - 1] = item.total;
    });
    
    new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Monthly Revenue',
                data: revenueData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Revenue Distribution Chart
    const distributionCtx = document.getElementById('revenueDistributionChart').getContext('2d');
    
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Overdue'],
            datasets: [{
                data: [{{ $totalRevenue }}, {{ $pendingAmount }}, {{ $overdueAmount }}],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
