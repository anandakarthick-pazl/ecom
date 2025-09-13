@extends('admin.layouts.app')

@section('title', 'Estimate Details - #' . $estimate->estimate_number)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar text-primary"></i> 
            Estimate Details - #{{ $estimate->estimate_number }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.estimates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Estimates
            </a>
            @if($estimate->status === 'draft')
                <a href="{{ route('admin.estimates.edit', $estimate) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
            @endif
            {{-- <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print me-2"></i>Print
            </button> --}}
            <a href="{{ route('admin.estimates.download', $estimate) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Estimate Details -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Estimate Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary">From:</h5>
                            <p class="mb-1"><strong>{{ $estimate->company->company_name ?? 'Company Name' }}</strong></p>
                            @if($estimate->company->company_address ?? null)
                                <p class="mb-1">{{ $estimate->company->company_address }}</p>
                            @endif
                            @if($estimate->company->company_phone ?? null)
                                <p class="mb-1">Phone: {{ $estimate->company->company_phone }}</p>
                            @endif
                            @if($estimate->company->company_email ?? null)
                                <p class="mb-1">Email: {{ $estimate->company->company_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">To:</h5>
                            @if($estimate->supplier ?? null)
                                <p class="mb-1"><strong>{{ $estimate->supplier->name }}</strong></p>
                                @if($estimate->supplier->address ?? null)
                                    <p class="mb-1">{{ $estimate->supplier->address }}</p>
                                @endif
                                @if($estimate->supplier->phone ?? null)
                                    <p class="mb-1">Phone: {{ $estimate->supplier->phone }}</p>
                                @endif
                                @if($estimate->supplier->email ?? null)
                                    <p class="mb-1">Email: {{ $estimate->supplier->email }}</p>
                                @endif
                            @else
                                <p class="text-muted">No supplier information</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="fw-bold text-muted">Estimate Number:</label>
                            <p class="text-primary">#{{ $estimate->estimate_number }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted">Date:</label>
                            <p>{{ $estimate->estimate_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted">Valid Until:</label>
                            <p>{{ $estimate->valid_until->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold text-muted">Status:</label>
                            <p>
                                @if($estimate->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($estimate->status === 'sent')
                                    <span class="badge bg-info">Sent</span>
                                @elseif($estimate->status === 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @elseif($estimate->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($estimate->status === 'expired')
                                    <span class="badge bg-warning">Expired</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estimate->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($item->product ?? null)
                                            <strong>{{ $item->product->name }}</strong>
                                            @if($item->product->sku ?? null)
                                                <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Product not found</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->description ?: '-' }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">₹{{ number_format($estimate->subtotal, 2) }}</td>
                                </tr>
                                @if(($estimate->tax_amount ?? 0) > 0)
                                <tr>
                                    <td colspan="5" class="text-end">Tax ({{ $estimate->tax_percentage ?? 0 }}%):</td>
                                    <td class="text-end">₹{{ number_format($estimate->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if(($estimate->discount_amount ?? 0) > 0)
                                <tr>
                                    <td colspan="5" class="text-end">Discount:</td>
                                    <td class="text-end text-danger">-₹{{ number_format($estimate->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-dark">
                                    <td colspan="5" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold">₹{{ number_format($estimate->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($estimate->notes ?? null)
                    <div class="mt-4">
                        <h6 class="text-primary">Notes:</h6>
                        <p class="border p-3 bg-light">{{ $estimate->notes }}</p>
                    </div>
                    @endif

                    @if($estimate->terms_conditions ?? null)
                    <div class="mt-4">
                        <h6 class="text-primary">Terms & Conditions:</h6>
                        <p class="border p-3 bg-light">{{ $estimate->terms_conditions }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Estimate Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Current Status:</label>
                        <p>
                            @if($estimate->status === 'draft')
                                <span class="badge bg-secondary fs-6">Draft</span>
                            @elseif($estimate->status === 'sent')
                                <span class="badge bg-info fs-6">Sent</span>
                            @elseif($estimate->status === 'accepted')
                                <span class="badge bg-success fs-6">Accepted</span>
                            @elseif($estimate->status === 'rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @elseif($estimate->status === 'expired')
                                <span class="badge bg-warning fs-6">Expired</span>
                            @endif
                        </p>
                    </div>

                    @if($estimate->status === 'draft')
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="updateStatus('sent')">
                            <i class="fas fa-paper-plane me-2"></i>Mark as Sent
                        </button>
                        <button class="btn btn-success btn-sm" onclick="updateStatus('accepted')">
                            <i class="fas fa-check me-2"></i>Mark as Accepted
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="updateStatus('rejected')">
                            <i class="fas fa-times me-2"></i>Mark as Rejected
                        </button>
                    </div>
                    @elseif($estimate->status === 'sent')
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-sm" onclick="updateStatus('accepted')">
                            <i class="fas fa-check me-2"></i>Mark as Accepted
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="updateStatus('rejected')">
                            <i class="fas fa-times me-2"></i>Mark as Rejected
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tools me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($estimate->status === 'accepted')
                            <button onclick="convertToSale()" class="btn btn-primary btn-sm">
                                <i class="fas fa-cash-register me-2"></i>Convert to POS Sale
                            </button>
                            <a href="{{ route('admin.purchase-orders.create', ['estimate' => $estimate->id]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-shopping-cart me-2"></i>Create Purchase Order
                            </a>
                        @elseif($estimate->status === 'converted')
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-info-circle me-2"></i>This estimate has been converted to a sale.
                            </div>
                            @if($estimate->convertedSale)
                                <a href="{{ route('admin.pos.show', $estimate->converted_to_sale_id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-receipt me-2"></i>View POS Sale
                                </a>
                            @endif
                        @endif
                        <button onclick="duplicateEstimate()" class="btn btn-secondary btn-sm">
                            <i class="fas fa-copy me-2"></i>Duplicate Estimate
                        </button>
                        @if($estimate->status === 'draft')
                            <button onclick="deleteEstimate()" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-2"></i>Delete Estimate
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-history me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Estimate Created</h6>
                                <small class="text-muted">{{ $estimate->created_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                        @if($estimate->status !== 'draft')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Status Updated</h6>
                                <small class="text-muted">{{ $estimate->updated_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Estimate Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to update the status of this estimate?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusUpdate()">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    @media print {
        .btn-group,
        .col-lg-4,
        .no-print {
            display: none !important;
        }
        
        .col-lg-8 {
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let statusToUpdate = null;

    function updateStatus(status) {
        statusToUpdate = status;
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    }

    function confirmStatusUpdate() {
        if (!statusToUpdate) return;

        // You can implement AJAX call here to update status
        fetch(`{{ route('admin.estimates.update-status', $estimate) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: statusToUpdate
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });

        const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
        modal.hide();
    }

    function convertToSale() {
        if (confirm('Are you sure you want to convert this estimate to a POS sale? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.estimates.convert-to-sale', $estimate) }}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function duplicateEstimate() {
        if (confirm('Are you sure you want to duplicate this estimate?')) {
            window.location.href = `{{ route('admin.estimates.create') }}?duplicate={{ $estimate->id }}`;
        }
    }

    function deleteEstimate() {
        if (confirm('Are you sure you want to delete this estimate? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.estimates.destroy', $estimate) }}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
