<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can upload a profile photo and it is saved', function () {
    Storage::fake('public');

    $user = User::create([
        'name' => 'Athlete User',
        'email' => 'athlete@example.com',
        'password' => bcrypt('password123'),
        'xp_total' => 0,
        'level' => 1,
    ]);

    $this->actingAs($user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    Livewire::test('pages::settings.profile')
        ->set('name', 'Athlete Updated')
        ->set('photo', $file)
        ->call('updateProfileInformation');

    $user->refresh();

    expect($user->name)->toBe('Athlete Updated');
    expect($user->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('user can delete their profile photo', function () {
    Storage::fake('public');

    $user = User::create([
        'name' => 'Athlete User',
        'email' => 'athlete@example.com',
        'password' => bcrypt('password123'),
        'profile_photo_path' => 'profile-photos/old-avatar.jpg',
    ]);

    Storage::disk('public')->put('profile-photos/old-avatar.jpg', 'fake content');

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->call('deleteProfilePhoto');

    $user->refresh();

    expect($user->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing('profile-photos/old-avatar.jpg');
});
