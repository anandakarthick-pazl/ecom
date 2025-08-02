{{-- Enhanced Category Dropdown Component --}}
<div class="form-group">
    <label class="form-label small">{{ $label ?? 'Category' }}</label>
    <select name="{{ $fieldName ?? 'category' }}" class="form-control form-control-sm category-select">
        <option value="">{{ $placeholder ?? 'All Categories' }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" 
                    {{ (request($fieldName ?? 'category') == $category->id || ($selected ?? null) == $category->id) ? 'selected' : '' }}
                    data-sort-order="{{ $category->sort_order }}"
                    data-products-count="{{ $category->products_count ?? $category->products->count() }}">
                {{ $showId ?? true ? $category->id . ' - ' : '' }}{{ $category->name }}
                {{ $showProductCount ?? false ? ' (' . ($category->products_count ?? $category->products->count()) . ')' : '' }}
            </option>
        @endforeach
    </select>
    @if($showSortInfo ?? false)
        <div class="form-text">Categories sorted by sort order ({{ $categories->min('sort_order') ?? 1 }} - {{ $categories->max('sort_order') ?? 1 }})</div>
    @endif
</div>

<style>
.category-select option[data-sort-order] {
    font-weight: normal;
}

.category-select option[data-sort-order="1"] {
    font-weight: 600;
    color: #2d5016;
}

.category-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
}
</style>
