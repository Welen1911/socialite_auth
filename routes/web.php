<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logged', function () {
    dd(auth()->user());
});

Route::get('/auth/{provider}/redirect', function (string $provider) {
    return Socialite::driver($provider)->redirect();
})->name('socialite.login');

Route::get('/auth/{provider}/callback', function (string $provider) {
    $providerUser = Socialite::driver($provider)->user();

    $user = User::updateOrCreate([
        'email' => $providerUser->getEmail(),
    ], [
        'provider_id' => $providerUser->getId(),
        'name' => $providerUser->getName() ? $providerUser->getName() : $providerUser->getNickname(),
        'provider_avatar' => $providerUser->getAvatar(),
        'provider_name' => $provider,
    ]);

    Auth::login($user);
    return redirect('/logged');
    // $user->token
});
