<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            Auth::login($existingUser);
            return redirect(RouteServiceProvider::HOME);
        }

        // If the user does not exist, create a new user
        $user = User::create([
            'username' => $googleUser->name,
            'email' => $googleUser->email,
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make(rand(100000, 999999)),
            'profile' => $googleUser->avatar,
        ]);

        // Log in the newly created user
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
