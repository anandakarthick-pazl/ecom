{{-- Pagination Controls Component --}}
@if(isset($paginationControls) && $paginationControls['enabled'])
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="pagination-info">
            @if(method_exists($items, 'total'))
                <small class="text-muted">
                    Showing {{ $items->firstItem() ?: 0 }} to {{ $items->lastItem() ?: 0 }} 
                    of {{ $items->total() }} results
                </small>
            @endif
        </div>
        
        @if($paginationControls['allowed_values'] && count($paginationControls['allowed_values']) > 1 && ($paginationControls['request_per_page'] || $paginationControls['current_per_page'] != $paginationControls['default_per_page']))
            <div class="per-page-selector">
                <form method="GET" class="d-inline-flex align-items-center">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page" class="form-label me-2 mb-0 small">Show:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        @foreach($paginationControls['allowed_values'] as $value)
                            <option value="{{ $value }}" {{ $paginationControls['current_per_page'] == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <span class="ms-2 small text-muted">per page</span>
                </form>
            </div>
        @endif
    </div>
    
    {{-- Standard pagination links --}}
    @if(method_exists($items, 'links'))
        {{ $items->links() }}
    @endif
@else
    {{-- When pagination is disabled, just show total count --}}
    @if(isset($items) && method_exists($items, 'count'))
        <div class="d-flex justify-content-between align-items-center mb-3">
            <small class="text-muted">
                Showing all {{ $items->count() }} results
            </small>
        </div>
    @endif
@endif
