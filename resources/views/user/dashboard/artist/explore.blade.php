@extends('user.layouts.master')

{{-- @section('head') --}}
{{-- @endsection --}}

@section('content')
    <div class="container-fluid explore_main_sec">
        <div class="row">
            <div class="col-md-12">
                <div class="search-section">
                    <p class="search-text">Over {{ $studios->count() }} Studios nearby</p>
                    <div class="search-controls">
                        <div class="search-input-wrapper">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="{{ __('search_paragraph') }}" class="search-input">
                        </div>
                        <button class="filter-button" data-bs-toggle="modal" data-bs-target="#filter-modal-overlay">
                            <i class="fa-solid fa-sliders"></i>
                            <span>{{ __('filter') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Left: Studio Cards -->
                <div class="cards_container">
                    <div class="row pt-2">
                        @forelse ($studios as $studio)
                            <div class="col-xxl-6 col-xl-12 col-lg-12">
                                <!-- Studio Card -->
                                <div class="studio-card" onclick="window.location.href='{{ route('dashboard.studio_detail', ['id' => $studio->id]) }}'" data-studio-id="{{ $studio->id }}" data-lat="{{ $studio->latitude }}" data-lng="{{ $studio->longitude }}">
                                    <div class="card_image_wrapper">
                                        <div class="swiper swiper_slider">
                                            <div class="swiper-wrapper">
                                                @forelse ($studio->studioImages->take(3) as $image)
                                                    <div class="swiper-slide">
                                                        {{-- Use asset() with the image path --}}
                                                        <img src="{{ asset($image->image_path) }}" alt="{{ $studio->studio_name ?? $studio->name }} Image" class="">
                                                    </div>
                                                @empty
                                                    <div class="swiper-slide">
                                                        <img src="{{ asset('studios/covers/default-cover.png') }}" alt="Placeholder" class="">
                                                    </div>
                                                @endforelse
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div>
                                    <div class="card_content_wrapper">
                                        <div class="card-header">
                                            <div class="card-logo-wrapper">
                                                {{-- Use asset() with the logo path --}}
                                                <img src="{{ $studio->studio_logo ? asset($studio->studio_logo) : asset('studios/logos/default-logo.png') }}"
                                                     alt="{{ $studio->studio_name ?? $studio->name . ' ' . $studio->last_name }} Logo" class="card-logo">
                                            </div>
                                            <div class="card_location_wrapper">
                                                <p title="{{($studio->studio_name ?? $studio->name)}}">{{ Str::limit(($studio->studio_name ?? $studio->name), 12, '...') }}</p>
                                                <img src="{{ asset('assets/web/extra/location_logo.png') }}" alt="Location"
                                                     style="width: 16px; height: 16px; vertical-align: middle;">
                                                <span title="{{($studio->city ?? '' ). (($studio->city && $studio->country) ? ', ' : '') . ($studio->country ? strtoupper($studio->country) : '')}}">{{ Str::limit(($studio->city ?? '' ). (($studio->city && $studio->country) ? ', ' : '') . ($studio->country ? strtoupper($studio->country) : ''), 12, '...') }}</span>
                                            </div>
                                            <button class="card-like-btn">
                                                <svg fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="card-info-section">
                                            <div class="card-status-row">
                                                <span class="card-verified" style="{{ $studio->verification_status == '2' ? '' : 'display:none;' }}">
                                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>Verified</span>
                                                </span>
                                                <span class="card-status-badge open">OPEN</span>
                                            </div>
                                            <div class="card-details">
                                                <div class="timings_detail">
                                                    {{-- Note: Studio Hours are not in your provided User table, placeholder used --}}
                                                    <span style="font-size: 13px">Open Today: 12:00 PM - 10:00 PM</span>
                                                </div>
                                                <div class="specialities_wrapper">
                                                    <span style="font-size: 13px">{{ round($studio->distance, 1) }} KM away</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center w-100 p-5">{{ __('no_studios_found') }} within {{ $maxDistance }}km.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Right: Map -->
                <div id="map-container">
                    <div id="map"></div>

                    <!-- NEW HTML STRUCTURE BASED ON YOUR CSS -->
                    <div id="details-panel-wrapper">
                        <div class="studio-card">
                            <div class="card-image-wrapper">
                                <button id="close-btn" class="modal-close-btn">×</button>
                                <div id="image-container">
                                    <!-- Main Image will be injected by JS here -->
                                </div>
                                <div id="slider-dots" class="card-image-dots">
                                    <!-- Slider dots will be injected by JS here -->
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="card-header">
                                    <div class="card-logo-wrapper">
                                        <img id="studio-logo" src="" alt="Studio Logo" class="card-logo">
                                        <div class="card-title-location">
                                            <h3 id="studio-name" class="card-title"></h3>
                                            <div id="studio-location" class="card-location"></div>
                                        </div>
                                    </div>
                                    <button class="card-like-btn" aria-label="Like this studio">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="card-info-section">
                                    <div class="card-status-row">
                                        <div class="card-verified">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span>Verified</span>
                                        </div>
                                        <span id="status-badge" class="card-status-badge"></span>
                                    </div>
                                    <div id="studio-details" class="card-details">
                                        <!-- Hours and Specialties will be injected here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- FILTER MODAL -->
        <!-- Bootstrap Modal -->
        <div class="modal fade" id="filter-modal-overlay" tabindex="-1" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content filter-modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h2 class="modal-title" id="filterModalLabel">Filter</h2>
                        <button type="button" class="modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <!-- Date Range -->
                        <div class="form-section">
                            <label class="form-label">Date Range</label>
                            <div class="calendar-container">
                                <div class="calendar-header">
                                    <button id="prev-month-btn" class="calendar-nav">&lt;</button>
                                    <span id="calendar-month-year" class="calendar-month-year"></span>
                                    <button id="next-month-btn" class="calendar-nav">&gt;</button>
                                </div>

                                <div class="calendar-grid calendar-grid-header">
                                    <div class="day-name">M</div>
                                    <div class="day-name">T</div>
                                    <div class="day-name">W</div>
                                    <div class="day-name">T</div>
                                    <div class="day-name">F</div>
                                    <div class="day-name">S</div>
                                    <div class="day-name">S</div>
                                </div>
                                <div id="calendar-grid-body" class="calendar-grid">
                                    <!-- Dynamic calendar days will be inserted here by JS -->
                                </div>
                            </div>
                        </div>

                        <!-- From / To Inputs -->
                        <div class="date-inputs-container">
                            <div class="date-input-group">
                                <label class="form-label-small">From</label>
                                <div id="from-date-display" class="date-input-display"></div>
                            </div>
                            <div class="date-input-group">
                                <label class="form-label-small">To</label>
                                <div id="to-date-display" class="date-input-display"></div>
                            </div>
                        </div>

                        <!-- Studio Type Dropdown -->
                        <div class="form-section">
                            <label class="form-label-small">Studio Type</label>
                            <div class="custom-select">
                                <span>Private Studio</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Country/Region Dropdown -->
                        <div class="form-section">
                            <label class="form-label-small">Country/Region</label>
                            <div class="custom-select">
                                <span>United States (+1)</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        {{--                    <button id="clear-dates-btn" class="back-btn_filter">Clear</button> --}}
                        <div class="button-group_filter">
                            <button id="clear-dates-btn" class="nav-btn_filter back-btn_filter">Clear</button>
                            <button class="nav-btn_filter next-btn_filter" data-bs-dismiss="modal">Apply</button>
                        </div>

                        {{--                    <button class="btn-apply">Apply</button> --}}
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')

        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap">
        </script>

        <script>
            const asset_path = @json(asset(''));
            // Data passed from Laravel Controller to JavaScript
            const studiosData = @json($studios);
            const artistLat = {{ $artist->latitude ?? '40.7128' }};
            const artistLng = {{ $artist->longitude ?? '-74.0060' }};

            let map;
            const panelWrapper = document.getElementById('details-panel-wrapper');
            let currentOpenPanelStudio = null;

            function initMap() {
                class CustomMarker extends google.maps.OverlayView {
                    constructor(position, map, studio) {
                        super();
                        this.position = position;
                        this.studio = studio;
                        this.div = null;
                        this.setMap(map);
                    }
                    onAdd() {
                        this.div = document.createElement("div");
                        this.div.className = "custom-marker";
                        // Adjusted innerHTML to use asset paths correctly
                        this.div.innerHTML =
                            `<div class="marker-label">${this.studio.studio_name}</div><div class="marker-pin"><svg width="48" height="56" viewBox="0 0 60 70"><defs><filter id="shadow" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.3"/></filter></defs><path d="M30 70C30 70 5 45 5 25C5 12.8 16.2 2 30 2C43.8 2 55 12.8 55 25C55 45 30 70 30 70Z" fill="white" filter="url(#shadow)"/><circle cx="30" cy="25" r="22" fill="none" stroke="#d1d5db" stroke-width="4" class="marker-border"/><image href="${asset_path+(this.studio.studio_logo || 'studios/logos/default-logo.png')}" x="8" y="3" height="44" width="44" clip-path="url(#circleClip${this.studio.lat})" /><clipPath id="circleClip${this.studio.lat}"><circle cx="30" cy="25" r="22"/></clipPath></svg></div>`;
                        this.addListeners();
                        const panes = this.getPanes();
                        panes.overlayMouseTarget.appendChild(this.div);
                    }
                    draw() {
                        const overlayProjection = this.getProjection();
                        if (!overlayProjection || !this.div) return;
                        const sw = overlayProjection.fromLatLngToDivPixel(this.position);
                        const totalHeight = this.div.offsetHeight;
                        this.div.style.left = sw.x + "px";
                        this.div.style.top = (sw.y - totalHeight) + "px";
                    }
                    onRemove() {
                        if (this.div) {
                            this.div.parentNode.removeChild(this.div);
                            this.div = null;
                        }
                    }
                    addListeners() {
                        const pin = this.div.querySelector('.marker-pin');
                        const border = this.div.querySelector('.marker-border');
                        pin.addEventListener("mouseover", () => {
                            border.style.transition = 'stroke 0.3s ease';
                            border.style.stroke = "#10B981";
                        });
                        pin.addEventListener("mouseout", () => {
                            border.style.stroke = "#d1d5db";
                        });
                        this.div.addEventListener("click", () => {
                            showStudioDetails(this.studio);
                        });
                    }
                }
                const initialCenter = { lat: artistLat, lng: artistLng };

                // Map ko initialize karein
                map = new google.maps.Map(document.getElementById("map"), {
                    center: initialCenter,
                    zoom: 12,
                    disableDefaultUI: true,
                    styles: [
                        { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                        { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                        { "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }] },
                        { "elementType": "labels.text.stroke", "stylers": [{ "color": "#f5f5f5" }] },
                        { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }] },
                        { "featureType": "road.highway", "elementType": "geometry", "stylers": [{ "color": "#dadada" }] },
                        { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#c9c9c9" }] }
                    ]
                });

                // Add Artist's location marker (optional but useful)
                new google.maps.Marker({
                    position: initialCenter,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 8,
                        fillColor: '#006434',
                        fillOpacity: 0.8,
                        strokeWeight: 0
                    },
                    title: 'Your Location'
                });


                const bounds = new google.maps.LatLngBounds();
                bounds.extend(initialCenter); // Include artist's location in bounds

                studiosData.forEach(studio => {
                    const position = new google.maps.LatLng(studio.latitude, studio.longitude);
                    new CustomMarker(position, map, studio);
                    bounds.extend(position);
                });

                // Adjust map view to fit all markers and artist location
                google.maps.event.addListenerOnce(map, 'idle', function() {
                    if (studiosData.length > 0) {
                        map.fitBounds(bounds, 50);
                    } else {
                        map.setCenter(initialCenter);
                        map.setZoom(12);
                    }
                });


                if (panelWrapper) {
                    panelWrapper.addEventListener('click', function(event) {
                        if (event.target && event.target.id === 'close-btn') {
                            panelWrapper.classList.remove('active');
                        }
                    });
                    map.addListener('dragstart', () => panelWrapper.classList.remove('active'));
                    map.addListener('zoom_changed', () => panelWrapper.classList.remove('active'));
                }
            }

            function showStudioDetails(studio) {
                // Is function mein koi badlaav nahi
                panelWrapper.innerHTML = `
            <div class="studio-card"  onclick="window.location.href='${'studios/'+studio.id}'">
                <div class="card-image-wrapper">
                    <button id="close-btn" class="modal-close-btn">×</button>
                    <img src="../${studio.studio_cover}" alt="${studio.studio_name} cover image" class="card-image">
                </div>
                <div class="card-body">
                     <div class="card-header">
                     <div class="d-flex gap-2 mt-2">
                        <div class="card-logo-wrapper">
                            <img src="../${studio.studio_logo}" alt="${studio.studio_name} logo" class="card-logo">
                        </div>
                        <div class="card-title-location">
                            <h3 class="card-title">${studio.studio_name}</h3>
                            <div class="card-location">
                                <img src="{{ asset('assets/web/extra/location_logo.png') }}" alt="Location">
                                <span>${studio.city ? studio.city+', ' : ''}${studio.country.toUpperCase() || 'N/A'}</span>
                            </div>
                        </div>
                        </div>
                        <button class="card-like-btn" aria-label="Like this studio">
                           <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </button>
                    </div>
                    <div class="card-info-section">
                        <div class="card-status-row">
                            <div class="card-verified">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>Verified</span>
                            </div>
                            <span class="card-status-badge ${studio.isOpen ? 'open' : 'closed'}">${studio.isOpen ? 'OPEN' : 'CLOSED'}</span>
                        </div>
                        <div class="card-details">
                            <strong>Hours:</strong> ${studio.hours}<br>
                            <strong>Specialties:</strong> ${studio.designSpecialties}
                        </div>
                    </div>
                </div>
            </div>`;

                panelWrapper.classList.add('active');
            }
        </script>
        <script>
            // This script handles the initial body opacity transition for a smooth load effect.
            window.addEventListener('load', () => {
                document.body.style.opacity = 1;
            });
        </script>
        <script>
            // ======================================================================
            // START: DYNAMIC FILTER MODAL & CALENDAR SCRIPT
            // ======================================================================
            document.addEventListener('DOMContentLoaded', () => {

                // --- Modal Control Elements ---
                const filterButton = document.querySelector('.filter-button');
                const modalOverlay = document.getElementById('filter-modal-overlay');
                const closeModalBtn = document.querySelector('.modal-close-btn');
                const applyBtn = document.querySelector('.next-btn_filter');
                const clearDatesBtn = document.getElementById('clear-dates-btn');

                // --- Calendar Elements ---
                const monthYearDisplay = document.getElementById('calendar-month-year');
                const calendarGridBody = document.getElementById('calendar-grid-body');
                const prevMonthBtn = document.getElementById('prev-month-btn');
                const nextMonthBtn = document.getElementById('next-month-btn');
                const fromDateDisplay = document.getElementById('from-date-display');
                const toDateDisplay = document.getElementById('to-date-display');

                // --- Calendar State ---
                let currentDate = new Date(2025, 8, 1); // September 2025 (months are 0-indexed)
                let startDate = new Date(2025, 8, 14);
                let endDate = new Date(2025, 8, 30);
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];



                // --- Date Formatting ---
                const formatDate = (date) => {
                    if (!date) return "";
                    return date.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                };

                const toISODateString = (date) => {
                    // Returns date in 'YYYY-MM-DD' format, ignoring time zones
                    const year = date.getFullYear();
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    const day = date.getDate().toString().padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }

                // --- Core Calendar Functionality ---
                const renderCalendar = () => {
                    const year = currentDate.getFullYear();
                    const month = currentDate.getMonth();

                    monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
                    calendarGridBody.innerHTML = '';

                    const firstDayOfMonth = new Date(year, month, 1);
                    const lastDayOfMonth = new Date(year, month + 1, 0);
                    const daysInMonth = lastDayOfMonth.getDate();
                    // Adjust to make Monday the first day (0)
                    const startDayIndex = (firstDayOfMonth.getDay() + 6) % 7;

                    // Add blank cells for days before the 1st of the month
                    for (let i = 0; i < startDayIndex; i++) {
                        calendarGridBody.innerHTML += `<div class="day-cell other-month"></div>`;
                    }

                    // Add cells for each day of the current month
                    for (let day = 1; day <= daysInMonth; day++) {
                        const cell = document.createElement('div');
                        cell.classList.add('day-cell');
                        cell.textContent = day.toString().padStart(2, '0');

                        const cellDate = new Date(year, month, day);
                        cell.dataset.date = toISODateString(cellDate);

                        // Add styling for selected dates and range
                        if (startDate && toISODateString(cellDate) === toISODateString(startDate)) {
                            cell.classList.add('start-date');
                            if (endDate && toISODateString(startDate) === toISODateString(endDate)) {
                                cell.classList.add('end-date'); // Also an end date if it's a single day selection
                            }
                        }
                        if (endDate && toISODateString(cellDate) === toISODateString(endDate)) {
                            cell.classList.add('end-date');
                        }
                        if (startDate && endDate && cellDate > startDate && cellDate < endDate) {
                            cell.classList.add('in-range');
                        }

                        cell.addEventListener('click', handleDateClick);
                        calendarGridBody.appendChild(cell);
                    }
                    updateDateInputs();
                };

                const updateDateInputs = () => {
                    fromDateDisplay.textContent = formatDate(startDate);
                    toDateDisplay.textContent = formatDate(endDate);
                };

                const handleDateClick = (e) => {
                    const clickedDateStr = e.target.dataset.date;
                    if (!clickedDateStr) return;

                    const clickedDate = new Date(clickedDateStr +
                    'T00:00:00'); // Use T00:00:00 to avoid timezone issues

                    // Logic for selecting start/end dates
                    if (!startDate || (startDate && endDate)) {
                        startDate = clickedDate;
                        endDate = null;
                    } else if (clickedDate < startDate) {
                        // If clicked date is before start date, make it the new start date
                        startDate = clickedDate;
                    } else {
                        // Otherwise, set it as the end date
                        endDate = clickedDate;
                    }
                    renderCalendar();
                };


                prevMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });

                nextMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });

                clearDatesBtn.addEventListener('click', () => {
                    startDate = null;
                    endDate = null;
                    renderCalendar(); // Re-render to clear highlights and inputs
                });

                // Initial render on page load
                renderCalendar();
            });
        </script>
    @endsection
