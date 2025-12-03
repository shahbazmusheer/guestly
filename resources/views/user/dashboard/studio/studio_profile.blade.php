@extends('user.layouts.master')

@section('title', $studio->studio_name.' Profile')

@section('content')
    <style>
        /* Main Page CSS (No Changes) */
        .location-map-container {
            position: relative;
        }

        #map {
            height: 450px;
            width: 100%;
            border-radius: 12px;
            background-color: #e9e9e9;
        }

        :root {
            --bg-color: #e6f4f0;
            --primary-green: #0b3d27;
            --secondary-green-btn: #5e8082;
            --text-primary: #0b3d27;
            --text-secondary: #5d7a70;
            --border-active: #0b3d27;
            --border-inactive: #e0e7e5;
            --card-bg: #ffffff;
            --label-color: #888;
        }

        .studio-hero:hover .studio-hero-overlay{
            background: linear-gradient(to top, rgba(0,0,0,0.6) 30%, rgba(0,0,0,0.5) 100%);
        }

    </style>

    <style>

        /* --- MODAL STYLES (UPDATED) --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(94, 128, 130, 0.3);
            /* Semi-transparent background */
            display: none;
            /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: var(--card-bg);
            padding: 40px;
            /* Increased padding for better spacing */
            border-radius: 24px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        .success-icon {
            width: 120px;
            /* <<< YAHAN IMAGE SIZE INCREASE KI HAI */
            height: auto;
            margin: auto;
            /*margin-bottom: -100px;*/
            /* Adjusted margin for better spacing */
            /*margin-top: -90px;*/
        }

        .modal-content h2 {
            font-family: 'Arial Rounded MT Bold', sans-serif;
            font-size: 24px;
            font-weight: 500;
            color: #014122;
            margin: 0 0 15px;
        }

        .modal-content p {
            font-size: 16px;
            font-family: 'Actor', sans-serif;
            color: #333333;
            line-height: 1.2;
            margin: 0 auto 30px auto;
            max-width: 90%;

        }

        .modal-continue-btn {
            width: 100%;
            background-color: #014122;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'Arial Rounded MT Bold', sans-serif;
            transition: background-color 0.2s;
            margin-left: -8px;
            margin-top: -15px;
        }

        .modal-continue-btn:hover {
            background-color: #083120;
            /* A slightly darker green for hover */
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
            display: flex;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .studio-flow-container {
            background: var(--card-bg);
            border-radius: 35px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: filter 0.3s ease;
            position: relative;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }

        .form-slider-track {
            position: relative;
            overflow: hidden;
            flex: 1;
            display: flex;
        }

        .form-step {
            width: 100%;
            padding: 40px;
            display: none;
            flex-direction: column;
            flex-shrink: 0;
        }

        .form-step.active {
            display: flex;
        }

        .form-step h2 {
            font-size: 29px;
            font-weight: 500;
            margin: 0 0 5px;
            font-family: 'Arial Rounded MT Bold', sans-serif;
            color: var(--primary-green);
        }

        .form-step p.subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 30px;
        }

        .progress-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .progress-step {
            width: 100px;
            height: 4px;
            background-color: #dbe3e0;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .progress-step.active {
            background-color: var(--primary-green);
        }

        .form-content-scrollable {
            flex: 1;
            overflow-y: auto;
            padding: 5px 15px 10px 0;
            margin-right: -15px;
        }

        .form-content-scrollable::-webkit-scrollbar {
            width: 8px;
        }

        .form-content-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .form-content-scrollable::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* --- STYLES FOR STEP 1 & Floating Labels --- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            margin-bottom: -10px;
            margin-top: -4px;
        }

        .form-group {
            text-align: left;
            width: 100%;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group textarea{
            width: 100%;
        }

        .floating-label-group {
            position: relative;
            margin-bottom: 16px;
        }

        .floating-label-group input, textarea,
        .floating-label-group select {
            width: 100%;
            padding: 22px 16px 10px 16px !important;
            font-size: 16px;
            border: 1px solid #92a5a0;
            border-radius: 8px;
            background-color: var(--card-bg);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .floating-label-group.with-icon select {
            padding-left: 50px;
        }

        .floating-label-group label {
            position: absolute;
            top: 18px;
            left: 17px;
            color: var(--label-color);
            font-size: 16px;
            font-weight: 400;
            pointer-events: none;
            transition: all 0.2s ease-out;
            margin-bottom: 0;
        }

        .floating-label-group.with-icon label {
            left: 50px;
        }

        .floating-label-group input:focus+label,
        .floating-label-group textarea:focus+label,
        .floating-label-group input:not(:placeholder-shown)+label,
        .floating-label-group textarea:not(:placeholder-shown)+label,
        .floating-label-group select:focus+label,
        .floating-label-group select:valid+label {
            top: 7px;
            font-size: 12px;
        }

        .floating-label-group select:focus+label,
        .floating-label-group select:valid+label {
            color: var(--text-secondary);
        }

        .floating-label-group input:focus+label {
            color: var(--primary-green);
        }

        .floating-label-group select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 16px center;
            background-repeat: no-repeat;
            background-size: 1.25em;
            padding-right: 40px;
            appearance: none;
        }

        .form-group-social {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid #92a5a0;
            border-radius: 8px;
        }

        .social-icon img {
            width: 32px;
            height: 32px;
            margin-right: 12px;
        }

        .social-text {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: left;
        }

        .social-name {
            font-size: 14px;
            color: #555;
        }

        .social-status {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .social-disconnect {
            color: #000;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
        }

        .input-with-icon .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: auto;
        }

        /* --- STYLES FOR STEP 2 --- */
        .field-row-bordered {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-inactive);
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            text-align: left;
        }

        .amenities-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 500;
            padding: 10px;
            color: var(--text-primary);
        }

        .amenity-content{
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .policy-preview-container{
            display: none;
        }

        .policy-preview-container.active{
            display: block;
        }

        .billing-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            user-select: none;
            font-family: 'Arial Rounded MT Bold', sans-serif;
            font-weight: 500;
        }

        .billing-toggle span {
            font-size: 14px;
            transition: color 0.4s ease;
            cursor: pointer;
        }

        .billing-toggle span:first-of-type {
            color: var(--primary-green);
        }

        .billing-toggle span:last-of-type {
            color: #828282;
        }

        .billing-toggle:has(input:checked) span:first-of-type {
            color: #828282;
        }

        .billing-toggle:has(input:checked) span:last-of-type {
            color: var(--primary-green);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 6px;
            background-color: var(--primary-green);
            transition: .4s;
            border-radius: 10px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: -6px;
            bottom: -4px;
            background-color: #e6f4f0;
            border: 2px solid var(--primary-green);
            box-sizing: border-box;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .field-group-container {
            border: 1px solid var(--border-inactive);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .field-group-container .field-row-bordered {
            margin-bottom: 0;
            border: none;
            border-radius: 0;
            border-bottom: 1px solid var(--border-inactive);
        }

        .field-group-container .field-row-bordered:last-child {
            border-bottom: none;
        }

        .radio-container {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .radio-container input {
            display: none;
        }

        .radio-visual {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid var(--border-inactive);
            position: relative;
            transition: border-color 0.2s;
        }

        .radio-container input:checked+.radio-visual {
            border-color: #5E8082;
        }

        .radio-visual::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #5E8082;
            transition: transform 0.2s ease-in-out;
        }

        .radio-container input:checked+.radio-visual::after {
            transform: translate(-50%, -50%) scale(1);
        }

        .upload-policy-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-inactive);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .upload-policy-row .placeholder-text {
            font-size: 16px;
            color: var(--label-color);
        }

        .upload-policy-row .upload-link {
            font-weight: 600;
            font-size: 16px;
            color: var(--primary-green);
            text-decoration: underline;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        /* --- STYLES FOR STEP 3 --- */
        .upload-item-container {
            margin-bottom: 24px;
        }

        .upload-box-dotted {
            border: 2px dotted var(--primary-green);
            border-radius: 12px;
            padding: 45px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
            position: relative;
            min-height: 80px;
        }

        .upload-box-dotted:hover {
            background-color: #f7f9f8;
        }

        .upload-box-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: opacity 0.3s;
        }

        .upload-box-content .icon {
            color: var(--primary-green);
        }

        .upload-box-content .label {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-green);
        }

        .upload-info-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 12px;
            text-align: left;
            padding-left: 5px;
        }

        .upload-info-text .icon {
            color: var(--text-secondary);
            flex-shrink: 0;
        }

        .image-preview-inline {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .upload-box-dotted.has-preview .upload-box-content {
            opacity: 0;
        }

        .upload-box-dotted.has-preview .image-preview-inline {
            opacity: 1;
        }

        .image-preview-gallery {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 0;
            min-height: 110px;
        }

        .image-preview-gallery img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .image-preview-gallery::-webkit-scrollbar {
            height: 6px;
        }

        .image-preview-gallery::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 6px;
        }

        .swal2-popup {
            font-family: 'Poppins', sans-serif !important;
        }

        .swal2-confirm {
            background-color: var(--primary-green) !important;
            box-shadow: none !important;
        }

        .swal2-icon.swal2-warning {
            border-color: #f8bb86 !important;
            color: #f8bb86 !important;
        }

        /* --- STYLES FOR STEP 4 --- */
        .supplies-group {
            margin-top: 10px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .supply-tag {
            background-color: #BBDBC3;
            padding: 13px 15px;
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 500;
            font-size: 14px;
            min-width: 160px;
            max-width: fit-content;
            gap: 10px;
            font-family: 'Actor', sans-serif
        }

        .supply-tag .cross {
            color: #BBDBC3;
            cursor: pointer;
            font-weight: bold;
            background-color: #014122;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            margin-left: 5px;
            margin-top: -1px;
            position: relative;
            transition: background-color 0.2s;
            font-family: 'Arial Rounded MT Bold', sans-serif;
        }

        .amenities-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
            margin-top: 16px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .amenity-label {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .amenity-checkbox {
            width: 22px;
            height: 22px;
            border: 2px solid var(--border-inactive);
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            transition: border-color 0.2s;
        }

        .amenity-checkbox::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #5E8082;
            transition: transform 0.2s ease-in-out;
        }

        .amenity-checkbox.checked {
            border-color: #5E8082;
        }

        .amenity-checkbox.checked::after {
            transform: translate(-50%, -50%) scale(1);
        }

        /* --- SHARED STYLES --- */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid var(--border-inactive);
        }

        .nav-btn {
            flex: 1;
            border-radius: 50px;
            padding: 15px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, opacity 0.3s;
            border: none;
            margin: -2px
        }

        .back-btn {
            background-color: var(--secondary-green-btn);
            color: white;
            font-size: 16px;
            font-family: 'Arial Rounded MT Bold', sans-serif;
            font-weight: 500;
        }

        .next-btn {
            background-color: var(--primary-green);
            color: white;
            font-family: 'Arial Rounded MT Bold', sans-serif;
            font-weight: 100;
        }

    </style>
    <div class="studio-detail-layout">

        {{-- ========== LEFT COLUMN: MAIN CONTENT ========== --}}
        <div class="studio-main-content">

            {{-- Hero Image Section (No changes here) --}}
            <section class="studio-hero">
                <img src="{{ $studio->studio_cover ? asset($studio->studio_cover) : asset('assets/web/dashboard/logo_main.jpg') }}" alt="{{ $studio->studio_name }} Cover">
                <div class="studio-hero-overlay">
                    <div class="hero-top">
                        <div class="studio-info-header">
                            <img src="{{ $studio->studio_logo ? asset($studio->studio_logo) : asset('assets/web/dashboard/default_1.png') }}"
                                 alt="{{ $studio->studio_name }} Logo"
                                class="studio-logo">
                            <div>
                                <h1 class="studio-name">{{ $studio->studio_name ?? $studio->name }}</h1>
                                <p class="studio-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                    </svg>
                                    {{ $studio->city ?? 'City Not Set' }}, {{ $studio->country ?? 'Country Not Set' }}
                                </p>
                            </div>
                        </div>
                        <button class="like-button active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z" />
                            </svg>
                        </button>
                    </div>
                    @if(Auth::user()->id == $studio->id)
                        <div class="hero-center">
                            <button class="btn btn-edit-profile"><i class="fa fa-edit me-2 fs-5 text-white"></i>Edit Profile</button>
                        </div>
                    @endif
                    <div class="hero-bottom">
                        <div class="verified-badge" style="{{ $studio->verification_status == '2' ? '' : 'display:none;' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.638.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.639a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.638-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.638zM8.982 1.75c.576-.576 1.48-.576 2.056 0l.622.637.89-.011c1.033 0 1.9.867 1.9 1.9l-.01.89.638.622c.576.576.576 1.48 0 2.056l-.637.622.011.89c0 1.033-.867 1.9-1.9 1.9l-.89-.01.622.639c.576.576.576 1.48 0 2.056l-.622.637-.89.011c-1.033 0-1.9-.867-1.9-1.9l.01-.89-.638-.622c-.576-.576-.576-1.48 0-2.056l.637-.622-.011-.89c0-1.033.867-1.9 1.9-1.9l.89.01-.622-.638a1.44 1.44 0 0 1-.515-.923L8.982 1.75zM6.5 12.011l-3-3 1.054-1.054 1.946 1.947 4.95-4.95 1.054 1.054-6 6z" />
                            </svg>
                            {{ $studio->verification_status == '2' ? 'Verified' : 'Pending Review' }}
                        </div>
                        <div class="carousel-dots">
                            {{-- Display dots based on available gallery images (up to 5) --}}
                            @if($studio->studioImages)
                                @foreach($studio->studioImages->take(5) as $index => $image)
                                    <span class="dot {{ $index === 0 ? 'active' : '' }}"></span>
                                @endforeach
                            @else
                                <span class="dot active"></span>
                            @endif
                        </div>
                        <div class="star-rating">★★★★☆ <span>{{-- Dynamic Review Count Here --}} 0 Review</span></div>
                    </div>
                </div>
            </section>

            {{-- Small Image Gallery --}}
            <section class="studio-image-gallery">
                @forelse($studio->studioImages as $image)
                    {{-- Use the provided asset path --}}
                    <img src="{{ asset($image->image_path) }}" alt="Studio Gallery Image">
                @empty
                    {{-- Placeholder if no images are uploaded --}}
                    <img src="{{ asset('assets/web/dashboard/placeholder.jpg') }}" alt="Placeholder Image 1">
                    <img src="{{ asset('assets/web/dashboard/placeholder.jpg') }}" alt="Placeholder Image 2">
                    <img src="{{ asset('assets/web/dashboard/placeholder.jpg') }}" alt="Placeholder Image 3">
                @endforelse
            </section>

            {{-- About Section --}}
            <section class="studio-section">
                <h2 class="section-title-md">About {{ $studio->studio_name ?? 'Studio' }}</h2>
                <p>{{ $studio->bio ?? 'The studio bio has not been set yet. This is where you describe your studio, specialties, and vibe.' }}</p>
            </section>

            {{-- NEW: 2-Column Grid for Amenities and Map --}}
            <div class="amenities-map-grid">
                {{-- Column 1: Amenities --}}
                <section>
                    <h2 class="section-title-md">Amenities</h2>
                    <div class="amenities-list">
                        @forelse($studio->stationAmenitiesProvided as $amenity)
                            <div class="amenity-item" style="justify-content: left">
                                <div class="amenity-icon-container">
                                    {{-- NOTE: Assuming amenity->icon holds the path or a reference --}}
                                    <img src="{{ $amenity->icon ? asset($amenity->icon) : asset('assets/web/extra/amenities_1.png') }}"
                                         alt="{{ $amenity->name }} Icon"
                                         class="amenity-icon">
                                </div>
                                <span>{{ $amenity->name }}</span>
                            </div>
                        @empty
                            <p>No amenities have been listed yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="location-map-container">
                    <h2 class="section-title-md">Our Location</h2>

                    <div id="map"></div>
                </section>
            </div>

            {{-- Reviews Section --}}
            <section class="studio-section">
                <h2 class="section-title-md">What other people say</h2>
                <div class="reviews-grid">
                    {{-- Review Cards... --}}
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-user-info"><img src="https://i.pravatar.cc/40?u=jessica1" alt="Jessica L.">
                                <div><strong>Jessica L.</strong><span>San Diego, California, USA</span></div>
                            </div>
                            <div class="review-date">Jan 21 2025 <span>9:08PM</span></div>
                        </div>
                        <p class="review-text">"Absolutely loved my experience at Ink Haven! The studio is spotless, and my
                            fine line piece came out even better than I imagined. Super professional and welcoming vibe.
                            Will definitely be back!"</p>
                        <img src="{{ asset('assets/web/dashboard/review_logo.jpg') }}" class="review-image" alt="Tattoo example">
                    </div>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-user-info"><img src="https://i.pravatar.cc/40?u=jessica2" alt="Jessica L.">
                                <div><strong>Jessica L.</strong><span>San Diego, California, USA</span></div>
                            </div>
                            <div class="review-date">Jan 21 2025 <span>8:08PM</span></div>
                        </div>
                        <p class="review-text">"Absolutely loved my experience at Ink Haven! The studio is spotless, and my
                            fine line piece came out even better than I imagined. Super professional and welcoming vibe.
                            Will definitely be back!"</p>
                        <img src="{{ asset('assets/web/dashboard/review_logo.jpg') }}" class="review-image" alt="Tattoo example">
                    </div>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-user-info"><img src="https://i.pravatar.cc/40?u=jessica2"
                                    alt="Jessica L.">
                                <div><strong>Jessica L.</strong><span>San Diego, California, USA</span></div>
                            </div>
                            <div class="review-date">Jan 21 2025 <span>8:08PM</span></div>
                        </div>
                        <p class="review-text">"Absolutely loved my experience at Ink Haven! The studio is spotless, and my
                            fine line piece came out even better than I imagined. Super professional and welcoming vibe.
                            Will definitely be back!"</p>
                        <img src="{{ asset('assets/web/dashboard/review_logo.jpg') }}" class="review-image" alt="Tattoo example">
                    </div>
                </div>
            </section>
        </div>
        @if(Auth::user()->id == $studio->id)
        <div class="modal-overlay" id="edit-profile-modal">
            <div class="modal-content">
                <div>
                    <form id="steps-form" method="post" action="{{route('dashboard.update_studio_profile')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="studio-flow-container">
                            <div class="form-slider-track">
                                <!-- STEP 1 -->
                                <div class="form-step" data-step="0">
                                    <h2>Edit Your Studio</h2>
                                    <p class="subtitle">Update Your Information</p>
                                    <div class="progress-indicator">
                                        <div class="progress-step active"></div>
                                        <div class="progress-step"></div>
                                        <div class="progress-step"></div>
                                        <div class="progress-step"></div>
                                    </div>
                                    <div class="form-content-scrollable">
                                        <div class="form-grid">
                                            <div class="form-group floating-label-group"><input type="text" value="{{ $studio->studio_name ? $studio->studio_name : '' }}" name="studio_name" required
                                                                                                placeholder=" "><label for="studio-name">Studio Name</label></div>
                                            <div class="form-group floating-label-group"><input type="email" name="studio_email" value="{{ $studio->business_email ? $studio->business_email : '' }}" id="business-email" required
                                                                                                placeholder=" "><label for="business-email">Business Email</label></div>
                                            <div class="form-group full-width floating-label-group"><input type="text" name="studio_address" value="{{ $studio->address ? $studio->address : '' }}"
                                                                                                           id="studio-address" required placeholder=" "><label for="studio-address">Studio
                                                    Address</label></div>
                                            {{--                        <div class="form-group full-width floating-label-group with-icon input-with-icon"><img--}}
                                            {{--                                src="https://flagcdn.com/gb.svg" alt="UK Flag" class="icon"><select id="languages" name="studio_language"--}}
                                            {{--                                                                                                    required>--}}
                                            {{--                                <option value="" disabled selected hidden></option>--}}
                                            {{--                                <option value="English">English</option>--}}
                                            {{--                                <option value="Spanish">Spanish</option>--}}
                                            {{--                            </select><label for="languages">Languages</label></div>--}}
                                            <div class="form-group full-width floating-label-group"><input type="url" id="website-url" name="website_url" value="{{ $studio->website_url ? $studio->website_url : '' }}"
                                                                                                           placeholder=" "><label for="website-url">Website URL</label></div>
                                            {{--                        <div class="form-group full-width floating-label-group"><select id="country" name="country" required>--}}
                                            {{--                                <option value="" disabled selected hidden></option>--}}
                                            {{--                                <option value="US">United States (+1)</option>--}}
                                            {{--                                <option value="UK">United Kingdom (+44)</option>--}}
                                            {{--                            </select><label for="country">Country/Region</label></div>--}}
                                            {{--                        <div class="form-group full-width floating-label-group"><input type="tel" id="phone-number" name="studio_phone"--}}
                                            {{--                                                                                       value="+1 " placeholder=" "><label for="phone-number">Phone number</label>--}}
                                            {{--                        </div>--}}
                                        </div>
                                        <div class="form-group full-width">
                                            <div class="form-group-social">
                                                <div class="social-icon"><img
                                                        src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png"
                                                        alt="Instagram Logo"></div>
                                                <div class="social-text"><span class="social-name">Instagram</span><span
                                                        class="social-status">Connected</span></div><a href="#"
                                                                                                       class="social-disconnect">Disconnect</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="button-group"><button type="button" class="nav-btn next-btn">Next</button></div>
                                </div>

                                <!-- STEP 2 -->
                                <div class="form-step" data-step="1">
                                    <h2>Edit Your Studio</h2>
                                    <p class="subtitle">Update the Basic Information</p>
                                    <div class="progress-indicator">
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                        <div class="progress-step"></div>
                                        <div class="progress-step"></div>
                                    </div>
                                    <div class="form-content-scrollable">
                                        <div class="form-group floating-label-group"><input type="number" name="stations_available" value="{{  $studio->guest_spots ? $studio->guest_spots : '1' }}" min="1" id="stations_available" required
                                                                                            placeholder=" "><label for="stations_available">Total Guest Stations Available</label></div>
                                        <div class="form-group floating-label-group"><input type="number" name="total_stations" value="{{  $studio->total_stations ?  $studio->total_stations : '1' }}" min="1" id="total_stations" required
                                                                                            placeholder=" "><label for="stations_available">Total Stations</label></div>
                                        {{--                    <div class="floating-label-group"><select name="stations_available" required>--}}
                                        {{--                            <option value="12" selected>12</option>--}}
                                        {{--                            <option value="9">9</option>--}}
                                        {{--                            <option value="6">6</option>--}}
                                        {{--                            <option value="3">3</option>--}}
                                        {{--                            <option value="1">1</option>--}}
                                        {{--                        </select><label>Total Guest Stations Available</label></div>--}}
                                        <div class="floating-label-group"><select name="studio_type" required>
                                                <option value="1" {{ $studio->studio_type ? ($studio->studio_type=='1' ? 'selected' : '') : 'selected' }}>Walk-In</option>
                                                <option value="2" {{ $studio->studio_type=='2' ? 'selected' : '' }}>Appointment</option>
                                                <option value="3" {{ $studio->studio_type=='3' ? 'selected' : '' }}>Private Studio</option>
                                            </select><label>Studio Type</label></div>
                                        <div class="field-row-bordered"><span class="row-label">Require Portfolio</span>
                                            <div class="billing-toggle"><span>No</span><label class="switch"><input name="require_portfolio" {{ $studio->require_portfolio=='1' ? 'checked' : '' }} type="checkbox"><span
                                                        class="slider"></span></label><span>Yes</span></div>
                                        </div>
                                        <div class="field-row-bordered"><span class="row-label">Accept Bookings Now</span>
                                            <div class="billing-toggle"><span>No</span><label class="switch"><input name="accept_bookings" {{ $studio->accept_bookings=='1' ? 'checked' : '' }} type="checkbox"><span
                                                        class="slider"></span></label><span>Yes</span></div>
                                        </div>
                                        <div class="floating-label-group"><select name="preferred_duration" required>
                                                <option value="0" {{ $studio->preferred_duration=='0' ? 'selected' : '' }}>No Preference</option>
                                                <option value="1" {{ $studio->preferred_duration=='1' ? 'selected' : '' }}>Short term (less than a month)</option>
                                                <option value="2" {{ $studio->preferred_duration=='2' ? 'selected' : '' }}>Long term (more than a month)</option>
                                            </select><label>Preferred Guest Duration</label></div>
                                        <div class="field-row-bordered"><span class="row-label">Allow Guest to Choose</span>
                                            <div class="billing-toggle"><span>No</span><label class="switch"><input name="allow_guest_to_choose" {{ $studio->allow_guest_to_choose=='1' ? 'checked' : '' }} type="checkbox"><span
                                                        class="slider"></span></label><span>Yes</span></div>
                                        </div>
                                        <div class="form-group full-width floating-label-group"><textarea rows="3" name="bio"
                                                                                                          id="bio" placeholder=" ">{{ $studio->bio ? $studio->bio : '' }}</textarea><label for="studio-address">Write something about your studio</label></div>

                                        <div class="floating-label-group">
                                            <select name="commission_type" id="commission_type" required>
                                                <option value="" disabled hidden selected></option>
                                                <option value="0" {{ $studio->commission_type=='0' ? 'selected' : '' }}>Fixed</option>
                                                <option value="1" {{ $studio->commission_type=='1' ? 'selected' : '' }}>Percentage</option>
                                                <option value="2" {{ $studio->commission_type=='2' ? 'selected' : '' }}>Custom</option>
                                            </select>
                                            <label>Commission Options</label>
                                        </div>

                                        <div class="form-group floating-label-group" id="commission_value_group" style="{{$studio->commission_type ?? 'display:none;' }}">
                                            <input type="number" value="{{ $studio->commission_value ? $studio->commission_value : 0 }}" name="commission_value" id="commission_value" placeholder=" ">
                                            <label for="commission_value">Commission Value</label>
                                        </div>
                                        <div class="upload-policy-row">
                                            <div style="display: flex; flex-direction: column">
                                                <span class="placeholder-text">Upload Guest Policy</span>
                                                <!-- File name will appear here -->
                                                <div id="guest_policy_filename" style="margin-top:5px; font-size:12px; display: block; text-align: left"></div>
                                            </div>
                                            <input type="file" id="guest_policy_file" name="guest_policy" hidden accept="image/*">
                                            <div class="box">
                                                <button type="button" class="upload-link me-2 text-secondary" id="view_guest_policy_btn">View</button>
                                                <button type="button" class="upload-link" id="upload_guest_policy_btn">Change</button>
                                            </div>
                                        </div>
                                        <div class="policy-preview-container">
                                            <img src="../{{$studio->guest_policy}}" style="max-width: 100%; border: 2px solid rgba(255,255,255,0.65); border-radius: 10px;" alt="guest policy">
                                        </div>
                                    </div>
                                    <div class="button-group"><button type="button" class="nav-btn back-btn">Back</button><button
                                            type="button" class="nav-btn next-btn">Next</button></div>
                                </div>

                                <!-- STEP 3 -->
                                <div class="form-step" data-step="2">
                                    <h2>Edit Your Studio</h2>
                                    <p class="subtitle">Update Studio Photos</p>
                                    <div class="progress-indicator">
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                        <div class="progress-step"></div>
                                    </div>
                                    <div class="form-content-scrollable">
                                        <div class="upload-item-container">
                                            <div class="upload-box-dotted" id="logo-upload-box">
                                                <div class="upload-box-content"><span class="icon">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z" />
                                        </svg></span><span class="label">Studio Logo</span></div>
                                                <img id="logo-preview" class="image-preview-inline" src="" alt="Logo Preview">
                                            </div>
                                            <input type="file" name="logo" id="logo-file-input" hidden accept="image/*">
                                            <p class="upload-info-text"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                                                                width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.064.293.006.399.287.47l.45.083.082.38-2.29.287-.082-.38.45-.083a.39.39 0 0 0 .288-.469l.738-3.468a.39.39 0 0 0-.288-.469l-.45-.083-.082-.38 2.29-.287zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                    </svg></span><span>You can upload PDF, JPG, PNG, or ZIP files up to 50MB each.</span>
                                            </p>
                                        </div>
                                        <div class="upload-item-container">
                                            <div class="upload-box-dotted" id="cover-upload-box">
                                                <div class="upload-box-content"><span class="icon"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                        </svg></span><span class="label">Studio Cover</span></div>
                                                <img id="cover-preview" class="image-preview-inline" src="" alt="Cover Preview">
                                            </div>
                                            <input type="file" name="cover" id="cover-file-input" hidden accept="image/*">
                                            <p class="upload-info-text"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                                                                width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.064.293.006.399.287.47l.45.083.082.38-2.29.287-.082-.38.45-.083a.39.39 0 0 0 .288-.469l.738-3.468a.39.39 0 0 0-.288-.469l-.45-.083-.082-.38 2.29-.287zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                    </svg></span><span>You can upload PDF, JPG, PNG, or ZIP files up to 50MB each.</span>
                                            </p>
                                        </div>
                                        <div class="upload-item-container">
                                            <div class="upload-box-dotted" id="gallery-upload-box">
                                                <div class="upload-box-content"><span class="icon"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z" />
                                        </svg></span><span class="label">Upload 1–5 Studio Photos</span></div>
                                            </div>
                                            <p class="upload-info-text"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                                                                width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.064.293.006.399.287.47l.45.083.082.38-2.29.287-.082-.38.45-.083a.39.39 0 0 0 .288-.469l.738-3.468a.39.39 0 0 0-.288-.469l-.45-.083-.082-.38 2.29-.287zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                    </svg></span><span>You can upload PDF, JPG, PNG, or ZIP files up to 50MB each.</span>
                                            </p>
                                        </div>
                                        <input type="file" name="gallery[]" id="gallery-file-input" multiple hidden accept="image/*">
                                        <div class="image-preview-gallery" id="gallery-preview-container"></div>
                                    </div>
                                    <div class="button-group"><button type="button" class="nav-btn back-btn">Back</button><button
                                            type="button" class="nav-btn next-btn">Next</button></div>
                                </div>

                                <!-- STEP 4 -->
                                <div class="form-step" data-step="3">
                                    <h2>Edit Your Studio</h2>
                                    <p class="subtitle">Update Supplies & Amenities</p>
                                    <div class="progress-indicator">
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                        <div class="progress-step active"></div>
                                    </div>
                                    <div class="form-content-scrollable">
                                        <div class="floating-label-group">
                                            <select id="supplies-provided">
                                                <option value="" disabled selected hidden></option>
                                                @foreach($supplies as $supply)
                                                    <option data-name="{{$supply->name}}" value="{{$supply->id}}">{{$supply->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="supplies-provided">Supplies Provided</label>
                                        </div>
                                        <div class="supplies-group" id="selected-supplies"></div>

                                        {{-- === DESIGN SPECIALTIES (NEW SECTION) === --}}
                                        <div class="floating-label-group">
                                            <select id="design-specialties-provided">
                                                <option value="" disabled selected hidden></option>
                                                {{-- Assuming $design_specialties is passed from controller --}}
                                                @foreach($design_specialties as $specialty)
                                                    <option data-name="{{$specialty->name}}" value="{{$specialty->id}}">{{$specialty->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="design-specialties-provided">Design Specialties</label>
                                        </div>
                                        <div class="supplies-group" id="selected-design-specialties"></div>

                                        <h4>Select Station Amenities Needs</h4>

                                        <div class="field-group-container">
                                            @foreach($station_amenities as $amenity)
                                                <div class="amenities-row">
                                                    <div class="amenity-content">
                                                        <img src="../{{$amenity->icon}}" alt="icon" style="width: 20px;">
                                                        <span>{{$amenity->name}}</span>
                                                    </div>
                                                    <div>
                                                        <label class="radio-container">
                                                            <input type="checkbox" {{ $studio->stationAmenityIds ? (in_array($amenity->id, $studio->stationAmenityIds) ? 'checked' : '') : '' }} value="{{$amenity->id}}" name="amenities[]">
                                                            <span class="radio-visual"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="button-group"><button class="nav-btn back-btn">Back</button><button
                                            class="nav-btn next-btn" type="submit" id="finish-btn">Update</button></div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endif
        <!-- Success Modal -->
        <!-- Success Modal -->
        <div class="modal-overlay" id="success-modal">
            <div class="modal-content">
                <img class="success-icon" src="{{ asset('assets/web/thumbs_up.png') }}" alt="Thumbs Up">
                <h2>Profile Updated!</h2>
                <p>Your studio profile has been successfully updated.</p>
                <button class="modal-continue-btn" id="modal-continue-btn">Continue</button>
            </div>
        </div>

        <!-- FAILED Modal -->
        <div class="modal-overlay" id="failure-modal">
            <div class="modal-content">
                <img class="success-icon" src="{{ asset('assets/web/warning-error-triangle.png') }}" alt="Failure Icon" style="filter: hue-rotate(290deg) saturate(3);">
                <div id="failure-modal-message">
                    <h2>Update Failed</h2>
                    <p>There were issues with your submission. Please correct the errors listed below and try again.</p>
                </div>
                <div id="validation-errors-list" style="text-align: left; max-height: 150px; overflow-y: auto; background: #fdf6f6; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px; color: #cc0000; font-size: 14px;">
                        <!-- Errors will be injected here by JavaScript -->
                    </ul>
                </div>
                <button class="modal-continue-btn" id="modal-retry-btn">Try Again</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap">
    </script>
    <script>
        const studios = [{
                name: "{{ $studio->studio_name ?? $studio->name }}",
                location: "{{ $studio->city ?? 'N/A' }}, {{ $studio->country ?? 'N/A' }}",
                lat: {{ $studio->latitude && is_numeric($studio->latitude) ? $studio->latitude : '40.7128' }},
                lng: {{ $studio->longitude && is_numeric($studio->longitude) ? $studio->longitude : '-74.0060' }},
                logo: "{{ $studio->studio_logo ? asset($studio->studio_logo) : asset('assets/web/dashboard/default_1.png') }}",
                coverImage: "{{ $studio->studio_cover ? asset($studio->studio_cover) : asset('assets/web/dashboard/logo_main.jpg') }}",
                hours: 'Not available',
                specialties: 'Not Set',
                isOpen: true
            }
        ];

        const openModalBtn = document.querySelector('.btn-edit-profile');
        const profileModal = document.getElementById('edit-profile-modal');
        const container = document.querySelector('.studio-detail-layout');

        openModalBtn.addEventListener('click', function (){
            profileModal.classList.toggle('active');
            container.classList.toggle('blur-background');
        })

        const closeModal = (modal) => {
            modal.classList.remove('active');
            container.classList.remove('blur-background');
        };
        profileModal.addEventListener('click', (e) => {
            if (e.target === profileModal) closeModal(profileModal);
        });

        const viewPolicyBtn = document.getElementById('view_guest_policy_btn');
        const policyContainer = document.querySelector('.policy-preview-container')
        viewPolicyBtn.addEventListener('click', function (){
            policyContainer.classList.toggle('active');
        })

        let map;
        const panelWrapper = document.getElementById('details-panel-wrapper');

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
                    this.div.innerHTML =
                        `<div class="marker-label">${this.studio.name}</div><div class="marker-pin"><svg width="48" height="56" viewBox="0 0 60 70"><defs><filter id="shadow" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.3"/></filter></defs><path d="M30 70C30 70 5 45 5 25C5 12.8 16.2 2 30 2C43.8 2 55 12.8 55 25C55 45 30 70 30 70Z" fill="white" filter="url(#shadow)"/><circle cx="30" cy="25" r="22" fill="none" stroke="#d1d5db" stroke-width="4" class="marker-border"/><image href="${this.studio.logo}" x="8" y="3" height="44" width="44" clip-path="url(#circleClip${this.studio.lat})" /><clipPath id="circleClip${this.studio.lat}"><circle cx="30" cy="25" r="22"/></clipPath></svg></div>`;
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

            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: {{ $studio->latitude && is_numeric($studio->latitude) ? $studio->latitude : '40.7128' }},
                    lng: {{ $studio->longitude && is_numeric($studio->longitude) ? $studio->longitude : '-74.0060' }},
                },
                zoom: 12,
                disableDefaultUI: true,
                styles: [{
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#f5f5f5"
                    }]
                }, {
                    "elementType": "labels.icon",
                    "stylers": [{
                        "visibility": "off"
                    }]
                }, {
                    "elementType": "labels.text.fill",
                    "stylers": [{
                        "color": "#616161"
                    }]
                }, {
                    "elementType": "labels.text.stroke",
                    "stylers": [{
                        "color": "#f5f5f5"
                    }]
                }, {
                    "featureType": "road",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#ffffff"
                    }]
                }, {
                    "featureType": "road.highway",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#dadada"
                    }]
                }, {
                    "featureType": "water",
                    "elementType": "geometry",
                    "stylers": [{
                        "color": "#c9c9c9"
                    }]
                }]
            });

            const bounds = new google.maps.LatLngBounds();

            studios.forEach(studio => {
                const position = new google.maps.LatLng(studio.lat, studio.lng);
                new CustomMarker(position, map, studio);
                bounds.extend(position);
            });

            google.maps.event.addListenerOnce(map, 'idle', function() {
                if (studios.length > 0) {
                    if (studios.length > 1) {
                        map.fitBounds(bounds, 50);
                    } else {
                        map.setCenter(bounds.getCenter());
                        map.setZoom(14);
                    }
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
            if (!panelWrapper) return;
            panelWrapper.innerHTML = `
            <div class="studio-card">
                <div class="card-image-wrapper">
                    <button id="close-btn">×</button>
                    <img src="${studio.coverImage}" alt="${studio.name} cover image" class="card-image">
                </div>
                <div class="card-body">
                     <div class="card-header">
                        <div class="card-logo-wrapper">
                            <img src="${studio.logo}" alt="${studio.name} logo" class="card-logo">
                            <div class="card-title-location">
                                <h3 class="card-title">${studio.name}</h3>
                                <div class="card-location">
                                    <img src="{{ asset('assets/web/extra/location_logo.png') }}" alt="Location">
                                    <span>${studio.location}</span>
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
                            <strong>Specialties:</strong> ${studio.specialties}
                        </div>
                    </div>
                </div>
            </div>`;

            panelWrapper.classList.add('active');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nextBtns = document.querySelectorAll('.next-btn');
            const backBtns = document.querySelectorAll('.back-btn');
            const steps = document.querySelectorAll('.form-step');
            const selectedSupplies = new Set();
            const selectedSupplyIds = [];
            const selectedDesignSpecialties = new Set();
            const selectedDesignSpecialtyIds = [];
            let currentStep = 0;

            // --- New Logic to map fields to steps for error display ---
            const fieldToStepMap = {
                'studio_name': 0, 'studio_email': 0, 'studio_address': 0, 'website_url': 0,
                'stations_available': 1, 'total_stations': 1, 'studio_type': 1, 'preferred_duration': 1,
                'commission_type': 1, 'commission_value': 1, 'require_portfolio': 1, 'accept_bookings': 1,
                'allow_guest_to_choose': 1, 'bio': 1, 'guest_policy': 1,
                'logo': 2, 'cover': 2, 'gallery': 2, 'gallery.*': 2,
                'supplies': 3, 'supplies.*': 3, 'design_specialties': 3, 'design_specialties.*': 3, 'amenities': 3, 'amenities.*': 3,
            };

            function goToStep(stepIndex) {
                const totalSteps = steps.length;
                if (stepIndex >= totalSteps || stepIndex < 0) return;
                steps.forEach((step, index) => step.classList.toggle('active', index === stepIndex));
                currentStep = stepIndex;
                const activeStep = steps[currentStep];
                if (activeStep) {
                    const backBtn = activeStep.querySelector('.back-btn');
                    if (backBtn) backBtn.disabled = currentStep === 0;
                    // Scroll to top of the scrollable area on step change
                    const scrollable = activeStep.querySelector('.form-content-scrollable');
                    if (scrollable) scrollable.scrollTop = 0;
                }
                updateProgressBar();
            }

            const updateProgressBar = () => {
                steps.forEach((step) => {
                    const indicator = step.querySelector('.progress-indicator');
                    if (indicator) {
                        const stepIndicators = indicator.querySelectorAll('.progress-step');
                        stepIndicators.forEach((progress, progressIdx) => progress.classList.toggle(
                            'active', progressIdx <= currentStep));
                    }
                });
            };

            // --- COMMISSION VALUE LOGIC (Your existing code) ---
            document.getElementById('commission_type').addEventListener('change', function () {
                const type = this.value;
                const group = document.getElementById('commission_value_group');
                const input = document.getElementById('commission_value');

                if (type === "") {
                    group.style.display = "none";
                    return;
                }

                // Define event listener inside here to capture 'type' correctly
                const updateCommissionMax = function () {
                    if(document.getElementById('commission_type').value === "1" && parseFloat(this.value) > 100){
                        this.value = 100;
                    }
                };

                // Remove previous listener if it exists before adding a new one
                input.removeEventListener('change', updateCommissionMax);
                input.addEventListener('change', updateCommissionMax);

                group.style.display = "block";

                if (type === "1") { // Percentage
                    input.min = 0;
                    input.max = 100;
                    input.type = "number";
                    input.value = "";
                } else { // Fixed/Custom
                    input.removeAttribute('max');
                    input.min = 1;
                    input.type = "number";
                    input.value = "";
                }
                input.placeholder = " "; // Ensure placeholder is correct after type change
            });

            // --- GUEST POLICY UPLOAD LOGIC (Your existing code) ---
            function uploadGuestPolicy() {
                const fileInput = document.getElementById('guest_policy_file');
                const fileNameHolder = document.getElementById('guest_policy_filename');
                fileInput.click();
                fileInput.onchange = () => {
                    if (fileInput.files.length > 0) {
                        fileNameHolder.innerText = fileInput.files[0].name;
                    }
                };
            }
            document.getElementById('upload_guest_policy_btn').onclick = uploadGuestPolicy;


            // --- STEP NAVIGATION LOGIC (Your existing code) ---
            nextBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep < steps.length - 1) {
                        // You might want to add client-side validation logic here
                        goToStep(currentStep + 1);
                    }
                });
            });
            backBtns.forEach(btn => btn.addEventListener('click', () => {
                if (currentStep > 0) goToStep(currentStep - 1);
            }));

            function loadOldImages(boxId,previewId, fileSrc){
                const box = document.getElementById(boxId);
                const preview = document.getElementById(previewId);
                if (fileSrc) {
                    preview.src = '../'+fileSrc;
                    box.classList.add('has-preview');
                }
            }

            function loadOldGalleryImages(images ) {
                const galleryUploadBox = document.getElementById('gallery-upload-box');
                const galleryFileInput = document.getElementById('gallery-file-input');
                const galleryPreviewContainer = document.getElementById('gallery-preview-container');
                if (galleryUploadBox && galleryFileInput && galleryPreviewContainer) {
                    for (const studioImage of images) {
                            const img = document.createElement('img');
                            img.src = '../'+studioImage.image_path;
                            galleryPreviewContainer.appendChild(img);
                    }

                }
            }

            loadOldGalleryImages(@json($studio->studioImages));

            loadOldImages('logo-upload-box', 'logo-preview', @json($studio->studio_logo));
            loadOldImages('cover-upload-box', 'cover-preview', @json($studio->studio_cover));

            // --- STEP 3 UPLOAD LOGIC (Your existing code) ---
            function setupSingleUploader(boxId, inputId, previewId) {
                const box = document.getElementById(boxId);
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                if (box && input && preview) {
                    box.addEventListener('click', () => input.click());
                    input.addEventListener('change', (event) => {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = () => {
                                preview.src = reader.result;
                                box.classList.add('has-preview');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }
            setupSingleUploader('logo-upload-box', 'logo-file-input', 'logo-preview');
            setupSingleUploader('cover-upload-box', 'cover-file-input', 'cover-preview');

            const galleryUploadBox = document.getElementById('gallery-upload-box');
            const galleryFileInput = document.getElementById('gallery-file-input');
            const galleryPreviewContainer = document.getElementById('gallery-preview-container');

            if (galleryUploadBox && galleryFileInput && galleryPreviewContainer) {
                galleryUploadBox.addEventListener('click', () => galleryFileInput.click());
                galleryFileInput.addEventListener('change', (event) => {
                    const files = event.target.files;
                    if (files.length > 5) {
                        Swal.fire({
                            title: 'Limit Exceeded!', text: 'You can only upload a maximum of 5 photos.', icon: 'warning', confirmButtonText: 'Got it!',
                            customClass: { popup: 'swal2-popup', confirmButton: 'swal2-confirm' }
                        });
                        galleryFileInput.value = "";
                        return;
                    }

                    galleryPreviewContainer.innerHTML = '';
                    for (const file of files) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = () => {
                                const img = document.createElement('img');
                                img.src = reader.result;
                                galleryPreviewContainer.appendChild(img);
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            }
            // --- STEP 4 TAG SELECTOR LOGIC (Your existing code) ---
            function setupTagSelector(selectId, containerId, inputName, selectedNames) {
                const select = document.getElementById(selectId);
                const container = document.getElementById(containerId);
                // const selectedNames = new Set();
                const selectedIds = [];

                if (select && container) {
                    select.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const id = selectedOption.value;
                        const name = selectedOption.getAttribute('data-name');

                        if (name && !selectedNames.has(name)) {
                            selectedNames.add(name);
                            selectedIds.push(id);

                            const tag = document.createElement('div');
                            tag.className = 'supply-tag';
                            tag.innerHTML = `
                            <input type="hidden" value="${id}" name="${inputName}">
                            ${name} <span class="cross">×</span>
                        `;

                            tag.querySelector('.cross').onclick = () => {
                                selectedNames.delete(name);
                                const index = selectedIds.indexOf(id);
                                if (index !== -1) selectedIds.splice(index, 1);
                                tag.remove();
                            };

                            container.appendChild(tag);
                        }
                        this.value = ""; // Reset select dropdown
                    });
                }
            }
            setupTagSelector('supplies-provided', 'selected-supplies', 'supplies[]', selectedSupplies);
            setupTagSelector('design-specialties-provided', 'selected-design-specialties', 'design_specialties[]', selectedDesignSpecialties);

            function loadOldTags(selectId, containerId, inputName, selectedIds, selectedNames, oldData) {
                if (oldData) {
                    const select = document.getElementById(selectId);
                    const container = document.getElementById(containerId);
                    if (select && container) {
                        oldData.forEach(option => {
                                const id = option.id;
                                const name = option.name;

                                if (name && !selectedNames.has(name)) {
                                    selectedNames.add(name);
                                    selectedIds.push(id);

                                    const tag = document.createElement('div');
                                    tag.className = 'supply-tag';
                                    tag.innerHTML = `
                            <input type="hidden" value="${id}" name="${inputName}">
                            ${name} <span class="cross">×</span>
                        `;

                                    tag.querySelector('.cross').onclick = () => {
                                        selectedNames.delete(name);
                                        const index = selectedIds.indexOf(id);
                                        if (index !== -1) selectedIds.splice(index, 1);
                                        tag.remove();
                                    };

                                    container.appendChild(tag);

                            }
                        })
                    }
                }
            }
            loadOldTags('supplies-provided', 'selected-supplies', 'supplies[]', selectedSupplyIds, selectedSupplies, @json($studio->supplies));
            loadOldTags('design-specialties-provided', 'selected-design-specialties', 'design_specialties[]', selectedDesignSpecialtyIds, selectedDesignSpecialties, @json($studio->designSpecialties));

            const amenityCheckboxes = document.querySelectorAll('.amenities-row input[type="checkbox"]');
            amenityCheckboxes.forEach(checkbox => {
                checkbox.closest('.amenities-row').addEventListener('click', (e) => {
                    if (e.target.tagName.toLowerCase() !== 'input') {
                        const input = checkbox.closest('.radio-container').querySelector('input');
                        input.checked = !input.checked;
                        input.closest('.radio-container').querySelector('.radio-visual').classList.toggle('checked', input.checked);
                    }
                });
                const visual = checkbox.closest('.radio-container').querySelector('.radio-visual');
                visual.classList.toggle('checked', checkbox.checked);
                checkbox.addEventListener('change', () => {
                    visual.classList.toggle('checked', checkbox.checked);
                });
            });

            // --- FINAL MODAL LOGIC FOR SUCCESS/FAILURE ---
            const stepsForm = document.getElementById('steps-form');
            const successModal = document.getElementById('success-modal');
            const modalContinueBtn = document.getElementById('modal-continue-btn');
            const mainContainer = document.querySelector('.studio-flow-container');
            const allValidationErrors = @json($errors->messages());

            // Function to handle redirection on continue button click
            const handleModalContinue = () => {
                const nextRoute = modalContinueBtn.dataset.nextRoute || '{{ route('dashboard.studio_home') }}';
                window.location.href = nextRoute;
            };

            if (modalContinueBtn) {
                modalContinueBtn.addEventListener('click', function (){
                    successModal.classList.remove('active')
                });
            }

            const finishBtn = document.getElementById('finish-btn');

            if (finishBtn) {
                finishBtn.addEventListener('click', () => {
                    stepsForm.submit();
                });

            }
            // --- SUCCESS MODAL: No changes needed in this section as it is triggered by Laravel session ---

            // --- FAILURE MODAL (You need to add the HTML for this to work) ---
            // Assuming you have added the <div class="modal-overlay" id="failure-modal">...</div>
            // with id="failure-modal-message" and id="validation-errors-list" based on previous suggestions.

            function showFailureModal(errors, isSystemError = false, message = null) {
                const failureModal = document.getElementById('failure-modal');
                if (!failureModal) {
                    // Fallback to simple alert if modal HTML is missing
                    let errorMsg = isSystemError ? message : "Validation Failed. Check console for details.";
                    console.error("Failure Modal HTML Missing. Showing alert instead. Errors: ", errors);
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                    return;
                }

                const failureModalMessage = document.getElementById('failure-modal-message');
                const validationErrorsList = document.getElementById('validation-errors-list');
                const modalRetryBtn = document.getElementById('modal-retry-btn');

                // mainContainer.style.filter = 'blur(5px)';
                failureModal.classList.add('active');

                if (isSystemError) {
                    failureModalMessage.innerHTML = '<h2>Setup Failed</h2>' + (message || 'A critical system error occurred. Please try again.');
                    validationErrorsList.style.display = 'none';
                } else if (errors) {
                    failureModalMessage.innerHTML = '<h2>Validation Failed</h2><p>Please fix the errors below:</p>';
                    validationErrorsList.style.display = 'block';
                    const ul = validationErrorsList.querySelector('ul');
                    ul.innerHTML = '';
                    for (const field in errors) {
                        errors[field].forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error;
                            ul.appendChild(li);
                        });
                    }
                }

                if (modalRetryBtn) {
                    modalRetryBtn.onclick = () => {
                        mainContainer.style.filter = 'none';
                        failureModal.classList.remove('active');
                    };
                }
            }

            // --- Page Load Logic ---
            window.addEventListener('load', () => {
                document.body.style.opacity = 1;

                const systemError = "{{ session('system_error') }}";
                const successMessage = "{{ session('success') }}";
                const hasValidationErrors = Object.keys(allValidationErrors).length > 0;

                let errorStep = 0; // Default to step 0

                // 1. Handle SUCCESS on page load
                if (successMessage) {
                    modalContinueBtn.setAttribute('onclick', successModal.classList.remove('click'));
                    successModal.classList.add('active');
                }

                // 2. Handle System or Validation Failure on page load
                if (systemError) {
                    showFailureModal(null, true, systemError);
                } else if (hasValidationErrors) {
                    showFailureModal(allValidationErrors, false);

                    // Determine the lowest step with an error
                    const errorFields = Object.keys(allValidationErrors);
                    let lowestErrorStep = 99;
                    for (const field of errorFields) {
                        const step = fieldToStepMap[field] || fieldToStepMap[field.split('.')[0] + '.*'];
                        if (step !== undefined && step < lowestErrorStep) {
                            lowestErrorStep = step;
                        }
                    }
                    errorStep = lowestErrorStep === 99 ? 0 : lowestErrorStep;
                }

                // Set the active step based on validation result
                goToStep(errorStep);
            });

        });
    </script>
@endsection
