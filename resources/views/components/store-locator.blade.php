@props([
    'height' => '400px',
    'showSearch' => true,
    'showLocationList' => true,
    'mapType' => 'roadmap', // roadmap, satellite, hybrid, terrain
    'zoom' => 10,
    'centerLat' => null,
    'centerLng' => null
])

<div class="store-locator-widget">
    @if($showSearch)
    <div class="search-section mb-3">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="locationSearch" class="form-control" placeholder="Enter your location to find nearby stores...">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="fas fa-search"></i> Find Stores
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-secondary w-100" type="button" id="currentLocationBtn">
                    <i class="fas fa-location-arrow"></i> Use Current Location
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <div class="col-{{ $showLocationList ? 'md-8' : '12' }}">
            <div class="map-container">
                <div id="storeMap" style="height: {{ $height }}; width: 100%; border-radius: 8px;"></div>
            </div>
        </div>
        
        @if($showLocationList)
        <div class="col-md-4">
            <div class="locations-list">
                <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Our Locations</h6>
                <div id="locationsList" class="locations-container">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0 mt-2">Loading locations...</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.store-locator-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.locations-container {
    max-height: {{ $height }};
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #fff;
}

.location-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.2s;
}

.location-item:last-child {
    border-bottom: none;
}

.location-item:hover {
    background-color: #f8f9fa;
}

.location-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.location-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.location-address {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
}

.location-details {
    font-size: 0.85em;
    color: #777;
}

.location-details .badge {
    font-size: 0.75em;
    margin-right: 5px;
}

.location-distance {
    font-weight: 600;
    color: #2196f3;
    font-size: 0.9em;
}

