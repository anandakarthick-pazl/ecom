@extends('admin.layouts.app')

@section('title', 'Locations')
@section('page_title', 'Store Locations')

@section('page_actions')
<a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add New Location
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($locations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Open Now</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locations as $location)
                                <tr>
                                    <td>
                                        <img src="{{ $location->image_url }}" alt="{{ $location->name }}" 
                                             class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $location->name }}</strong>
                                        @if($location->email)
                                            <br><small class="text-muted">{{ $location->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $location->address }}</small>
                                        <br><small class="text-muted">{{ $location->latitude }}, {{ $location->longitude }}</small>
                                    </td>
                                    <td>{{ $location->phone ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.locations.toggle', $location) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $location->is_active ? 'success' : 'secondary' }}">
                                                <i class="fas fa-{{ $location->is_active ? 'check' : 'times' }}"></i>
                                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @if($location->is_open_now === null)
                                            <span class="badge bg-secondary">Hours not set</span>
                                        @elseif($location->is_open_now)
                                            <span class="badge bg-success"><i class="fas fa-clock"></i> Open</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-clock"></i> Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $location->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-{{ $location->id }}" 
                                              action="{{ route('admin.locations.destroy', $location) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $locations->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5>No Locations Found</h5>
                        <p class="text-muted">Start by adding your first store location.</p>
                        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Location
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Map Preview -->
@if($locations->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map"></i> Quick Map Preview</h5>
            </div>
            <div class="card-body">
                <div id="quickMap" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function confirmDelete(locationId) {
    if (confirm('Are you sure you want to delete this location? This action cannot be undone.')) {
        document.getElementById('delete-form-' + locationId).submit();
    }
}

@if($locations->count() > 0)
// Quick Map Preview
function initQuickMap() {
    const map = new google.maps.Map(document.getElementById('quickMap'), {
        zoom: 10,
        center: { lat: {{ $locations->first()->latitude }}, lng: {{ $locations->first()->longitude }} },
        mapTypeId: 'roadmap'
    });
    
    const locations = @json($locations->map(function($location) {
        return [
            'id' => $location->id,
            'name' => $location->name,
            'address' => $location->address,
            'lat' => floatval($location->latitude),
            'lng' => floatval($location->longitude),
            'is_active' => $location->is_active,
            'phone' => $location->phone,
            'is_open_now' => $location->is_open_now
        ];
    }));
    
    const bounds = new google.maps.LatLngBounds();
    
    locations.forEach(location => {
        const position = { lat: location.lat, lng: location.lng };
        
        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: location.name,
            icon: {
                url: location.is_active ? 
                    'https://maps.google.com/mapfiles/ms/icons/green-dot.png' : 
                    'https://maps.google.com/mapfiles/ms/icons/grey-dot.png'
            }
        });
        
        const infoContent = `
            <div style="max-width: 200px;">
                <h6>${location.name}</h6>
                <p class="mb-1"><small>${location.address}</small></p>
                ${location.phone ? `<p class="mb-1"><small><i class="fas fa-phone"></i> ${location.phone}</small></p>` : ''}
                <p class="mb-0">
                    <span class="badge bg-${location.is_active ? 'success' : 'secondary'}">${location.is_active ? 'Active' : 'Inactive'}</span>
                    ${location.is_open_now !== null ? 
                        `<span class="badge bg-${location.is_open_now ? 'success' : 'danger'}">${location.is_open_now ? 'Open' : 'Closed'}</span>` 
                        : ''}
                </p>
            </div>
        `;
        
        const infoWindow = new google.maps.InfoWindow({
            content: infoContent
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        bounds.extend(position);
    });
    
    if (locations.length > 1) {
        map.fitBounds(bounds);
    }
}

// Load Google Maps API (Replace YOUR_GOOGLE_MAPS_API_KEY with actual key)
function loadGoogleMaps() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initQuickMap';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', loadGoogleMaps);
@endif
</script>
@endpush
