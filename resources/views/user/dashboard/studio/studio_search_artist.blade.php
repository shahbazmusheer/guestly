@extends('user.layouts.master')

{{-- @push('styles') --}}
<link rel="stylesheet" href="https//unpkg.com/swiper/swiper-bundle.min.css" />

<style>
    :root {
        --primary-green: #008080;
        --light-green-bg: #F0F7F7;
        --tag-bg-realism: #E6F4F0;
        --tag-text-realism: #014122;
        --tag-bg-bold: #E6F4F0;
        --tag-text-bold: #014122;
        --border-color: #D5E9E9;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --verified-green: #014122;
        --card-header-bg: #E6F4F0;
        --dark-green: #004D40;
        --text-color: #333;
        --label-color: #868e96;
        --card-bg: #ffffff;
    }

    body {
        background-color: var(--light-green-bg);
    }

    .content-card {
        background-color: var(--light-green-bg);
        padding: 24px;
        width: 100%;
        box-sizing: border-box;
    }

    /* --- HEADER SECTION --- */
    .page-header-wrapper {
        display: flex;
        justify-content: flex-start;
        /* Items ko left se start karega */
        align-items: center;
        margin-bottom: 24px;
    }

    /* New wrapper for search and filter */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        /* Search aur filter ke beech space */
    }

    .search-bar {
        position: relative;
    }

    .search-bar svg {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        fill: var(--text-light);
    }

    .search-bar input {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 50px;
        padding: 16px 20px 16px 52px;
        font-size: 16px;
        width: 350px;
        /* Thori si fixed width dedi */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .search-bar input::placeholder {
        color: var(--text-light);
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 50px;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 500;
        color: var(--text-dark);
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    /* Dropdown arrow styling */
    .dropdown-arrow {
        margin-left: 8px;
        transition: transform 0.3s ease;
    }

    /* Class to rotate arrow on toggle */
    .filter-btn.active .dropdown-arrow {
        transform: rotate(180deg);
    }


    /* Grid Layout */
    .spots-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    /* --- ARTIST CARD STYLING --- */
    .artist-card {
        background-color: #ffffff;
        border-radius: 20px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .artist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.07);
    }

    .card_image_wrapper {
        position: relative;
        padding: 8px;
    }

    .swiper_slider img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
    }

    .swiper-pagination {
        bottom: 18px !important;
    }

    .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.8);
        width: 6px;
        height: 6px;
        opacity: 1;
    }

    .swiper-pagination-bullet-active {
        background: #fff;
        width: 8px;
        height: 8px;
    }

    .card_content_wrapper {
        padding: 0 16px 16px 16px;
        flex-grow: 1;
    }

    .artist-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .artist-profile-pic {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid #fff;
        object-fit: cover;
    }

    .artist-details {
        flex-grow: 1;
    }

    .artist-details h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .artist-details p {
        font-size: 12px;
        color: var(--text-light);
        margin: 2px 0 0 0;
    }

    .message-icon {
        background-color: #F0F7F7;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    .artist-verification {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-light);
        margin-bottom: 12px;
    }

    .artist-verification .verified-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        color: var(--verified-green);
        font-weight: 500;
    }

    .artist-verification .verified-badge svg {
        width: 16px;
        height: 16px;
        fill: var(--verified-green);
    }

    .artist-tags {
        display: inline-table;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .tag {
        font-size: 12px;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 50px;
    }

    .tag.realism {
        background-color: var(--tag-bg-realism);
        color: var(--tag-text-realism);
    }

    .tag.bold {
        background-color: var(--tag-bg-bold);
        color: var(--tag-text-bold);
    }

    .artist-bio {
        font-size: 13px;
        color: var(--text-dark);
        line-height: 1.5;
        margin: 0;
    }

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


    /* Responsive */
    @media (max-width: 1200px) {
        .spots-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .spots-grid {
            grid-template-columns: 1fr;
        }

        .page-header-wrapper {
            flex-direction: column;
            gap: 16px;
            align-items: stretch;
        }

        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .search-bar input {
            width: 100%;
        }
    }
</style>
<style>
    .profile-page-wrapper {
        background-color: var(--light-green-bg);
        padding: 40px 20px;
        width: 100%;
        display: flex;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }
    .profile-card-container {
        background-color: #fff;
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 600px;
        padding: 24px;
        box-sizing: border-box;
    }
    .profile-header {
        background-color: var(--card-bg);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin-bottom: 24px;
    }
    /*.profile-picture {*/
    /*    width: 90px;*/
    /*    height: 90px;*/
    /*    border-radius: 50%;*/
    /*    object-fit: cover;*/
    /*    margin-bottom: 12px;*/
    /*}*/
    .profile-name {
        font-size: 22px;
        font-weight: 600;
        color: #014122;
        margin: 0;
    }
    .profile-role {
        font-size: 14px;
        color: #555;
        margin: 4px 0 0;
    }
    .verification-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #5E8082;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .verification-section h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--dark-green);
    }
    .verified-status {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--dark-green);
        font-weight: 600;
        font-size: 16px;
    }
    .verified-status svg {
        width: 20px;
        height: 20px;
        fill: var(--dark-green);
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .info-field-full {
        grid-column: 1 / -1;
    }
    .info-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #5E8082;
        border-radius: 12px;
        padding: 8px 16px;
    }
    .field-data {
        flex-grow: 1;
    }
    .field-label {
        display: block;
        font-size: 12px;
        color: #5E8082;
        margin-bottom: 2px;
    }
    .value {
        font-size: 15px;
        font-weight: 500;
        color: var(--text-color);
    }
    .editable-input {
        display: block;
        width: 100%;
        border: none;
        background-color: transparent;
        padding: 0;
        font-size: 15px;
        font-weight: 500;
        color: var(--text-color);
    }
    .editable-input:focus {
        outline: none;
        box-shadow: none;
    }
    .action-btn {
        background: none;
        border: none;
        color: #014122;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        padding: 0 0 0 10px;
        text-decoration: underline;
    }
    .save-changes-container {
        text-align: center;
        margin-top: 32px;
    }
    .save-changes-btn {
        background-color: #014122;
        color: #fff;
        border: none;
        padding: 18px 40px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .profile-picture-container {
        position: relative;
        display: inline-block;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #f0f2f5;
        border: 2px solid var(--card-header-bg);
    }

    .profile-placeholder-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 30px;
        color: #aab0b9;
        z-index: 1;
    }

    .profile-picture {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        position: relative;
        z-index: 2;
    }

    .profile-picture-container .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: rgba(1, 65, 34, 0.7);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        cursor: pointer;
        z-index: 3;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .profile-picture-container:hover .upload-overlay {
        opacity: 1;
    }
    @media (max-width: 640px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
{{-- @endpush --}}


@section('content')
    <div class="content-card">

        <div class="page-header-wrapper">
            <div class="header-actions">
                <div class="search-bar">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z">
                        </path>
                    </svg>
                    <input type="text" placeholder="Search Guest Artists?">
                </div>
                <button class="filter-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                    <span>Filter</span>
                    <svg class="dropdown-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="spots-grid">
            @foreach ($artists as $artist)
                <div class="artist-card" onclick='loadProfileModal(@json($artist))'>
                    <div class="card_image_wrapper">
                        <div class="swiper swiper_slider">
                            <div class="swiper-wrapper">
                                {{-- Backend se aane wale dynamic images yahan loop honge --}}
                                <div class="swiper-slide"><img src="{{ asset('assets/web/dashboard/default_1_profile.jpg') }}"
                                                               alt="Artwork"></div>
                                <div class="swiper-slide"><img src="{{ asset('assets/web/dashboard/default_2_profile.jpg') }}"
                                                               alt="Artwork"></div>
                                <div class="swiper-slide"><img src="{{ asset('assets/web/dashboard/default_3_profile.jpg') }}"
                                                               alt="Artwork"></div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>

                    <div class="card_content_wrapper">
                        <div class="artist-header">
                            {{-- Backend se aane wali profile pic yahan lagegi --}}
                            <img src="{{ $artist->avatar ? asset($artist->avatar) : asset('artists/avatar/default-avatar.png') }}" alt="Artist Name" class="artist-profile-pic">
                            <div class="artist-details">
                                {{-- Backend se aane wala naam aur experience yahan aayega --}}
                                <h3 title="{{($artist->name. ' ' .$artist->last_name)}}">{{Str::limit(($artist->name. ' ' .$artist->last_name), 12, '...')}}</h3>
                                <p>4+ Years of Experience</p>
                            </div>
                            <div class="message-icon" onclick="window.location.href='{{route('dashboard.studio_chat')}}'">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#014122"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <div class="artist-verification">
                            <span class="verified-badge">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z">
                                    </path>
                                </svg>
                                Verified
                            </span>
                            <span>&bull;</span>
                            {{-- Backend se aane wali location yahan aayegi --}}
                            <span>{{$artist->address ?? ( ($artist->city).(($artist->city && $artist->country) ? ', ' : ''). $artist->country)}}</span>
                        </div>


                        @foreach($artist->tattooStyles as $style)
                            <div class="artist-tags">
                                <span class="tag realism">{{$style->name}}</span>
                            </div>
                        @endforeach

                        <p class="artist-bio">
                            {{$artist->bio ?? 'Bio not added yet...'}}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="profile-modal">
        <div class="modal-content">
            <div class="profile-card-container">
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper sliders on all cards
            const swipers = document.querySelectorAll('.swiper_slider');
            swipers.forEach(function(swiperElement) {
                new Swiper(swiperElement, {
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                });
            });

            // JavaScript for filter button toggle
            const filterButton = document.querySelector('.filter-btn');
            if (filterButton) {
                filterButton.addEventListener('click', function() {
                    this.classList.toggle('active');
                    // Yahan aap filters ko show/hide karne ka logic add kar sakte hain
                });
            }
        });

        function loadProfileModal(artist) {
            const asset_path = @json(asset(''));
            const profileModal = document.getElementById('profile-modal');
            const modalContent = profileModal.querySelector('.modal-content')
            var htmlContent = `
                <div class="profile-header">
                    <!-- Profile Image Upload (Updated Structure) -->
                    <div class="profile-picture-container">
                <img id="profilePreview"
                     src="${(artist.avatar && (artist.avatar.includes('avatar/001-boy.svg'))) ? asset_path+artist.avatar : asset_path+'avatar/001-boy.svg' }"
                             alt="Profile Picture"
                             class="profile-picture"
                             style="${ (artist.avatar && (artist.avatar.includes('avatar/001-boy.svg'))) ? '' : 'background-color: transparent;' }">


                        <label for="avatarInput" class="upload-overlay">
                            <i class="fa fa-camera"></i>
                        </label>

                    </div>

                    <h2 class="profile-name">${ artist.name + ' ' + artist.last_name }</h2>
                    <p class="profile-role">${ artist.user_type }</p>
                     <form method="POST" action="{{ route('dashboard.artist_profile_update') }} " enctype="multipart/form-data">
                @csrf
            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">

            <div class="verification-section">
                <h3>Identification Verification</h3>
                <span class="verified-status">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.9502 12.7L9.5002 11.275C9.31686 11.0917 9.08786 11 8.8132 11C8.53853 11 8.30086 11.1 8.1002 11.3C7.91686 11.4834 7.8252 11.7167 7.8252 12C7.8252 12.2834 7.91686 12.5167 8.1002 12.7L10.2502 14.85C10.4502 15.05 10.6835 15.15 10.9502 15.15C11.2169 15.15 11.4502 15.05 11.6502 14.85L15.9002 10.6C16.1002 10.4 16.1959 10.1667 16.1872 9.90004C16.1785 9.63337 16.0829 9.40004 15.9002 9.20004C15.7002 9.00004 15.4629 8.89604 15.1882 8.88804C14.9135 8.88004 14.6759 8.97571 14.4752 9.17504L10.9502 12.7ZM8.1502 21.75L6.7002 19.3L3.9502 18.7C3.7002 18.65 3.5002 18.521 3.3502 18.313C3.2002 18.105 3.14186 17.8757 3.1752 17.625L3.4502 14.8L1.5752 12.65C1.40853 12.4667 1.3252 12.25 1.3252 12C1.3252 11.75 1.40853 11.5334 1.5752 11.35L3.4502 9.20004L3.1752 6.37504C3.14186 6.12504 3.2002 5.89571 3.3502 5.68704C3.5002 5.47837 3.7002 5.34937 3.9502 5.30004L6.7002 4.70004L8.1502 2.25004C8.28353 2.03337 8.46686 1.88737 8.7002 1.81204C8.93353 1.73671 9.16686 1.74937 9.4002 1.85004L12.0002 2.95004L14.6002 1.85004C14.8335 1.75004 15.0669 1.73737 15.3002 1.81204C15.5335 1.88671 15.7169 2.03271 15.8502 2.25004L17.3002 4.70004L20.0502 5.30004C20.3002 5.35004 20.5002 5.47937 20.6502 5.68804C20.8002 5.89671 20.8585 6.12571 20.8252 6.37504L20.5502 9.20004L22.4252 11.35C22.5919 11.5334 22.6752 11.75 22.6752 12C22.6752 12.25 22.5919 12.4667 22.4252 12.65L20.5502 14.8L20.8252 17.625C20.8585 17.875 20.8002 18.1044 20.6502 18.313C20.5002 18.5217 20.3002 18.6507 20.0502 18.7L17.3002 19.3L15.8502 21.75C15.7169 21.9667 15.5335 22.1127 15.3002 22.188C15.0669 22.2634 14.8335 22.2507 14.6002 22.15L12.0002 21.05L9.4002 22.15C9.16686 22.25 8.93353 22.2627 8.7002 22.188C8.46686 22.1134 8.28353 21.9674 8.1502 21.75Z" fill="#014122" />
                    </svg>
                    Verified
                </span>
            </div>
            <div class="verification-section">
                <h3>Email Address</h3>
                <span class="verified-status">
                   ${artist.email}
                </span>
            </div>

            <div class="info-grid mt-4">

                <!-- Legal Name -->
                <div class="info-field">
                    <div class="field-data">
                        <label class="field-label">Legal Name</label>
                        <span class="value">${ artist.name + ' ' + artist.last_name }</span>
                        </div>
                    </div>

                    <!-- Email Address (Not Editable) -->
                    <div class="info-field">
                        <div class="field-data">
                            <label class="field-label">Email Address</label>
                            <span class="value">${ artist.email }</span>
                        </div>
                    </div>
</form>
    </div>`;
            modalContent.innerHTML = htmlContent;

            profileModal.classList.add('active')
        }
    </script>
@endsection
