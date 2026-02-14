<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        \Illuminate\Support\Facades\Log::info("SocialAuth: Callback started for $provider");

        try {
            $socialUser = Socialite::driver($provider)->user();
            \Illuminate\Support\Facades\Log::info("SocialAuth: User retrieved", ['email' => $socialUser->getEmail(), 'id' => $socialUser->getId()]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SocialAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }

        try {
        try {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'avatar' => $socialUser->getAvatar(),
                    "{$provider}_id" => $socialUser->getId(),
                    'password' => bcrypt(str()->random(24)),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update avatar and provider ID, but KEEP existing name
                $user->update([
                    'avatar' => $socialUser->getAvatar(),
                    "{$provider}_id" => $socialUser->getId(),
                ]);
            }
            
            \Illuminate\Support\Facades\Log::info("SocialAuth: Database user updated/created", ['id' => $user->id]);

            Auth::login($user, true);
            \Illuminate\Support\Facades\Log::info("SocialAuth: Auth::login called");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SocialAuth Database/Login Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login error: ' . $e->getMessage());
        }

        return redirect()->intended('/dashboard');
    }
}
