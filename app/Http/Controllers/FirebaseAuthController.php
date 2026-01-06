<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FirebaseAuthController extends Controller
{
    /**
     * Verify Firebase ID token and login/register user
     */
    public function verify(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            $idToken = $request->idToken;
            $projectId = config('services.firebase.project_id');
            
            // Verify Firebase ID token using Google's public keys
            // First, decode the token to get the payload (without verification for user data)
            $tokenParts = explode('.', $idToken);
            
            if (count($tokenParts) !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format token tidak valid',
                ], 401);
            }
            
            // Decode payload (middle part)
            $payload = json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);
            
            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal decode token',
                ], 401);
            }
            
            // Verify token with Firebase/Google
            // Using securetoken.google.com endpoint
            $response = Http::asForm()->post('https://identitytoolkit.googleapis.com/v1/accounts:lookup', [
                'key' => config('services.firebase.api_key'),
                'idToken' => $idToken,
            ]);
            
            if (!$response->successful()) {
                \Log::error('Firebase token verification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Verifikasi token gagal',
                ], 401);
            }
            
            $userData = $response->json();
            
            if (!isset($userData['users'][0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ], 401);
            }
            
            $firebaseUser = $userData['users'][0];
            $email = $firebaseUser['email'] ?? null;
            $name = $firebaseUser['displayName'] ?? $email;
            $avatar = $firebaseUser['photoUrl'] ?? null;
            $googleId = $firebaseUser['localId'] ?? $payload['sub'];
            
            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak tersedia dari akun Google',
                ], 400);
            }
            
            // Cari atau buat user
            $user = User::where('google_id', $googleId)
                        ->orWhere('email', $email)
                        ->first();

            if ($user) {
                // Update google_id dan avatar jika belum ada
                $user->update([
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'role' => 'user',
                    'password' => null,
                ]);
            }

            // Login user
            Auth::login($user, true);

            // Determine redirect URL based on role
            $redirectUrl = $user->role === 'admin' ? '/admin' : '/';

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Firebase Auth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
