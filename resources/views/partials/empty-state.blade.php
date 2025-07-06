<!-- Empty State Component -->
<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-{{ $icon ?? 'box' }}"></i>
    </div>
    <h3 class="empty-title">{{ $title ?? 'No Items Found' }}</h3>
    <p class="empty-message">{{ $message ?? 'Please check back later.' }}</p>
    @if(isset($action) && isset($actionUrl))
        <a href="{{ $actionUrl }}" class="btn btn-primary empty-action-btn">{{ $action }}</a>
    @endif
</div>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 4rem;
    color: #e5e7eb;
    margin-bottom: 1.5rem;
    opacity: 0.7;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.empty-message {
    font-size: 1rem;
    margin-bottom: 2rem;
    color: #6b7280;
    line-height: 1.5;
}

.empty-action-btn {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }} !important;
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }} !important;
    color: white !important;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.empty-action-btn:hover {
    background: {{ $globalCompany->secondary_color ?? '#10b981' }} !important;
    border-color: {{ $globalCompany->secondary_color ?? '#10b981' }} !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.empty-action-btn::before {
    content: '🛍️';
    margin-right: 0.25rem;
}
</style>