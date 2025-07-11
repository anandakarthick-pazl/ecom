{{-- Frontend Pagination Controls Component for Customer-Facing Pages --}}
@if(isset($frontendPaginationControls) && $frontendPaginationControls['enabled'])
    <div class="frontend-pagination-wrapper">
        {{-- Pagination Info --}}
        @if(method_exists($items, 'total'))
            <div class="pagination-info text-center mb-3">
                <small class="text-muted">
                    Showing {{ $items->firstItem() ?: 0 }} to {{ $items->lastItem() ?: 0 }} 
                    of {{ $items->total() }} {{ Str::plural('result', $items->total()) }}
                </small>
            </div>
        @endif
        
        {{-- Check if Load More is enabled --}}
        @if(isset($frontendPaginationSettings) && $frontendPaginationSettings['frontend_load_more_enabled'] ?? false)
            {{-- Load More Button Style Pagination --}}
            @if(method_exists($items, 'hasMorePages') && $items->hasMorePages())
                <div class="load-more-section text-center mt-4">
                    <button type="button" 
                            class="btn btn-outline-primary btn-lg load-more-btn"
                            data-next-page="{{ $items->currentPage() + 1 }}"
                            data-url="{{ $items->nextPageUrl() }}"
                            onclick="loadMoreProducts(this)">
                        <i class="fas fa-plus-circle me-2"></i>
                        Load More Products
                        <small class="d-block mt-1">
                            {{ $items->total() - $items->lastItem() }} more items available
                        </small>
                    </button>
                </div>
                
                {{-- Loading indicator --}}
                <div class="load-more-loading text-center mt-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading more products...</p>
                </div>
            @endif
        @else
            {{-- Standard Pagination Links --}}
            @if(method_exists($items, 'links'))
                <div class="frontend-pagination-links">
                    {{ $items->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @endif
        
        {{-- Per Page Selector (if enabled and not using load more) --}}
        @if(!($frontendPaginationSettings['frontend_load_more_enabled'] ?? false) && 
            $frontendPaginationControls['allowed_values'] && 
            count($frontendPaginationControls['allowed_values']) > 1)
            <div class="per-page-selector-frontend text-center mt-3">
                <form method="GET" class="d-inline-flex align-items-center">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="frontend_per_page" class="form-label me-2 mb-0 small">Show:</label>
                    <select name="per_page" id="frontend_per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        @foreach($frontendPaginationControls['allowed_values'] as $value)
                            <option value="{{ $value }}" {{ $frontendPaginationControls['current_per_page'] == $value ? 'selected' : '' }}>
                                {{ $value }} {{ $value == 1 ? 'item' : 'items' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="ms-2 small text-muted">per page</span>
                </form>
            </div>
        @endif
    </div>
    
    {{-- Load More JavaScript --}}
    @if(isset($frontendPaginationSettings) && $frontendPaginationSettings['frontend_load_more_enabled'] ?? false)
        <script>
        function loadMoreProducts(button) {
            const loadingDiv = document.querySelector('.load-more-loading');
            const nextPageUrl = button.dataset.url;
            
            // Show loading, hide button
            button.style.display = 'none';
            loadingDiv.style.display = 'block';
            
            // Make AJAX request
            fetch(nextPageUrl + '&ajax=1', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Find products container
                const productsContainer = document.querySelector('.products-grid, .themes-grid, .items-grid');
                
                if (data.html && productsContainer) {
                    // Create temporary div to parse new content
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    
                    // Extract just the product cards (not the container)
                    const newItems = tempDiv.querySelectorAll('.product-card, .theme-card, .item-card');
                    
                    // Append new items to existing container
                    newItems.forEach(item => {
                        productsContainer.appendChild(item);
                    });
                    
                    // Check if there are more pages
                    if (data.pagination && data.pagination.includes('Next')) {
                        // Update button for next page
                        const nextPage = parseInt(button.dataset.nextPage) + 1;
                        button.dataset.nextPage = nextPage;
                        
                        // Extract next page URL from pagination HTML
                        const tempPagination = document.createElement('div');
                        tempPagination.innerHTML = data.pagination;
                        const nextLink = tempPagination.querySelector('a[rel="next"]');
                        
                        if (nextLink) {
                            button.dataset.url = nextLink.href;
                            
                            // Update remaining items count
                            const remainingItems = data.total - (data.current_page * data.per_page);
                            const smallText = button.querySelector('small');
                            if (smallText) {
                                smallText.textContent = `${remainingItems} more items available`;
                            }
                            
                            // Show button again
                            button.style.display = 'inline-block';
                        }
                    }
                    
                    // Update pagination info if exists
                    const paginationInfo = document.querySelector('.pagination-info');
                    if (paginationInfo && data.pagination_info) {
                        paginationInfo.innerHTML = data.pagination_info;
                    }
                } else {
                    console.error('No products container found or no HTML returned');
                    button.style.display = 'inline-block';
                }
                
                loadingDiv.style.display = 'none';
            })
            .catch(error => {
                console.error('Error loading more products:', error);
                loadingDiv.style.display = 'none';
                button.style.display = 'inline-block';
                
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-warning text-center mt-3';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Failed to load more items. Please try again.';
                button.parentNode.appendChild(errorDiv);
                
                // Remove error after 5 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            });
        }
        </script>
    @endif
    
    {{-- Custom Styling for Frontend Pagination --}}
    <style>
    .frontend-pagination-wrapper {
        margin: 2rem 0;
    }
    
    .load-more-btn {
        min-width: 200px;
        border-radius: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .load-more-btn small {
        opacity: 0.8;
        font-size: 0.75rem;
    }
    
    .frontend-pagination-links .pagination {
        justify-content: center;
        margin-bottom: 0;
    }
    
    .frontend-pagination-links .page-link {
        border-radius: 50px;
        margin: 0 2px;
        min-width: 40px;
        text-align: center;
    }
    
    .per-page-selector-frontend {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    
    .per-page-selector-frontend .form-select {
        border-radius: 20px;
        border: 1px solid #ddd;
    }
    
    @media (max-width: 768px) {
        .load-more-btn {
            width: 100%;
            max-width: 300px;
        }
        
        .per-page-selector-frontend {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .frontend-pagination-links .pagination {
            flex-wrap: wrap;
            gap: 0.25rem;
        }
    }
    </style>
@else
    {{-- When pagination is disabled, just show total count --}}
    @if(isset($items) && method_exists($items, 'count'))
        <div class="pagination-info text-center my-3">
            <small class="text-muted">
                Showing all {{ $items->count() }} {{ Str::plural('result', $items->count()) }}
            </small>
        </div>
    @endif
@endif
