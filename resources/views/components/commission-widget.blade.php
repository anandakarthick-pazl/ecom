@props(['commission', 'size' => 'sm'])

@if($commission)
<div class="commission-widget d-inline-block">
    <div class="d-flex align-items-center gap-2">
        <span class="badge badge-{{ $commission->status_color }}">
            {{ $commission->status_text }}
        </span>
        
        @if($commission->status === 'pending')
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success btn-{{ $size }}" 
                        onclick="quickMarkAsPaid({{ $commission->id }})" 
                        title="Mark as Paid">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn btn-danger btn-{{ $size }}" 
                        onclick="quickCancelCommission({{ $commission->id }})" 
                        title="Cancel">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @elseif($commission->status === 'paid')
            <button type="button" class="btn btn-warning btn-{{ $size }}" 
                    onclick="quickRevertToPending({{ $commission->id }})" 
                    title="Revert to Pending">
                <i class="fas fa-undo"></i>
            </button>
        @endif
        
        <span class="text-success font-weight-bold">
            {{ $commission->formatted_commission_amount }}
        </span>
    </div>
    
    @if($commission->reference_name)
        <small class="text-muted d-block">{{ $commission->reference_name }}</small>
    @endif
</div>

@push('scripts')
<script>
function quickMarkAsPaid(commissionId) {
    if (confirm('Mark this commission as paid?')) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/mark-paid`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to update commission status'));
            }
        });
    }
}

function quickCancelCommission(commissionId) {
    const reason = prompt('Please provide a reason for cancelling this commission:');
    if (reason && reason.trim()) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/cancel`,
            method: 'POST',
            data: {
                reason: reason.trim(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to cancel commission'));
            }
        });
    }
}

function quickRevertToPending(commissionId) {
    if (confirm('Revert this commission back to pending status?')) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/revert-pending`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to revert commission status'));
            }
        });
    }
}
</script>
@endpush

@else
<span class="text-muted">No Commission</span>
@endif
