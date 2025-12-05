<?php

namespace App\Http\Controllers\Web\Studio;

use App\Http\Controllers\Controller;
use App\Models\StationAmenity;
use App\Models\StudioImage;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudioController extends Controller
{
    public function home()
    {
        return view('user.dashboard.studio.studio_home', [
            'pageTitle' => __('studio_dashboard_heading')
        ]);
    }

    public function chat()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            $database = $factory->createDatabase();
            $auth = $factory->createAuth();

            $user = auth()->user();
            if (!$user) {
                return redirect('/login');
            }

            $firebaseToken = null;
            $currentFirebaseUid = null;

            $role = strtolower($user->role_id);
            $businessId = $user->id;
//            $role = 'studio';
//            $businessId = 26;

            if ($role && $businessId) {
                $path = "business_uid/{$role}/{$businessId}";
                $uids = $database->getReference($path)->getValue();

                if (!empty($uids)) {
                    $firebaseUid = array_key_first($uids);
                    $customToken = $auth->createCustomToken($firebaseUid);

                    $firebaseToken = $customToken->toString();
                    $currentFirebaseUid = $firebaseUid;

                    $database->getReference("users/{$firebaseUid}")->update([
                        'isOnline' => true,
                        'lastActive' => ['.sv' => 'timestamp'],
                    ]);
                }
            }

            return view('user.dashboard.studio.studio_chat', [
                'firebaseToken' => $firebaseToken,
                'currentUser' => $user,
                'currentFirebaseUid' => $currentFirebaseUid,
                'pageTitle' => __('messages_heading')
            ]);

        } catch (\Throwable $e) {
            if (env('APP_DEBUG', false)) {
                return "<h1>An Unexpected Error Occurred</h1><h2>" . $e->getMessage() . "</h2><pre>" . $e->getTraceAsString() . "</pre>";
            } else {
                return "<h1>Error</h1><p>Something went wrong while connecting to the chat service. Please try again later.</p>";
            }
        }
    }

    public function guestArtists()
    {
        $artists = User::all()->where('user_type','artist')->where('verification_status', 2);
        $artists->load([
            'tattooStyles',
        ]);
        return view('user.dashboard.studio.studio_search_artist', [
            'pageTitle' => __('studio_guest_artist_search_heading'),
            'artists' => $artists,
        ]);
    }
    public function studioRequest()
    {
        return view('user.dashboard.studio.studio_request', [
            'pageTitle' => __('studio_guest_artist_request_heading')
        ]);
    }
    public function studioSubscription()
    {
        return view('user.dashboard.studio.studio_subscription', [
            'pageTitle' => __('profile_heading')
        ]);
    }
    public function studioAvailability()
    {
        return view('user.dashboard.studio.studio_availability', [
            'pageTitle' => __('profile_heading')
        ]);
    }
    public function studioPromotion()
    {
        return view('user.dashboard.studio.studio_promotion', [
            'pageTitle' => __('profile_heading')
        ]);
    }

    public function profile()
    {
        // 1. Get the authenticated studio user
        $studio = Auth::user();

        // 2. Eager load the required relationships to populate the profile
        // - studioImages: for the gallery
        // - stationAmenitiesProvided: for the amenities list
        $studio->load([
            'studioImages',
            'stationAmenitiesProvided',
            'supplies',
            'designSpecialties'
        ]);
        $studio->stationAmenityIds = $studio->stationAmenitiesProvided->pluck('id')->toArray();

        $supplies = Supply::all();
        $amenities = StationAmenity::all();
        $designSpecialties = \App\Models\DesignSpecialty::all();

        return view('user.dashboard.studio.studio_profile', [
            'pageTitle' => __('profile_heading'),
            'studio' => $studio, // Pass the studio object to the view
            'supplies' => $supplies,
            'station_amenities' => $amenities,
            'design_specialties' => $designSpecialties,
        ]);
    }

    public function updateStudioProfile(Request $request)
    {
        $user = Auth::user();

        // 1. UNAUTHORIZED CHECK
        if (!$user || $user->user_type !== 'studio') {
            return redirect()->route('login')->with('error', 'Unauthorized access or session expired.');
        }

        // Determine if we are on initial setup or a later update
        // If the user already has a logo, we make the upload optional.
        $logoIsRequired = empty($user->studio_logo);
        $coverIsRequired = empty($user->studio_cover);

        // 2. VALIDATION
        $validator = Validator::make($request->all(), [
            'studio_name' => 'required|string|max:255',
            'studio_email' => 'nullable|email|max:255',
            'studio_address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'website_url' => 'nullable|string|max:255',
            'total_stations' => 'nullable|integer|min:1',
            // Max validation check for guest spots against total stations
            'stations_available' => 'nullable|integer|min:1|max:' . ($request->total_stations ?? 1),
            'studio_type' => 'nullable|int|in:1,2,3',
            'preferred_duration' => 'nullable|string|max:255',
            'commission_type' => 'nullable|in:0,1,2',
            'commission_value' => 'nullable|numeric|min:0',

            'require_portfolio' => 'nullable|string|in:on',
            'accept_bookings' => 'nullable|string|in:on',
            'allow_guest_to_choose' => 'nullable|string|in:on',

            'supplies' => 'nullable|array',
            'supplies.*' => 'integer|exists:supplies,id',
            'design_specialties' => 'nullable|array',
            'design_specialties.*' => 'integer|exists:design_specialties,id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:station_amenities,id',

            // Dynamic File Validation: 'required' only if not already set.
            'logo' => ($logoIsRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,webp|max:15360',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:15360',
            'guest_policy' => 'nullable|file|mimes:png,jpeg,jpg,pdf,txt,doc,docx|max:15360',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:15360',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // 3. DATA CLEANUP AND MAPPING
        $getDataValue = function($key) use ($validatedData) {
            return isset($validatedData[$key]) && $validatedData[$key] === 'on' ? '1' : '0';
        };

        $userUpdateData = [
            'studio_name' => $validatedData['studio_name'],
            'business_email' => $validatedData['studio_email'] ?? null,
            'address' => $validatedData['studio_address'] ?? null,
            'bio' => $validatedData['bio'] ?? null,
            'website_url' => $validatedData['website_url'] ?? null,
            'guest_spots' => $validatedData['stations_available'] ?? 1,
            'total_stations' => $validatedData['total_stations'] ?? 1,
            'studio_type' => $validatedData['studio_type'] ?? 1,
            'preferred_duration' => $validatedData['preferred_duration'],
            'commission_type' => $validatedData['commission_type'] ?? '0',
            'commission_value' => $validatedData['commission_value'] ?? 0.00,
            'require_portfolio' => $getDataValue('require_portfolio'),
            'accept_bookings' => $getDataValue('accept_bookings'),
            'guest_to_choose' => $getDataValue('allow_guest_to_choose'),
        ];

        // 4. TRANSACTION START
        DB::beginTransaction();

        try {
            // 4.1 Handle Single File Uploads (logo, cover, policy)
//            $fileColumns = ['studio_logo', 'studio_cover', 'guest_policy']; guest_policy is guest_policy_file
            $fileColumns = ['logo', 'cover', 'guest_policy'];

            foreach ($fileColumns as $column) {
                if ($request->hasFile($column)) {
                    $file = $request->file($column);
                    $directory = 'studios/' . $column.($column !== 'guest_policy' ? 's' : '');
                    // Delete old file if exists
                    $dbColumn = $column == 'logo' ? 'studio_logo' : ($column == 'cover' ? 'studio_cover' : 'guest_policy');
                    if ($user->$dbColumn && file_exists(public_path($user->$dbColumn))) {
                        unlink(public_path($user->$dbColumn));
                    }

                    $filename = 'studio-' . $column . '-' . time() . '.' . $file->getClientOriginalExtension();
//
//                    // Store in public folder
                    $file->move(public_path($directory), $filename);
                    // Store new file
                    $path = $directory .'/' . $filename;;
                    $userUpdateData[$dbColumn] = $path;
                }
            }

            // 4.2 Update core User data
            $user->update($userUpdateData);

            // 4.3 Handle Many-to-Many Relationships (Pivot Tables) - Always sync to update/delete/add all
            $user->supplies()->sync($validatedData['supplies'] ?? []);
            $user->stationAmenities()->sync($validatedData['amenities'] ?? []);
            $user->designSpecialties()->sync($validatedData['design_specialties'] ?? []);

            // 4.4 Handle Gallery Images (One-to-Many)
            // Only modify if new gallery images are provided
            if ($request->hasFile('gallery')) {
                // Clear old gallery images and DB records
                $user->studioImages()->each(function (StudioImage $image) {
                    if (File::exists(public_path($image->image_path))) {
                        File::delete(public_path($image->image_path));
                    }
                    $image->delete();
                });

                // Save new images
                foreach ($request->file('gallery') as $imageFile) {
                    $filename = 'studio-gallery'.'-' . time() . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    $directory = 'studios/gallery';
                    $imageFile->move(public_path($directory), $filename);
                    $path = $directory . '/' . $filename;

                    StudioImage::create([
                        'user_id' => $user->id,
                        'image_path' => $path,
                    ]);
                }
            }

            // No need to change verification_status as this is a profile update, not a new user flow

            DB::commit();

            // Redirect back to the profile editing page itself, with a success message
            return redirect()->back()->with('success', 'Studio profile updated successfully!')->with('next_step', 'dashboard.studio_profile');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Studio Profile Update Critical Error: ' . $e->getMessage(), ['user_id' => $user->id, 'trace' => $e->getTraceAsString()]);

            // Redirect back with a system error flag
            return back()->withInput()->with('system_error', 'A critical error occurred while updating your profile. Please try again.');
        }
    }
    public function rating()
    {
        return view('user.dashboard.studio.studio_rating', [
            'pageTitle' => __('profile_heading')
        ]);
    }

    public function payment()
    {
        return view('user.dashboard.studio.studio_payment', [
            'pageTitle' => __('profile_heading')
        ]);
    }

    public function security()
    {
        return view('user.dashboard.studio.studio_security', [
            'pageTitle' => __('profile_heading')
        ]);
    }
}
