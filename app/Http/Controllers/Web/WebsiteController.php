<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StationAmenity;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\StudioImage;
use App\Models\DesignSpecialty;
use Illuminate\Support\Facades\DB;



class WebsiteController extends Controller
{
    public function formSlider()
    {

        return view('user.common.form_slider');
    }

    public function formSliderTwo()
    {
        return view('user.common.form_slider_two');
    }

    public function formLoginSignup()
    {
        return view('user.common.form_login_signup');
    }

    public function choosePlan(Request $request)
{

    $role_type = auth()->user()->user_type ?? 'user';

    $plans = Plan::where('user_type', $role_type)
        ->with('features')
        ->get();

    return view('user.common.choose_plan', compact('plans', 'role_type'));
}


    public function studioChoosePlan()
    {
        return view('user.common.studio_choose_plan');
    }

    public function userIdentification()
    {
        $user = Auth::user();
        return view('user.common.user_identification', ['user' => $user]);
    }

    public function docVerification()
    {
        return view('user.common.doc_verification');
    }

    public function phoneEmailVerification()
    {
        return view('user.common.phone_email_verification');
    }

    public function studioStepForm()
    {
        $supplies = Supply::all();
        $amenities = StationAmenity::all();
        $designSpecialties = \App\Models\DesignSpecialty::all();
        return view('user.common.studio_step_form', ['supplies'=>$supplies,'station_amenities' => $amenities, 'design_specialties' => $designSpecialties]);
    }

