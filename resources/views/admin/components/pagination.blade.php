{{-- Universal Pagination Component for Admin Views --}}
{{-- 
  Usage: @include('admin.components.pagination', ['items' => $categories, 'type' => 'categories'])
--}}

@if(isset($items) && $items)
    <div class="d-flex justify-content-between align-items-center mt-3">
        {{-- Pagination Info --}}
        <div class="pagination-info">
            @if(method_exists($items, 'total'))
                <small class="text-muted">
                    Showing {{ $items->firstItem() ?? 1 }} to {{ $items->lastItem() ?? $items->count() }} 
                    of {{ $items->total() }} {{ $type ?? 'items' }}
                </small>
            @else
                <small class="text-muted">
                    Showing all {{ $items->count() }} {{ $type ?? 'items' }}
                </small>
            @endif
        </div>

        {{-- Pagination Links --}}
        <div class="pagination-links">
            @if(method_exists($items, 'links'))
                {{ $items->links() }}
            @else
                {{-- No pagination when pagination is disabled --}}
                @if($items->count() > 20)
                    <small class="text-info">
                        <i class="fas fa-info-circle"></i> 
                        Pagination is disabled. Consider enabling it in settings for better performance.
                    </small>
                @endif
            @endif
        </div>
    </div>
@endif

<style>
.pagination-info {
    flex: 1;
}

.pagination-links {
    flex: 1;
    text-align: right;
}

.pagination .page-link {
    color: #2d5016;
    border-color: #dee2e6;
    font-size: 14px;
    padding: 0.5rem 0.75rem;
}

.pagination .page-item.active .page-link {
    background-color: #2d5016;
    border-color: #2d5016;
    color: white;
}

.pagination .page-link:hover {
    color: white;
    background-color: #2d5016;
    border-color: #2d5016;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 10px;
    }
    
    .pagination-info,
    .pagination-links {
        text-align: center;
        flex: none;
    }
}
</style>
