<?php

use App\Models\User;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can update language preference and it translates', function () {
    $user = User::create([
        'name' => 'Athlete User',
        'email' => 'athlete@example.com',
        'password' => bcrypt('password123'),
        'xp_total' => 0,
        'level' => 1,
        'locale' => 'en',
    ]);

    $this->actingAs($user);

    // Initial check
    expect(App::getLocale())->toBe('en');

    // Switch to id
    Livewire::test('pages::settings.language')
        ->set('locale', 'id')
        ->call('updateLanguage');

    $user->refresh();
    expect($user->locale)->toBe('id');
    expect(App::getLocale())->toBe('id');
});
