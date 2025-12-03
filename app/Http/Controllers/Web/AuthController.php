<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string',
        ]);

        $roleName = $request->query('role', 'artist');

        $roleId = ($roleName === 'studio') ? 'studio' : 'artist';


        // 3. Reverse Geocode the location
        $location = $this->reverseGeocode($request->latitude, $request->longitude);

        dd($location);
        $user = User::create([
            'name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $location['city'] ?? null,
            'country' => $location['country'] ?? $request->country_region,
            'phone' => $request->phone_number,
            'role_id' => $roleId,
            'user_type' => $roleId,
        ]);

        Auth::login($user);

        return redirect()->route('choose_plan');

    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|string',
        ]);

        $requestedRole = strtolower($request->input('role'));
        $validRoles = ['artist', 'studio'];

        if (!in_array($requestedRole, $validRoles)) {
            return back()->withErrors([
                'email' => 'Please select a valid role: Artist or Studio.',
            ])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $userRole = strtolower($user->role_id);

            if ($userRole !== $requestedRole) {
                Auth::logout();
                return back()->withErrors([
                    'email' => "You cannot log in as $requestedRole with these credentials.",
                ])->onlyInput('email');
            }

            // ✅ Save Latitude & Longitude on login
            if (($request->filled('latitude') && $request->filled('longitude')) && $userRole == 'artist') {
                $user->latitude = $request->latitude;
                $user->longitude = $request->longitude;
                $user->save();
            }

            // Redirect user according to role
            if ($userRole === 'artist') {
                return redirect()->intended(route('dashboard.explore'));
            } elseif ($userRole === 'studio') {
                return redirect()->intended(route('dashboard.studio_home'));
            }
        }

        return back()->withErrors([
            'email' => 'Invalid login details. Please check your email or password.',
        ])->onlyInput('email');

    }

    public function reverseGeocode($lat, $lng): array
    {
        // 1. Check for valid coordinates
        if (empty($lat) || empty($lng)) {
            return ['city' => null, 'country' => null];
        }

        // 2. Get API Key and build URL
        $apiKey = config('services.google_maps.key') ?? env('GOOGLE_MAPS_API_KEY');
        if (empty($apiKey)) {
            Log::warning('Google Maps API key is missing for reverse geocoding.');
            return ['city' => null, 'country' => null];
        }

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

        try {
            // 3. Make API Call
            $response = Http::get($url);
            $data = $response->json();

            $city = null;
            $country = null;

            // 4. Parse Response
            if ($data['status'] === 'OK' && !empty($data['results'])) {
                foreach ($data['results'][0]['address_components'] as $component) {
                    // Find Country Name
                    if (in_array('country', $component['types'])) {
                        $country = $component['long_name'];
                    }
                    // Find City/Locality Name
                    if (in_array('locality', $component['types']) || in_array('postal_town', $component['types'])) {
                        $city = $component['long_name'];
                    }
                }
            }

            return ['city' => $city, 'country' => $country];

        } catch (\Exception $e) {
            Log::error('Reverse Geocoding Failed: ' . $e->getMessage());
            return ['city' => null, 'country' => null];
        }
    }


    public function logout(Request $request)
    {
        $role = Auth::user()->role_id ?? null;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role == 'artist') {
            return redirect()->route('login', ['role' => 'artist']);
        } elseif ($role == 'studio') {
            return redirect()->route('login', ['role' => 'studio']);
        }
        return redirect()->route('login');
    }

}
