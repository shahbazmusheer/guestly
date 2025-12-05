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
                <img src="{{ $studio->studio_cover ? asset($studio->studio_cover) : asset('studios/covers/default-cover.png') }}" alt="{{ $studio->studio_name }} Cover">
                <div class="studio-hero-overlay">
                    <div class="hero-top">
                        <div class="studio-info-header">
                            <img src="{{ $studio->studio_logo ? asset($studio->studio_logo) : asset('studios/logos/default-logo.png') }}"
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
                                    {{ $studio->city ?? '' }}{{($studio->city && $studio->country) ? ', ': ''}}{{ $studio->country ? strtoupper($studio->country) : '' }}
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
                    <div class="booking-card shadow">
                        <div class="booking-card-content"><svg class="calendar-icon" width="24" height="24"
                                                               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z" />
                            </svg> <span>Calendar Availability</span></div>
                        <button id="open-booking-modal-btn" class="book-now-button">Book Now</button>
                    </div>
                    <div id="map"></div>
                </section>
            </div>

            {{-- Reviews Section --}}
            @if(1 != 1)
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
            @endif
        </div>

        <div class="booking-modal-overlay" id="booking-modal-overlay">
            <div class="booking-modal-container" id="booking-modal-container">
                <button id="close-modal-btn">&times;</button>
                <div class="form-slider-track">

                    <!-- Step 1: Date Selection -->
                    <div class="form-step active" data-step="0">
                        <h2>Book your Spot</h2>
                        <p class="subtitle">Please proceed to book your guest spot as an artist.</p>
                        <div class="progress-indicator">
                            <div class="progress-step active"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                        </div>
                        <div class="form-content-scrollable">
                            <div class="calendar">
                                <div class="calendar-header">
                                    <span class="calendar-nav" id="prev-month-btn">&lt;</span>
                                    <strong id="calendar-month-year"></strong>
                                    <span class="calendar-nav" id="next-month-btn">&gt;</span>
                                </div>
                                <div id="calendar-grid-body"></div>
                            </div>
                            <div class="date-range-inputs">
                                <div class="form-group"><label>From</label>
                                    <div class="date-display" id="from-date-display"></div>
                                </div>
                                <div class="form-group"><label>To</label>
                                    <div class="date-display" id="to-date-display"></div>
                                </div>
                            </div>
                            <div class="field-row-bordered">
                                <span>Request a Studio Tour</span>
                                <div class="billing-toggle"><span>Yes</span><label class="switch"><input type="checkbox"
                                                                                                         checked><span class="slider"></span></label><span>No</span></div>
                            </div>
                        </div>
                        <div class="button-group"><button class="nav-btn back-btn" disabled>Back</button><button
                                class="nav-btn next-btn">Next</button></div>
                    </div>

                    <div class="form-step" data-step="1">
                        <h2>Book your Spot</h2>
                        <p class="subtitle">Please proceed to book your guest spot as an artist.</p>
                        <div class="progress-indicator">
                            <div class="progress-step active"></div>
                            <div class="progress-step active"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                        </div>

                        <div class="form-content-scrollable">
                            <div class="form-section-title">Upload Your Work</div>

                            <!-- File Type Dropdown with Floating Label -->
                            <div class="floating-label-group">
                                <select id="file-type" required>
                                    <option value="Portfolio" selected>Portfolio</option>
                                    <option value="CV">CV / Resume</option>
                                    <option value="Flash Sheet">Flash Sheet</option>
                                </select>
                                <label for="file-type">File Type</label>
                            </div>

                            <!-- Dotted Upload Box -->
                            <div class="upload-box-dotted" id="upload-box">
                                <div class="upload-box-content">
                                    <svg class="upload-icon" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                                            stroke="#0B3D27" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M14 2V8H20" stroke="#0B3D27" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round" />
                                        <path d="M12 18V12" stroke="#0B3D27" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round" />
                                        <path d="M9 15L12 12L15 15" stroke="#0B3D27" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round" />
                                    </svg>
                                    <span>Upload File</span>
                                </div>
                            </div>
                            <input type="file" id="file-input" multiple hidden>

                            <!-- Info Text -->
                            <p class="upload-info-text">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15Z"
                                        stroke="#5D7A70" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M8 10.5V8" stroke="#5D7A70" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    <path d="M8 5.5H8.0075" stroke="#5D7A70" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                                <span>You can upload PDF, JPG, PNG, or ZIP files up to 50MB each.</span>
                            </p>

                            <!-- Image Preview Gallery -->
                            <div class="image-preview-gallery" id="image-preview-gallery">
                                <!-- Image previews will be added here by JS -->
                            </div>

                            <!-- Uploaded Files List -->
                            <div class="uploaded-files-list" id="uploaded-files-list">
                                <!-- Uploaded file rows will be added here by JS -->
                            </div>
                        </div>

                        <div class="button-group">
                            <button class="nav-btn back-btn">Back</button>
                            <button class="nav-btn next-btn">Next</button>
                        </div>
                    </div>

                    <!-- Template for an uploaded file row -->
                    <template id="uploaded-file-template">
                        <div class="uploaded-file-row" data-id="">
                            {{--                        <div class="file-icon-container"> --}}
                            {{--                            <svg width="32" height="32" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="40" height="40" rx="8" fill="#E6F4F0"/><path d="M28 13H15C14.4696 13 13.9609 13.2107 13.5858 13.5858C13.2107 13.9609 13 14.4696 13 15V26C13 26.5304 13.2107 27.0391 13.5858 27.4142C13.9609 27.7893 14.4696 28 15 28H28C28.5304 28 29.0391 27.7893 29.4142 27.4142C29.7893 27.0391 30 26.5304 30 26V15C30 14.4696 29.7893 13.9609 29.4142 13.5858C29.0391 13.2107 28.5304 13 28 13Z" stroke="#0B3D27" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 18C16.5 19 19 16.5 20.5 15" stroke="#0B3D27" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M11 15V13.5C11 12.6716 11.6716 12 12.5 12H25.5C26.3284 12 27 12.6716 27 13.5V15" stroke="#0B3D27" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> --}}
                            {{--                        </div> --}}
                            <div class="file-icon-container">
                                <img src="{{ asset('assets/web/document.png') }}" width="60" height="80" alt="File Preview" />
                            </div>
                            <div class="file-info">
                                <div class="file-name"></div>
                                <div class="file-time"></div>
                            </div>
                            <div class="file-size-pill"></div>
                            <button class="file-remove-btn" aria-label="Remove file">&times;</button>
                        </div>
                    </template>

                    <!-- Step 3: Message & Artist Details -->
                    <div class="form-step" data-step="2">
                        <h2>Book your Spot</h2>
                        <p class="subtitle">Please proceed to book your guest spot as an artist.</p>
                        <div class="progress-indicator">
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                        </div>
                        <div class="form-content-scrollable">
                            <div class="form-group"><label>Leave a Message for the Studio (Optional)</label>
                                <textarea placeholder="Write Something Here..."></textarea>
                            </div>
                            <div class="artist-details-group">
                                <div class="form-group"><label>Are You Booking Solo or With a Group?</label><select>
                                        <option>Group</option>
                                        <option>Solo</option>
                                    </select></div>
                                <div class="form-group" style="margin-top:15px;"><label>Name of the Artist</label><input
                                        type="text" value="Jessica L."></div>
                                <a href="#" class="add-more-link">Add More</a>
                            </div>
                        </div>
                        <div class="button-group"><button class="nav-btn back-btn">Back</button><button
                                class="nav-btn next-btn">Next</button></div>
                    </div>

                    <!-- Step 4: Agreement -->
                    <div class="form-step" data-step="3">
                        <h2>Book your spot</h2>
                        <p class="subtitle">Please proceed to book your guest spot as an artist.</p>
                        <div class="progress-indicator">
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                            <div class="progress-step"></div>
                        </div>
                        <div class="form-content-scrollable">
                            <div class="form-section-title">Guest Artist Terms & Conditions</div>
                            <div class="agreement-box">

                                <p>Before submitting your booking request, please take a moment to carefully review and agree to
                                    our Guest Artist Terms & Conditions. These terms have been created to ensure a smooth,
                                    professional, and respectful experience for both you and the studio.</p>
                                <br>
                                <p>As a guest artist, you are expected to adhere to all studio policies, which include but are
                                    not limited to:</p><br>
                                <ul>
                                    <li><strong>Health & Hygiene Standards:</strong> Maintaining a clean and sanitary workspace
                                        at all times, properly sterilizing equipment, and following all local health
                                        regulations.</li>
                                    <li><strong>Professional Conduct:</strong> Treating staff, clients, and fellow artists with
                                        respect and professionalism. Any form of discrimination, harassment, or unprofessional
                                        behavior will not be tolerated.</li>
                                    <li><strong>Client Management:</strong> Being responsible for your own clients, including
                                        their communication, comfort, and consent throughout the session.</li>
                                    <li><strong>Studio Property & Etiquette:</strong> Respecting the space, equipment, and any
                                        provided resources. Any damage caused will be your responsibility.</li>
                                    <li><strong>Scheduling & Punctuality:</strong> Arriving on time for your scheduled days and
                                        informing the studio in advance if there are any changes or emergencies.</li>
                                </ul>
                                <br>
                                <p>By agreeing to these terms, you acknowledge your role in maintaining the studio's reputation
                                    and creating a safe, welcoming environment for everyone. Failure to comply may result in
                                    your booking being canceled.</p>
                            </div>
                        </div>
                        <div class="button-group"><button class="nav-btn back-btn">Back</button><button
                                class="nav-btn agree-btn" id="agree-btn">Agree and Continue</button></div>
                    </div>

                    <!-- Step 5: Submitted Confirmation -->
                    <div class="form-step" style="margin-bottom: -50px" data-step="4">
                        <div class="final-modal-content">
                            <img class="success-icon" src="{{ asset('assets/web/thumbs_up.png') }}" alt="Thumbs Up">
                            <h2 style="font-weight: 500; max-width: 345px;">Booking Request Submitted!</h2>
                            <p>Thank you! Your booking request has been successfully submitted and is now pending studio
                                approval. You’ll receive a notification once the studio reviews and responds to your request.'
                            </p>
                            <div class="button-group"><button class="nav-btn next-btn"
                                                              id="submitted-continue-btn">Continue</button></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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
    </script>

    <script>
        // Data passed from Laravel Controller to JavaScript
        const currentStudio = @json($studio);

        // Ensure latitude and longitude are numbers, using default NYC values as fallback
        const studioLat = parseFloat(currentStudio.latitude) || 40.7128;
        const studioLng = parseFloat(currentStudio.longitude) || -74.0060;

        let map;
        // Note: panelWrapper is not strictly used on this single-studio profile page
        const panelWrapper = document.getElementById('location-map-container');

        // 2. Map Initialization Logic
        function initMap() {

            // 1. CustomMarker Class Definition (Restored for the fancy pin)
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

                    // Build the URL for the logo, correctly handling the asset path
                    const logoUrl = this.studio.studio_logo ? '{{ asset('') }}' + this.studio.studio_logo : '{{ asset('assets/web/dashboard/default_1.png') }}';

                    this.div.innerHTML =
                        `<div class="marker-label">${this.studio.studio_name || this.studio.name}</div><div class="marker-pin">
                    <svg width="48" height="56" viewBox="0 0 60 70">
                        <defs><filter id="shadow" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.3"/></filter></defs>
                        <path d="M30 70C30 70 5 45 5 25C5 12.8 16.2 2 30 2C43.8 2 55 12.8 55 25C55 45 30 70 30 70Z" fill="white" filter="url(#shadow)"/>
                        <circle cx="30" cy="25" r="22" fill="none" stroke="#d1d5db" stroke-width="4" class="marker-border"/>
                        <image href="${logoUrl}" x="8" y="3" height="44" width="44" clip-path="url(#circleClip${this.studio.latitude})"/>
                        <clipPath id="circleClip${this.studio.latitude}"><circle cx="30" cy="25" r="22"/></clipPath>
                    </svg>
                </div>`;

                    // NOTE: Removed addListeners() as we don't need interactive panel logic on the profile page map.
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
                // NOTE: addListeners is removed as we only need a static display.
            }
            const studioLocation = { lat: studioLat, lng: studioLng };

            // Map ko initialize karein: Centered on the current studio
            map = new google.maps.Map(document.getElementById("map"), {
                center: studioLocation,
                zoom: 14, // Zoomed in for detail
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

            // 3. Add the Custom Marker for the current studio
            const position = new google.maps.LatLng(studioLat, studioLng);
            new CustomMarker(position, map, currentStudio);

            // NOTE: All extra logic related to bounds, multiple studios, and panelWrapper
            // has been correctly removed for the single-studio profile view.
        }
    </script>
    <script>
        // This script handles the initial body opacity transition for a smooth load effect.
        window.addEventListener('load', () => {
            document.body.style.opacity = 1;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- MODAL & STEP NAVIGATION ELEMENTS ---
            const openModalBtn = document.getElementById('open-booking-modal-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const modalOverlay = document.getElementById('booking-modal-overlay');
            const nextBtns = document.querySelectorAll('.next-btn, .agree-btn');
            const backBtns = document.querySelectorAll('.back-btn');
            const steps = document.querySelectorAll('.form-step');
            let currentStep = 0;

            // --- STEP 1: CALENDAR ELEMENTS ---
            const monthYearDisplay = document.getElementById('calendar-month-year');
            const calendarGridBody = document.getElementById('calendar-grid-body');
            const prevMonthBtn = document.getElementById('prev-month-btn');
            const nextMonthBtn = document.getElementById('next-month-btn');
            const fromDateDisplay = document.getElementById('from-date-display');
            const toDateDisplay = document.getElementById('to-date-display');

            // --- STEP 2: UPLOAD ELEMENTS ---
            const uploadBox = document.getElementById('upload-box');
            const fileInput = document.getElementById('file-input');
            const imagePreviewGallery = document.getElementById('image-preview-gallery');
            const uploadedFilesList = document.getElementById('uploaded-files-list');
            const fileTemplate = document.getElementById('uploaded-file-template');

            // --- CALENDAR STATE ---
            let currentDate = new Date(2025, 8, 1); // September 2025
            let startDate = new Date(2025, 8, 14);
            let endDate = new Date(2025, 8, 30);
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                "September", "October", "November", "December"
            ];
            const uploadedFiles = new Map();

            // Modal and Step Navigation (No changes needed here)
            const openModal = () => {
                document.body.classList.add('modal-is-open');
                modalOverlay.classList.add('active');
            }
            const closeModal = () => {
                document.body.classList.remove('modal-is-open');
                modalOverlay.classList.remove('active');
                setTimeout(() => goToStep(0), 300);
            };
            if (openModalBtn) openModalBtn.addEventListener('click', openModal);
            if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
            if (modalOverlay) modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });

            function goToStep(stepIndex) {
                if (stepIndex >= steps.length || stepIndex < 0) return;
                steps[currentStep].classList.remove('active');
                steps[stepIndex].classList.add('active');
                currentStep = stepIndex;
                updateProgressBar();
                const backBtn = steps[stepIndex].querySelector('.back-btn');
                if (backBtn) backBtn.disabled = (stepIndex === 0);
            }
            const updateProgressBar = () => {
                const indicators = document.querySelectorAll('.progress-indicator');
                indicators.forEach(indicator => {
                    if (indicator) {
                        const progressSteps = indicator.querySelectorAll('.progress-step');
                        progressSteps.forEach((progress, progressIdx) => {
                            progress.classList.toggle('active', progressIdx < currentStep);
                        });
                        if (progressSteps[currentStep]) {
                            progressSteps[currentStep].classList.add('active');
                        }
                    }
                });
            };
            nextBtns.forEach(btn => btn.addEventListener('click', () => {
                if (btn.id === 'agree-btn') goToStep(4);
                else if (currentStep < steps.length - 1) goToStep(currentStep + 1);
            }));
            backBtns.forEach(btn => btn.addEventListener('click', () => {
                if (currentStep > 0) goToStep(currentStep - 1);
            }));
            document.getElementById('submitted-continue-btn')?.addEventListener('click', closeModal);

            // =========================================================
            // === START OF UPDATED JAVASCRIPT FOR CALENDAR RENDERER ===
            // =========================================================
            const formatDate = (d) => d ? d.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : "";
            const toISO = (d) =>
                `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, '0')}-${d.getDate().toString().padStart(2, '0')}`;

            const renderCalendar = () => {
                const year = currentDate.getFullYear(),
                    month = currentDate.getMonth();
                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
                calendarGridBody.innerHTML = '';

                ['M', 'T', 'W', 'T', 'F', 'S', 'S'].forEach(d => calendarGridBody.innerHTML +=
                    `<div class="day-cell day-name">${d}</div>`);

                const firstDayOfMonth = new Date(year, month, 1);
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const startDayIndex = (firstDayOfMonth.getDay() + 6) % 7; // Monday = 0

                for (let i = 0; i < startDayIndex; i++) {
                    calendarGridBody.innerHTML += `<div class="day-cell other-month"></div>`;
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const cell = document.createElement('div');
                    const cellDate = new Date(year, month, day);
                    const dayOfWeek = (cellDate.getDay() + 6) % 7; // Monday = 0, Sunday = 6

                    cell.className = 'day-cell';
                    cell.textContent = day.toString().padStart(2, '0');
                    cell.dataset.date = toISO(cellDate);
                    cell.dataset.day = day.toString().padStart(2, '0'); // For CSS content: attr(data-day)

                    if (startDate && endDate && cellDate > startDate && cellDate < endDate) {
                        cell.classList.add('in-range');
                        if (dayOfWeek === 0) cell.classList.add('pill-left');
                        if (dayOfWeek === 6) cell.classList.add('pill-right');
                    }

                    if (startDate && toISO(cellDate) === toISO(startDate)) {
                        cell.classList.add('start-date', 'pill-left');
                        if (endDate && toISO(startDate) === toISO(endDate)) {
                            cell.classList.add('pill-right');
                        }
                    }

                    if (endDate && toISO(cellDate) === toISO(endDate)) {
                        cell.classList.add('end-date', 'pill-right');
                    }

                    cell.addEventListener('click', handleDateClick);
                    calendarGridBody.appendChild(cell);
                }
                updateDateInputs();
            };
            // =======================================================
            // === END OF UPDATED JAVASCRIPT FOR CALENDAR RENDERER ===
            // =======================================================

            const updateDateInputs = () => {
                fromDateDisplay.textContent = formatDate(startDate);
                toDateDisplay.textContent = formatDate(endDate);
            };
            const handleDateClick = (e) => {
                const clickedDate = new Date(e.target.dataset.date + 'T00:00:00');
                if (!startDate || (startDate && endDate)) {
                    startDate = clickedDate;
                    endDate = null;
                } else if (clickedDate < startDate) {
                    startDate = clickedDate;
                } else {
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
            renderCalendar();

            // File Upload Logic (No changes needed)
            if (uploadBox && fileInput && imagePreviewGallery && uploadedFilesList && fileTemplate) {
                uploadBox.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', (event) => {
                    const files = event.target.files;
                    for (const file of files) {
                        const fileKey = `${file.name}-${file.size}`;
                        if (!uploadedFiles.has(fileKey)) {
                            const fileData = {
                                file: file,
                                timestamp: new Date()
                            };
                            uploadedFiles.set(fileKey, fileData);
                            renderFile(fileKey, fileData);
                        }
                    }
                    fileInput.value = '';
                });
            }

            function renderFile(key, data) {
                const {
                    file,
                    timestamp
                } = data;
                const templateClone = fileTemplate.content.cloneNode(true);
                const fileRow = templateClone.querySelector('.uploaded-file-row');
                fileRow.dataset.key = key;
                fileRow.querySelector('.file-name').textContent = file.name;
                const timeEl = fileRow.querySelector('.file-time');
                timeEl.textContent = timeAgo(timestamp);
                timeEl.dataset.timestamp = timestamp;
                fileRow.querySelector('.file-size-pill').textContent = formatBytes(file.size);
                fileRow.querySelector('.file-remove-btn').addEventListener('click', () => removeFile(key));
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.dataset.key = key;
                    imagePreviewGallery.appendChild(img);
                }
                uploadedFilesList.appendChild(fileRow);
            }

            function removeFile(key) {
                const fileRow = uploadedFilesList.querySelector(`.uploaded-file-row[data-key="${key}"]`);
                if (fileRow) fileRow.remove();
                const imgPreview = imagePreviewGallery.querySelector(`img[data-key="${key}"]`);
                if (imgPreview) imgPreview.remove();
                uploadedFiles.delete(key);
            }

            function formatBytes(bytes, d = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024,
                    i = Math.floor(Math.log(bytes) / Math.log(k));
                return `${parseFloat((bytes / Math.pow(k, i)).toFixed(d))} ${['Bytes', 'KB', 'MB', 'GB'][i]}`;
            }

            function timeAgo(date) {
                const s = Math.floor((new Date() - date) / 1000);
                if (s >= 31536000) return Math.floor(s / 31536000) + "y ago";
                if (s >= 2592000) return Math.floor(s / 2592000) + "mo ago";
                if (s >= 86400) return Math.floor(s / 86400) + "d ago";
                if (s >= 3600) return Math.floor(s / 3600) + "h ago";
                if (s >= 60) return Math.floor(s / 60) + "m ago";
                if (s > 0) return s + "s ago";
                return "just now";
            }

            function updateAllFileTimes() {
                document.querySelectorAll('.file-time').forEach(el => {
                    const ts = el.dataset.timestamp;
                    if (ts) el.textContent = timeAgo(new Date(ts));
                });
            }
            setInterval(updateAllFileTimes, 10000);
            goToStep(0);
        });
    </script>
@endsection