.map-container {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.search-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.gm-style-iw {
    max-width: 300px !important;
}

.custom-info-window {
    padding: 10px;
}

.custom-info-window h6 {
    margin-bottom: 8px;
    color: #333;
}

.custom-info-window .address {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 8px;
}

.custom-info-window .contact {
    font-size: 0.85em;
    color: #777;
}

.custom-info-window .status {
    margin-top: 8px;
}

@media (max-width: 768px) {
    .search-section .row > div {
        margin-bottom: 10px;
    }
    
    .search-section .col-md-4 button {
        width: 100%;
    }
}
</style>

<script>
// Initialize store locator when Google Maps is loaded
function initStoreLocator() {
    if (typeof google !== 'undefined' && google.maps) {
        window.storeLocator = new StoreLocator();
    } else {
        console.error('Google Maps API not loaded');
    }
}

class StoreLocator {
    constructor(options = {}) {
        this.map = null;
        this.markers = [];
        this.locations = [];
        this.userLocation = null;
        this.activeInfoWindow = null;
        this.geocoder = null;
        
        this.options = {
            height: '{{ $height }}',
            showSearch: {{ $showSearch ? 'true' : 'false' }},
            showLocationList: {{ $showLocationList ? 'true' : 'false' }},
            mapType: '{{ $mapType }}',
            zoom: {{ $zoom }},
            centerLat: {{ $centerLat ?? 'null' }},
            centerLng: {{ $centerLng ?? 'null' }},
            ...options
        };
        
        this.init();
    }
    
    async init() {
        await this.loadLocations();
        this.initMap();
        this.bindEvents();
    }
    
    async loadLocations() {
        try {
            const response = await fetch('/api/locations');
            const data = await response.json();
            
            if (data.success) {
                this.locations = data.locations;
                this.renderLocationsList();
            } else {
                console.error('Failed to load locations');
            }
        } catch (error) {
            console.error('Error loading locations:', error);
        }
    }
    
    initMap() {
        // Default center (India)
        let center = { lat: 20.5937, lng: 78.9629 };
        
        // Use provided center coordinates
        if (this.options.centerLat && this.options.centerLng) {
            center = { lat: this.options.centerLat, lng: this.options.centerLng };
        } else if (this.locations.length > 0) {
            // Use first location as center
            center = {
                lat: parseFloat(this.locations[0].latitude),
                lng: parseFloat(this.locations[0].longitude)
            };
        }
        
        this.map = new google.maps.Map(document.getElementById('storeMap'), {
            zoom: this.options.zoom,
            center: center,
            mapTypeId: this.options.mapType,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
        
        this.geocoder = new google.maps.Geocoder();
        
        this.addLocationMarkers();
        
        // Fit bounds to show all locations
        if (this.locations.length > 1) {
            this.fitBoundsToLocations();
        }
    }
    
    addLocationMarkers() {
        this.locations.forEach((location, index) => {
            const position = {
                lat: parseFloat(location.latitude),
                lng: parseFloat(location.longitude)
            };
            
            const marker = new google.maps.Marker({
                position: position,
                map: this.map,
                title: location.name,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(32, 32)
                }
            });
            
            const infoContent = this.createInfoWindowContent(location);
            const infoWindow = new google.maps.InfoWindow({
                content: infoContent
            });
            
            marker.addListener('click', () => {
                if (this.activeInfoWindow) {
                    this.activeInfoWindow.close();
                }
                infoWindow.open(this.map, marker);
                this.activeInfoWindow = infoWindow;
                this.highlightLocationItem(location.id);
            });
            
            this.markers.push({ marker, location, infoWindow });
        });
    }
    
    createInfoWindowContent(location) {
        const isOpenBadge = location.is_open_now !== null ?
            `<span class="badge bg-${location.is_open_now ? 'success' : 'danger'}">
                <i class="fas fa-clock"></i> ${location.is_open_now ? 'Open' : 'Closed'}
            </span>` : '';
            
        const distanceBadge = location.distance ?
            `<span class="badge bg-primary">
                <i class="fas fa-route"></i> ${location.distance} km away
            </span>` : '';
        
        return `
            <div class="custom-info-window">
                <h6>${location.name}</h6>
                <div class="address">
                    <i class="fas fa-map-marker-alt"></i> ${location.address}
                </div>
                ${location.phone ? `
                    <div class="contact">
                        <i class="fas fa-phone"></i> ${location.phone}
                    </div>
                ` : ''}
                ${location.email ? `
                    <div class="contact">
                        <i class="fas fa-envelope"></i> ${location.email}
                    </div>
                ` : ''}
                <div class="status">
                    ${isOpenBadge}
                    ${distanceBadge}
                </div>
            </div>
        `;
    }
    
    renderLocationsList() {
        if (!this.options.showLocationList) return;
        
        const container = document.getElementById('locationsList');
        
        if (this.locations.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No locations found</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.locations.map(location => {
            const isOpenBadge = location.is_open_now !== null ?
                `<span class="badge bg-${location.is_open_now ? 'success' : 'danger'}">
                    ${location.is_open_now ? 'Open' : 'Closed'}
                </span>` : '';
                
            const distanceBadge = location.distance ?
                `<span class="location-distance">${location.distance} km away</span>` : '';
            
            return `
                <div class="location-item" data-location-id="${location.id}">
                    <div class="location-name">${location.name}</div>
                    <div class="location-address">${location.address}</div>
                    <div class="location-details">
                        ${location.phone ? `<i class="fas fa-phone"></i> ${location.phone}<br>` : ''}
                        <div class="mt-1">
                            ${isOpenBadge}
                            ${distanceBadge}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Add click handlers to location items
        container.querySelectorAll('.location-item').forEach(item => {
            item.addEventListener('click', () => {
                const locationId = parseInt(item.dataset.locationId);
                this.focusLocation(locationId);
            });
        });
    }
    
    focusLocation(locationId) {
        const markerData = this.markers.find(m => m.location.id === locationId);
        if (markerData) {
            this.map.setCenter(markerData.marker.getPosition());
            this.map.setZoom(15);
            
            if (this.activeInfoWindow) {
                this.activeInfoWindow.close();
            }
            markerData.infoWindow.open(this.map, markerData.marker);
            this.activeInfoWindow = markerData.infoWindow;
            
            this.highlightLocationItem(locationId);
        }
    }
    
    highlightLocationItem(locationId) {
        if (!this.options.showLocationList) return;
        
        // Remove previous highlights
        document.querySelectorAll('.location-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add highlight to selected item
        const selectedItem = document.querySelector(`[data-location-id="${locationId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active');
        }
    }
    
    fitBoundsToLocations() {
        const bounds = new google.maps.LatLngBounds();
        this.locations.forEach(location => {
            bounds.extend(new google.maps.LatLng(
                parseFloat(location.latitude),
                parseFloat(location.longitude)
            ));
        });
        this.map.fitBounds(bounds);
    }
    
    async findNearbyStores(address) {
        try {
            const response = await fetch(`/api/locations?address=${encodeURIComponent(address)}`);
            const data = await response.json();
            
            if (data.success) {
                this.locations = data.locations;
                this.clearMarkers();
                this.addLocationMarkers();
                this.renderLocationsList();
                
                if (this.locations.length > 0) {
                    this.fitBoundsToLocations();
                }
            }
        } catch (error) {
            console.error('Error searching locations:', error);
        }
    }
    
    async useCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                try {
                    const response = await fetch(`/api/locations?lat=${lat}&lng=${lng}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.locations = data.locations;
                        this.userLocation = { lat, lng };
                        
                        // Add user location marker
                        this.addUserLocationMarker(lat, lng);
                        
                        // Update location markers
                        this.clearMarkers();
                        this.addLocationMarkers();
                        this.renderLocationsList();
                        
                        // Center map on user location
                        this.map.setCenter({ lat, lng });
                        this.map.setZoom(12);
                    }
                } catch (error) {
                    console.error('Error loading nearby locations:', error);
                }
            },
            (error) => {
                console.error('Error getting location:', error);
                alert('Unable to get your location. Please try searching manually.');
            }
        );
    }
    
    addUserLocationMarker(lat, lng) {
        const marker = new google.maps.Marker({
            position: { lat, lng },
            map: this.map,
            title: 'Your Location',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            }
        });
        
        const infoWindow = new google.maps.InfoWindow({
            content: '<div class="custom-info-window"><h6>Your Location</h6></div>'
        });
        
        marker.addListener('click', () => {
            if (this.activeInfoWindow) {
                this.activeInfoWindow.close();
            }
            infoWindow.open(this.map, marker);
            this.activeInfoWindow = infoWindow;
        });
        
        this.markers.push({ marker, location: null, infoWindow });
    }
    
    clearMarkers() {
        this.markers.forEach(markerData => {
            markerData.marker.setMap(null);
        });
        this.markers = [];
    }
    
    bindEvents() {
        if (this.options.showSearch) {
            const searchBtn = document.getElementById('searchBtn');
            const locationSearch = document.getElementById('locationSearch');
            const currentLocationBtn = document.getElementById('currentLocationBtn');
            
            if (searchBtn && locationSearch) {
                searchBtn.addEventListener('click', () => {
                    const address = locationSearch.value.trim();
                    if (address) {
                        this.findNearbyStores(address);
                    }
                });
                
                locationSearch.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        const address = locationSearch.value.trim();
                        if (address) {
                            this.findNearbyStores(address);
                        }
                    }
                });
            }
            
            if (currentLocationBtn) {
                currentLocationBtn.addEventListener('click', () => {
                    this.useCurrentLocation();
                });
            }
        }
    }
}

// Load Google Maps API if not already loaded
if (typeof google === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry&callback=initStoreLocator';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    // Google Maps already loaded
    initStoreLocator();
}
</script>
