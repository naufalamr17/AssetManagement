<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;

class AzureAuthController extends Controller
{
    public function redirectToAzure()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function handleAzureCallback()
    {
        try {
            $user = Socialite::driver('azure')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to login with Azure. Please try again.');
        }

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            Auth::login($existingUser);
            return redirect()->intended('dashboard');
        } else {
            return redirect()->route('login')->with('error', 'User belum terdaftar silahkan menghubungi admin.');
        }
    }
}
