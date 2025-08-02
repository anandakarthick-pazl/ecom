@props([
    'height' => '300px',
    'showSearch' => false,
    'showLocationList' => false,
    'mapType' => 'roadmap',
    'zoom' => 12,
    'centerLat' => null,
    'centerLng' => null,
    'title' => 'Our Locations'
])

@php
    $mapId = 'simpleMap' . uniqid();
@endphp

<div class="simple-map-widget mb-4">
    @if($title)
    <h5 class="mb-3"><i class="fas fa-map-marker-alt"></i> {{ $title }}</h5>
    @endif
    
    <div class="map-container" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div id="{{ $mapId }}" style="height: {{ $height }}; width: 100%;"></div>
    </div>
    
    <div class="mt-3 text-center">
        <a href="/store-locations" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-search-location"></i> View All Locations
        </a>
    </div>
</div>

<style>
.simple-map-widget {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e0e0e0;
}

.simple-map-widget h5 {
    color: #333;
    margin-bottom: 15px;
}

.simple-map-widget .map-container {
    position: relative;
}
</style>

<script>
// Simple Map Widget
function initSimpleMap{{ str_replace(['simpleMap', '-'], ['SimpleMap', ''], $mapId) }}() {
    const mapId = '{{ $mapId }}';
    const mapElement = document.getElementById(mapId);
    
    if (!mapElement) return;
    
    // Load locations
    fetch('/api/locations')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.locations.length > 0) {
                const locations = data.locations;
                
                // Default center
                let center = { lat: 20.5937, lng: 78.9629 };
                
                // Use provided center or first location
                @if($centerLat && $centerLng)
                    center = { lat: {{ $centerLat }}, lng: {{ $centerLng }} };
                @else
                    center = {
                        lat: parseFloat(locations[0].latitude),
                        lng: parseFloat(locations[0].longitude)
                    };
                @endif
                
                const map = new google.maps.Map(mapElement, {
                    zoom: {{ $zoom }},
                    center: center,
                    mapTypeId: '{{ $mapType }}',
                    styles: [
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        }
                    ]
                });
                
                // Add markers
                const bounds = new google.maps.LatLngBounds();
                
                locations.forEach(location => {
                    const position = {
                        lat: parseFloat(location.latitude),
                        lng: parseFloat(location.longitude)
                    };
                    
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: location.name,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                            scaledSize: new google.maps.Size(25, 25)
                        }
                    });
                    
                    const infoContent = `
                        <div style="max-width: 200px; padding: 10px;">
                            <h6 style="margin-bottom: 8px;">${location.name}</h6>
                            <p style="font-size: 0.9em; color: #666; margin-bottom: 5px;">
                                <i class="fas fa-map-marker-alt"></i> ${location.address}
                            </p>
                            ${location.phone ? `
                                <p style="font-size: 0.85em; color: #777; margin-bottom: 5px;">
                                    <i class="fas fa-phone"></i> ${location.phone}
                                </p>
                            ` : ''}
                            ${location.is_open_now !== null ? `
                                <span class="badge bg-${location.is_open_now ? 'success' : 'danger'}" style="font-size: 0.75em;">
                                    <i class="fas fa-clock"></i> ${location.is_open_now ? 'Open' : 'Closed'}
                                </span>
                            ` : ''}
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
                
                // Fit bounds if multiple locations
                if (locations.length > 1) {
                    map.fitBounds(bounds);
                }
            }
        })
        .catch(error => {
            console.error('Error loading locations:', error);
            mapElement.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #666;">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                        <p>Unable to load map</p>
                    </div>
                </div>
            `;
        });
}

// Load Google Maps API if not already loaded
if (typeof google === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initSimpleMap{{ str_replace(['simpleMap', '-'], ['SimpleMap', ''], $mapId) }}';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    // Google Maps already loaded
    initSimpleMap{{ str_replace(['simpleMap', '-'], ['SimpleMap', ''], $mapId) }}();
}
</script>
