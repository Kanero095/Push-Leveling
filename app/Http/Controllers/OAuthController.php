<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Simulate OAuth provider login and automatically log in or create a mock user.
     */
    public function handleMockLogin($provider)
    {
        $provider = strtolower($provider);
        if (! in_array($provider, ['google', 'github'])) {
            abort(404);
        }

        // Generate a random mock user name and email
        $randomId = rand(10, 99);
        $name = 'OAuth '.ucfirst($provider).' User '.$randomId;
        $email = $provider.'_user_'.$randomId.'@example.com';

        // Find or create the user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(16)),
                'xp_total' => 0,
                'level' => 1,
                'user_level' => 'beginner',
            ]
        );

        Auth::login($user);

        session()->flash('status', 'Successfully logged in via '.ucfirst($provider).'! Welcome, '.$name);

        return redirect()->route('dashboard');
    }
}
