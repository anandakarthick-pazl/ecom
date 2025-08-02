@extends('admin.layouts.app')

@section('title', 'Add Location')
@section('page_title', 'Add New Location')

@section('page_actions')
<a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Locations
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Location Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g., Main Store, Downtown Branch">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                   min="0" placeholder="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required 
                                      placeholder="Enter full address with city, state, postal code">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Map Section -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Location on Map *</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="latitude" class="form-label">Latitude *</label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                                   id="latitude" name="latitude" value="{{ old('latitude') }}" required
                                                   placeholder="e.g., 28.6139">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="longitude" class="form-label">Longitude *</label>
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                                   id="longitude" name="longitude" value="{{ old('longitude') }}" required
                                                   placeholder="e.g., 77.2090">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getCurrentLocation()">
                                            <i class="fas fa-location-arrow"></i> Use Current Location
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="geocodeAddress()">
                                            <i class="fas fa-search"></i> Find by Address
                                        </button>
                                    </div>
                                    
                                    <div id="map" style="height: 300px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="e.g., +91 98765 43210">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="e.g., store@company.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Additional information about this location">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Location Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload an image of the store (max 2MB)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Working Hours Section -->
                    <hr class="my-4">
                    <h5 class="mb-3"><i class="fas fa-clock"></i> Working Hours</h5>
                    
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    
                    @foreach($days as $index => $day)
                    <div class="row mb-2">
                        <div class="col-md-2 d-flex align-items-center">
                            <strong>{{ $dayLabels[$index] }}</strong>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input day-toggle" 
                                       id="working_hours_{{ $day }}_is_open" 
                                       name="working_hours[{{ $day }}][is_open]" 
                                       value="1" {{ old("working_hours.{$day}.is_open", true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="working_hours_{{ $day }}_is_open">
                                    Open
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control day-time" 
                                   id="working_hours_{{ $day }}_open" 
                                   name="working_hours[{{ $day }}][open]" 
                                   value="{{ old("working_hours.{$day}.open", '09:00') }}">
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control day-time" 
                                   id="working_hours_{{ $day }}_close" 
                                   name="working_hours[{{ $day }}][close]" 
                                   value="{{ old("working_hours.{$day}.close", '18:00') }}">
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Location
                        </button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Tips for Adding Locations</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Use accurate coordinates for precise mapping</li>
                    <li><i class="fas fa-check text-success"></i> Upload a clear store image</li>
                    <li><i class="fas fa-check text-success"></i> Set realistic working hours</li>
                    <li><i class="fas fa-check text-success"></i> Include contact information</li>
                </ul>
                
                <hr>
                
                <h6>Finding Coordinates</h6>
                <ul class="list-unstyled small">
                    <li>• Use the "Find by Address" button</li>
                    <li>• Click on the map to set location</li>
                    <li>• Use "Current Location" if on-site</li>
                    <li>• Copy from Google Maps</li>
                </ul>
                
                <hr>
                
                <h6>Image Guidelines</h6>
                <ul class="list-unstyled small">
                    <li>• Formats: JPG, PNG, GIF</li>
                    <li>• Max size: 2MB</li>
                    <li>• Recommended: 800x600px</li>
                    <li>• Clear, well-lit photos work best</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let map;
let marker;
let geocoder;

function initMap() {
    // Default to India center
    const defaultCenter = { lat: 20.5937, lng: 78.9629 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 6,
        center: defaultCenter,
        mapTypeId: 'roadmap'
    });
    
    geocoder = new google.maps.Geocoder();
    
    // Add click listener to map
    map.addListener('click', function(event) {
        setMarker(event.latLng);
    });
    
    // Initialize marker if coordinates exist
    const lat = document.getElementById('latitude').value;
    const lng = document.getElementById('longitude').value;
    
    if (lat && lng) {
        const position = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
        setMarker(position);
        map.setCenter(position);
        map.setZoom(15);
    }
}

function setMarker(position) {
    if (marker) {
        marker.setMap(null);
    }
    
    marker = new google.maps.Marker({
        position: position,
        map: map,
        draggable: true,
        title: 'Store Location'
    });
    
    // Update form fields
    document.getElementById('latitude').value = position.lat().toFixed(8);
    document.getElementById('longitude').value = position.lng().toFixed(8);
    
    // Add drag listener
    marker.addListener('dragend', function(event) {
        document.getElementById('latitude').value = event.latLng.lat().toFixed(8);
        document.getElementById('longitude').value = event.latLng.lng().toFixed(8);
    });
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            setMarker(pos);
            map.setCenter(pos);
            map.setZoom(15);
        }, function() {
            alert('Error: The Geolocation service failed.');
        });
    } else {
        alert('Error: Your browser doesn\\'t support geolocation.');
    }
}

function geocodeAddress() {
    const address = document.getElementById('address').value;
    if (!address) {
        alert('Please enter an address first.');
        return;
    }
    
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK') {
            const location = results[0].geometry.location;
            setMarker(location);
            map.setCenter(location);
            map.setZoom(15);
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}

// Working hours toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.day-toggle').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const day = this.id.split('_')[2]; // Extract day from ID
            const openInput = document.getElementById(`working_hours_${day}_open`);
            const closeInput = document.getElementById(`working_hours_${day}_close`);
            
            if (this.checked) {
                openInput.disabled = false;
                closeInput.disabled = false;
                openInput.style.opacity = '1';
                closeInput.style.opacity = '1';
            } else {
                openInput.disabled = true;
                closeInput.disabled = true;
                openInput.style.opacity = '0.5';
                closeInput.style.opacity = '0.5';
            }
        });
        
        // Trigger change event for initial state
        checkbox.dispatchEvent(new Event('change'));
    });
});

// Load Google Maps API
function loadGoogleMaps() {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry&callback=initMap';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', loadGoogleMaps);
</script>
@endpush