    /**
     * Handles the submission of the Studio Step Form (Business Registration).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveStudioStepForm(Request $request)
    {
        $user = Auth::user();

        // 1. UNAUTHORIZED CHECK
        if (!$user || $user->user_type !== 'studio') {
            // Log the event or just return a general error
//            \Log::warning('Unauthorized access attempt to saveStudioStepForm.', ['user_id' => $user->id ?? 'guest']);
            return redirect()->route('login')->with('error', 'Unauthorized access or session expired.');
        }

        // 2. VALIDATION
        $validator = Validator::make($request->all(), [
            'studio_name' => 'required|string|max:255',
            'studio_email' => 'nullable|email|max:255',
            'studio_address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'website_url' => 'nullable|string|max:255', // |url for url validation
            'total_stations' => 'nullable|integer|min:1',
            'stations_available' => 'nullable|integer|min:1|max:' . ($request->total_stations ?? 1), // Ensure guest_spots <= total_stations
            'studio_type' => 'nullable|int|in:1,2,3',
            'preferred_duration' => 'nullable|string|max:255',
            'commission_type' => 'nullable|in:0,1,2',
            'commission_value' => 'nullable|numeric|min:0', // Added missing validation for commission value

            'require_portfolio' => 'nullable|string|in:on',
            'accept_bookings' => 'nullable|string|in:on',
            'allow_guest_to_choose' => 'nullable|string|in:on',

            'supplies' => 'nullable|array',
            'supplies.*' => 'integer|exists:supplies,id',
            'design_specialties' => 'nullable|array',
            'design_specialties.*' => 'integer|exists:design_specialties,id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:station_amenities,id',

            // File Validation
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:15360',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:15360',
            'guest_policy' => 'nullable|file|mimes:png,jpeg,jpg,pdf,txt,doc,docx|max:15360',
            'gallery' => 'nullable|array|max:5',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:15360',
        ]);

        if ($validator->fails()) {
            // Redirect with all validation errors. The blade will intercept this.
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // 3. DATA CLEANUP AND MAPPING

        // Convert checkbox 'on' values to database '1' or '0'
        $getDataValue = function($key) use ($validatedData) {
            return isset($validatedData[$key]) && $validatedData[$key] === 'on' ? '1' : '0';
        };

        // Prepare data for the User model update
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

            // 4.3 Handle Many-to-Many Relationships (Pivot Tables)
            $user->supplies()->sync($validatedData['supplies'] ?? []);
            $user->stationAmenities()->sync($validatedData['amenities'] ?? []);
            $user->designSpecialties()->sync($validatedData['design_specialties'] ?? []);

            // 4.4 Handle Gallery Images (One-to-Many)
            if ($request->hasFile('gallery')) {
                $user->studioImages()->delete(); // Clear old gallery images and DB records

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

            // 4.5 Finalize Verification Status
            $user->verification_status = '2'; // Status 2: Complete profile, dashboard access granted
            $user->save();

            DB::commit();

            return redirect()->back()->with('success', 'Studio profile completed successfully!')->with('next_step', 'boost_studio');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Studio Step Form Submission Error: ' . $e->getMessage(), ['user_id' => $user->id, 'trace' => $e->getTraceAsString()]);

            // Redirect back with a system error flag
            return back()->withInput()->with('system_error', 'A critical error occurred while saving your profile. Please try again.<br>'. $e->getMessage())->with(['logo'=>$request['logo'], 'cover' => $request['cover']]);
        }
    }

    public function boostStudio()
    {
        return view('user.common.boost_studio');
    }

    public function forgotPassword()
    {
        return view('user.common.forgot_password');
    }

    public function resetPassword()
    {
        return view('user.common.reset_password');
    }

    public function verifyOtp()
    {
        return view('user.common.verify_otp');
    }

    public function forgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found in our records.']);
        }

        // Generate 4 digit OTP
        $otp = rand(1000, 9999);

        // Save OTP in user table
        $user->otp = $otp;
        $user->save();
        session(['email' => $request->email]);

        // Redirect to verify otp page
        $role = $user->role_id??'artist';

        Mail::send('emails.reset-password-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your Verification Code');
        });
        // Redirect to verify_otp page with role param
        return redirect()->to(route('verify_otp', ['role' => $role]))
            ->with(['email' => $user->email]);
    }

    public function verifyOtpSubmit(Request $request)
    {
        $otp = $request->otp1 . $request->otp2 . $request->otp3 . $request->otp4;

        $request->merge(['otp' => $otp]);

        $request->validate([
            'otp' => 'required|digits:4',
        ]);
        session(['email' => $request->email]);

        $user = User::where('otp', $otp)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid OTP, please try again.']);
        }

        return redirect()->route('reset_password', ['role' => $request->role])
            ->with('success', 'OTP verified successfully!');
    }

    public function resetPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Password and Confirm Password must match.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);


        $email = session('email');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->otp = null;
            $user->save();

            return redirect()->route('form_login_signup', ['role' => $request->role ?? 'artist'])
                ->with('success', 'Password updated successfully, please login.');
        }

        return back()->withErrors(['password' => 'Something went wrong, please try again.']);
    }

    public function resendOtp(Request $request)
    {
        $email = $request->email ?? session('email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please go back and try again.'
            ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();

        Mail::send('emails.reset-password-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your Verification Code');
        });
        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to your email.'
        ]);
    }

    public function verifyDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'front_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'back_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Back image optional hai
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        if ($request->hasFile('front_image')) {
            if ($user->document_front) { Storage::disk('public')->delete($user->document_front); }
            $path = $request->file('front_image')->store('user_documents', 'public');
            $user->document_front = $path;
        }

        if ($request->hasFile('back_image')) {
            if ($user->document_back) { Storage::disk('public')->delete($user->document_back); }
            $path = $request->file('back_image')->store('user_documents', 'public');
            $user->document_back = $path;
        }

        $user->verification_status = '2';
        $user->otp = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded and verified successfully!',
            'redirect_url' => route('dashboard.explore') // Dashboard ka route
        ]);
    }


    /**
     * OTP generate karke database mein save karne ke liye.
     */
    public function generateOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.']);
        }

        $otp = rand(1000, 9999);

        $user->otp = $otp;
        $user->save();

        // Send email directly using the Blade view
        Mail::send('emails.reset-password-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your Verification Code');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP generated and sent successfully.'
        ]);
    }

    public function authVerifyOtpSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|min:4|max:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();

        if ($user && $user->otp == $request->otp) {

            $user->verification_status = '2';
            $user->otp = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully! Redirecting...',
                'redirect_url' => route('dashboard.explore')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The OTP you entered is incorrect. Please try again.'
        ], 400);
    }

    public function authResendOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();

        Mail::send('emails.reset-password-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your Verification Code');
        });

        return response()->json([
            'success' => true,
            'message' => 'A new OTP has been sent to you.'
        ]);
    }

    public function authVerifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), ['phone' => 'required|string|min:10']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $user->phone = $request->phone;
//        $user->phone_verified_at = now();
        $user->verification_status = '2';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully!',
            'redirect_url' => route('dashboard.explore')
        ]);
    }
}
